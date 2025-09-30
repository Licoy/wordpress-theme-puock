<?php

use Yurun\OAuthLogin\QQ\OAuth2;

class PkOAuthBase
{
    public $oauth;
    public $base;

    /**
     * @param $oauth
     * @param $base
     */
    public function __construct($oauth, $base)
    {
        $this->oauth = $oauth;
        $this->base = $base;
    }

}

function pk_oauth_list($user = null)
{
    $list = [
        'qq' => [
            'label' => 'QQ',
            'openid' => $user ? get_the_author_meta('qq_oauth', $user->ID) : null,
            'class' => OAuth2::class,
            'name_field' => 'nickname',
            'icon' => 'fa-brands fa-qq',
            'color_type' => 'danger',
            'secret_field' => 'key',
            'system' => true,
        ],
        'github' => [
            'label' => 'GitHub',
            'openid' => $user ? get_the_author_meta('github_oauth', $user->ID) : null,
            'class' => \Yurun\OAuthLogin\Github\OAuth2::class,
            'icon' => 'fa-brands fa-github',
            'color_type' => 'primary',
            'name_field' => 'name',
            'system' => true,
        ],
        'weibo' => [
            'label' => '微博',
            'openid' => $user ? get_the_author_meta('weibo_oauth', $user->ID) : null,
            'class' => \Yurun\OAuthLogin\Weibo\OAuth2::class,
            'name_field' => 'name',
            'icon' => 'fa-brands fa-weibo',
            'color_type' => 'danger',
            'id_field' => 'key',
            'system' => true,
        ],
        'gitee' => [
            'label' => '码云',
            'openid' => $user ? get_the_author_meta('gitee_oauth', $user->ID) : null,
            'class' => \Yurun\OAuthLogin\Gitee\OAuth2::class,
            'icon' => 'fa-solid fa-globe',
            'color_type' => 'info',
            'name_field' => 'name',
            'system' => true,
        ],
        'linuxdo' => [
            'label' => 'LinuxDo',
            'openid' => $user ? get_the_author_meta('linuxdo_oauth', $user->ID) : null,
            'class' => \Yurun\OAuthLogin\LinuxDo\OAuth2::class,
            'icon' => PUOCK_ABS_URI . '/assets/img/oauth/linuxdo.png',
            'color_type' => 'warning',
            'name_field' => 'name',
            'system' => true,
        ]
    ];
    return apply_filters('pk_oauth_list', $list);
}

function pk_extra_user_profile_oauth($user)
{
    $oauth_list = pk_oauth_list($user)
    ?>
    <h3>第三方账号绑定</h3>
    <table class="form-table">
        <?php foreach ($oauth_list as $item_key => $item_val):
            if (!pk_is_checked('oauth_' . $item_key)) {
                continue;
            } ?>
            <tr>
                <th><label for="<?php echo $item_key ?>_oauth"><?php echo $item_val['label'] ?></label></th>
                <td>
                    <?php if (empty($item_val['openid'])): ?>
                        <a href="<?php echo pk_oauth_url_page_ajax($item_key, get_edit_profile_url()) ?>"
                           target="_blank"
                           class="button"
                           id="<?php echo $item_key ?>_oauth">立即绑定</a>
                    <?php else: ?>
                        <a id="<?php echo $item_key ?>_oauth"
                           href="<?php echo pk_oauth_clear_bind_url($item_key, get_edit_profile_url()) ?>"
                           class="button">解除绑定<?php echo $item_val['label'] ?></a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php
}

add_action('show_user_profile', 'pk_extra_user_profile_oauth');
add_action('edit_user_profile', 'pk_extra_user_profile_oauth');

function pk_oauth_clear_bind_url($type, $redirect = null)
{
    if (!$redirect) {
        $redirect = get_edit_profile_url();
    }
    return admin_url() . "admin-ajax.php?action=pk_oauth_clear_bind&type={$type}&redirect={$redirect}";
}

function pk_oauth_clear_bind()
{
    $type = $_GET['type'];
    $redirect = $_GET['redirect'];
    $oauth_list = pk_oauth_list();
    if (isset($oauth_list[$type])) {
        delete_user_meta(get_current_user_id(), $type . '_oauth');
    }
    wp_redirect($redirect);
}

pk_ajax_register('pk_oauth_clear_bind', 'pk_oauth_clear_bind');

//授权返回页面回调
function oauth_redirect_page($success = true, $info = '', $from_redirect = '')
{
    if ($success) {
        if (empty($from_redirect)) {
            wp_redirect(get_admin_url());
        } else {
            wp_redirect($from_redirect);
        }
    } else {
        pk_session_call(function () use ($info) {
            if (empty($info)){
                $info = '发生未知错误';
            }
            $_SESSION['error_info'] = $info;
        });
        wp_redirect(PUOCK_ABS_URI . '/error.php');
        exit;
    }
}

function pk_oauth_get_callback_url($type, $redirect = '')
{
    return admin_url() . 'admin-ajax.php?action=pk_oauth_callback&type=' . $type . '&redirect=' . urlencode($redirect);
}

function pk_oauth_url_page_ajax($type, $redirect = '')
{
    return admin_url() . "admin-ajax.php?action=pk_oauth_start_redirect&type={$type}&redirect={$redirect}";
}

