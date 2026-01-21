<?php

define('PUOCK_ABS_DIR', get_template_directory());
define('PUOCK_ABS_URI', get_template_directory_uri());
define('PUOCK_CUR_VER_STR', wp_get_theme()->get('Version'));
const PUOCK = 'puock';
const PUOCK_OPT = 'puock_options';
$puock_colors_name = ['primary', 'danger', 'info', 'success', 'warning', 'dark', 'secondary'];

include_once('vendor/autoload.php');
include_once('inc/fun/core.php');
include_once('gutenberg/index.php');




//去除感谢使用wordpress创作
if (pk_is_checked('hide_footer_wp_t')) {
    function my_admin_footer_text()
    {
        return '';
    }

    function my_update_footer()
    {
        return '';
    }

    function annointed_admin_bar_remove()
    {
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu('wp-logo');
    }

    add_action('wp_before_admin_bar_render', 'annointed_admin_bar_remove', 0);
    add_filter('admin_footer_text', 'my_admin_footer_text', 10);
    add_filter('update_footer', 'my_update_footer', 50);
}

//禁用5.0古登堡编辑器
if (pk_is_checked('stop5x_editor')) {
    add_filter('use_block_editor_for_post', '__return_false');
    remove_action('wp_enqueue_scripts', 'wp_common_block_scripts_and_styles');
    function remove_global_styles_and_svg_filters()
    {
        remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
        remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');
    }

    add_action('init', 'remove_global_styles_and_svg_filters');
}

//区块小工具
if (!pk_is_checked('use_widgets_block')) {
    pk_off_widgets_block();
}

//获取评论等级
function pk_the_author_class_out($count)
{
    if ($count <= 0) {
        return '';
    }
    switch ($count) {
        case $count >= 1 && $count < 20:
            $level = 1;
            break;
        case $count >= 20 && $count < 40:
            $level = 2;
            break;
        case $count >= 40 && $count < 60:
            $level = 3;
            break;
        case $count >= 60 && $count < 80:
            $level = 4;
            break;
        case $count >= 120 && $count < 120:
            $level = 5;
            break;
        case $count >= 140 && $count < 140:
            $level = 6;
            break;
        case $count >= 160 && $count < 160:
            $level = 7;
            break;
        default:
            return '';
    }
    return '<span class="t-sm c-sub"><i class="fa-regular fa-gem mr-1"></i>' . __('评论达人', PUOCK) . ' LV.' . $level . '</span>';
}

function pk_the_author_class($echo = true, $in_comment = null)
{
    global $wpdb, $comment;
    if (!$comment) {
        $comment = $in_comment;
    }
    if ($comment->user_id == '1') {
        $res = '<span class="t-sm text-danger"><i class="fa-regular fa-gem mr-1"></i>' . __('博主', PUOCK) . '</span>';
    } else {
        $comment_author_email = $comment->comment_author_email;
        $cache_key = sprintf(PKC_AUTHOR_COMMENTS, md5($comment_author_email));
        $author_count = pk_cache_get($cache_key);
        if (!$author_count) {
            $query = $wpdb->prepare("SELECT count(1) as c FROM $wpdb->comments WHERE comment_author_email = %s", $comment_author_email);
            $author_count = $wpdb->get_results($query)[0]->c;
            pk_cache_set($cache_key, $author_count);
        }
        $res = pk_the_author_class_out($author_count);
    }
    if (!$echo) {
        return $res;
    }
    echo $res;
}

//获取Gravatar头像
function pk_get_gravatar($email, $echo = true)
{
    $link = get_avatar_url($email);
    if (!$echo) {
        return $link;
    }
    echo $link;
}

//获取文章分类链接
function get_post_category_link($class = '', $icon = '', $cid = null, $default = null, $index = 0)
{
    return get_post_category_link_exec(false, $class, $icon, $cid, $default, $index);
}

