<?php
/*短代码*/
$shortCodeColors = array('primary', 'danger', 'warning', 'info', 'success', 'dark');

function pk_shortcode_register()
{
    global $shortCodeColors;
    $list = array(
        'music' => array('name' => __('音乐播放', PUOCK), 'content' => __('输入链接地址', PUOCK)),
        'pre' => array('name' => __('代码嵌入', PUOCK), 'content' => __('输入代码', PUOCK)),
        'reply' => array('name' => __('回复可见', PUOCK), 'content' => __('输入内容', PUOCK)),
        'login' => array('name' => __('登录可见', PUOCK), 'content' => __('输入内容', PUOCK)),
        'github' => array('name' => __('Github仓库卡片', PUOCK), 'content' => 'Licoy/wordpress-theme-puock'),
        'login_email' => array('name' => __('登录并验证邮箱可见', PUOCK), 'content' => __('输入内容', PUOCK)),
        'video' => array('name' => __('视频播放', PUOCK), 'attr' => array(
            'url'=>'example.com/test.mp4',
            'autoplay' => false, 'type' => 'auto',
            'pic' => '', 'class' => ''
        )),
        'download' => array('name' => __('文件下载', PUOCK), 'content' => __('文件地址', PUOCK), 'attr' => array(
            'file' => 'xxx.zip', 'size' => '12MB'
        )),
        'password' => array('name' => __('输入密码可见', PUOCK), 'content' => __('输入内容', PUOCK), 'attr' => array(
            'pass' => '123456', 'desc' => __('输入密码可见', PUOCK),
        )),
        'collapse' => array('name' => __('折叠面板', PUOCK), 'content' => __('输入内容', PUOCK), 'attr' => array(
            'title' => 'title'
        )),
    );
    foreach ($shortCodeColors as $sc_tips) {
        $list['t-' . $sc_tips] = array(
            'name' => __('提示框', PUOCK) . $sc_tips,
            'attr' => array(
                'icon' => ''
            ),
            'content' => __('输入内容', PUOCK)
        );
    }
    return $list;
}
// 解析pre标签 感谢阿云。小恐龙太好拉注。
function pk_pre($atts, $content = null) {
    $content = '<pre>' . htmlspecialchars($content) . '</pre>';
    return $content;
}
add_shortcode('pre', 'pk_pre');

function pk_sc_safe_class($classes): string
{
    $out = [];
    foreach (preg_split('/\s+/', (string)$classes) as $class) {
        $class = sanitize_html_class($class);
        if ($class !== '') {
            $out[] = $class;
        }
    }
    return implode(' ', $out);
}

function pk_sc_safe_html($content): string
{
    return do_shortcode((string)$content);
}

//提示框部分
function sc_tips($attr, $content, $tag)
{
    global $shortCodeColors;
    $type = str_replace('t-', '', $tag);
    extract(shortcode_atts(array(
        'icon' => '',
        'outline' => false,
        'class' => ''
    ), $attr));
    if (!in_array($type, $shortCodeColors, true)) {
        $type = 'primary';
    }
    $_class =  'alert alert-' . $type;
    if (!empty($icon)) {
        $content = "<i class=\"" . esc_attr(pk_sc_safe_class($icon . ' me-1')) . "\"></i>" . pk_sc_safe_html($content);
    } else {
        $content = pk_sc_safe_html($content);
    }
    if ($outline) {
        $_class .= ' alert-outline';
    }
    if($class){
        $_class .= ' ' . pk_sc_safe_class($class);
    }
    return "<div class=\"" . esc_attr($_class) . "\">{$content}</div>";
}

foreach ($shortCodeColors as $sc_tips) {
    add_shortcode('t-' . $sc_tips, 'sc_tips');
}
//按钮部分
function sc_btn($attr, $content, $tag)
{
    $type = str_replace('btn-', '', $tag);
    $href = $attr['href'] ?? "javascript:void(0)";
    $type = sanitize_html_class($type);
    if (pk_is_cur_site($href)) {
        return '<a href="' . esc_url($href) . '" class="btn btn-sm sc-btn btn-' . esc_attr($type) . '">' . pk_sc_safe_html($content) . '</a>';
    }
    return '<a target="_blank" rel="nofollow noopener" href="' . esc_url(pk_go_link($href)) . '" class="btn btn-sm sc-btn btn-' . esc_attr($type) . '">' . pk_sc_safe_html($content) . '</a>';
}

foreach (array_merge($shortCodeColors, array('link')) as $sc_btn) {
    add_shortcode('btn-' . $sc_btn, 'sc_btn');
}

