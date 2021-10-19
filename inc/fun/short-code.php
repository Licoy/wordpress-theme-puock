<?php
/*短代码*/
$shortCodeColors = array('primary', 'danger', 'warning', 'info', 'success', 'dark');
//提示框部分
function sc_tips_common($type, $attr, $content)
{
    return '<div class="alert alert-' . $type . '">' . $content . '</div>';
}

function sc_tips_primary($attr, $content = null)
{
    global $shortCodeColors;
    return sc_tips_common($shortCodeColors[0], $attr, $content);
}

function sc_tips_danger($attr, $content = null)
{
    global $shortCodeColors;
    return sc_tips_common($shortCodeColors[1], $attr, $content);
}

function sc_tips_warning($attr, $content = null)
{
    global $shortCodeColors;
    return sc_tips_common($shortCodeColors[2], $attr, $content);
}

function sc_tips_info($attr, $content = null)
{
    global $shortCodeColors;
    return sc_tips_common($shortCodeColors[3], $attr, $content);
}

function sc_tips_success($attr, $content = null)
{
    global $shortCodeColors;
    return sc_tips_common($shortCodeColors[4], $attr, $content);
}

function sc_tips_dark($attr, $content = null)
{
    global $shortCodeColors;
    return sc_tips_common($shortCodeColors[5], $attr, $content);
}

foreach ($shortCodeColors as $sc_tips) {
    add_shortcode('t-' . $sc_tips, 'sc_tips_' . $sc_tips);
}
//按钮部分
function sc_btn_common($type = 'primary', $attr = null, $content = null)
{
    $href = isset($attr['href']) ? $attr['href'] : "javascript:void(0)";
    if (pk_is_cur_site($href)) {
        return '<a href="' . $href . '" class="btn btn-sm sc-btn btn-' . $type . '">' . $content . '</a>';
    }
    return '<a target="_blank" rel="nofollow" href="' . pk_go_link($href) . '" class="btn btn-sm sc-btn btn-' . $type . '">' . $content . '</a>';
}

function sc_btn_primary($attr, $content = null)
{
    return sc_btn_common('primary', $attr, $content);
}

function sc_btn_danger($attr, $content = null)
{
    return sc_btn_common('danger', $attr, $content);
}

function sc_btn_warning($attr, $content = null)
{
    return sc_btn_common('warning', $attr, $content);
}

function sc_btn_info($attr, $content = null)
{
    return sc_btn_common('info', $attr, $content);
}

function sc_btn_success($attr, $content = null)
{
    return sc_btn_common('success', $attr, $content);
}

function sc_btn_dark($attr, $content = null)
{
    return sc_btn_common('dark', $attr, $content);
}

function sc_btn_link($attr, $content = null)
{
    return sc_btn_common('link', $attr, $content);
}

foreach (array_merge($shortCodeColors, array('link')) as $sc_btn) {
    add_shortcode('btn-' . $sc_btn, 'sc_btn_' . $sc_btn);
}
//短代码兼容部分（可对之前使用的主题中的短代码进行转换适配）
foreach ($shortCodeColors as $scc) {
    add_shortcode('t-' . $scc, 'sc_tips_' . $scc);
    add_shortcode('btn-' . $scc, 'sc_btn_' . $scc);
}
add_shortcode('v_organge', 'sc_tips_warning');
add_shortcode('v_notice', 'sc_tips_success');
add_shortcode('v_red', 'sc_tips_danger');
add_shortcode('v_lvse', 'sc_tips_success');
add_shortcode('v_error', 'sc_tips_danger');
add_shortcode('v_blue', 'sc_tips_primary');
add_shortcode('v_warn', 'sc_tips_warning');
add_shortcode('v_act', 'sc_tips_primary');
add_shortcode('bb', 'sc_btn_primary');
add_shortcode('sgbtn_orange', 'sc_btn_warning');
add_shortcode('sgbtn_blue', 'sc_btn_primary');
add_shortcode('sgbtn_red', 'sc_btn_danger');
add_shortcode('sgbtn_lv', 'sc_btn_success');

//集成dplayer播放器
function pk_dplayer_videos($attr, $content = null)
{
    extract(shortcode_atts(array(
        'url' => '', 'href' => '',
        'autoplay' => false, 'type' => 'auto',
        'pic' => '', 'class' => '',
    ), $attr));
    if (empty($url) && empty($href)) {
        return sc_tips_warning(null, '视频警告：播放链接不能为空');
    }
    if (empty($url)) {
        $url = $href;
    }
    $id = mt_rand(0, 9) . mt_rand(0, 9) . mt_rand(0, 9) . mt_rand(0, 9);
    $auto = ($autoplay && $autoplay === 'true') ? 'true' : 'false';
    $out = "<div id='dplayer-{$id}' class='{$class}'></div>";
    $out .= "<script>$(function() {
            new DPlayer({
                container: document.getElementById('dplayer-{$id}'),
                autoplay: {$auto},
                video: {
                    url: '{$url}',
                    pic: '{$pic}',
                    type: '{$type}'
                },
            });
})</script>";
    return $out;
}

add_shortcode('video', 'pk_dplayer_videos');
add_shortcode('videos', 'pk_dplayer_videos');
//解析音频链接
function pk_music($attr, $content = null)
{
    if (empty($content)) {
        return sc_tips_warning(null, '音频警告：播放链接不能为空');
    }
    return '<div class="text-center"><audio class="mt-2" src="' . trim($content) . '" controls></audio></div>';
}

