<?php

function pk_link_request_activation()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'pk_link_requests';
    
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        link_name varchar(200) NOT NULL,
        link_url varchar(500) NOT NULL,
        link_description varchar(500) DEFAULT '',
        link_notes varchar(500) DEFAULT '',
        link_contact varchar(200) DEFAULT '',
        link_image varchar(500) DEFAULT '',
        link_visible enum('pending','approved','rejected') DEFAULT 'pending',
        request_ip varchar(50) DEFAULT '',
        request_time datetime DEFAULT CURRENT_TIMESTAMP,
        approve_time datetime DEFAULT NULL,
        approve_by int(11) DEFAULT NULL,
        reject_reason varchar(500) DEFAULT '',
        wp_link_id mediumint(9) DEFAULT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

register_activation_hook(__FILE__, 'pk_link_request_activation');

add_action('after_setup_theme', 'pk_link_request_init');
function pk_link_request_init()
{
    pk_link_request_activation();
    
    pk_ajax_register('pk_link_request_submit', 'pk_ajax_link_request_submit', true);
    pk_ajax_register('pk_link_request_list', 'pk_ajax_link_request_list');
    pk_ajax_register('pk_link_request_approve', 'pk_ajax_link_request_approve');
    pk_ajax_register('pk_link_request_reject', 'pk_ajax_link_request_reject');
    pk_ajax_register('pk_link_request_delete', 'pk_ajax_link_request_delete');
    pk_ajax_register('pk_link_request_approve_all', 'pk_ajax_link_request_approve_all');
    pk_ajax_register('pk_link_request_clear_rejected', 'pk_ajax_link_request_clear_rejected');
    pk_ajax_register('pk_link_request_batch_approve', 'pk_ajax_link_request_batch_approve');
    pk_ajax_register('pk_link_request_batch_reject', 'pk_ajax_link_request_batch_reject');
    pk_ajax_register('pk_link_request_batch_delete', 'pk_ajax_link_request_batch_delete');
    pk_ajax_register('pk_link_request_batch_delete_all', 'pk_ajax_link_request_batch_delete_all');
}

function pk_ajax_link_request_submit()
{
    $body = pk_ajax_get_req_body();
    
    $link_name = sanitize_text_field($body['link_name'] ?? '');
    $link_url = esc_url_raw($body['link_url'] ?? '');
    $link_description = sanitize_text_field($body['link_description'] ?? '');
    $link_notes = sanitize_text_field($body['link_notes'] ?? '');
    $link_contact = sanitize_email($body['link_contact'] ?? '');
    $link_image = esc_url_raw($body['link_image'] ?? '');
    
    if (empty($link_name) || empty($link_url)) {
        wp_send_json_error('站点名称和站点URL不能为空');
    }
    
    if (!filter_var($link_url, FILTER_VALIDATE_URL)) {
        wp_send_json_error('请输入有效的站点URL');
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'pk_link_requests';
    
    $wpdb->insert($table_name, array(
        'link_name' => $link_name,
        'link_url' => $link_url,
        'link_description' => $link_description,
        'link_notes' => $link_notes,
        'link_contact' => $link_contact,
        'link_image' => $link_image,
        'request_ip' => $_SERVER['REMOTE_ADDR'] ?? '',
        'link_visible' => 'pending'
    ));
    
    if ($wpdb->insert_id) {
        wp_send_json_success('申请已提交，将在后台审核后显示');
    } else {
        wp_send_json_error('提交失败，请稍后重试');
    }
}

function pk_ajax_link_request_list()
{
    if (!current_user_can('manage_options')) {
        wp_send_json_error('权限不足');
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'pk_link_requests';
    
    $status = sanitize_text_field($_GET['status'] ?? 'pending');
    $page = intval($_GET['page'] ?? 1);
    $per_page = intval($_GET['per_page'] ?? 20);
    
    // 使用正确的prepare方式处理状态参数
    $total = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE link_visible = %s",
        $status
    ));
    
    $offset = ($page - 1) * $per_page;
    $requests = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name WHERE link_visible = %s ORDER BY request_time DESC LIMIT %d OFFSET %d",
        $status,
        $per_page,
        $offset
    ));
    
    wp_send_json_success(array(
        'data' => $requests,
        'total' => $total,
        'page' => $page,
        'per_page' => $per_page,
        'total_pages' => ceil($total / $per_page)
    ));
}

