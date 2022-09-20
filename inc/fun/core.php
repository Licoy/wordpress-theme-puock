<?php

add_action('after_setup_theme', 'puock_theme_setup');
function puock_theme_setup()
{
    load_theme_textdomain(PUOCK, PUOCK_ABS_DIR . '/languages');
}

if (is_dir(PUOCK_ABS_DIR . '/inc/puock')) {
    if (file_exists(PUOCK_ABS_DIR . '/inc/puock/fun.php')) {
        require_once PUOCK_ABS_DIR . '/inc/puock/fun.php';
    }
}

function pk_ajax_resp($data = null, $msg = 'success', $code = 0)
{
    header('Content-Type: application/json; charset=utf-8');
    return json_encode(array('data' => $data, 'msg' => $msg, 'code' => $code));
}

function pk_ajax_resp_error($msg = 'fail', $data = null)
{
    return pk_ajax_resp($data, $msg, -1);
}

require_once PUOCK_ABS_DIR . '/inc/fun/cache.php';
require_once PUOCK_ABS_DIR . '/inc/setting/index.php';
require_once PUOCK_ABS_DIR . '/inc/ext/init.php';
require_once PUOCK_ABS_DIR . '/inc/fun/ajax.php';
require_once PUOCK_ABS_DIR . '/inc/fun/oauth.php';
require_once PUOCK_ABS_DIR . '/inc/fun/comment-ajax.php';
require_once PUOCK_ABS_DIR . '/inc/fun/widget.php';
require_once PUOCK_ABS_DIR . '/inc/init.php';
require_once PUOCK_ABS_DIR . '/inc/category-seo.php';
require_once PUOCK_ABS_DIR . '/inc/fun/comment.php';
require_once PUOCK_ABS_DIR . '/inc/fun/short-code.php';
require_once PUOCK_ABS_DIR . '/inc/fun/opt.php';
require_once PUOCK_ABS_DIR . '/inc/fun/post-meta.php';
require_once PUOCK_ABS_DIR . '/inc/fun/sidebar.php';
require_once PUOCK_ABS_DIR . '/inc/fun/post-tags.php';
require_once PUOCK_ABS_DIR . '/inc/fun/comment-notify.php';
require_once PUOCK_ABS_DIR . '/inc/user-agent-parse.php';
require_once PUOCK_ABS_DIR . '/inc/phpqrcode.php';
require_once PUOCK_ABS_DIR . '/inc/php-captcha.php';
if (pk_is_checked('no_category')) {
    require_once PUOCK_ABS_DIR . '/inc/no-category.php';
}

/*Auth-Domains*/

//钩子添加集合
if (pk_is_checked('html_page_permalink')) {
    add_action('init', 'html_page_permalink', -1);
}
add_filter('user_trailingslashit', 'add_init_trailingslashit', 10, 2);


function pk_open_session()
{
    session_start();
}

function pk_wclose_session()
{
    session_write_close();
}

function pk_session_call($function)
{
    pk_open_session();
    try {
        $function();
    } finally {
        session_write_close();
    }
}


// 顶部添加自定义菜单
function pk_toolbar_link($bar)
{
    $bar->add_node(array(
        'id' => 'theme-setting',
        'title' => '🎨 主题设置',
        'href' => admin_url() . 'admin.php?page=puock-options'
    ));
    $bar->add_node(array(
        'id' => 'theme-docs',
        'title' => '📚 主题使用文档',
        'href' => 'https://licoy.cn/puock-doc.html',
        'meta' => array(
            'target' => 'blank'
        )
    ));
    $bar->add_node(array(
        'id' => 'theme-qq-qun',
        'title' => '🐧 主题交流QQ群',
        'href' => 'https://licoy.cn/go/puock-update.php?r=qq_qun',
        'meta' => array(
            'target' => 'blank'
        )
    ));
}

if (is_user_logged_in() && current_user_can('manage_options')) {
    add_action('admin_bar_menu', 'pk_toolbar_link', 999);
}