add_shortcode('music', 'pk_music');
//下载
function pk_download($attr, $content = null)
{
    $filename = isset($attr['file']) ? $attr['file'] : '';
    $size = isset($attr['size']) ? $attr['size'] : '';
    $down_tips = pk_get_option('down_tips');
    return "<div class=\"p-block p-down-box\">
        <div class='mb15'><i class='czs-zip-folder-l'></i>&nbsp;<span>文件名称：$filename</span></div>
        <div class='mb15'><i class='czs-download-l'></i>&nbsp;<span>文件大小：$size</span></div>
        <div class='mb15'><i class='czs-about-l'></i>&nbsp;<span>下载声明：$down_tips</span></div>
        <div><i class='czs-paper-plane-l'></i>&nbsp;<span>下载地址：$content</span></div>
    </div>";
}

add_shortcode('download', 'pk_download');
add_shortcode('dltable', 'pk_download');
//回复可见
function pk_reply_read($attr, $content = null)
{
    global $wpdb;
    $email = null;
    $user_id = (int)wp_get_current_user()->ID;
    $msg = sc_tips_primary(null, "<i class='czs-eye'></i>&nbsp;此处含有隐藏内容，请提交评论并审核通过刷新后即可查看！");
    if ($user_id > 0) {
        $email = get_userdata($user_id)->user_email;
        if ($email == get_bloginfo('admin_email')) {
            return $content;
        }
    } else {
        if (isset($_COOKIE['comment_author_email_' . COOKIEHASH])) {
            $email = $_COOKIE['comment_author_email_' . COOKIEHASH];
        }
    }
    if (empty($email)) {
        return $msg;
    }
    $post_id = get_the_ID();
    $query = "SELECT count(comment_ID) as c FROM {$wpdb->comments} WHERE comment_post_ID={$post_id} 
                and comment_approved='1' and comment_author_email='{$email}' LIMIT 1";
    if ($wpdb->get_row($query)->c > 0) {
        return do_shortcode($content);
    }
    return $msg;
}

add_shortcode('reply', 'pk_reply_read');
//登录可见
function pk_login_read($attr, $content = null)
{
    $msg = sc_tips_primary(null, "<i class='czs-eye'></i>&nbsp;此处含有隐藏内容，登录后即可查看！");
    return is_user_logged_in() ? do_shortcode($content) : $msg;
}

add_shortcode('login', 'pk_login_read');
//登录并验证邮箱可见
function pk_login_email_read($attr, $content = null)
{
    if (is_user_logged_in()) {
        $user_id = (int)wp_get_current_user()->ID;
        if ($user_id > 0) {
            $email = get_userdata($user_id)->user_email;
            if (!empty($email) && !pk_check_email_is_sysgen($email)) {
                return do_shortcode($content);
            }
        }
    }
    return sc_tips_primary(null, "<i class='czs-eye'></i>&nbsp;此处含有隐藏内容，需要登录并验证邮箱后即可查看！");
}

add_shortcode('login_email', 'pk_login_email_read');
//加密内容
function pk_password_read($attr, $content = null)
{
    global $wpdb;
    $email = null;
    $user_id = (int)wp_get_current_user()->ID;
    if ($user_id > 0) {
        $email = get_userdata($user_id)->user_email;
        if ($email == get_bloginfo('admin_email')) {
            return $content;
        }
    }
    extract(shortcode_atts(array(
        'pass' => null,
        'desc' => null,
    ), $attr));
    $out = '';
    $error = '';
    if (isset($_REQUEST['pass'])) {
        if ($_REQUEST['pass'] == $pass) {
            return do_shortcode($content);
        } else {
            $error = sc_tips_danger(null, "<i class='czs-eye'></i>&nbsp;密码输入错误，请重新输入！");
        }
    }
    if (trim($desc) == "") {
        $desc = "此处含有隐藏内容，需要正确输入密码后可见！";
    }
    $out .= '<div class="p-block">' . sc_tips_primary(null, "<i class='czs-eye'></i>&nbsp;{$desc}") . '
            ' . $error . '<form action="' . get_permalink() . '" method="post">
            <div class="row">
            <div class="col-8 col-md-10">
            <input type="text" placeholder="请输入密码" required class="form-control form-control-sm" name="pass">
            </div>
            <div class="col-4 col-md-2 pl-0">
            <button class="btn btn-sm btn-primary w-100">立即查看</button>
            </div>
            </div>
            </form>
            </div>';
    return $out;
}

add_shortcode('password', 'pk_password_read');
//隐藏收缩
function pk_sc_collapse($attr, $content = null)
{
    $index = @$GLOBALS['collapse-' . get_the_ID()];
    if (empty($index)) {
        $index = 1;
        $GLOBALS['collapse-' . get_the_ID()] = 1;
    } else {
        $index++;
        $GLOBALS['collapse-' . get_the_ID()] += 1;
    }
    $scId = "collapse-" . get_the_ID() . '-' . $index;
    extract(shortcode_atts(array(
        'title' => null,
    ), $attr));
    $out = '<div><a class="btn btn-primary btn-sm" data-toggle="collapse" href="#' . $scId . '" role="button"
        aria-expanded="false" aria-controls="' . $scId . '"><i class="czs-angle-up-l"></i>&nbsp;' . $title . '</a></div>';
    $out .= '<div class="collapse" id="' . $scId . '">' . $content . '</div>';
    return $out;
}

add_shortcode('collapse', 'pk_sc_collapse');
function p_wpautop($content)
{
    return wpautop($content, false);
}

//TODO 添加到后台配置
remove_filter('the_content', 'wpautop');
add_filter('the_content', 'p_wpautop', 11);