function get_post_category_link_exec($all = true, $class = '', $icon = '', $cid = null, $default = null, $index = 0)
{
    global $cat;
    if ($default == null) {
        $default = __('无分类', PUOCK);
    }
    if ($cid != null) {
        $cate = get_category($cid);
        if ($cate != null) {
            return '<a ' . pk_link_target(false) . ' class="' . $class . '" href="' . get_category_link($cate) . '">' . $icon . $cate->name . '</a>';
        }
    } else {
        $cats = get_the_category();
        if (count($cats) > 0) {
            if ($all) {
                $out = "";
                foreach ($cats as $cate) {
                    $out .= '<a ' . pk_link_target(false) . ' class="' . $class . ' mr5" href="' . get_category_link($cate) . '"><i class="fa-regular fa-folder-open"></i> ' . $icon . $cate->name . '</a> ';
                }
                $out = mb_substr($out, 0, mb_strlen($out) - 1);
                return $out;
            } else {
                $cate = $cats[0];
                return '<a ' . pk_link_target(false) . ' class="' . $class . '" href="' . get_category_link($cate) . '"><i class="fa-regular fa-folder-open"></i> ' . $icon . $cate->name . '</a>';
            }
        }
    }
    return '<a class="' . $class . '" href="javascript:void(0)">' . $icon . $default . '</a>';
}

//获取文章标签
function get_post_tags($class = '', $item_class = '')
{
    global $puock_colors_name;
    $tags = get_the_tags();
    $out = '';
    if ($tags && count($tags) > 0) {
        $out .= '<div class="' . $class . '">';
        foreach ($tags as $tag) {
            //            $color_index = mt_rand(0, count($puock_colors_name) - 1);
            $out .= '<a href="' . get_tag_link($tag) . '" class="pk-badge pk-badge-sm mr5 ' . $item_class . '"><i class="fa-solid fa-tag"></i> ' . $tag->name . '</a>';
        }
        $out .= '</div>';
    } else {
        //
    }
    return $out;
}

function pk_get_post_date()
{
    $time = get_post_time();
    $c_time = time() - $time;
    $day = 86400;
    switch ($c_time) {
        case $c_time < $day:
            $res = __('近一天内',PUOCK);
            break;
        case $c_time < ($day * 2):
            $res = __('近两天内',PUOCK);
            break;
        case $c_time < ($day * 3):
            $res = __('近三天内',PUOCK);
            break;
        case $c_time < ($day * 4):
            $res = __('四天前',PUOCK);
            break;
        case $c_time < ($day * 5):
            $res = __('五天前',PUOCK);
            break;
        case $c_time < ($day * 6):
            $res = __('六天前',PUOCK);
            break;
        default:
            $res = get_the_date(get_option('date_format'));
    }
    echo $res;
}

//获取随机的bootstrap的颜色表示
function pk_get_color_tag($ex = array())
{
    global $puock_colors_name;
    while (true) {
        $c = $puock_colors_name[mt_rand(0, count($puock_colors_name) - 1)];
        if (!in_array($c, $ex)) {
            return $c;
        }
    }
}


function get_smiley_codes()
{
    return array(
        ":?:" => __("疑问", PUOCK), ":razz:" => __("调皮", PUOCK), ":sad:" => __("难过", PUOCK), ":evil:" => __("抠鼻", PUOCK), ":naughty:" => __("顽皮", PUOCK),
        ":!:" => __("吓", PUOCK), ":smile:" => __("微笑", PUOCK), ":oops:" => __("憨笑", PUOCK), ":neutral:" => __("亲亲", PUOCK), ":cry:" => __("大哭", PUOCK), ":mrgreen:" => __("呲牙", PUOCK),
        ":grin:" => __("坏笑", PUOCK), ":eek:" => __("惊讶", PUOCK), ":shock:" => __("发呆", PUOCK), ":???:" => __("撇嘴", PUOCK), ":cool:" => __("酷", PUOCK), ":lol:" => __("偷笑", PUOCK),
        ":mad:" => __("咒骂", PUOCK), ":twisted:" => __("发怒", PUOCK), ":roll:" => __("白眼", PUOCK), ":wink:" => __("鼓掌", PUOCK), ":idea:" => __("想法", PUOCK), ":despise:" => __("蔑视", PUOCK),
        ":celebrate:" => __("庆祝", PUOCK), ":watermelon:" => __("西瓜", PUOCK), ":xmas:" => __("圣诞", PUOCK), ":warn:" => __("警告", PUOCK), ":rainbow:" => __("彩虹", PUOCK),
        ":loveyou:" => __("爱你", PUOCK), ":love:" => __("爱", PUOCK), ":beer:" => __("啤酒", PUOCK),
    );
}