//视频
function pk_sc_video($attr, $content = null)
{
    extract(shortcode_atts(array(
        'url' => '', 'href' => '',
        'autoplay' => false, 'type' => 'auto',
        'pic' => '', 'class' => '',
        'ssl'=>false,
    ), $attr));
    if (empty($url) && empty($href)) {
        return sc_tips(array('outline'=>true), '<span class="c-sub fs14">' . __('视频警告：播放链接不能为空', PUOCK) . '</span>', 't-warning');
    }
    if (empty($url)) {
        $url = $href;
    }
    if(strpos($url, 'http://') === false && strpos($url, 'https://') === false){
        $url = ($ssl ? 'https://' : 'http://') . $url;
    }
    $auto = ($autoplay === 'true') ? 'true' : 'false';
    $biliIframeSrc = null;
    $parsedUrl = parse_url($url);
    $host = strtolower($parsedUrl['host'] ?? '');
    $path = $parsedUrl['path'] ?? '';
    $query = $parsedUrl['query'] ?? '';
    if (!empty($host) && (preg_match('/(^|\.)bilibili\.com$/', $host) || preg_match('/(^|\.)b23\.tv$/', $host))) {
        if ($host === 'player.bilibili.com') {
            $biliIframeSrc = $url;
        } elseif (preg_match('/(^|\.)bilibili\.com$/', $host)) {
            if (preg_match('/\/video\/(BV[a-zA-Z0-9]+)/', $path, $match)) {
                $bvid = $match[1];
                parse_str($query, $queryArgs);
                $page = isset($queryArgs['p']) ? max(1, intval($queryArgs['p'])) : 1;
                $autoplayValue = $auto === 'true' ? '1' : '0';
                $biliIframeSrc = "https://player.bilibili.com/player.html?bvid={$bvid}&page={$page}&as_wide=1&high_quality=1&danmaku=0&autoplay={$autoplayValue}";
            }
        }
        if (!empty($biliIframeSrc)) {
            if (strpos($biliIframeSrc, '//') === 0) {
                $biliIframeSrc = 'https:' . $biliIframeSrc;
            }
            $wrapStyle = 'position: relative; width: 100%; padding-top: 56.25%;';
            $iframeStyle = 'position: absolute; width: 100%; height: 100%; left: 0; top: 0;';
            $iframeClass = $class ? ' ' . pk_sc_safe_class($class) : '';
            return "<div class=\"" . esc_attr("pk-sc-bili{$iframeClass}") . "\" style=\"{$wrapStyle}\"><iframe style=\"{$iframeStyle}\" src=\"" . esc_url($biliIframeSrc) . "\" scrolling=\"no\" border=\"0\" frameborder=\"no\" framespacing=\"0\" allowfullscreen=\"true\" sandbox=\"allow-top-navigation allow-same-origin allow-forms allow-scripts\"></iframe></div>";
        }
        return sc_tips(array('outline'=>true), '<span class="c-sub fs14">' . __('视频警告：未能识别有效的B站链接', PUOCK) . '</span>', 't-warning');
    }
    if (pk_is_checked('dplayer')) {
        $id = mt_rand(0, 9) . mt_rand(0, 9) . mt_rand(0, 9) . mt_rand(0, 9);
        $out = "<div id='dplayer-{$id}' class='" . esc_attr(pk_sc_safe_class($class)) . "'></div>";
        $out .= "<script>jQuery(function() {
            new DPlayer({
                container: document.getElementById('dplayer-{$id}'),
                autoplay: {$auto},
                video: {
                    url: " . wp_json_encode(esc_url_raw($url)) . ",
                    type: " . wp_json_encode(sanitize_key($type)) . "
                },
            });
})</script>";
        return $out;
    } else {
        $autoplay = $auto == 'true' ? 'autoplay' : '';
        return "<video $autoplay src=\"" . esc_url($url) . "\" controls></video>";
    }
}
add_shortcode('video', 'pk_sc_video');
add_shortcode('videos', 'pk_sc_video');

//解析音频链接
function pk_music($attr, $content = null)
{
    if (empty($content)) {
        return sc_tips(array('outline' => true), '<span class="c-sub fs14">' . __('音频警告：播放链接不能为空', PUOCK) . '</span>', 't-warning');
    }
    return '<div class="text-center"><audio class="mt-2" src="' . esc_url(trim($content)) . '" controls></audio></div>';
}