function pk_admin_scripts()
{
    wp_enqueue_script('puock-admin', get_stylesheet_directory_uri() . '/assets/dist/admin.min.js',
        array(), PUOCK_CUR_VER_STR, true);
}

add_action('admin_enqueue_scripts', 'pk_admin_scripts');
function pk_admin_print_scripts()
{
    $settings = json_encode(array(
        'compatible' => [
            'githubermd' => defined('GITHUBER_PLUGIN_NAME')
        ]
    ));
    echo "<script type='text/javascript'>var puock_admin_setting = $settings</script>";
}

add_action('admin_print_footer_scripts', 'pk_admin_print_scripts', 1);


function pk_is_pjax()
{
    return pk_is_checked('page_ajax_load', false);
}

//判断阅读数量是否需要增加并进行操作
function the_views_add($post_ID, $count, $key, $ajax = false)
{
    if (is_single() || is_page() || $ajax) {
        if ($count == '') {
            add_post_meta($post_ID, $key, '0');
        } else {
            update_post_meta($post_ID, $key, $count + 1);
            $count++;
        }
    }
    return $count;
}

//获取当前的阅读数量与自增
if (!function_exists('the_views')) {
    function the_views($post_id = null, $echo = true, $ajax = false)
    {
        global $post;
        if ($post_id == null) {
            $post_id = $post->ID;
        }
        $key = 'views';
        $count = get_post_meta($post_id, $key, true);
        if ($count == '') {
            $count = 0;
        }
        $count = the_views_add($post_id, $count, $key, $ajax);
        $count_view = number_format_i18n($count);
        if (!$echo) {
            return $count_view;
        }
        echo $count_view;
    }
}
//异步请求浏览量
function async_pk_views()
{
    $postId = $_POST['id'];
    if (empty($postId)) {
        echo pk_ajax_resp(0);
        return;
    }
    echo pk_ajax_resp(the_views($postId, false, true));
    wp_die();
}

add_action('wp_ajax_nopriv_async_pk_views', 'async_pk_views');
add_action('wp_ajax_async_pk_views', 'async_pk_views');

//设置文章发布的时候进行字段添加
function set_views($post_ID)
{
    $key = 'views';
    $count = get_post_meta($post_ID, $key, true);
    if ($count == '') {
        add_post_meta($post_ID, $key, '0');
    }
}

add_action('publish_post', 'set_views');

function get_total_views()
{
    global $wpdb;
    $views = pk_cache_get(PKC_TOTAL_VIEWS);
    if (!$views) {
        $views = $wpdb->get_var("SELECT SUM(meta_value) FROM $wpdb->postmeta where meta_key='views'");
        pk_cache_set(PKC_TOTAL_VIEWS, $views);
    }
    return $views;
}


/**
 * 获取查看最多的文章
 * @param $days N天内
 * @param $nums 数量
 * @return array|object|null
 */
function get_views_most_post($days, $nums)
{
    global $wpdb;
    $sql = "select posts.*, meta.meta_value as views
            from {$wpdb->posts} as posts INNER JOIN (select post_id,(meta_value+0) as meta_value from 
            {$wpdb->postmeta} where meta_key='views' order by (meta_value+0) DESC) as meta
            on meta.post_id = posts.ID
            where posts.post_type = 'post'
            AND posts.post_status = 'publish' AND TO_DAYS(now()) - TO_DAYS(posts.post_date) < {$days}
            ORDER BY meta.meta_value DESC limit 0, {$nums}";
    return $wpdb->get_results($sql);
}

//是否隐藏侧边栏
function pk_hide_sidebar($post_id = null)
{
    global $post;
    if (pk_is_checked("hide_global_sidebar")) {
        return true;
    }
    if (is_single() || is_page()) {
        if ($post_id == null) {
            $post_id = $post->ID;
        }
        return get_post_meta($post_id, 'hide_side', true) == "true";
    }
    return false;
}