function get_smiley_image($key)
{
    $imgKey = mb_substr($key, 1, mb_strlen($key) - 2);
    if ($imgKey == '?') {
        $imgKey = 'doubt';
    }
    if ($imgKey == '!') {
        $imgKey = 'scare';
    }
    if ($imgKey == '???') {
        $imgKey = 'bz';
    }
    return $imgKey;
}

function custom_smilies_src($old, $img)
{
    return get_stylesheet_directory_uri() . '/assets/img/smiley/' . $img;
}

add_filter('smilies_src', 'custom_smilies_src', 10, 2);

function puock_twemoji_smiley()
{
    global $wpsmiliestrans;
    if (!get_option('use_smilies'))
        return;
    $wpsmiliestrans = array();
    foreach (get_smiley_codes() as $key => $val) {
        $wpsmiliestrans[$key] = get_smiley_image($key) . '.png';
    }
    return $wpsmiliestrans;
}

add_action('init', 'puock_twemoji_smiley', 3);

function get_wpsmiliestrans()
{
    global $wpsmiliestrans, $output;
    if (!is_array($wpsmiliestrans)) {
        $wpsmiliestrans = array();
    }
    $wpsmilies = array_unique($wpsmiliestrans);
    foreach ($wpsmilies as $alt => $src_path) {
        $output .= '<a class="add-smily" data-smilies="' . $alt . '"><img src="' . get_bloginfo('template_directory') . '/assets/img/smiley/' . rtrim($src_path, "png") . 'png" /></a>';
    }
    return $output;
}

add_action('media_buttons', 'smilies_custom_button');
function smilies_custom_button($context)
{
    echo '<a id="insert-smiley-button" style="position:relative" class="button" 
        title="' . __('添加表情', PUOCK) . '" data-editor="content" href="javascript:;">  
        <span>' . __('添加表情', PUOCK) . '</span> 
        </a><div id="insert-smiley-wrap" class="pk-media-wrap" style="display: none">' . get_wpsmiliestrans() . '</div>';
}
/**
 * 获取文章封面图：优先级 = 特色图 > 内容第一张图（支持 Markdown）> 随机默认图
 *
 * @param int|WP_Post|null $_post 文章 ID 或对象，null 则使用全局 $post
 * @return string 图片 URL
 */
function get_post_images($_post = null): string
{
    global $post;

    // 1. 获取文章对象
    $post_obj = $_post ? get_post($_post) : $post;
    if (!$post_obj) {
        return get_random_default_image();
    }

    $post_id = $post_obj->ID;
    $content = $post_obj->post_content;

    // 2. 优先：特色图（支持 attachment 和 外部链接）
    if (has_post_thumbnail($post_id)) {
        $featured_url = get_the_post_thumbnail_url($post_id, 'large');
        if ($featured_url) {
            return esc_url($featured_url);
        }
    }

    // 可选：支持外部特色图（如果你用了之前“external_thumbnail_url”的方案）
    $external_thumb = get_post_meta($post_id, 'external_thumbnail_url', true);
    if ($external_thumb) {
        return esc_url($external_thumb);
    }

    // 3. 次选：从内容提取第一张图（支持 Markdown 和 HTML）
    $first_image = null;

    // 匹配 Markdown 图片：![alt](url)
    if (preg_match('/!\[[^\]]*\]\(\s*([^\s\)]+?)\s*([\'"][^\'"]*?[\'"])?\s*\)/i', $content, $matches)) {
        $first_image = trim($matches[1]);
    }
    // 如果没找到 Markdown 图片，再匹配 HTML 图片
    elseif (preg_match('/<img[^>]+src=[\'"]([^\'"]+)[\'"]/i', $content, $matches)) {
        $first_image = $matches[1];
    }

    if ($first_image && filter_var($first_image, FILTER_VALIDATE_URL)) {
        return esc_url($first_image);
    }

    // 4. 最后：返回随机默认图
    return get_random_default_image();
}

