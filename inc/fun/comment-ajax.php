<?php

function pk_comment_err($msg)
{
    $protocol = $_SERVER['SERVER_PROTOCOL'];
    if (!in_array($protocol, array('HTTP/1.1', 'HTTP/2', 'HTTP/2.0'))) {
        $protocol = 'HTTP/1.0';
    }

    header('Allow: POST');
    header("$protocol 405 Method Not Allowed");
    header('Content-Type: text/plain');
    echo $msg;
    exit();
}

function pk_comment_ajax()
{
    global $wpdb;

    nocache_headers();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        pk_comment_err('无效的请求方式');
    }

    //是否需要进行验证
    if (pk_is_checked('vd_comment')) {

        $v_api = pk_get_option('vd_vaptcha_api');

        $v_id = pk_get_option('vd_vaptcha_id');

        $v_key = pk_get_option('vd_vaptcha_key');

        if (empty($v_api) || empty($v_id) || empty($v_key)) {
            pk_comment_err('未配置验证参数');
        }

        $token = $_REQUEST['comment-vd'];

        if (empty($token)) {
            pk_comment_err('无效的请求');
        }

        $req_params = array(
            'id' => $v_id,
            'secretkey' => $v_key,
            'scene' => 3,
            'token' => $token,
            'ip' => $_SERVER['REMOTE_ADDR']
        );

        $v_res = wp_remote_post($v_api, array('body' => $req_params, 'timeout' => 3));

        if (!$res_body = json_decode($v_res['body'], true)) {
            pk_comment_err('验证用户防刷API解析异常');
        }

        if (isset($res_body['success']) && $res_body['success'] !== 1) {
            $sx = "";
            if (isset($res_body['msg'])) {
                $sx = ": ${res_body['msg']}";
            }
            pk_comment_err("检测到请求异常，请重新验证${sx}");
        }

    }

    $comment_post_ID = isset($_POST['comment_post_ID']) ? (int)$_POST['comment_post_ID'] : 0;

    $post = get_post($comment_post_ID);

    if (!$post || empty($post->comment_status)) {
        do_action('comment_id_not_found', $comment_post_ID);
        pk_comment_err('无效的评论回复');
    }

    $status = get_post_status($post);

    $status_obj = get_post_status_object($status);

    if (!comments_open($comment_post_ID)) {
        do_action('comment_closed', $comment_post_ID);
        pk_comment_err('评论已关闭');
    } elseif ('trash' == $status) {
        do_action('comment_on_trash', $comment_post_ID);
        pk_comment_err('无效评论');
    } elseif (!$status_obj->public && !$status_obj->private) {
        do_action('comment_on_draft', $comment_post_ID);
        pk_comment_err('无法对草稿进行评论');
    } elseif (post_password_required($comment_post_ID)) {
        do_action('comment_on_password_protected', $comment_post_ID);
        pk_comment_err('无法对受密码保护进行评论');
    } else {
        do_action('pre_comment_on_post', $comment_post_ID);
    }

    $comment_author = (isset($_POST['author'])) ? trim(strip_tags($_POST['author'])) : null;
    $comment_author_email = (isset($_POST['email'])) ? trim($_POST['email']) : null;
    $comment_author_url = (isset($_POST['url'])) ? trim($_POST['url']) : null;
    $comment_content = (isset($_POST['comment'])) ? trim($_POST['comment']) : null;

    $user = wp_get_current_user();

    if ($user && $user->ID) {
        $user_ID = $user->ID;
        if (empty($user->display_name)) $user->display_name = $user->user_login;
        $comment_author = $wpdb->prepare($user->display_name);
        $comment_author_email = $wpdb->prepare($user->user_email);
        $comment_author_url = $wpdb->prepare($user->user_url);
        if (current_user_can('unfiltered_html')) {
            if (wp_create_nonce('unfiltered-html-comment_' . $comment_post_ID) != $_POST['_wp_unfiltered_html_comment']) {
                kses_remove_filters();
                kses_init_filters();
            }
        }
    } else if (get_option('comment_registration') || 'private' == $status) {
        pk_comment_err('对不起，您必须登录后才能发表评论');
    }

    $comment_type = '';

    if (get_option('require_name_email') && !$user->ID) {
        if (empty($comment_author) || empty($comment_author_email))
            pk_comment_err('评论之前必须填写昵称及邮件');
        elseif (!is_email($comment_author_email))
            pk_comment_err('电子邮箱格式不正确');
    }

    if (empty($comment_content)) pk_comment_err('评论内容不能为空');

    // 检查重复评论功能
    $dupe = "SELECT comment_ID FROM $wpdb->comments WHERE comment_post_ID = '$comment_post_ID' AND ( comment_author = '$comment_author' ";
    if ($comment_author_email) $dupe .= "OR comment_author_email = '$comment_author_email' ";
    $dupe .= ") AND comment_content = '$comment_content' LIMIT 1";
    if ($wpdb->get_var($dupe)) {
        pk_comment_err('您已经发表过相同的评论了!');
    }

    // 检查评论时间
    if ($lasttime = $wpdb->get_var($wpdb->prepare("SELECT comment_date_gmt FROM $wpdb->comments WHERE comment_author = %s ORDER BY comment_date DESC LIMIT 1", $comment_author))) {
        $time_last_comment = mysql2date('U', $lasttime, false);
        $time_new_comment = mysql2date('U', current_time('mysql', 1), false);
        $flood_die = apply_filters('comment_flood_filter', false, $time_last_comment, $time_new_comment);
        if ($flood_die) {
            pk_comment_err('您的评论发表速度太快了！');
        }
    }

    $comment_parent = isset($_POST['comment_parent']) ? absint($_POST['comment_parent']) : 0;

    $comment_data = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'comment_parent', 'user_ID');

    $comment_id = wp_new_comment($comment_data, false);

    $comment = get_comment($comment_id);

    $GLOBALS['comment'] = $comment;

    if (!$user->ID) {
        do_action('set_comment_cookies', $comment, $user, isset($_POST['wp-comment-cookies-consent']));
    }

    $comment_approved_str = '';

    if ($comment->comment_approved == '0') {
        $comment_approved_str = '<p class="c-sub mt-1"><i class="czs-warning-l mr-1"></i>您的评论正在等待审核！</p>';
    }

    wp_set_comment_cookies($comment, $user);

    echo '<div id="comment-' . get_comment_ID() . '" class="post-comment">
            <div class="info clearfix">
                <div class="float-left">' . get_avatar($comment, 64, '', '', array('class' => 'md-avatar')) . '</div>
                <div class="float-left ml-3 two-info">
                    <div class="puock-text ta3b">
                        <span class="t-md puock-links">' . get_comment_author_link($comment_id) . '</span>
                        ' . pk_the_author_class(false, $comment) . '
                    </div>
                    <div class="t-sm c-sub">' . get_comment_date('Y-m-d H:i:s', $comment_id) . '</div>
                </div>
            </div>
            <div class="content t-sm mt10 puock-text">
                <div class="content-text">
                    ' . get_comment_text($comment_id) . '
                    ' . $comment_approved_str . '
                </div>
            </div>
        </div>';

    wp_die();
}

add_action('wp_ajax_nopriv_comment_ajax', 'pk_comment_ajax');
add_action('wp_ajax_comment_ajax', 'pk_comment_ajax');

