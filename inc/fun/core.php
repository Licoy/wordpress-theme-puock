<?php


define('PUOCK_CUR_VER', (float)wp_get_theme()->get('Version'));
define('PUOCK', 'puock');
define('PUOCK_OPT', 'puock_options');

$puock = 'Puock';

add_action('after_setup_theme', 'puock_theme_setup');
function puock_theme_setup()
{
    load_theme_textdomain(PUOCK, get_template_directory() . '/languages');
}

if (is_dir(get_template_directory() . '/inc/puock')) {
    if (file_exists(get_template_directory() . '/inc/puock/fun.php')) {
        require get_template_directory() . '/inc/puock/fun.php';
    }
}


function pk_ajax_resp($data = null, $msg = 'success', $code = 0)
{
    return json_encode(array('data' => $data, 'msg' => $msg, 'code' => $code));
}

function pk_ajax_resp_error($msg = 'fail', $data = null)
{
    return pk_ajax_resp($data, $msg, -1);
}

require get_template_directory() . '/inc/setting/options-framework.php';
require get_template_directory() . '/inc/fun/comment-ajax.php';
require get_template_directory() . '/inc/fun/widget.php';
require get_template_directory() . '/inc/init.php';
require get_template_directory() . '/inc/category-seo.php';
require get_template_directory() . '/inc/fun/comment.php';
require get_template_directory() . '/inc/fun/short-code.php';
require get_template_directory() . '/inc/fun/opt.php';
require get_template_directory() . '/inc/fun/post-meta.php';
require get_template_directory() . '/inc/fun/sidebar.php';
require get_template_directory() . '/inc/fun/post-tags.php';
require get_template_directory() . '/inc/fun/comment-notify.php';
if (pk_is_checked('no_category')) {
    require get_template_directory() . '/inc/no-category.php';
}

/*Auth-Domains*/

//钩子添加集合
if (pk_is_checked('html_page_permalink')) {
    add_action('init', 'html_page_permalink', -1);
}
add_filter('user_trailingslashit', 'add_init_trailingslashit', 10, 2);

//添加session支持
function register_session()
{
    if (!session_id())
        session_start();
}

add_action('init', 'register_session');

// 顶部添加自定义菜单
function pk_toolbar_link($bar)
{
    $bar->add_node(array(
        'id' => 'theme-setting',
        'title' => '主题设置',
        'href' => admin_url() . 'themes.php?page=options-framework'
    ));
}

add_action('admin_bar_menu', 'pk_toolbar_link', 999);

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
        $count = number_format_i18n($count);
        if (!$echo) {
            return $count;
        }
        echo $count;
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
    return $wpdb->get_var("SELECT SUM(meta_value) FROM $wpdb->postmeta where meta_key='views'");
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
    return get_template_directory_uri() . "/timthumb.php?w={$width}&h={$height}&a=&zc=1&src=" . $src;
}

//获取文章样式是否是卡片式
function pk_post_style_list()
{
    return pk_get_option('post_style', 'list') == 'list';
}

function extra_user_profile_qq_oauth($user)
{
    $qq_oauth = get_the_author_meta('qq_oauth', $user->ID);
    $oauth = pk_get_oauth_info();
    $is_conn = true;
    $href = null;
    if (empty($oauth['qq_oauth_id']) || empty($oauth['qq_oauth_key'])) {
        $is_conn = false;
    } else {
        $href = $oauth['oauth_url'];
    }
    ?>
    <h3>第三方账号绑定</h3>
    <table class="form-table">
        <tr>
            <th><label for="qq_oauth">QQ互联</label></th>
            <td>
                <?php if (empty($qq_oauth)): ?>
                    <a href="<?php echo $href ?>" target="_blank" id="qq_oauth" <?php echo $is_conn ? '' : 'disabled' ?>
                       class="button">立即去绑定</a>
                <?php else: ?>
                    <a id="qq_oauth" class="button" disabled>已绑定QQ</a>
                <?php endif; ?>
            </td>
        </tr>
    </table>
<?php }