/**
 * 获取随机默认图片
 *
 * @return string 默认图 URL
 */
function get_random_default_image(): string
{
    $img_dir = get_template_directory_uri() . '/assets/img/random/';
    return esc_url($img_dir . mt_rand(1, 8) . '.jpg');
}

//分页功能
if (!function_exists('pk_paging')) {
    function pk_paging($pnum = 2)
    {
        if (is_singular()) {
            return;
        };
        global $wp_query, $paged;
        $max_page = $wp_query->max_num_pages;
        if ($max_page == 1) return;
        echo '<div class="mt20 p-flex-s-right"><ul class="pagination">';
        if (empty($paged)) $paged = 1;
        echo '<li class="prev-page puock-bg">';
        previous_posts_link('&laquo;');
        echo '</li>';
        if ($paged > $pnum + 1) page_link(1);
        if ($paged > $pnum + 2) echo "<li><a href='javascript:void(0)'>...</a></li>";
        for ($i = $paged - $pnum; $i <= $paged + $pnum; $i++) {
            if ($i > 0 && $i <= $max_page) {
                if ($i == $paged) {
                    echo "<li ><a class='cur'>{$i}</a></li>";
                } else {
                    page_link($i);
                }
            }
        }
        if ($paged < $max_page - $pnum - 1) {
            echo "<li><a href='javascript:void(0)'>...</a></li>";
            page_link($max_page);
        }
        echo '<li class="next-page">';
        next_posts_link('&raquo;');
        echo '</li>';
        echo '</ul></div>';
    }

    function page_link($i, $title = '')
    {
        echo "<li><a href='", esc_html(get_pagenum_link($i)), "'>{$i}</a></li>";
    }
}

//获取面包屑导航
function pk_breadcrumbs()
{
    global $cat;
    $custom_seo_title = pk_get_custom_seo()['title'] ?? '';
    $out = '<div id="breadcrumb" class="' . (pk_open_box_animated('animated fadeInUp', false)) . '">';
    $out .= '<nav aria-label="breadcrumb">';
    $out .= '<ol class="breadcrumb">';
    $out .= '<li class="breadcrumb-item"><a class="a-link" href="' . home_url() . '">' . __('首页', PUOCK) . '</a></li>';
    if (is_single() || is_category()) {
        $categorys = get_the_category();
        if (count($categorys) <= 0 && is_single()) {
            return false;
        }
        if (is_single()) {
            $category = $categorys[0];
            if ($category == null && is_category()) {
                $category = get_category($cat);
            }
            $cats = get_category_parents($category->term_id, true, '');
        } else {
            $cats = get_category_parents($cat, true, '');
        }
        $cats = str_replace("<a", '<li class="breadcrumb-item"><a class="a-link"', $cats);
        $cats = str_replace("</a>", '</a></li>', $cats);
        $out .= $cats;
        if (is_single()) {
            $out .= '<li class="breadcrumb-item active " aria-current="page">' . __('正文', PUOCK) . '</li>';
        } else if (is_category()) {
            $out .= '<li class="breadcrumb-item active " aria-current="page">' . __('文章列表', PUOCK) . '</li>';
        }
    } else if (is_search()) {
        $out .= '<li class="breadcrumb-item active " aria-current="page">' . (esc_html($_GET['s'])) . '</li>';
        $out .= '<li class="breadcrumb-item active " aria-current="page">' . __('搜索结果', PUOCK) . '</li>';
    } else if (is_author()) {
        $out .= '<li class="breadcrumb-item active " aria-current="page">' . get_the_author_meta('nickname') . '</li>';
    } else if (is_date()) {
        $out .= '<li class="breadcrumb-item active " aria-current="page">' . get_the_date() . '</li>';
    } else if (is_page()) {
        global $post;
        $out .= '<li class="breadcrumb-item active " aria-current="page">' . ($post->post_title) . '</li>';
    } else if (is_tag()) {
        $tag_name = single_tag_title('', false);
        $out .= '<li class="breadcrumb-item active " aria-current="page">' . __('标签', PUOCK) . '</li>';
        $out .= '<li class="breadcrumb-item active " aria-current="page">' . ($tag_name) . '</li>';
    } else if (!empty($custom_seo_title)) {
        $out .= '<li class="breadcrumb-item active " aria-current="page">' . $custom_seo_title . '</li>';
    } else if (is_404()) {
        $out .= '<li class="breadcrumb-item active " aria-current="page">' . __('你访问的资源不存在', PUOCK) . '</li>';
    }
    $out .= '</div></nav></ol>';
    return $out;
}