add_shortcode('music', 'pk_music');
//下载
function pk_download($attr, $content = null)
{
    $filename = isset($attr['file']) ? $attr['file'] : '';
    $size = isset($attr['size']) ? $attr['size'] : '';
    $down_tips = pk_get_option('down_tips');
    $file_name_label = __('文件名称', PUOCK);
    $file_size_label = __('文件大小', PUOCK);
    $download_notice_label = __('下载声明', PUOCK);
    $download_url_label = __('下载地址', PUOCK);
    return "<div class=\"p-block p-down-box\">
        <div class='mb15'><i class='fa fa-file-zipper'></i>&nbsp;<span>{$file_name_label}：" . esc_html($filename) . "</span></div>
        <div class='mb15'><i class='fa fa-download'></i>&nbsp;<span>{$file_size_label}：" . esc_html($size) . "</span></div>
        <div class='mb15'><i class='fa-regular fa-bell'></i>&nbsp;<span>{$download_notice_label}：" . wp_kses_post($down_tips) . "</span></div>
        <div><i class='fa fa-link'></i><span>{$download_url_label}：" . esc_html($content) . "</span></div>
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
    $msg = sc_tips(array('outline' => true), "<span class='c-sub fs14'><i class='fa-regular fa-eye'></i>&nbsp;" . __('此处含有隐藏内容，请提交评论并审核通过刷新后即可查看！', PUOCK) . "</span>", 't-primary');
    if ($user_id > 0) {
        $email = get_userdata($user_id)->user_email;
        if ($email == get_bloginfo('admin_email')) {
            return do_shortcode($content);
        }
    } else {
        if (isset($_COOKIE['comment_author_email_' . COOKIEHASH])) {
            $email = sanitize_email(wp_unslash($_COOKIE['comment_author_email_' . COOKIEHASH]));
        }
    }
    if (empty($email)) {
        return $msg;
    }
    $post_id = get_the_ID();
    $query_args = [$post_id, $email];
    $query = "SELECT count(comment_ID) as c FROM {$wpdb->comments} WHERE comment_post_ID=%d and comment_approved='1' and comment_author_email=%s";
    if ($user_id <= 0) {
        $proof = pk_get_comment_proof_cookie();
        if (!$proof || $proof['post_id'] !== (int)$post_id || !hash_equals($proof['email_hash'], pk_comment_proof_email_hash($email))) {
            return $msg;
        }
        $query .= ' and comment_ID=%d';
        $query_args[] = $proof['comment_id'];
    }
    $query .= ' LIMIT 1';
    if ((int)$wpdb->get_row($wpdb->prepare($query, $query_args))->c > 0) {
        return do_shortcode($content);
    }
    return $msg;
}

add_shortcode('reply', 'pk_reply_read');
//登录可见
function pk_login_read($attr, $content = null)
{
    $msg = sc_tips(array('outline' => true), "<span class='c-sub fs14'><i class='fa-regular fa-eye'></i>&nbsp;" . __('此处含有隐藏内容，登录后即可查看！', PUOCK) . "</span>", 't-primary');
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
    return sc_tips(array('outline' => true), "<span class='c-sub fs14'><i class='fa-regular fa-eye'></i>&nbsp;" . __('此处含有隐藏内容，需要登录并验证邮箱后即可查看！', PUOCK) . "</span>", 't-primary');
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
    if (empty(trim($desc ?? ''))) {
        $desc = __('此处含有隐藏内容，需要正确输入密码后可见！', PUOCK);
    }
    $info = "<p class='fs14 c-sub'><i class='fa-regular fa-eye'></i>&nbsp;" . esc_html($desc) . "</p>";
    if (isset($_REQUEST['pass'])) {
        if ($_REQUEST['pass'] == $pass) {
            return do_shortcode($content);
        } else {
            $info .= "<p class='fs14 text-danger'><i class='fa-solid fa-triangle-exclamation'></i>&nbsp;" . __('您的密码输入错误，请核对后重新输入', PUOCK) . "</p>";
        }
    }
    $placeholder = __('请输入密码', PUOCK);
    $btn_text = __('立即查看', PUOCK);
    $out .= "<div class='alert alert-primary alert-outline'>{$info}"
        . "$error<form action=\"" . esc_url(get_permalink()) . "\" method=\"post\"><div class=\"row\"><div class=\"col-8 col-md-10\">"
        . "<input type=\"password\" placeholder=\"" . esc_attr($placeholder) . "\" required class=\"form-control form-control-sm\" name=\"pass\"/>"
        . "</div><div class=\"col-4 col-md-2 ps-0\"><button class=\"btn btn-sm btn-primary w-100\">{$btn_text}</button></div></div></form>"
        . "</div>";
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
    $out = '<div class="pk-sc-collapse"><a class="btn btn-primary btn-sm" data-bs-toggle="collapse" href="#' . esc_attr($scId) . '" role="button"
        aria-expanded="false" aria-controls="' . esc_attr($scId) . '"><i class="fa fa-angle-up"></i>&nbsp;' . esc_html($title) . '</a></div>';
    $out .= '<div class="collapse" id="' . esc_attr($scId) . '">' . pk_sc_safe_html($content) . '</div>';
    return $out;
}

add_shortcode('collapse', 'pk_sc_collapse');

//github项目展示
function pk_sc_github($attr, $content = null)
{
    return '<div class="github-card text-center" data-repo="' . esc_attr(trim((string)$content)) . '"><div class="spinner-grow text-primary"></div></div>';
}

add_shortcode('github', 'pk_sc_github');


function p_wpautop($content)
{
    return wpautop($content, true);
}

//TODO 添加到后台配置
remove_filter('the_content', 'wpautop');
add_filter('the_content', 'p_wpautop', 11);
