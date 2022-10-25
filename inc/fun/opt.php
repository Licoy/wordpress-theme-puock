<?php
// 点赞
function puock_post_like()
{
    $id = $_POST["um_id"];
    $action = $_POST["um_action"];
    if ($action == 'like') {
        $cookie_key = 'puock_like_' . $id;
        if (!empty($_COOKIE[$cookie_key])) {
            echo json_encode(array('e' => 1, 't' => '你已经点过赞了'));
            die;
        }
        $like_num = get_post_meta($id, 'puock_like', true);
        $expire = time() + (86400);
        $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
        setcookie($cookie_key, $id, $expire, '/', $domain, false);
        if (!$like_num || !is_numeric($like_num)) {
            update_post_meta($id, 'puock_like', 1);
        } else {
            update_post_meta($id, 'puock_like', ($like_num + 1));
        }
        echo json_encode(array('e' => 0, 't' => '点赞成功', 'd' => get_post_meta($id, 'puock_like', true)));
    }
    die;
}

add_action('wp_ajax_nopriv_puock_like', 'puock_post_like');
add_action('wp_ajax_puock_like', 'puock_post_like');

// 获取当前访问cookie是否点赞
function puock_post_is_like()
{
    global $post;
    return !empty($_COOKIE['puock_like_' . $post->ID]);
}

//移除wp自带的widget
function init_unregister_widgets()
{
    unregister_widget('WP_Widget_Recent_Comments');
    unregister_widget('WP_Widget_Recent_Posts');
    unregister_widget('WP_Widget_RSS');
    unregister_widget('WP_Widget_Search');
    unregister_widget('WP_Widget_Tag_Cloud');
    unregister_widget('WP_Widget_Text');
    unregister_widget('WP_Nav_Menu_Widget');
//    unregister_widget( 'WP_Widget_Archives' );
    unregister_widget('WP_Widget_Calendar');
    unregister_widget('WP_Widget_Categories');
    unregister_widget('WP_Widget_Links');
    unregister_widget('WP_Widget_Meta');
    unregister_widget('WP_Widget_Pages');
    unregister_widget('WP_Widget_Media_Gallery');
    unregister_widget('WP_Widget_Media_Video');
    unregister_widget('WP_Widget_Media_Audio');
}

add_action('widgets_init', 'init_unregister_widgets');

// 页面添加html后缀
function html_page_permalink()
{
    global $wp_rewrite;
    if (!strpos($wp_rewrite->get_page_permastruct(), '.html')) {
        $wp_rewrite->page_structure = $wp_rewrite->page_structure . '.html';
    }
}

// 添加斜杠
function add_init_trailingslashit($string, $type_of_url)
{
    if ($type_of_url != 'single' && $type_of_url != 'page')
        $string = trailingslashit($string);
    return $string;
}

//解析get参数
function get_path_query($query)
{
    $querys = explode('&', $query);
    $params = array();
    foreach ($querys as $param) {
        $item = explode('=', $param);
        $params[$item[0]] = $item[1];
    }
    return $params;
}

//GrAvatar头像源
$gravatar_urls = array('www.gravatar.com', '0.gravatar.com', '1.gravatar.com', '2.gravatar.com', 'secure.gravatar.com', 'cn.gravatar.com');
function cn_avatar($avatar)
{
    global $gravatar_urls;
    return str_replace($gravatar_urls, 'cn.gravatar.com', $avatar);
}

function cr_avatar($avatar)
{
    global $gravatar_urls;
    return str_replace($gravatar_urls, 'cravatar.cn', $avatar);
}

function cn_ssl_avatar($avatar)
{
    global $gravatar_urls;
    return str_replace("http://", "https://", str_replace($gravatar_urls, 'cn.gravatar.com', $avatar));
}

function loli_ssl_avatar($avatar)
{
    global $gravatar_urls;
    return str_replace("http://", "https://", str_replace($gravatar_urls, 'gravatar.loli.net', $avatar));
}

function v2ex_ssl_avatar($avatar)
{
    global $gravatar_urls;
    return str_replace("http://", "https://", str_replace("/avatar", "/gravatar", str_replace($gravatar_urls, 'cdn.v2ex.com', $avatar)));
}