//添加qq授权之后的回调请求
function oauth_qq_redirect_ajax()
{
    $oauth = pk_get_oauth_info('qq', '', false);
    $qq_oauth_id = $oauth['qq_oauth_id'];
    $qq_oauth_key = $oauth['qq_oauth_key'];
    $redirect = $oauth['oauth_redirect'];
    if (empty($qq_oauth_id) || empty($qq_oauth_key)) {
        oauth_qq_redirect_page(false, '站点未填写QQ互联授权信息');
        return;
    }
    $state = $_GET['state'];
    $from_redirect = $_GET['redirect'];
    $state_session = $_SESSION['qq_oauth_state'];
    $code = $_GET['code'];
    if ($state !== $state_session || empty($code)) {
        oauth_qq_redirect_page(false, '非法State - 授权请求');
        return;
    }
    $access_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&client_id={$qq_oauth_id}&client_secret={$qq_oauth_key}&code={$code}&redirect_uri={$redirect}";
    $access_token_res = wp_remote_get($access_url);
    $body = $access_token_res['body'];
    $querys = get_path_query($body);
    if (!isset($querys['access_token'])) {
        oauth_qq_redirect_page(false, '获取access_token失败');
        return;
    }
    $info_url = "https://graph.qq.com/oauth2.0/me?access_token=" . $querys['access_token'];
    $info_res = wp_remote_get($info_url);
    $info_body = str_replace('callback( ', '', $info_res['body']);
    $info_body = str_replace(' );', '', $info_body);
    if ($info = json_decode($info_body, true)) {
        $openid = $info['openid'];
        $user_info_url = "https://graph.qq.com/user/get_user_info?access_token={$querys['access_token']}&openid={$openid}&oauth_consumer_key={$qq_oauth_id}&format=json";
        $user_info_res = wp_remote_get($user_info_url);
        if ($user_info = json_decode($user_info_res['body'], true)) {
            if (is_user_logged_in()) {
                $user = wp_get_current_user();
                update_user_meta($user->ID, "qq_oauth", $openid);
                oauth_qq_redirect_page(true, '', $from_redirect);
            } else {
                $users = get_users(array('meta_key' => 'qq_oauth', 'meta_value' => $openid));
                if (!$users || count($users) <= 0) {
                    //不存在用户，先自动注册再登录
                    $wp_create_nonce = wp_create_nonce($openid);
                    $username = 'qq' . $wp_create_nonce;
                    $password = wp_generate_password($length = 10);
                    $user_data = array(
                        'user_login' => 'qq' . wp_create_nonce($openid),
                        'display_name' => $user_info['nickname'],
                        'user_pass' => $password,
                        'nickname' => $user_info['nickname'],
                        'user_email' => '_p_' . $username . '@null.null'
                    );
                    $user_id = wp_insert_user($user_data);
                    wp_signon(array("user_login" => $username, "user_password" => $password), true);
                    update_user_meta($user_id, "qq_oauth", $openid);
                    oauth_qq_redirect_page(true, '', $from_redirect);
                } else {
                    //存在，直接登录
                    wp_set_auth_cookie($users[0]->ID);
                    oauth_qq_redirect_page(true, '', $from_redirect);
                }
            }
        } else {
            oauth_qq_redirect_page(false, '获取用户信息失败');
            return;
        }
    } else {
        oauth_qq_redirect_page(false, '获取OPENID失败');
        return;
    }

}

//授权返回页面回调
function oauth_qq_redirect_page($success = true, $info = '', $from_redirect = '')
{
    if ($success) {
        if (empty($from_redirect)) {
            echo "<html><script>window.location=\"" . get_admin_url() . "\"</script></html>";
        } else {
            echo "<html><script>window.location=\"" . $from_redirect . "\"</script></html>";
        }
    } else {
        $_SESSION['error_info'] = $info;
        echo "<html><script>window.location=\"" . get_template_directory_uri() . "/error.php\"</script></html>";
    }
}

function pk_get_oauth_info($type = 'qq', $redirect = '', $gen_state = true)
{
    $qq_open = pk_is_checked('oauth_qq');
    $qq_oauth_id = pk_get_option('oauth_qq_id');
    $qq_oauth_key = pk_get_option('oauth_qq_key');
    $redirect = urlencode(admin_url() . 'admin-ajax.php?action=oauth_qq_redirect_ajax&redirect=' . $redirect);
    if ($gen_state) {
        $_SESSION['qq_oauth_state'] = md5(time() . mt_rand(0, 9) . mt_rand(0, 9) . mt_rand(0, 9));
    }
    $auth_url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id={$qq_oauth_id}&redirect_uri={$redirect}&state=" . $_SESSION['qq_oauth_state'];
    return array(
        'qq_open' => $qq_open,
        'qq_oauth_id' => $qq_oauth_id,
        'qq_oauth_key' => $qq_oauth_key,
        'oauth_redirect' => $redirect,
        'oauth_state' => $_SESSION['qq_oauth_state'],
        'oauth_url' => $auth_url,
    );
}

function pk_oauth_url($type = 'qq', $redirect = '')
{
    if ($type == 'qq') {
        $oauth = pk_get_oauth_info('qq', $redirect);
        if (!$oauth['qq_open'] || empty($oauth['qq_oauth_id']) || empty($oauth['qq_oauth_key'])) {
            return '#';
        }
        return $oauth['oauth_url'];
    }
}

function pk_oauth_url_page_ajax($type = 'qq', $redirect = '')
{
    return admin_url() . "admin-ajax.php?action=pk_oauth_url_page_ajax_exec&type={$type}&redirect={$redirect}";
}

//执行第三方登录页面跳转
function pk_oauth_url_page_ajax_exec()
{
    $type = $_GET['type'];
    $redirect = $_GET['redirect'];
    if ($type == 'qq') {
        echo "<html><script>window.location=\"" . pk_oauth_url($type, $redirect) . "\"</script></html>";
        wp_die();
    }
    oauth_qq_redirect_page(false, '无效授权请求', $redirect);
}