//隐藏/显示侧边栏的输出字符
function pk_hide_sidebar_out($hide = '', $show = '', $post_id = null, $echo = true)
{
    $out = $show;
    if (pk_hide_sidebar()) {
        $out = $hide;
    }
    if (!$echo) {
        return $out;
    }
    echo $out;
}

//获取链接对象，用于书籍推荐及其他页面使用
function pk_get_wp_links($link_cats = '')
{
    global $wpdb;
    if (empty($link_cats)) {
        return null;
    }
    $sql = "select links.*,terms.term_id,terms.name  from {$wpdb->links} as links
            LEFT JOIN (select * from {$wpdb->term_relationships} where term_taxonomy_id in ({$link_cats})) as relat on links.link_id = relat.object_id
            LEFT JOIN (selecT * from {$wpdb->terms} where term_id in ({$link_cats})) as terms on terms.term_id = relat.term_taxonomy_id
             where links.link_id in (relat.object_id) and links.link_visible='Y'";
    return $wpdb->get_results($sql);
}

//获取懒加载图片信息
function pk_get_lazy_pl_img()
{
    return pk_get_static_url() . "/assets/img/z/load-tip.png";
}

function pk_get_lazy_img_info($origin, $class = '', $width = null, $height = null, $thumbnail = true)
{
    if (!pk_is_checked('basic_img_lazy_s')) {
        if ($thumbnail) {
            $out = "src='" . pk_get_img_thumbnail_src($origin, $width, $height) . "' ";
            $out .= "class='" . $class . "' ";
        } else {
            $out = "src='{$origin}' ";
            $out .= "class='{$class}' ";
        }
    } else {
        if ($thumbnail) {
            $out = "src='" . pk_get_img_thumbnail_src(pk_get_lazy_pl_img(), $width, $height) . "' ";
            $out .= "class='lazyload " . $class . "' ";
            $out .= "data-src='" . pk_get_img_thumbnail_src($origin, $width, $height) . "'";
        } else {
            $out = "src='" . pk_get_lazy_pl_img() . "' ";
            $out .= "class='lazyload " . $class . "' ";
            $out .= "data-src='" . $origin . "'";
        }
    }
    return $out;
}

function pk_content_img_lazy($content)
{
    return preg_replace('/<img(.+)src=[\'"]([^\'"]+)[\'"](.*)>/i', "<img\$1data-src=\"\$2\" data-lazy=\"true\" src=\"" . pk_get_lazy_pl_img() . "\"\$3/>", $content);
}

if (pk_is_checked('basic_img_lazy_z')) {
    add_filter('the_content', 'pk_content_img_lazy');
}
//获取图片缩略图链接
function pk_get_img_thumbnail_src($src, $width, $height)
{
    if ($width == null || $height == null) {
        return $src;
    }
    return PUOCK_ABS_URI . "/timthumb.php?w={$width}&h={$height}&a=&zc=1&src=" . $src;
}

//获取文章样式是否是卡片式
function pk_post_style_list()
{
    return pk_get_option('post_style', 'list') == 'list';
}

//评论添加@功能
if (pk_is_checked('comment_has_at')) {
    add_filter('comment_text', 'pk_comment_add_at', 20, 2);
}
//GrAvatar头像源切换
if (pk_get_option('gravatar_url', 'wp') != 'wp') {
    $type = pk_get_option('gravatar_url', 'wp');
    if ($type == 'cravatar') {
        add_filter('get_avatar', 'cr_avatar');
        add_filter('get_avatar_url', 'cr_avatar');
    }
    if ($type == 'cn') {
        add_filter('get_avatar', 'cn_avatar');
        add_filter('get_avatar_url', 'cn_avatar');
    }
    if ($type == 'cn_ssl') {
        add_filter('get_avatar', 'cn_ssl_avatar');
        add_filter('get_avatar_url', 'cn_ssl_avatar');
    }
    if ($type == 'loli_ssl') {
        add_filter('get_avatar', 'loli_ssl_avatar');
        add_filter('get_avatar_url', 'loli_ssl_avatar');
    }
    if ($type == 'v2ex') {
        add_filter('get_avatar', 'v2ex_ssl_avatar');
        add_filter('get_avatar_url', 'v2ex_ssl_avatar');
    }
}
//评论者链接
function pk_comment_author_url($comment_ID = 0)
{
    $url = get_comment_author_url($comment_ID);
    $author = get_comment_author($comment_ID);
    echo empty($url) ? $author : "<a target='_blank' href='" . pk_go_link($url) . "' rel='external nofollow' class='url'>$author</a>";
}