function pk_oauth_get_base($type, $redirect = '')
{
    if (!pk_is_checked('oauth_' . $type)) {
        return null;
    }
    $oauth_list = pk_oauth_list();
    if (array_key_exists($type, $oauth_list)) {
        $oauth = $oauth_list[$type];
        $oauth_id = pk_get_option('oauth_' . $type . '_' . (empty($oauth['id_field']) ? 'id' : $oauth['id_field']));
        $oauth_key = pk_get_option('oauth_' . $type . '_' . (empty($oauth['secret_field']) ? 'secret' : $oauth['secret_field']));
        return new PkOAuthBase($oauth, new $oauth['class']($oauth_id, $oauth_key, pk_oauth_get_callback_url($type, $redirect)));
    }
    return null;
}

// 第三方授权登录开始跳转
function pk_oauth_start_redirect()
{
    $type = $_GET['type'];
    $redirect = $_GET['redirect'];
    $oauth = pk_oauth_get_base($type, $redirect);
    if (!$oauth) {
        oauth_redirect_page(false, '不支持的第三方授权请求', $redirect);
        exit;
    }
    $url = $oauth->base->getAuthUrl();
    if (!empty($url)) {
        pk_session_call(function () use ($oauth, $type) {
            $_SESSION['oauth_state_' . $type] = $oauth->base->state;
        });
        wp_redirect($url);
    }
    exit;
}

pk_ajax_register('pk_oauth_start_redirect', 'pk_oauth_start_redirect', true);

function pk_oauth_callback()
{
    $type = $_GET['type'];
    $redirect = $_GET['redirect'];
    pk_oauth_callback_execute($type, $redirect);
}

function pk_oauth_callback_execute($type, $redirect)
{
    if (!empty($redirect)) {
        $redirect = urldecode($redirect);
    }
    $oauth = pk_oauth_get_base($type, $redirect);
    if (!$oauth) {
        oauth_redirect_page(false, '无效授权请求', $redirect);
        exit;
    }
    $oauth_state = null;
    pk_session_call(function () use (&$oauth_state, $type) {
        $oauth_state = $_SESSION['oauth_state_' . $type];
    });
    if (empty($oauth_state)) {
        oauth_redirect_page(false, '无效的授权状态', $redirect);
        exit;
    }
    $oauthBase = $oauth->base;
    try {
        $oauthBase->getAccessToken($oauth_state);
        $userInfo = $oauthBase->getUserInfo();
    } catch (Exception $e) {
        oauth_redirect_page(false, '授权失败：' . $e->getMessage(), $redirect);
        exit;
    }
    if (is_user_logged_in()) {
        $bind_users = get_users(array('meta_key' => $type . '_oauth', 'meta_value' => $oauthBase->openid, 'exclude' => get_current_user_id()));
        if ($bind_users && count($bind_users) > 0) {
            oauth_redirect_page(false, '绑定失败：此授权' . $oauth->oauth['label'] . '账户已被其他账户使用', $redirect);
            exit;
        }
        if (!empty(get_user_meta(get_current_user_id(), $type . "_oauth"))) {
            oauth_redirect_page(false, '绑定失败：此账户已绑定其他' . $oauth->oauth['label'] . '授权账户', $redirect);
            exit;
        }
        $user = wp_get_current_user();
        update_user_meta($user->ID, $type . "_oauth", $oauthBase->openid);
        oauth_redirect_page(true, '', $redirect);
        exit;
    } else {
        $users = get_users(array('meta_key' => $type . '_oauth', 'meta_value' => $oauthBase->openid));
        if (!$users || count($users) <= 0) {
            //不存在用户，先自动注册再登录
            if (pk_is_checked('oauth_close_register')) {
                oauth_redirect_page(false, '您的' . $oauth->oauth['label'] . '账号未绑定本站账户，当前已关闭自动注册，请手动注册后再进入个人资料中进行绑定', $redirect);
                exit;
            }
            $wp_create_nonce = wp_create_nonce($oauthBase->openid);
            $username = $type . '_' . $wp_create_nonce;
            $password = wp_generate_password(10);
            $nickname = $userInfo[$oauth->oauth['name_field']] ?? $username;
            $user_data = array(
                'user_login' => $username,
                'display_name' => $nickname,
                'user_pass' => $password,
                'nickname' => $nickname,
                'user_email' => 'null@null.null'
            );
            $user_id = wp_insert_user($user_data);
            update_user_meta($user_id, $type . "_oauth", $oauthBase->openid);
            wp_set_auth_cookie($user_id);
        } else {
            wp_set_auth_cookie($users[0]->ID);
        }
        oauth_redirect_page(true, '', $redirect);
    }
}

pk_ajax_register('pk_oauth_callback', 'pk_oauth_callback', true);

//登录页快捷按钮
function pk_oauth_form()
{
    $out = "<div>";
    $oauth_list = pk_oauth_list();
    foreach ($oauth_list as $key => $val) {
        if (pk_is_checked('oauth_' . $key)) {
            $out .= '<a style="margin-right:5px;margin-bottom:10px" href="' . pk_oauth_url_page_ajax($key, admin_url()) . '" class="button button-large">' . $val['label'] . '登录</a>';
        }
    }
    $out .= "</div>";
    echo $out;
}

add_action('login_form', 'pk_oauth_form');
add_action('register_form', 'pk_oauth_form');

function pk_oauth_platform_count()
{
    $count = 0;
    $oauth_list = pk_oauth_list();
    foreach ($oauth_list as $key => $val) {
        if (pk_is_checked('oauth_' . $key)) {
            $count++;
        }
    }
    return apply_filters('pk_oauth_platform_count', $count);
}