/**
 * 返回图标信息
 *
 * @return string
 * @author lvshujun
 * @date 2024-03-19
 */
function pk_icon_mate() {
    //获取icon地址
    $pk_icon = pk_get_option('favicon');
    //未设置返回空
    if ($pk_icon === '') return '';
    
    //连接字符串
    $str = '<link rel="shortcut icon" href="' . $pk_icon . '">
    <link rel="apple-touch-icon" href="' . $pk_icon . '"/>';

    return $str;
}

/**
 * 输出SEO标题
 *
 * @return string SEO标题
 * @author lvshujun
 * @date 2024-03-19
 */
function pk_get_seo_title() {
    // 未启用SEO返回空
    if (!pk_is_checked('seo_open',true) || defined('THE_SEO_FRAMEWORK_VERSION')) {
        return '';
    }
    // 用户定义的连接符
    $pk_title_conn = ' ' . pk_get_option("title_conn") . ' ';
    // 网站名称
    $pk_blog_name = pk_get_web_title();
    // 分页情况
    $pk_paged_title = '';
    if (get_query_var('paged')) {
        $pk_paged_title = $pk_title_conn . sprintf(__('第%d页', PUOCK), get_query_var('paged'));
    }
    // 获取SEO设置
    $pk_custom_seo_title = pk_get_custom_seo()['title'] ?? '';
    // 输出内容
    $pk_title = '';
    // 通用结尾
    $pk_common_end = $pk_paged_title . $pk_title_conn . $pk_blog_name;
    // 已经自定义标题
    if (!empty($pk_custom_seo_title)) {
        $pk_title .= $pk_custom_seo_title . $pk_common_end;
    } else if (is_home()) {
        $pk_description = pk_get_option('web_title_2');
        if (!empty($pk_description)) {
            $pk_title .= $pk_blog_name . $pk_paged_title . $pk_title_conn . $pk_description;
        } else {
            $pk_title .= $pk_blog_name . $pk_paged_title;
        }
    } else if (is_search()) {
        $pk_title .= sprintf(__('搜索"%s"的结果', PUOCK), esc_html($_REQUEST['s'])) . $pk_common_end;
    } else if (is_single() || is_page()) {
        $pk_title .= single_post_title('', false) . $pk_common_end;
    } else if (is_year()) {
        $pk_title .= sprintf(__('%s年的所有文章', PUOCK), get_the_time('Y')) . $pk_common_end;
    } else if (is_month()) {
        $pk_title .= sprintf(__('%s月的所有文章', PUOCK), get_the_time('m')) . $pk_common_end;
    } else if (is_day()) {
        $pk_title .= sprintf(__('%s年%s月%s日的所有文章', PUOCK), get_the_time('Y'), get_the_time('m'), get_the_time('d')) . $pk_common_end;
    } else if (is_author()) {
        $pk_title .= sprintf(__('作者：%s', PUOCK), get_the_author()) . $pk_common_end;
    } else if (is_category()) {
        $pk_title .= single_cat_title('', false) . $pk_common_end;
    } else if (is_tag()) {
        $pk_title .= single_tag_title('', false) . $pk_common_end;
    } else if (is_404()) {
        $pk_title .= __('你访问的资源不存在', PUOCK) . $pk_common_end;
    } else {
        $pk_title .= $pk_blog_name . $pk_paged_title;
    }
    return '<title>'.$pk_title.'</title>';
}

