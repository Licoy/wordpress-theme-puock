<?php

function pk_comment_err($msg, $refresh_code = true)
{
    $protocol = $_SERVER['SERVER_PROTOCOL'];
    if (!in_array($protocol, array('HTTP/1.1', 'HTTP/2', 'HTTP/2.0'))) {
        $protocol = 'HTTP/1.0';
    }

    header('Allow: POST');
    header("$protocol 405 Method Not Allowed");
    header('Content-Type: text/plain');
    echo json_encode([
        'msg' => $msg,
        'refresh_code' => $refresh_code,
    ]);
    exit();
}

function pk_check_comment_for_chinese($comment) {
    $pattern = '/[\x{4e00}-\x{9fa5}]/u';
    if (!preg_match($pattern, $comment)) {
        pk_comment_err('您的评论必须包含至少一个中文字符');
    }
    return $comment;
}
if(pk_is_checked('vd_comment_need_chinese')){
    add_filter('pre_comment_content', 'pk_check_comment_for_chinese');
}


function pk_comment_ajax()
{
    global $wpdb;

    nocache_headers();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        pk_comment_err('无效的请求方式', false);
    }

    if (pk_post_comment_is_closed()) {
        pk_comment_err('评论功能已关闭', false);
    }

    //是否需要进行验证
    if (pk_is_checked('vd_comment')) {
        if (pk_get_option('vd_type', 'img') === 'img') {
            $token = $_REQUEST['comment-vd'];

            if (empty($token)) {
                pk_comment_err('无效验证码，已刷新请重新输入');
            }
            $validate_pass = true;
            pk_session_call(function () use ($token, &$validate_pass) {
                $session_comment_captcha = $_SESSION['comment_vd'];
                if (!$session_comment_captcha || $session_comment_captcha == '' || trim($token) != $session_comment_captcha) {
                    $validate_pass = false;
                }
                unset($_SESSION['comment_vd']);
            });
            if (!$validate_pass) {
                pk_comment_err('验证码不正确', false);
            }
        } else {
            try {
                pk_vd_gt_validate();
            } catch (Exception $e) {
                pk_comment_err($e->getMessage());
            }
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
    $query_params = [$comment_post_ID, $comment_author];
    $dupe = "SELECT comment_ID FROM $wpdb->comments WHERE comment_post_ID = %d AND ( comment_author = %s ";
    if ($comment_author_email) {
        $dupe .= "OR comment_author_email = %s ";
        $query_params[] = $comment_author_email;
    }
    $dupe .= ") AND comment_content = %s LIMIT 1";
    $query_params[] = $comment_content;
    if ($wpdb->get_var($wpdb->prepare($dupe, $query_params))) {
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
        $comment_approved_str = '<p class="c-sub mt-1"><i class="fa fa-warning mr-1"></i>您的评论正在等待审核！</p>';
    }

    wp_set_comment_cookies($comment, $user);

    echo '<div id="comment-' . get_comment_ID() . '" class="post-comment">
            <div class="info">
                <div>' . get_avatar($comment, 64, '', '', array('class' => 'md-avatar')) . '</div>
                <div class="ml-3 two-info">
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