//评论回复通知
if (pk_is_checked('comment_mail_notify')) {
    add_action('comment_unapproved_to_approved', 'comment_mail_notify');
    add_action('comment_post', 'comment_mail_notify');
}
//覆盖邮件配置
if (pk_is_checked('smtp_open')) {
    function mail_smtp_set($phpmailer)
    {
        $phpmailer->From = pk_get_option('smtp_form', '');
        $phpmailer->FromName = pk_get_option('smtp_form_n', '');
        $phpmailer->Host = pk_get_option('smtp_host', '');
        $phpmailer->Port = pk_get_option('smtp_port', '');
        $phpmailer->SMTPSecure = pk_is_checked('smtp_ssl') ? 'ssl' : '';
        $phpmailer->Username = pk_get_option('smtp_u', '');
        $phpmailer->Password = pk_get_option('smtp_p', '');
        $phpmailer->IsSMTP();
        $phpmailer->SMTPAuth = true;
    }

    add_action('phpmailer_init', 'mail_smtp_set');
}
//检测是否默认的第三方生成邮箱
function pk_email_change_email($email_change_email, $user = null, $userdata = null)
{
    if (pk_check_email_is_sysgen($email_change_email['to'])) {
        return null;
    }
    return $email_change_email;
}

add_filter('email_change_email', 'pk_email_change_email');
//检测邮箱是否系统生成
function pk_check_email_is_sysgen($email)
{
    return preg_match("/^_p_[\w].+@null.null/", $email);
}

//后台登录保护
function login_protection()
{
    if (!is_user_logged_in()) {
        if ($_GET[pk_get_option('lp_user', 'admin')] != pk_get_option('lp_pass', 'admin')) {
            header("Location: " . home_url());
        }
    }
}

if (pk_is_checked('login_protection')) {
    add_action('login_enqueue_scripts', 'login_protection');
}
if (pk_is_checked('compress_html')) {
    add_action('get_header', 'wp_compress_html');
}
//百度主动推送
function pk_baidu_submit($post_ID)
{
    if (get_post_meta($post_ID, 'baidu_submit_url_status', true) == 1) return;
    $post_url = get_permalink($post_ID);
    $api_url = pk_get_option('baidu_submit_url');
    $resp = wp_remote_post($api_url, array('body' => $post_url, 'headers' => 'Content-Type: text/plain'));
    $res = json_decode($resp['body'], true);
    if (isset($res['success'])) {
        add_post_meta($post_ID, 'baidu_submit_url_status', 1, true);
    }
}

if (pk_is_checked('open_baidu_submit')) {
    add_action('publish_post', 'pk_baidu_submit', 0);
}
//对pre里面的内容进行转义
function pk_tag_pre_encode($content)
{
    preg_match_all("/<pre.*?>(.+?)<\/pre>/is", $content, $matches);
    if (isset($matches[1])) {
        foreach ($matches[1] as $match) {
            $m = trim($match);
            if (!(substr($m, 0, strlen("<code")) === "<code")) {
                $m = "<code class='language-default'>$m</code>";
            }
            if (substr($m, 0, strlen("<code>")) === "<code>") {
                $m = "<code class='language-default'>" . substr($m, strlen("<code>"));
            }
            $content = str_replace($match, $m, $content);
        }
    }
    return $content;
}