//评论回复邮件通知
function comment_mail_notify($comment_id)
{
    $comment = get_comment($comment_id);
    $parent_id = $comment->comment_parent ? $comment->comment_parent : '';
    $spam_confirmed = $comment->comment_approved;
    if ($spam_confirmed != 1) {
        return;
    }
    if ($parent_id != '' && $spam_confirmed != 'spam') {
        $wp_email = 'no-reply@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));
        $to = trim(get_comment($parent_id)->comment_author_email);
        if (pk_check_email_is_sysgen($to)) { // 默认生成的第三方注册邮箱，未进行修改
            return;
        }
        if ($to == $comment->comment_author_email) {
            return;
        }
        $subject = '您在 [' . get_option("blogname") . '] 的文章评论有了新的回复';
        $message = get_comment_notify_template($comment, $parent_id);
        $from = "From: \"" . get_option('blogname') . "\" <$wp_email>";
        $headers = "$from\nContent-Type: text/html; charset=" . get_option('blog_charset') . "\n";
        wp_mail($to, $subject, $message, $headers);
    }
}

//评论添加@
function pk_comment_add_at($text, $comment = '')
{
    if ($comment->comment_parent > 0) {
        $text = "<a class='c-sub' href='#comment-{$comment->comment_parent}'>@" . get_comment_author($comment->comment_parent) . "&nbsp;</a>" . $text;
    }
    return $text;
}

add_action('media_buttons', 'pk_shortcode_box_init', 99);
function pk_shortcode_box_init()
{
    $shortcodes = pk_shortcode_register();
    $output = "";
    foreach ($shortcodes as $key => $item) {
        $attr = '';
        $content = $item['content'] ?? '';
        if (isset($item['attr']) && count($item) > 0) {
            $attr = 'data-attr=\'' . json_encode($item['attr']) . '\'';
        }
        $output .= "<a href='javascript:void(0)' class='add-shortcode button button-small' data-key='{$key}' {$attr} data-content='{$content}'>{$item['name']}</a>";
    }
    echo '<a id="insert-shortcode-button" style="position:relative" class="button" 
        title="' . __('添加短代码', PUOCK) . '" data-editor="content" href="javascript:;">  
        <span>' . __('添加短代码', PUOCK) . '</span>
        </a><div id="insert-shortcode-wrap" class="pk-media-wrap" style="display: none">' . $output . '</div>';
}

//压缩HTML
function wp_compress_html()
{

    //禁止pre标签压缩
    function pre_no_compress($content)
    {
        if (preg_match_all('/<\/pre>/i', $content, $matches)) {
            $content = '<!--wp-compress-html--><!--wp-compress-html no compression-->' . $content;
            $content .= '<!--wp-compress-html no compression--><!--wp-compress-html-->';
        }
        return $content;
    }

    add_filter("the_content", "pre_no_compress");

    function wp_compress_html_main($buffer)
    {
        $initial = strlen($buffer);
        $buffer = explode("<!--wp-compress-html-->", $buffer);
        $count = count($buffer);
        $out = "";
        for ($i = 0; $i <= $count; $i++) {
            if (stristr($buffer[$i], '<!--wp-compress-html no compression-->')) {
                $buffer[$i] = (str_replace("<!--wp-compress-html no compression-->", " ", $buffer[$i]));
            } else {
                $buffer[$i] = (str_replace("\t", " ", $buffer[$i]));
                $buffer[$i] = (str_replace("\n\n", "\n", $buffer[$i]));
                $buffer[$i] = (str_replace("\n", "", $buffer[$i]));
                $buffer[$i] = (str_replace("\r", "", $buffer[$i]));
                while (stristr($buffer[$i], '  ')) {
                    $buffer[$i] = (str_replace("  ", " ", $buffer[$i]));
                }
            }
            $out .= $buffer[$i];
        }
        $final = strlen($out);
        $savings = ($initial - $final) / $initial * 100;
        $savings = round($savings, 2);
        $info = "<!--压缩前为:{$initial}bytes;压缩后为:{$final}bytes;节约:{$savings}%-->";
        return $out . $info;
    }

    ob_start("wp_compress_html_main");
}

//跳转链接
function pk_go_link($url, $name = '')
{
    if (pk_is_cur_site($url)) {
        return $url;
    }
    $url = PUOCK_ABS_URI . '/inc/go.php?to=' . base64_encode($url);
    if (!empty($name)) {
        $url .= '&name=' . base64_encode($name);
    }
    return $url;
}

//检测链接是否属于本站
function pk_is_cur_site($url)
{
    if (strpos($url, home_url()) === 0) {
        return true;
    }
    return false;
}

if (pk_is_checked('use_post_menu')) {
    //生成目录锚点
    function pk_post_menu_id($content)
    {
        if (preg_match_all("/<h[1234]>(.*?)<\/h[1234]>/im", $content, $ms)) {
            foreach ($ms[1] as $i => $title) {
                $start = stripos($content, $ms[0][$i]);
                $end = strlen($ms[0][$i]);
                $level = substr($ms[0][$i], 1, 2);
                $content = substr_replace($content, "<$level id='pk-menu-${i}'>{$title}</{$level}>", $start, $end);
            }
        }
        return $content;
    }

    add_filter("the_content", "pk_post_menu_id");
}
//兼容处理
function pk_compatible()
{
    wp_scripts()->remove("Editormd_Front");
}