//登录页快捷按钮
function pk_oauth_form()
{
    $out = "<div style='margin-bottom:10px'>";
    if (pk_is_checked('oauth_qq')) {
        $out .= '<a href="' . pk_oauth_url_page_ajax('qq', admin_url()) . '" class="button button-large">QQ登录</a>';
    }
    $out .= "</div>";
    echo $out;
}

add_action('wp_ajax_nopriv_pk_oauth_url_page_ajax_exec', 'pk_oauth_url_page_ajax_exec');
add_action('wp_ajax_pk_oauth_url_page_ajax_exec', 'pk_oauth_url_page_ajax_exec');
if (pk_is_checked('oauth_qq')) {
    add_action('wp_ajax_nopriv_oauth_qq_redirect_ajax', 'oauth_qq_redirect_ajax');
    add_action('wp_ajax_oauth_qq_redirect_ajax', 'oauth_qq_redirect_ajax');
    //添加用户QQ——OPENID字段
    add_action('show_user_profile', 'extra_user_profile_qq_oauth');
    add_action('edit_user_profile', 'extra_user_profile_qq_oauth');
    add_action('login_form', 'pk_oauth_form');
    add_action('register_form', 'pk_oauth_form');
}
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
            //兼容WP Editor.md插件（如果pre标签里面含有code标签，则进行去除）
            $rep_match = preg_replace("/<(\/?code.*?)>/si", "", $match);
            $content = str_replace($match, pk_htmlspecialchars($rep_match), $content);
        }
    }
    return $content;
}

add_filter('the_content', 'pk_tag_pre_encode');
function pk_htmlspecialchars($content)
{
    $content = str_replace("<", "&lt;", $content);
    $content = str_replace(">", "&gt;", $content);
    return $content;
}

//新标签页打开
if (pk_is_checked('link_blank_content')) {
    add_filter('the_content', 'pk_link_blank');
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
    return pk_get_option('theme_mode', 'light') == 'light';
}

//配置是否选择
function pk_is_checked($name, $default = 0)
{
    return pk_get_option($name, $default) == 1;
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

//获取菜单数据
function pk_get_main_menu($mobile = false)
{
    $menus = get_nav_menu_object('primary');
    $out = $mobile ? "<ul class='puock-links t-md'>" : "<ul>";
    if ($menus && count($menus) > 0) {
        pk_get_menu_obj_to_html($menus, $out, $mobile);
    }
    if (is_user_logged_in()) {
        $user = get_currentuserinfo();
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
    foreach ($menus as $menu) {
        $classes = join(" ", $menu->classes);
        $cur = $menu->current ? 'menu-current' : '';
        $out .= "<li id='menu-item-{$menu->ID}' class='menu-item-{$menu->ID} {$classes} {$child_class} {$cur}'>";
        if (!$mobile) {
            $out .= "<a href='{$menu->url}'>{$menu->title}";
        } else {
            $out .= '<span><a href="' . $menu->url . '">' . $menu->title . '</a>';
        }
        if (count($menu->children) > 0) {
            if($mobile){
                $out .= '<a href="#menu-sub-'.$menu->ID.'" data-toggle="collapse"><i class="czs-angle-down-l t-sm ml-1"></i></a>';
            }else{
                $out .= '<i class="czs-angle-down-l t-sm ml-1"></i>';
            }
        }
        if($mobile){
            $out .= '</span>';
        }else{
            $out .= '</a>';
        }
        if (count($menu->children) > 0 && $dpath_cur < $max_dpath) {
            $out .= '<ul ' . ($mobile ? 'id="menu-sub-' . $menu->ID . '"' : '') . ' class="sub-menu ' . ($mobile
                    ? 'collapse' : 'animated bounceIn') . '">';
            pk_get_menu_obj_to_html($menu->children, $out, $mobile, $dpath_cur + 1, $max_dpath);
            $out .= '</ul>';
        }
    }
}

//获取分类的子集菜单
function get_category_child($parentId)
{
    $child = get_categories("child_of={$parentId}&hide_empty=0");
    $list = array();
    foreach ($child as $child_item) {
        array_push($list, array(
            'url' => get_category_link($child_item),
            'item' => $child_item
        ));
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
//摘要长度控制
function pk_chinese_excerpt($text, $len = 100)
{
    $end_str = '';
    if (strlen($text) > $len) {
        $len -= 3;
        $end_str = '...';
    }
    $text = mb_substr($text, 0, $len);
    return $text . $end_str;
}

add_filter('the_excerpt', 'pk_chinese_excerpt');

//静态资源加载源的链接
function pk_get_static_url(){
    $type = pk_get_option('static_load_origin', 'self');
    switch ($type){
        case "jsdelivr":
            $url_pre = "https://cdn.jsdelivr.net/gh/Licoy/wordpress-theme-puock@v".PUOCK_CUR_VER;
            break;
        default: $url_pre = get_template_directory_uri();
    }
    return $url_pre;
}

//是否打开讨论-显示头像
function pk_open_show_comment_avatar(){
    return get_option('show_avatars') == "1";
}