// 修改邮件发送函数，添加更详细的日志和错误处理
function pk_send_link_request_email($request, $status, $reason = '') {
    if (empty($request->link_contact)) {
        error_log("[友链邮件] 跳过发送：未提供邮箱");
        return false; // 没有邮箱，无法发送邮件
    }
    
    $blogname = get_option('blogname');
    $wp_email = 'no-reply@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));
    $to = $request->link_contact;
    
    if ($status == 'approved') {
        $subject = '您在 [' . $blogname . '] 的友链申请已通过';
        $message = '<p>您好，</p>';
        $message .= '<p>您在 <strong>' . $blogname . '</strong> 的友链申请已通过审核！</p>';
        $message .= '<p>申请信息：</p>';
        $message .= '<ul>';
        $message .= '<li>站点名称：' . $request->link_name . '</li>';
        $message .= '<li>站点URL：' . $request->link_url . '</li>';
        $message .= '<li>站点描述：' . $request->link_description . '</li>';
        $message .= '</ul>';
        $message .= '<p>感谢您的关注和支持！</p>';
    } else if ($status == 'rejected') {
        $subject = '您在 [' . $blogname . '] 的友链申请未通过';
        $message = '<p>您好，</p>';
        $message .= '<p>您在 <strong>' . $blogname . '</strong> 的友链申请未通过审核。</p>';
        $message .= '<p>拒绝原因：' . $reason . '</p>';
        $message .= '<p>申请信息：</p>';
        $message .= '<ul>';
        $message .= '<li>站点名称：' . $request->link_name . '</li>';
        $message .= '<li>站点URL：' . $request->link_url . '</li>';
        $message .= '<li>站点描述：' . $request->link_description . '</li>';
        $message .= '</ul>';
        $message .= '<p>感谢您的关注！</p>';
    }
    
    $headers = array(
        'From: "' . $blogname . '" <' . $wp_email . '>',
        'Reply-To: ' . $wp_email,
        'Content-Type: text/html; charset=' . get_option('blog_charset')
    );
    
    // 添加详细日志
    error_log("[友链邮件] 准备发送：收件人=$to, 主题=$subject, 状态=$status");
    
    // 发送邮件并捕获可能的错误
    $result = wp_mail($to, $subject, $message, $headers);
    
    // 记录发送结果
    error_log("[友链邮件] 发送" . ($result ? "成功" : "失败") . ": $to - $status");
    
    // 如果发送失败，尝试使用备用方式或记录更详细信息
    if (!$result) {
        // 检查wp_mail错误
        if (function_exists('error_get_last')) {
            $last_error = error_get_last();
            if ($last_error) {
                error_log("[友链邮件] 详细错误信息: " . print_r($last_error, true));
            }
        }
    }
    
    return $result;
}

function pk_ajax_link_request_approve()
{
    if (!current_user_can('manage_options')) {
        wp_send_json_error('权限不足');
    }
    
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        wp_send_json_error('无效的申请ID');
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'pk_link_requests';
    
    $request = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));
    if (!$request) {
        wp_send_json_error('申请不存在');
    }
    
    // 检查申请状态，只有待审核或已拒绝的申请才能被通过
    if ($request->link_visible == 'approved') {
        wp_send_json_error('该申请已通过审核，无需重复操作');
    }
    
    $link_cid = pk_get_option('index_link_id', '');
    if (empty($link_cid)) {
        wp_send_json_error('请先在主题设置中配置友链分类ID');
    }
    
    $link_data = array(
        'link_name' => $request->link_name,
        'link_url' => $request->link_url,
        'link_description' => $request->link_description,
        'link_notes' => $request->link_notes,
        'link_image' => $request->link_image,
        'link_rel' => '',
        'link_target' => '_blank',
        'link_category' => $link_cid,
        'link_visible' => 'Y'
    );
    
    $result = wp_insert_link($link_data);
    
    if (is_wp_error($result)) {
        wp_send_json_error('添加友链失败: ' . $result->get_error_message());
    }
    
    $update_result = $wpdb->update($table_name, array(
        'link_visible' => 'approved',
        'approve_time' => current_time('mysql'),
        'approve_by' => get_current_user_id(),
        'wp_link_id' => $result
    ), array('id' => $id));
    
    if ($update_result === false) {
        // 记录详细错误信息到日志
        error_log('[友链审核] 更新状态失败: ' . $wpdb->last_error . ', ID: ' . $id);
        wp_send_json_error('更新申请状态失败: ' . $wpdb->last_error);
    }
    
    // 发送通过邮件
    pk_send_link_request_email($request, 'approved');
    
    pk_cache_delete(PKC_FOOTER_LINKS);
    
    wp_send_json_success('审核通过，已添加到友链');
}