add_action('wp_enqueue_scripts', 'pk_compatible', 999);

if (pk_is_checked("upload_webp")) {
    add_filter('plupload_default_settings', function ($defaults) {
        $defaults['webp_upload_error'] = false;
        return $defaults;
    }, 10, 1);

    add_filter('plupload_init', function ($plupload_init) {
        $plupload_init['webp_upload_error'] = false;
        return $plupload_init;
    }, 10, 1);
}

function pk_get_comment_os_images($name)
{
    if (in_array($name, array("Android", "Chrome", "Edge", "Firefox", "Linux",
        "Macintosh", "Safari", "Windows"))) {
        return pk_get_static_url() . '/assets/img/os/' . $name . '.svg';
    }
    return pk_get_static_url() . '/assets/img/os/Unknown.svg';
}

// 二维码生成
function pk_post_qrcode($url)
{
    $file = '/cache/qr-' . md5($url) . '.png';
    $filepath = PUOCK_ABS_DIR . $file;
    if (!file_exists($filepath)) {
        QRcode::png($url, $filepath, QR_ECLEVEL_L, 7, 1);
    }
    return $file;
}

// 评论验证码
// request url: {host}/wp-admin/admin-ajax.php?action=puock_comment_captcha
function pk_comment_captcha()
{
    $width = $_GET['w'];
    $height = $_GET['h'];
    $captch = new CaptchaBuilder();
    $captch->initialize([
        'width' => intval($width),     // 宽度
        'height' => intval($height),     // 高度
        'line' => true,     // 直线
        'curve' => true,   // 曲线
        'noise' => 1,   // 噪点背景
        'fonts' => [PUOCK_ABS_DIR . '/assets/fonts/G8321-Bold.ttf']       // 字体
    ]);
    $result = $captch->create();
    $text = $result->getText();
    pk_session_call(function () use ($text) {
        $_SESSION['comment_captcha'] = $text;
    });
    $result->output();
    die;
}

add_action('wp_ajax_nopriv_puock_comment_captcha', 'pk_comment_captcha');
add_action('wp_ajax_puock_comment_captcha', 'pk_comment_captcha');

function pk_get_favicon_url($url)
{
    return PUOCK_ABS_URI . '/inc/favicon.php?url=' . $url;
}

function pk_post_comment_is_closed()
{
    return pk_is_checked('close_post_comment', false);
}

//add_filter('clean_url', 'pk_compatible_githuber_md_katex', 10, 3);
function pk_compatible_githuber_md_katex($good_protocol_url, $original_url, $_context)
{
    if (false !== strpos($original_url, '/assets/vendor/katex/katex.min.js')) {
        remove_filter('clean_url', 'unclean_url');
        $url_parts = parse_url($good_protocol_url);
        return $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . "' data-instant nil='";
    }
    return $good_protocol_url;
}

//获取网站标题
function pk_get_web_title()
{
    return pk_get_option('web_title', get_bloginfo('name'));
}

// 获取链接的target属性
function pk_link_target($echo = true)
{
    $target = "";
    if (!pk_is_checked('page_ajax_load') && pk_is_checked('link_blank_content')) {
        $target = "target=\"_blank\"";
    }
    if ($echo) {
        echo $target;
    }
    return $target;
}

// 头部样式定义
function pk_head_style_var()
{
    $vars = [
        "--puock-block-not-tran:" . pk_get_option('block_not_tran', 100) . "%",
    ];
    return "<style>:root{" . join(";", $vars) . "}</style>";
}

// 加载文件媒体文件
function pk_load_media_files()
{
    wp_enqueue_media();
}

add_action('admin_enqueue_scripts', 'pk_load_media_files');

// debug sql
function pk_debug_print_sql_list()
{
    global $wpdb;
    $show_sql_count = pk_get_option('debug_sql_count');
    $show_sql_detail = pk_get_option('debug_sql_detail');
    $out = "<script>";
    if ($show_sql_count) {
        $out .= "console.log('共计查询SQL：" . get_num_queries() . "次，耗时：" . timer_stop() . "秒');";
    }
    if ($show_sql_detail) {
        $out .= "console.log(" . json_encode($wpdb->queries) . ");";
    }
    echo $out . "</script>";
}

function pk_get_excerpt_more_filter(){
    return '...';
}
add_filter('excerpt_more', 'pk_get_excerpt_more_filter');