//获取阅读数量
function pk_get_post_views()
{
    if (function_exists('the_views')) {
        echo the_views(null, false);
    } else {
        echo 0;
    }
}

//字数统计
function count_words($text = null)
{
    global $post;
    if (empty($text)) {
        $text = $post->post_content;
    }
    return mb_strlen(preg_replace('/\s/', '', html_entity_decode(strip_tags($text))), 'UTF-8');
}

//给文章内容添加灯箱
function light_box_text_replace($content)
{
    $pattern = "/<a(.*?)href=('|\")([A-Za-z0-9\/_\.\~\:-]*?)(\.bmp|\.gif|\.jpg|\.jpeg|\.png)('|\")([^\>]*?)>/i";
    $replacement = '<a$1href=$2$3$4$5$6 class="fancybox" data-no-instant target="_blank">';
    $content = preg_replace($pattern, $replacement, $content);
    return $content;
}

add_filter('the_content', 'light_box_text_replace', 99);


//给图片加上alt/title
function content_img_add_alt_title($content)
{
    global $post;
    preg_match_all('/<img (.*?)\/>/', $content, $images);
    if (!is_null($images)) {
        $title = @$post->post_title;
        foreach ($images[1] as $index => $value) {
            $new_img = str_replace('<img', '<img title="' . $title . '"
             alt="' . $title . '"', $images[0][$index]);
            $content = str_replace($images[0][$index], $new_img, $content);
        }
    }
    return $content;
}

add_filter('the_content', 'content_img_add_alt_title', 99);

//加上bootstrap的表格class
function pk_bootstrap_table_class($content)
{
    global $post;
    preg_match_all('/<table.*?>[\s\S]*<\/table>/', $content, $tables);
    if (!is_null($tables)) {
        foreach ($tables[0] as $index => $value) {
            $out = str_replace('<table', '<table class="table table-bordered puock-text"', $tables[0][$index]);
            $content = str_replace($tables[0][$index], $out, $content);
        }
    }
    return $content;
}

add_filter('the_content', 'pk_bootstrap_table_class', 99);

//初始化wp_style函数，以防止出现Invalid argument supplied for foreach()错误
function pk_init_wp_empty_style()
{
    wp_enqueue_style('');
}

add_action('wp_enqueue_scripts', 'pk_init_wp_empty_style');

require_once dirname(__FILE__) . '/fun-custom.php';

//更新支持
function pk_update()
{
    $update_server = pk_get_option('update_server');
    $check_period = pk_get_option('update_server_check_period');
    if (empty($check_period) || !is_numeric($check_period)) {
        $check_period = 6;
    }
    $current_theme_dir_name = basename(dirname(__FILE__));
    include('update-checker/plugin-update-checker.php');
    switch ($update_server) {
        case 'github':
            {
                $pkUpdateChecker = YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
                    'https://github.com/Licoy/wordpress-theme-puock',
                    __FILE__,
                    PUOCK,
                    $check_period
                );
            }
            break;
        case 'fastgit':
            {
                $pkUpdateChecker = YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
                    'https://licoy.cn/go/puock-update.php?r=fastgit',
                    __FILE__,
                    PUOCK,
                    $check_period
                );
            }
            break;
        default:
        {
            $pkUpdateChecker = YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
                'https://licoy.cn/go/puock-update.php?r=worker',
                __FILE__,
                PUOCK,
                $check_period
            );
        }
    }
}