function pk_ajax_link_request_reject()
{
    if (!current_user_can('manage_options')) {
        wp_send_json_error('权限不足');
    }
    
    $id = intval($_POST['id'] ?? 0);
    $reason = sanitize_textarea_field($_POST['reason'] ?? '');
    
    if ($id <= 0) {
        wp_send_json_error('无效的申请ID');
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'pk_link_requests';
    
    $request = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));
    if (!$request) {
        wp_send_json_error('申请不存在');
    }
    
    $result = $wpdb->update($table_name, array(
        'link_visible' => 'rejected',
        'reject_reason' => $reason
    ), array('id' => $id));
    
    if ($result !== false) {
        // 发送拒绝邮件
        pk_send_link_request_email($request, 'rejected', $reason);
        wp_send_json_success('已拒绝该申请');
    } else {
        wp_send_json_error('操作失败');
    }
}

function pk_ajax_link_request_delete()
{
    if (!current_user_can('manage_options')) {
        wp_send_json_error('权限不足');
    }
    
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        wp_send_json_error('无效的申请ID');
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'pk_link_requests';
    
    // 获取申请记录
    $request = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));
    if (!$request) {
        wp_send_json_error('申请不存在');
    }
    
    // 如果是已通过的申请，同时删除WordPress原生链接
    if ($request->link_visible == 'approved' && !empty($request->wp_link_id)) {
        wp_delete_link($request->wp_link_id);
    }
    
    $result = $wpdb->delete($table_name, array('id' => $id));
    
    if ($result) {
        // 清除缓存
        pk_cache_delete(PKC_FOOTER_LINKS);
        wp_send_json_success('删除成功');
    } else {
        wp_send_json_error('删除失败');
    }
}

function pk_ajax_link_request_approve_all()
{
    if (!current_user_can('manage_options')) {
        wp_send_json_error('权限不足');
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'pk_link_requests';
    
    $pending_requests = $wpdb->get_results("SELECT * FROM $table_name WHERE link_visible = 'pending'");
    
    if (empty($pending_requests)) {
        wp_send_json_success('没有待审核的申请');
    }
    
    $link_cid = pk_get_option('index_link_id', '');
    if (empty($link_cid)) {
        wp_send_json_error('请先在主题设置中配置友链分类ID');
    }
    
    $success_count = 0;
    foreach ($pending_requests as $request) {
        $link_data = array(
            'link_name' => $request->link_name,
            'link_url' => $request->link_url,
            'link_description' => $request->link_description,
            'link_notes' => $request->link_notes,
            'link_image' => $request->link_image,
            'link_rel' => '',
            'link_target' => '_blank',
            'link_category' => $link_cid,
            'link_visible' => 'Y'
        );
        
        $result = wp_insert_link($link_data);
        if (!is_wp_error($result)) {
            $update_result = $wpdb->update($table_name, array(
                'link_visible' => 'approved',
                'approve_time' => current_time('mysql'),
                'approve_by' => get_current_user_id(),
                'wp_link_id' => $result
            ), array('id' => $request->id));
            if ($update_result !== false) {
                // 发送通过邮件
                pk_send_link_request_email($request, 'approved');
                $success_count++;
            } else {
                // 记录详细错误信息到日志
                error_log('[友链审核] 批量更新状态失败: ' . $wpdb->last_error . ', ID: ' . $request->id);
            }
        }
    }
    
    pk_cache_delete(PKC_FOOTER_LINKS);
    
    wp_send_json_success("已批量审核通过 $success_count 个友链申请");
}

function pk_ajax_link_request_clear_rejected()
{
    if (!current_user_can('manage_options')) {
        wp_send_json_error('权限不足');
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'pk_link_requests';
    
    $result = $wpdb->delete($table_name, array('link_visible' => 'rejected'));
    
    if ($result !== false) {
        wp_send_json_success('已清空所有已拒绝的申请记录');
    } else {
        wp_send_json_error('清空记录失败');
    }
}

// 批量通过申请
function pk_ajax_link_request_batch_approve()
{
    if (!current_user_can('manage_options')) {
        wp_send_json_error('权限不足');
    }
    
    $ids = isset($_POST['ids']) ? array_map('intval', $_POST['ids']) : array();
    if (empty($ids)) {
        wp_send_json_error('请选择要通过的申请');
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'pk_link_requests';
    
    $link_cid = pk_get_option('index_link_id', '');
    if (empty($link_cid)) {
        wp_send_json_error('请先在主题设置中配置友链分类ID');
    }
    
    $success_count = 0;
    foreach ($ids as $id) {
        $request = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));
        if (!$request || $request->link_visible == 'approved') {
            // 跳过不存在或已通过的申请
            continue;
        }
        
        $link_data = array(
            'link_name' => $request->link_name,
            'link_url' => $request->link_url,
            'link_description' => $request->link_description,
            'link_notes' => $request->link_notes,
            'link_image' => $request->link_image,
            'link_rel' => '',
            'link_target' => '_blank',
            'link_category' => $link_cid,
            'link_visible' => 'Y'
        );
        
        $result = wp_insert_link($link_data);
        if (!is_wp_error($result)) {
            $update_result = $wpdb->update($table_name, array(
                'link_visible' => 'approved',
                'approve_time' => current_time('mysql'),
                'approve_by' => get_current_user_id(),
                'wp_link_id' => $result
            ), array('id' => $id));
            
            if ($update_result !== false) {
                // 发送通过邮件
                pk_send_link_request_email($request, 'approved');
                $success_count++;
            } else {
                // 记录详细错误信息到日志
                error_log('[友链审核] 批量更新状态失败: ' . $wpdb->last_error . ', ID: ' . $id);
            }
        }
    }
    
    pk_cache_delete(PKC_FOOTER_LINKS);
    
    wp_send_json_success("已批量通过 $success_count 个友链申请");
}

// 批量拒绝申请
function pk_ajax_link_request_batch_reject()
{
    if (!current_user_can('manage_options')) {
        wp_send_json_error('权限不足');
    }
    
    $ids = isset($_POST['ids']) ? array_map('intval', $_POST['ids']) : array();
    if (empty($ids)) {
        wp_send_json_error('请选择要拒绝的申请');
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'pk_link_requests';
    
    $success_count = 0;
    foreach ($ids as $id) {
        $request = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));
        if (!$request) {
            continue;
        }
        
        $result = $wpdb->update($table_name, array(
            'link_visible' => 'rejected',
            'reject_reason' => '批量拒绝'
        ), array('id' => $id));
        
        if ($result !== false) {
            // 发送拒绝邮件
            pk_send_link_request_email($request, 'rejected', '批量拒绝');
            $success_count++;
        }
    }
    
    wp_send_json_success("已批量拒绝 $success_count 个友链申请");
}