add_filter('the_content', 'pk_tag_pre_encode');
add_filter('comment_text', 'pk_tag_pre_encode');
function pk_htmlspecialchars($content)
{
    $content = str_replace("<", "&lt;", $content);
    $content = str_replace(">", "&gt;", $content);
    return $content;
}


function create_taxs($tax_slug, $hook_type, $tax_name)
{
    //自定义分类法标签
    $labels_tax = array(
        'name' => $tax_name,
        'singular_name' => $tax_name,
        'search_items' => '搜索' . $tax_name,
        'all_items' => '所有' . $tax_name,
        'parent_item' => '父级' . $tax_name,
        'parent_item_colon' => '父级' . $tax_name,
        'edit_item' => '编辑' . $tax_name,
        'update_item' => '更新' . $tax_name,
        'add_new_item' => '添加新' . $tax_name,
        'new_item_name' => '新' . $tax_name . '名称',
        'menu_name' => $tax_name,
    );

    //自定义分类法参数
    $args_tax = array(
        'hierarchical' => true,
        'labels' => $labels_tax,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => $tax_slug),
    );

    register_taxonomy($tax_slug, array($hook_type), $args_tax);
}


//注册菜单
register_nav_menus(array(
    'primary' => '主要菜单',
));

//获取主题配置
function pk_get_option($name, $default = false)
{
    $config = get_option(PUOCK_OPT);
    if ($config && isset($config[$name])) {
        if (!empty($config[$name])) {
            return $config[$name];
        }
    }
    return $default;
}

//主题模式
function pk_theme_light()
{
    if (isset($_COOKIE['mode'])) {
        return $_COOKIE['mode'] == 'light';
    }
    return pk_get_option('theme_mode', 'light') == 'light';
}

//配置是否选择
function pk_is_checked($name, $default = 0)
{
    return pk_get_option($name, $default) == 1;
}

//配置选择输出
function pk_checked_out($name, $out = '', $default = 0)
{
    if (pk_is_checked($name, $default)) {
        echo $out;
    }
}

//动画载入
function pk_open_box_animated($class, $echo = true)
{
    $open = pk_is_checked("page_animate") == 1;
    if ($open) {
        if (!$echo) {
            return $class;
        }
        echo $class;
    }
}


//获取所有站点分类id
function get_all_category_id($type = null)
{
    global $wpdb;
    $sql = "SELECT term_id, name FROM $wpdb->terms";
    if ($type != null) {
        $sql .= " where term_id in (select term_id from $wpdb->term_taxonomy where taxonomy='{$type}')";
    }
    $cats = $wpdb->get_results($sql);
    $out = '<span style="line-height: 1.5rem">';
    foreach ($cats as $cat) {
        $out .= '<span>[' . $cat->name . "：<code>" . $cat->term_id . '</code></span>]、';
    }
    if (count($cats) > 0) {
        $out = mb_substr($out, 0, mb_strlen($out) - 1);
    }
    $out .= '</span>';
    return $out;
}

//获取所有站点分类id
function get_all_category_id_row($type = null)
{
    global $wpdb;
    $sql = "SELECT term_id, name FROM $wpdb->terms";
    if ($type != null) {
        $sql .= " where term_id in (select term_id from $wpdb->term_taxonomy where taxonomy='{$type}')";
    }
    $cats = $wpdb->get_results($sql);
    $result = [];
    foreach ($cats as $cat) {
        $result[] = ['label' => $cat->name, 'value' => $cat->term_id];
    }
    return $result;
}