////WordPress 评论回复邮件通知代码 TODO 等待测试改进
//function pk_comment_mail_notify($comment_id)
//{
//    $admin_notify = '1'; // admin 要不要收回复通知 ( '1'=要 ; '0'=不要 )
//    $admin_email = get_bloginfo('admin_email');
//    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
//    $comment = get_comment($comment_id);
//    $parent_id = $comment->comment_parent ? $comment->comment_parent : '';
//    $spam_confirmed = $comment->comment_approved;
//    global $wpdb;
//    $comments_waiting = $wpdb->get_var("SELECT count(comment_ID) FROM $wpdb->comments WHERE comment_approved = '0'");
//    if (($parent_id != '') && ($spam_confirmed != 'spam') && ($to != $admin_email)) {
//        $wp_email = 'no-reply@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));
//        $to = trim(get_comment($parent_id)->comment_author_email);
//        $subject = '您在 [' . $blogname . ']' . ' 中的留言有了新回复！';
//        $message = '
//                  <div style="background-color:white;border-left: 2px solid #555555;box-shadow:0 1px 3px #AAAAAA;line-height:180%;padding:0 15px 12px;width:500px;margin:50px auto;color:#555555;font-family:"Source Sans Pro","Hiragino Sans GB","Microsoft Yahei",SimSun,Helvetica,Arial,Sans-serif,monospace;font-size:14px;">
//                         <h2 style="border-bottom:1px solid #DDD;font-size:14px;font-weight:normal;padding:13px 0 10px 8px;">
//                                    <span style="color: #409eff;font-weight: bold;">&gt; </span>
//                                    您在 <a style="text-decoration:none; color:#409eff;font-weight:600;" href="' . home_url() . '">' . $blogname . '</a> 的留言有回复啦！
//                           </h2>
//                           <div style="font-size: 14px; color: #777; padding: 0 10px; margin-top: 18px;">
//                                    <p><b>' . trim(get_comment($parent_id)->comment_author) . '</b> 同学，您曾在文章<b>《' . get_the_title($comment->comment_post_ID) . '》</b>上发表评论:</p>
//                                    <p style="background: #F5F5F5; padding: 10px 15px; margin: 18px 0;">' . nl2br(strip_tags(get_comment($parent_id)->comment_content)) . '</p>
//                                    <p>' . '<b>' . trim($comment->comment_author) . '</b>' . ' 给您的回复如下:</p>
//                                    <p style="background: #F5F5F5; padding: 10px 15px; margin: 18px 0;">' . nl2br(strip_tags($comment->comment_content)) . '</p>
//                                    <p>您可以点击 <a style="text-decoration:none; color:#409eff" href="' . htmlspecialchars(get_comment_link($parent_id)) . '">查看完整的回复內容</a>，也欢迎再次光临 <a style="text-decoration:none; color:#409eff"
//                                    href="' . home_url() . '">' . $blogname . '</a>。祝您生活愉快！</p>
//                                    <p style="padding-bottom: 15px;">(此邮件由系统自动发出,请勿直接回复!)</p>
//                           </div>
//                  </div>';
//        $from = "From: \"" . get_option('blogname') . "\" <$wp_email>";
//        $headers = "$from\nContent-Type: text/html; charset=" . get_option('blog_charset') . "\n";
//        wp_mail($to, $subject, $message, $headers);
//    }
//    //文章有新评论时通知管理员
//    if ($parent_id == '' && (trim($comment->comment_author_email) != trim($admin_email)) && ($spam_confirmed != 'spam') && ($comment->comment_approved != 0)) {
//        $wp_email = '';
//        $subject = '在「' . $blogname . '」的文章《' . get_the_title($comment->comment_post_ID) . '》一文有新的评论';
//        $message = '
//            <div style="background-color:white;border-left: 2px solid #555555;box-shadow:0 1px 3px #AAAAAA;line-height:180%;padding:0 15px 12px;width:500px;margin:50px auto;color:#555555;font-family:"Source Sans Pro","Hiragino Sans GB","Microsoft Yahei",SimSun,Helvetica,Arial,Sans-serif,monospace;font-size:14px;">
//                     <h2 style="border-bottom:1px solid #DDD;font-size:14px;font-weight:normal;padding:13px 0 10px 8px;">
//                              <span style="color: #409eff;font-weight: bold;">&gt; </span>
//                              <a style="text-decoration:none;color: #409eff;" href="' . home_url() . '">' . $blogname . '</a> 博客有新的评论啦！
//                     </h2>
//                     <div style="padding:0 12px 0 12px;margin-top:18px;">
//                              <p><b>' . $comment->comment_author . '</b> 同学在文章<b>《' . get_the_title($comment->comment_post_ID) . '》</b>上发表评论:</p>
//                              <p style="background-color: #f5f5f5;border: 0px solid #DDD;padding: 10px 15px;margin:18px 0;">' . $comment->comment_content . '</p>
//                              <p>您可以点击 <a style="text-decoration:none; color:#409eff" href="' . htmlspecialchars(get_comment_link($parent_id)) . '">查看完整的回复內容</a>，也欢迎再次光临 <a style="text-decoration:none; color:#409eff" href="' . home_url() . '">' . $blogname . '</a>。祝您生活愉快！</p>
//                     </div>
//            </div>';
//        $headers = "Content-Type: text/html; charset=" . get_option('blog_charset') . "\n";
//        wp_mail($admin_email, $subject, $message, $headers);
//    }
//    //评论需要审核时通知
//    if ($parent_id == '' && (trim($comment->comment_author_email) != trim($admin_email)) && ($spam_confirmed != 'spam') && ($spam_confirmed != 'trash') && ($comment->comment_approved == 0)) {
//        $wp_email = '';
//        $subject = '在「' . $blogname . '」的文章《' . get_the_title($comment->comment_post_ID) . '》中有新的评论需要审核';
//        $message = '
//            <div style="background-color:white;border-left: 2px solid #555555;box-shadow:0 1px 3px #AAAAAA;line-height:180%;padding:0 15px 12px;width:500px;margin:50px auto;color:#555555;font-family:"Source Sans Pro","Hiragino Sans GB","Microsoft Yahei",SimSun,Helvetica,Arial,Sans-serif,monospace;font-size:14px;">
//                     <h2 style="border-bottom:1px solid #DDD;font-size:14px;font-weight:normal;padding:13px 0 10px 8px;">
//                              <span style="color: #409eff;font-weight: bold;">&gt; 「 </span>
//                              <a style="text-decoration:none;color: #409eff;" href="' . home_url() . '">' . $blogname . '」</a> 中有一条评论等待您的审核
//                     </h2>
//                     <div style="padding:0 12px 0 12px;margin-top:18px;">
//                              <p><b>' . $comment->comment_author . '</b> 同学在文章<b><a style="text-decoration:none;color: #409eff;" href="' . get_permalink($comment->comment_post_ID) . '">《' . get_the_title($comment->comment_post_ID) . '》</a></b>上发表评论:</p>
//                              <p style="background-color: #f5f5f5;border: 0px solid #DDD;padding: 10px 15px;margin:18px 0;">' . $comment->comment_content . '</p>
//                              <p><a style="text-decoration:none;color: #007017;" href="' . admin_url("comment.php?action=approve&c={$comment_id}#wpbody-content") . '">[批准评论]</a> | <a style="text-decoration:none;color: #b32d2e;" href="' . admin_url("comment.php?action=trash&c={$comment_id}#wpbody-content") . '">[移至回收站]</a>。您还可以：<a style="text-decoration:none; color:#b32d2e" href="' . admin_url("comment.php?action=delete&c={$comment_id}#wpbody-content") . '">永久删除评论</a> | <a style="text-decoration:none;color: #b32d2e;" href="' . admin_url("comment.php?action=spam&c={$comment_id}#wpbody-content") . '">标记为垃圾评论</a>
//                              <p>当前有 ' . $comments_waiting . ' 条评论等待审核。请移步<a style="text-decoration:none;color: #409eff;" href="' . admin_url('edit-comments.php?comment_status=moderated#wpbody-content') . '">审核页面</a>来查看。</p>也欢迎再次光临 <a style="text-decoration:none; color:#409eff" href="' . home_url() . '">' . $blogname . '</a>。祝您生活愉快！</p>
//                     </div>
//            </div>';
//        $headers = "Content-Type: text/html; charset=" . get_option('blog_charset') . "\n";
//        wp_mail($admin_email, $subject, $message, $headers);
//    }
//}
//
//add_action('comment_post', 'pk_comment_mail_notify');

if (is_admin()) {
    // 在线更新支持
    pk_update();
}

