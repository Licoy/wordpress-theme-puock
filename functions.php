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

function pk_the_author_class($echo = true, $in_comment = null){
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
                    $out .= '<a ' . pk_link_target(false) . ' class="' . $class . '" href="' . get_category_link($cate) . '">' . $icon . $cate->name . '</a>、';
                }
                $out = mb_substr($out, 0, mb_strlen($out) - 1);
                return $out;
            } else {
                if (!is_category()) {
                    $cate = $cats[0];
                } else {
                    $cate = get_category($cat);
                }
                return '<a ' . pk_link_target(false) . ' class="' . $class . '" href="' . get_category_link($cate) . '">' . $icon . $cate->name . '</a>';
            }
        }
    }
    return '<a class="' . $class . '" href="javascript:void(0)">' . $icon . $default . '</a>';
}

//获取文章标签
function get_post_tags($class = '')
{
    global $puock_colors_name;
    $tags = get_the_tags();
    $out = '';
    if ($tags && count($tags) > 0) {
        $out .= '<div class="' . $class . '">';
        foreach ($tags as $tag) {
            $color_index = mt_rand(0, count($puock_colors_name) - 1);
            $out .= '<a href="' . get_tag_link($tag) . '" class="ahfff curp mr-1 badge badge-' . $puock_colors_name[$color_index] . '"># ' . $tag->name . '</a>';
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
        //todo 本地化翻译
        case $c_time < $day:
            $res = '近一天内';
            break;
        case $c_time < ($day * 2):
            $res = '近两天内';
            break;
        case $c_time < ($day * 3):
            $res = '近三天内';
            break;
        case $c_time < ($day * 4):
            $res = '四天前';
            break;
        case $c_time < ($day * 5):
            $res = '五天前';
            break;
        case $c_time < ($day * 6):
            $res = '六天前';
            break;
        default:
            $res = date('Y-m-d', $time);
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
    };
}


function get_smiley_codes()
{
    //todo 本地化翻译
    return array(":?:" => "疑问", ":razz:" => "调皮", ":sad:" => "难过", ":evil:" => "抠鼻", ":naughty:" => "顽皮",
        ":!:" => "吓", ":smile:" => "微笑", ":oops:" => "憨笑", ":neutral:" => "亲亲", ":cry:" => "大哭", ":mrgreen:" => "呲牙",
        ":grin:" => "坏笑", ":eek:" => "惊讶", ":shock:" => "发呆", ":???:" => "撇嘴", ":cool:" => "酷", ":lol:" => "偷笑",
        ":mad:" => "咒骂", ":twisted:" => "发怒", ":roll:" => "白眼", ":wink:" => "鼓掌", ":idea:" => "想法", ":despise:" => "蔑视",
        ":celebrate:" => "庆祝", ":watermelon:" => "西瓜", ":xmas:" => "圣诞", ":warn:" => "警告", ":rainbow:" => "彩虹",
        ":loveyou:" => "爱你", ":love:" => "爱", ":beer:" => "啤酒",
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

function get_post_images($post_id = null)
{
    global $post;
    // 如果有封面图取封面图
    if (has_post_thumbnail()) {
        $res = get_the_post_thumbnail_url($post, 'large');
        if ($res != null) {
            return $res;
        }
    }
    if ($post_id == null && $post) {
        $content = $post->post_content;
    } else {
        $content = get_post($post_id)->post_content;
    }
    preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches);
    if ($matches && $matches[1]) {
        $res = $matches[1][0];
    } else {
        $res = get_stylesheet_directory_uri() . '/assets/img/random/' . mt_rand(1, 8) . '.jpg';
    }
    return $res;
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
    global $cat, $other_page_title;
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
        $out .= '<li class="breadcrumb-item active " aria-current="page">' . ($_GET['s']) . '</li>';
        $out .= '<li class="breadcrumb-item active " aria-current="page">' . __('搜索结果', PUOCK) . '</li>';
    } else if (is_author()) {
        $out .= '<li class="breadcrumb-item active " aria-current="page">' . get_the_author_meta('nickname') . '' . __('的文章列表', PUOCK) . '</li>';
    } else if (is_date()) {
        $out .= '<li class="breadcrumb-item active " aria-current="page">' . get_the_date() . '</li>';
    } else if (is_page()) {
        global $post;
        $out .= '<li class="breadcrumb-item active " aria-current="page">' . ($post->post_title) . '</li>';
    } else if (is_tag()) {
        $tag_name = single_tag_title('', false);
        $out .= '<li class="breadcrumb-item active " aria-current="page">' . __('标签', PUOCK) . '</li>';
        $out .= '<li class="breadcrumb-item active " aria-current="page">' . ($tag_name) . '</li>';
    }  else if (isset($other_page_title)) {
        $out .= '<li class="breadcrumb-item active " aria-current="page">' . $other_page_title . '</li>';
    } else if (is_404()) {
        $out .= '<li class="breadcrumb-item active " aria-current="page">' . __('你访问的资源不存在', PUOCK) . '</li>';
    }
    $out .= '</div></nav></ol>';
    return $out;
}

//获取阅读数量
function pk_get_post_views()
{
    if (function_exists('the_views')) {
        echo the_views();
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
        foreach ($images[1] as $index => $value) {
            $new_img = str_replace('<img', '<img title="'. $post->post_title . '"
             alt="' . $post->post_title .'"', $images[0][$index]);
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
    include('update-checker/update-checker.php');
    switch ($update_server) {
        case 'github':
            {
                $pkUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
                    'https://github.com/Licoy/wordpress-theme-puock',
                    __FILE__,
                    'unique-plugin-or-theme-slug',
                    $check_period
                );
            }
            break;
        case 'cnpmjs':
            {
                $pkUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
                    'https://licoy.cn/go/puock-update.php?r=cnpmjs',
                    __FILE__,
                    'unique-plugin-or-theme-slug',
                    $check_period
                );
            }
            break;
        case 'fastgit':
            {
                $pkUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
                    'https://licoy.cn/go/puock-update.php?r=fastgit',
                    __FILE__,
                    'unique-plugin-or-theme-slug',
                    $check_period
                );
            }
            break;
        default:
        {
            $pkUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
                'https://licoy.cn/go/puock-update.php?r=worker',
                __FILE__,
                $current_theme_dir_name,
                $check_period
            );
        }
    }
}

if (is_admin()) {
    // 在线更新支持
    pk_update();
}