//获取菜单数据
function pk_get_main_menu($mobile = false)
{
    $menus = get_nav_menu_object('primary');
    $out = $mobile ? "<ul class='puock-links t-md'>" : "<ul>";
    if ($menus && count($menus) > 0) {
        pk_get_menu_obj_to_html($menus, $out, $mobile);
    }
    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        $avatar = get_avatar_url($user->user_email);
        $out .= '<li><a data-no-instant data-toggle="tooltip" title="用户中心" href="' . get_edit_profile_url() . '"><img alt="用户中心" src="' . $avatar . '" class="min-avatar"></a></li>';
    } else {
        if (pk_is_checked('show_login_url')) {
            $out .= '<li><a data-no-instant data-toggle="tooltip" title="登入" href="' . wp_login_url() . '"><img alt="登入" src="' . get_avatar_url("no-login") . '" class="min-avatar"></a></li>';
        }
    }
    if (!$mobile) {
        if (pk_is_checked('theme_mode_s')) {
            $out .= '<li><a class="colorMode" data-toggle="tooltip" title="模式切换" href="javascript:void(0)"><i class="czs-moon-l"></i></a></li>';
        }
        $out .= '<li><a class="search-modal-btn" data-toggle="tooltip" title="搜索" href="javascript:void(0)"><i class="czs-search-l"></i></a></li>';
    }
    $out .= '</ul>';
    return $out;
}

//获取菜单对象数据
function get_nav_menu_object($location)
{
    $locations = get_nav_menu_locations();
    if (!$locations) {
        return null;
    }
    $menu_id = $locations[$location];
    $menu_object = wp_get_nav_menu_object($menu_id);
    $menu_items = wp_get_nav_menu_items($menu_object->term_id);
    $menus = array();
    if ($menu_items == null || count($menu_items) == 0) {
        return $menus;
    } else {
        foreach ($menu_items as &$menu_item) {
            if (!isset($menu_item->classes) || $menu_item->classes == null) {
                $menu_item->classes = array();
            }
        }
    }
    _wp_menu_item_classes_by_context($menu_items);
    $submenus = array();
    foreach ($menu_items as $m) {
        $m->children = array();
        if (!$m->menu_item_parent) {
            $menus[$m->ID] = $m;
        } else {
            $submenus[$m->ID] = $m;
            if (isset($menus[$m->menu_item_parent])) {
                $menus[$m->menu_item_parent]->children[$m->ID] = &$submenus[$m->ID];
            } else {
                $submenus[$m->menu_item_parent]->children[$m->ID] = $submenus[$m->ID];
            }
        }
    }
    return $menus;
}

//将匹配的到的菜单数据转换为html
function pk_get_menu_obj_to_html($menus, &$out, $mobile = false, $dpath_cur = 1, $max_dpath = 2)
{
    $child_class = $dpath_cur != 1 ? 'menu-item-child' : '';
    $target = pk_link_target(false);
    foreach ($menus as $menu) {
        $classes = join(" ", $menu->classes);
        $cur = $menu->current ? 'menu-current' : '';
        $out .= "<li id='menu-item-{$menu->ID}' class='menu-item-{$menu->ID} {$classes} {$child_class} {$cur}'>";
        if (!$mobile) {
            $out .= "<a {$target} href='{$menu->url}'>{$menu->title}";
        } else {
            $out .= '<span><a ' . $target . ' href="' . $menu->url . '">' . $menu->title . '</a>';
        }
        if (count($menu->children) > 0) {
            if ($mobile) {
                $out .= '<a href="#menu-sub-' . $menu->ID . '" data-toggle="collapse"><i class="czs-angle-down-l t-sm ml-1"></i></a>';
            } else {
                $out .= '<i class="czs-angle-down-l t-sm ml-1"></i>';
            }
        }
        if ($mobile) {
            $out .= '</span>';
        } else {
            $out .= '</a>';
        }
        if (count($menu->children) > 0 && $dpath_cur < $max_dpath) {
            $out .= '<ul ' . ($mobile ? 'id="menu-sub-' . $menu->ID . '"' : '') . ' class="sub-menu ' . ($mobile
                    ? 'collapse' : '') . '">';
            pk_get_menu_obj_to_html($menu->children, $out, $mobile, $dpath_cur + 1, $max_dpath);
            $out .= '</ul>';
        }
    }
}