// 批量删除申请
function pk_ajax_link_request_batch_delete()
{
    if (!current_user_can('manage_options')) {
        wp_send_json_error('权限不足');
    }
    
    $ids = isset($_POST['ids']) ? array_map('intval', $_POST['ids']) : array();
    if (empty($ids)) {
        wp_send_json_error('请选择要删除的申请');
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'pk_link_requests';
    
    $success_count = 0;
    foreach ($ids as $id) {
        // 获取申请记录
        $request = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));
        if (!$request) {
            continue;
        }
        
        // 如果是已通过的申请，同时删除WordPress原生链接
        if ($request->link_visible == 'approved' && !empty($request->wp_link_id)) {
            wp_delete_link($request->wp_link_id);
        }
        
        $result = $wpdb->delete($table_name, array('id' => $id));
        if ($result) {
            $success_count++;
        }
    }
    
    pk_cache_delete(PKC_FOOTER_LINKS);
    
    wp_send_json_success("已批量删除 $success_count 个友链申请");
}

// 批量删除所有指定状态的申请
function pk_ajax_link_request_batch_delete_all()
{
    if (!current_user_can('manage_options')) {
        wp_send_json_error('权限不足');
    }
    
    $status = sanitize_text_field($_POST['status'] ?? '');
    if (empty($status)) {
        wp_send_json_error('请指定要删除的状态');
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'pk_link_requests';
    
    // 获取所有指定状态的记录
    $requests = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE link_visible = %s", $status));
    
    $success_count = 0;
    foreach ($requests as $request) {
        // 如果是已通过的申请，同时删除WordPress原生链接
        if ($request->link_visible == 'approved' && !empty($request->wp_link_id)) {
            wp_delete_link($request->wp_link_id);
        }
        
        $result = $wpdb->delete($table_name, array('id' => $request->id));
        if ($result) {
            $success_count++;
        }
    }
    
    pk_cache_delete(PKC_FOOTER_LINKS);
    
    wp_send_json_success("已批量删除 $success_count 个友链申请");
}

add_action('admin_menu', 'pk_link_request_admin_menu');
function pk_link_request_admin_menu()
{
    add_menu_page(
        '友链审核',
        '友链审核',
        'manage_options',
        'pk-link-requests',
        'pk_link_request_admin_page',
        'dashicons-admin-links',
        30
    );
}

function pk_link_request_admin_page()
{
    ?>
    <div class="wrap">
        <h1>友链申请审核</h1>
        <style>
            .pk-link-requests { margin-top: 20px; }
            .pk-link-requests .tab-nav { margin-bottom: 20px; }
            .pk-link-requests .tab-nav a { 
                padding: 8px 16px; 
                margin-right: 5px; 
                text-decoration: none;
                border: 1px solid #ccc;
                background: #f0f0f0;
            }
            .pk-link-requests .tab-nav a.active {
                background: #2271b1;
                color: #fff;
                border-color: #2271b1;
            }
            .pk-link-requests table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            .pk-link-requests th, .pk-link-requests td { 
                padding: 12px; 
                text-align: left; 
                border-bottom: 1px solid #ddd; 
            }
            .pk-link-requests th { background: #f5f5f5; }
            .pk-link-requests .status-pending { color: #ff9800; font-weight: bold; }
            .pk-link-requests .status-approved { color: #4caf50; font-weight: bold; }
            .pk-link-requests .status-rejected { color: #f44336; font-weight: bold; }
            .pk-link-requests .actions button {
                margin-right: 5px;
                padding: 5px 10px;
                cursor: pointer;
            }
            .pk-link-requests .pagination {
                margin-top: 20px;
                display: flex;
                gap: 5px;
            }
            .pk-link-requests .pagination a, 
            .pk-link-requests .pagination span {
                padding: 5px 10px;
                border: 1px solid #ddd;
                text-decoration: none;
            }
            .pk-link-requests .pagination .current {
                background: #2271b1;
                color: #fff;
                border-color: #2271b1;
            }
            .pk-link-requests .approve-all-btn {
                float: right;
                margin-bottom: 15px;
            }
            
            #pk-reject-modal h3 {
                margin-top: 0;
                margin-bottom: 15px;
            }
            
            #pk-reject-modal textarea {
                width: 100%;
                height: 80px;
                padding: 8px;
                border: 1px solid #ddd;
                border-radius: 4px;
                resize: vertical;
            }
            
            #pk-reject-modal .button {
                margin-top: 10px;
            }
            
            #pk-reject-modal .button-primary {
                margin-right: 10px;
            }
        </style>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script>
        toastr.options = {
            closeButton: true,
            debug: false,
            newestOnTop: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            preventDuplicates: false,
            onclick: null,
            showDuration: '300',
            hideDuration: '1000',
            timeOut: '3000',
            extendedTimeOut: '1000',
            showEasing: 'swing',
            hideEasing: 'linear',
            showMethod: 'fadeIn',
            hideMethod: 'fadeOut'
        };
        </script>
        
        <div class="pk-link-requests">
            <div class="tab-nav">
                <a href="#" data-status="pending" class="active">待审核 (<span id="pending-count">0</span>)</a>
                <a href="#" data-status="approved">已通过</a>
                <a href="#" data-status="rejected">已拒绝</a>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th><input type="checkbox" id="pk-select-all" /></th>
                        <th>ID</th>
                        <th>站点名称</th>
                        <th>站点URL</th>
                        <th>站点描述</th>
                        <th>联系邮箱</th>
                        <th>申请时间</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody id="pk-requests-body">
                    <tr><td colspan="8">加载中...</td></tr>
                </tbody>
            </table>
            
            <div class="pagination-container" style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center;">
                <div class="pagination" id="pk-pagination"></div>
                <div class="bulk-actions" style="display: flex; gap: 10px; align-items: center;">
                    <select id="pk-bulk-action" style="padding: 5px;">
                        <option value="">批量操作</option>
                    </select>
                    <button class="button button-primary" id="pk-bulk-apply">应用</button>
                </div>
            </div>
        </div>
        
        <div id="pk-reject-modal" style="display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:#fff;padding:20px;border:1px solid #ddd;z-index:1000;box-shadow:0 0 10px rgba(0,0,0,0.3);">
            <h3>拒绝申请</h3>
            <textarea id="pk-reject-reason" placeholder="请输入拒绝原因" style="width:300px;height:100px;margin:10px 0;"></textarea>
            <br>
            <button class="button button-primary" id="pk-confirm-reject">确认拒绝</button>
            <button class="button" onclick="jQuery('#pk-reject-modal, #pk-modal-overlay').hide()">取消</button>
        </div>
        
        <div id="pk-confirm-modal" style="display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:#fff;padding:20px;border:1px solid #ddd;z-index:1000;box-shadow:0 0 10px rgba(0,0,0,0.3);">
            <h3 id="pk-confirm-title">确认操作</h3>
            <div id="pk-confirm-message" style="margin:10px 0;"></div>
            <br>
            <button class="button button-primary" id="pk-confirm-action">确认</button>
            <button class="button" onclick="jQuery('#pk-confirm-modal, #pk-modal-overlay').hide()">取消</button>
        </div>
        
        <div id="pk-modal-overlay" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:999;"></div>
        
        <script>
        jQuery(document).ready(function($) {
            var currentStatus = 'pending';
            var currentPage = 1;
            
            function loadRequests() {
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'GET',
                    data: {
                        action: 'pk_link_request_list',
                        status: currentStatus,
                        page: currentPage,
                        per_page: 20
                    },
                    success: function(res) {
                        if (res.success) {
                            renderRequests(res.data);
                            updateCounts();
                        }
                    }
                });
            }
            
            function updateCounts() {
                // 只更新待审核计数，因为其他标签页没有计数元素
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'GET',
                    data: {
                        action: 'pk_link_request_list',
                        status: 'pending',
                        page: 1,
                        per_page: 1
                    },
                    success: function(res) {
                        if (res.success) {
                            $('#pending-count').text(res.data.total);
                        }
                    }
                });
            }
            
            function renderRequests(data) {
                var html = '';
                if (data.data.length === 0) {
                    html = '<tr><td colspan="9" style="text-align:center;color:#999;">暂无数据</td></tr>';
                } else {
                    data.data.forEach(function(item) {
                        var statusClass = 'status-' + item.link_visible;
                        var statusText = {
                            'pending': '待审核',
                            'approved': '已通过',
                            'rejected': '已拒绝'
                        }[item.link_visible] || item.link_visible;
                        
                        html += '<tr>';
                        html += '<td><input type="checkbox" class="pk-request-checkbox" data-id="' + item.id + '" /></td>';
                        html += '<td>' + item.id + '</td>';
                        html += '<td>' + item.link_name + '</td>';
                        html += '<td><a href="' + item.link_url + '" target="_blank">' + item.link_url + '</a></td>';
                        html += '<td>' + (item.link_notes || '-') + '</td>';
                        html += '<td>' + (item.link_contact || '-') + '</td>';
                        html += '<td>' + item.request_time + '</td>';
                        html += '<td class="' + statusClass + '">' + statusText + '</td>';
                        html += '<td class="actions">';
                        if (item.link_visible === 'pending') {
                            html += '<button class="button button-primary pk-approve" data-id="' + item.id + '">通过</button>';
                            html += '<button class="button pk-reject" data-id="' + item.id + '">拒绝</button>';
                            html += '<button class="button pk-delete" data-id="' + item.id + '">删除</button>';
                        } else if (item.link_visible === 'approved') {
                            html += '<button class="button pk-delete" data-id="' + item.id + '">删除</button>';
                        } else if (item.link_visible === 'rejected') {
                            html += '<button class="button button-primary pk-approve" data-id="' + item.id + '">通过</button>';
                            html += '<button class="button button-danger pk-clear" data-id="' + item.id + '">清除</button>';
                        }
                        html += '</td>';
                        html += '</tr>';
                    });
                }
                $('#pk-requests-body').html(html);
                
                // 初始化全选复选框状态
                updateSelectAllStatus();
                
                // Render pagination
                var paginationHtml = '';
                for (var i = 1; i <= data.total_pages; i++) {
                    if (i === currentPage) {
                        paginationHtml += '<span class="current">' + i + '</span>';
                    } else {
                        paginationHtml += '<a href="#" data-page="' + i + '">' + i + '</a>';
                    }
                }
                $('#pk-pagination').html(paginationHtml);
            }
            
            function getSelectedIds() {
                var selectedIds = [];
                $('.pk-request-checkbox:checked').each(function() {
                    selectedIds.push($(this).data('id'));
                });
                return selectedIds;
            }
            
            function updateSelectAllStatus() {
                var allChecked = $('.pk-request-checkbox').length > 0 && $('.pk-request-checkbox:checked').length === $('.pk-request-checkbox').length;
                $('#pk-select-all').prop('checked', allChecked);
            }
            
            // 全选/取消全选
            $('#pk-select-all').on('click', function() {
                var isChecked = $(this).prop('checked');
                $('.pk-request-checkbox').prop('checked', isChecked);
            });
            
            // 单个复选框点击事件，更新全选状态
            $('#pk-requests-body').on('click', '.pk-request-checkbox', function() {
                updateSelectAllStatus();
            });
            
            $('.tab-nav a').on('click', function(e) {
                e.preventDefault();
                currentStatus = $(this).data('status');
                currentPage = 1;
                $('.tab-nav a').removeClass('active');
                $(this).addClass('active');
                updateBulkActions();
                loadRequests();
            });
            
            $('#pk-pagination').on('click', 'a', function(e) {
                e.preventDefault();
                currentPage = $(this).data('page');
                loadRequests();
            });
            
            var rejectId = 0;
            var confirmAction = null;
            
            $('#pk-requests-body').on('click', '.pk-reject', function() {
                rejectId = $(this).data('id');
                $('#pk-reject-reason').val('');
                $('#pk-reject-modal, #pk-modal-overlay').show();
            });
            
            $('#pk-confirm-reject').on('click', function() {
                var reason = $('#pk-reject-reason').val();
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: {
                        action: 'pk_link_request_reject',
                        id: rejectId,
                        reason: reason
                    },
                    success: function(res) {
                        if (res.success) {
                            $('#pk-reject-modal, #pk-modal-overlay').hide();
                            toastr.success('已拒绝该申请');
                            loadRequests();
                        } else {
                            toastr.error(res.data || '操作失败');
                        }
                    }
                });
            });
            
            // 通用确认弹窗处理
            function showConfirmModal(title, message, callback) {
                $('#pk-confirm-title').text(title);
                $('#pk-confirm-message').text(message);
                confirmAction = callback;
                $('#pk-confirm-modal, #pk-modal-overlay').show();
            }
            
            $('#pk-confirm-action').on('click', function() {
                if (typeof confirmAction === 'function') {
                    confirmAction();
                    $('#pk-confirm-modal, #pk-modal-overlay').hide();
                }
            });
            
            $('#pk-requests-body').on('click', '.pk-approve', function() {
                var id = $(this).data('id');
                showConfirmModal('确认通过', '确定要通过该友链申请吗？', function() {
                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        type: 'POST',
                        data: {
                            action: 'pk_link_request_approve',
                            id: id
                        },
                        success: function(res) {
                            if (res.success) {
                                toastr.success(res.data);
                                loadRequests();
                            } else {
                                toastr.error(res.data || '操作失败');
                            }
                        }
                    });
                });
            });
            
            $('#pk-requests-body').on('click', '.pk-delete, .pk-clear', function() {
                var id = $(this).data('id');
                var actionText = $(this).hasClass('pk-clear') ? '清除' : '删除';
                showConfirmModal('确认' + actionText, '确定要' + actionText + '该申请吗？', function() {
                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        type: 'POST',
                        data: {
                            action: 'pk_link_request_delete',
                            id: id
                        },
                        success: function(res) {
                            if (res.success) {
                                toastr.success(actionText + '成功');
                                loadRequests();
                            } else {
                                toastr.error(res.data || '操作失败');
                            }
                        }
                    });
                });
            });
            
            $('#pk-batch-action').on('click', function() {
                var selectedIds = getSelectedIds();
                if (selectedIds.length === 0) {
                    toastr.warning('请先选择要操作的申请');
                    return;
                }
                
                if (currentStatus === 'pending') {
                    // 待审核状态：批量通过或批量拒绝
                    showConfirmModal('确认批量通过', '确定要通过选中的 ' + selectedIds.length + ' 个申请吗？', function() {
                        $.ajax({
                            url: '<?php echo admin_url('admin-ajax.php'); ?>',
                            type: 'POST',
                            data: {
                                action: 'pk_link_request_batch_approve',
                                ids: selectedIds
                            },
                            success: function(res) {
                                toastr.success(res.data);
                                loadRequests();
                            }
                        });
                    });
                } else if (currentStatus === 'rejected') {
                    // 已拒绝状态：批量通过或清空记录
                    showConfirmModal('确认批量通过', '确定要通过选中的 ' + selectedIds.length + ' 个已拒绝申请吗？', function() {
                        $.ajax({
                            url: '<?php echo admin_url('admin-ajax.php'); ?>',
                            type: 'POST',
                            data: {
                                action: 'pk_link_request_batch_approve',
                                ids: selectedIds
                            },
                            success: function(res) {
                                toastr.success(res.data);
                                loadRequests();
                            }
                        });
                    });
                } else if (currentStatus === 'approved') {
                    // 已通过状态：批量删除
                    showConfirmModal('确认批量删除', '确定要删除选中的 ' + selectedIds.length + ' 个已通过申请吗？', function() {
                        $.ajax({
                            url: '<?php echo admin_url('admin-ajax.php'); ?>',
                            type: 'POST',
                            data: {
                                action: 'pk_link_request_batch_delete',
                                ids: selectedIds
                            },
                            success: function(res) {
                                toastr.success(res.data);
                                loadRequests();
                            }
                        });
                    });
                }
            });
            
            // 根据当前状态更新批量操作下拉选项
            function updateBulkActions() {
                var $select = $('#pk-bulk-action');
                var $tableHeaderCheckbox = $('#pk-select-all');
                var $tableRowCheckboxes = $('.pk-request-checkbox');
                
                $select.empty();
                $select.css({'padding': '5px 20px 5px 5px', 'width': 'auto', 'min-width': '150px'});
                
                if (currentStatus === 'pending') {
                    // 待审核状态：批量通过、批量拒绝
                    $select.append('<option value="" disabled selected style="display:none;">批量操作</option>');
                    $select.append('<option value="batch-approve">批量通过</option>');
                    $select.append('<option value="batch-reject">批量拒绝</option>');
                    // 显示复选框
                    $tableHeaderCheckbox.show();
                    $tableRowCheckboxes.show();
                } else if (currentStatus === 'rejected') {
                    // 已拒绝状态：批量通过、清空记录
                    $select.append('<option value="" disabled selected style="display:none;">批量操作</option>');
                    $select.append('<option value="batch-approve">批量通过</option>');
                    $select.append('<option value="clear-rejected">清空记录</option>');
                    // 显示复选框
                    $tableHeaderCheckbox.show();
                    $tableRowCheckboxes.show();
                } else if (currentStatus === 'approved') {
                    // 已通过状态：批量删除
                    $select.append('<option value="batch-delete">批量删除</option>');
                    // 隐藏复选框
                    $tableHeaderCheckbox.hide();
                    $tableRowCheckboxes.hide();
                }
            }
            
            // 批量操作应用按钮点击事件
            $('#pk-bulk-apply').on('click', function() {
                var action = $('#pk-bulk-action').val();
                if (!action) {
                    toastr.warning('请选择批量操作类型');
                    return;
                }
                
                var actionText, confirmText, ajaxAction, ajaxData;
                
                switch (action) {
                    case 'batch-approve':
                        var selectedIds = getSelectedIds();
                        if (selectedIds.length === 0) {
                            toastr.warning('请先选择要操作的申请');
                            return;
                        }
                        actionText = '批量通过';
                        confirmText = '确定要通过选中的 ' + selectedIds.length + ' 个申请吗？';
                        ajaxAction = 'pk_link_request_batch_approve';
                        ajaxData = { ids: selectedIds };
                        break;
                    case 'batch-reject':
                        var selectedIds = getSelectedIds();
                        if (selectedIds.length === 0) {
                            toastr.warning('请先选择要操作的申请');
                            return;
                        }
                        actionText = '批量拒绝';
                        confirmText = '确定要拒绝选中的 ' + selectedIds.length + ' 个申请吗？';
                        ajaxAction = 'pk_link_request_batch_reject';
                        ajaxData = { ids: selectedIds };
                        break;
                    case 'batch-delete':
                        if (currentStatus === 'approved') {
                            // 已通过状态：直接批量删除所有，不需要选择
                            actionText = '批量删除';
                            confirmText = '确定要删除所有已通过的申请吗？';
                            ajaxAction = 'pk_link_request_batch_delete_all';
                            ajaxData = { status: 'approved' };
                        } else {
                            // 其他状态：需要选择
                            var selectedIds = getSelectedIds();
                            if (selectedIds.length === 0) {
                                toastr.warning('请先选择要操作的申请');
                                return;
                            }
                            actionText = '批量删除';
                            confirmText = '确定要删除选中的 ' + selectedIds.length + ' 个申请吗？';
                            ajaxAction = 'pk_link_request_batch_delete';
                            ajaxData = { ids: selectedIds };
                        }
                        break;
                    case 'clear-rejected':
                        // 清空记录：直接删除所有已拒绝的，不需要选择
                        actionText = '清空记录';
                        confirmText = '确定要清空所有已拒绝的申请记录吗？';
                        ajaxAction = 'pk_link_request_clear_rejected';
                        ajaxData = {};
                        break;
                    default:
                        return;
                }
                
                showConfirmModal('确认' + actionText, confirmText, function() {
                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        type: 'POST',
                        data: {
                            action: ajaxAction,
                            ...ajaxData
                        },
                        success: function(res) {
                            toastr.success(res.data);
                            loadRequests();
                        }
                    });
                });
            });
            
            $('#pk-modal-overlay').on('click', function() {
                $('#pk-reject-modal, #pk-confirm-modal, #pk-modal-overlay').hide();
            });
            
            // 页面加载时先调用updateCounts更新待审核数字，再调用loadRequests加载数据
            updateBulkActions();
            updateCounts();
            loadRequests();
        });
        </script>
    </div>
    <?php
}