//获取分类的子集菜单
function get_category_child($parentId)
{
    $child = get_categories("parent={$parentId}&hide_empty=0");
    $list = array();
    foreach ($child as $child_item) {
        $list[] = array(
            'url' => get_category_link($child_item),
            'item' => $child_item
        );
    }
    return $list;
}

//主查询设置
function pk_pre_post_set($query)
{
    if ($query->is_home() && $query->is_main_query()) {
        if (pk_get_option('index_mode', '') == 'cms') {
            $query->set('posts_per_page', pk_get_option('cms_show_new_num', 5));
        }
    }
}

add_action('pre_get_posts', 'pk_pre_post_set');

//静态资源加载源的链接
function pk_get_static_url()
{
    $type = pk_get_option('static_load_origin', 'self');
    switch ($type) {
        case "jsdelivr":
            $url_pre = "https://cdn.jsdelivr.net/gh/Licoy/wordpress-theme-puock@v" . PUOCK_CUR_VER_STR;
            break;
        case "jsdelivr-fastly":
            $url_pre = "https://fastly.jsdelivr.net/gh/Licoy/wordpress-theme-puock@v" . PUOCK_CUR_VER_STR;
            break;
        case "jsdelivr-testingcf":
            $url_pre = "https://testingcf.jsdelivr.net/gh/Licoy/wordpress-theme-puock@v" . PUOCK_CUR_VER_STR;
            break;
        case "jsdelivr-gcore":
            $url_pre = "https://gcore.jsdelivr.net/gh/Licoy/wordpress-theme-puock@v" . PUOCK_CUR_VER_STR;
            break;
        case 'custom':
            $url_pre = pk_get_option('custom_static_load_origin', '');
            break;
        default:
            $url_pre = PUOCK_ABS_URI;
    }
    return $url_pre;
}

//是否打开讨论-显示头像
function pk_open_show_comment_avatar()
{
    return get_option('show_avatars') == "1";
}

//关闭区块小工具
function pk_off_widgets_block()
{
    add_filter('gutenberg_use_widgets_block_editor', '__return_false');
    add_filter('use_widgets_block_editor', '__return_false');
}

//获取中文格式化的实例
function pk_chinese_format($content)
{
    include_once dirname(__FILE__) . '/../lib/ChineseTypesetting.php';
    $typesetting = new ChineseTypesetting();
    $content = $typesetting->insertSpace($content);
    $content = $typesetting->removeSpace($content);
    $content = $typesetting->full2Half($content);
    return $typesetting->fixPunctuation($content);
}

if (pk_is_checked('chinese_format')) {
    add_filter('the_content', 'pk_chinese_format', 199);
}

//获取缩略图的白名单
function pk_get_thumbnail_allow_sites()
{
    $sites = [];
    $thumbnail_allows = trim(pk_get_option("thumbnail_allows", ''));
    if (!empty($thumbnail_allows)) {
        foreach (explode("\n", $thumbnail_allows) as $site) {
            $site = trim($site);
            if (!empty($site)) {
                $sites[] = $site;
            }
        }
    }
    return $sites;
}

//生成缩略图白名单文件名称
function pk_get_thumbnail_allow_sites_filepath()
{
    return PUOCK_ABS_DIR . '/.tas.php';
}

//生成缩略图白名单文件
function pk_generate_thumbnail_allow_sites_file()
{
    $sites = pk_get_thumbnail_allow_sites();
    $template = "<?php \$ALLOWED_SITES = [\n";
    if (count($sites) > 0) {
        foreach ($sites as $site) {
            $template .= "\t\"$site\",\n";
        }
    }
    $template .= "];";
    return file_put_contents(pk_get_thumbnail_allow_sites_filepath(), $template);
}

add_action('options-framework-saved', 'pk_generate_thumbnail_allow_sites_file');
