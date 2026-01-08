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
            'label' => __('微博', PUOCK),
            'openid' => $user ? get_the_author_meta('weibo_oauth', $user->ID) : null,
            'class' => \Yurun\OAuthLogin\Weibo\OAuth2::class,
            'name_field' => 'name',
            'icon' => 'fa-brands fa-weibo',
            'color_type' => 'danger',
            'id_field' => 'key',
            'system' => true,
        ],
        'gitee' => [
            'label' => __('码云', PUOCK),
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
        ],
    ];

    // 彩虹聚合登录（动态配置，固定前缀：ccy_）
    if (pk_is_checked('oauth_ccy')) {
        $ccyAppId = trim((string)pk_get_option('oauth_ccy_appid'));
        $ccyAppKey = trim((string)pk_get_option('oauth_ccy_appkey'));

        $typesRaw = pk_get_option('oauth_ccy_types');
        if (is_string($typesRaw)) {
            $types = json_decode((string)$typesRaw, true);
        } else {
            $types = $typesRaw;
        }

        if (is_array($types)) {
            foreach ($types as $typeItem) {
                if (!is_array($typeItem)) {
                    continue;
                }

                $typeValue = sanitize_key($typeItem['value'] ?? '');
                if (empty($typeValue)) {
                    continue;
                }

                $providerKey = 'ccy_' . $typeValue;
                $label = (string)($typeItem['label'] ?? $providerKey);
                $icon = (string)($typeItem['icon'] ?? '');
                $colorType = (string)($typeItem['color_type'] ?? 'primary');

                $list[$providerKey] = [
                    'label' => $label,
                    'openid' => $user ? get_the_author_meta($providerKey . '_oauth', $user->ID) : null,
                    'class' => \Puock\Theme\oauth\RainbowOAuth::class,
                    'icon' => $icon,
                    'color_type' => $colorType,
                    'name_field' => 'nickname',
                    'system' => true,
                    'enable_option' => 'oauth_ccy',
                    'oauth_id' => $ccyAppId,
                    'oauth_key' => $ccyAppKey,
                    'callback_url' => function ($type, $redirect) {
                        return PUOCK_ABS_URI . '/inc/oauth/callback/ccy.php?' . http_build_query([
                                'pk_type' => $type,
                                'redirect' => $redirect,
                            ]);
                    },
                ];
            }
        }
    }

    return apply_filters('pk_oauth_list', $list);
}

function pk_oauth_is_enabled($type, $oauth = null)
{
    if (!$oauth) {
        $oauth_list = pk_oauth_list();
        $oauth = $oauth_list[$type] ?? null;
    }
    if (!$oauth) {
        return false;
    }
    $enableOption = $oauth['enable_option'] ?? ('oauth_' . $type);
    return pk_is_checked($enableOption);
}

function pk_extra_user_profile_oauth($user)
{
    $oauth_list = pk_oauth_list($user);
    ?>
    <h3><?php _e('第三方账号绑定', PUOCK); ?></h3>
    <table class="form-table">
        <?php foreach ($oauth_list as $item_key => $item_val):
            if (!pk_oauth_is_enabled($item_key, $item_val)) {
                continue;
            } ?>
            <tr>
                <th><label for="<?php echo $item_key ?>_oauth"><?php echo $item_val['label'] ?></label></th>
                <td>
                    <?php if (empty($item_val['openid'])): ?>
                        <a href="<?php echo pk_oauth_url_page_ajax($item_key, get_edit_profile_url()) ?>"
                           target="_blank"
                           class="button"
                           id="<?php echo $item_key ?>_oauth"><?php _e('立即绑定', PUOCK); ?></a>
                    <?php else: ?>
                        <a id="<?php echo $item_key ?>_oauth"
                           href="<?php echo pk_oauth_clear_bind_url($item_key, get_edit_profile_url()) ?>"
                           class="button"><?php echo sprintf(__('解除绑定%s', PUOCK), $item_val['label']); ?></a>
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
    return pk_ajax_url('pk_oauth_clear_bind', [
        'type' => $type,
        'redirect' => $redirect,
    ]);
}

function pk_oauth_clear_bind_url2($type, $redirect = null)
{
    if (!$redirect) {
        $redirect = get_edit_profile_url();
    }
    return pk_ajax_url('pk_oauth_clear_bind2', [
        'type' => $type,
        'redirect' => $redirect,
        '_wpnonce' => wp_create_nonce('pk_oauth_clear_bind_' . $type),
    ]);
}

function pk_oauth_clear_bind()
{
    $type = sanitize_key($_GET['type'] ?? '');
    $redirect = $_GET['redirect'] ?? '';
    $oauth_list = pk_oauth_list();
    if ($type && isset($oauth_list[$type])) {
        delete_user_meta(get_current_user_id(), $type . '_oauth');
    }
    if (empty($redirect)) {
        $redirect = get_edit_profile_url();
    }
    wp_safe_redirect($redirect);
    exit;
}

pk_ajax_register('pk_oauth_clear_bind', 'pk_oauth_clear_bind');

function pk_oauth_clear_bind2()
{
    $type = sanitize_key($_GET['type'] ?? '');
    $redirect = $_GET['redirect'] ?? '';
    if (empty($redirect)) {
        $redirect = get_edit_profile_url();
    }

    $nonce = $_GET['_wpnonce'] ?? '';
    if (empty($type) || !wp_verify_nonce($nonce, 'pk_oauth_clear_bind_' . $type)) {
        wp_die(__('非法请求', PUOCK));
    }

    $oauth_list = pk_oauth_list();
    if (!isset($oauth_list[$type]) || !pk_oauth_is_enabled($type, $oauth_list[$type])) {
        wp_die(__('不支持的请求', PUOCK));
    }

    delete_user_meta(get_current_user_id(), $type . '_oauth');
    wp_safe_redirect($redirect);
    exit;
}

pk_ajax_register('pk_oauth_clear_bind2', 'pk_oauth_clear_bind2');

//授权返回页面回调
function oauth_redirect_page($success = true, $info = '', $from_redirect = '')
{
    if ($success) {
        if (empty($from_redirect)) {
            wp_safe_redirect(get_admin_url());
            exit;
        } else {
            wp_safe_redirect($from_redirect);
            exit;
        }
    } else {
        pk_session_call(function () use ($info) {
            if (empty($info)) {
                $info = __('发生未知错误', PUOCK);
            }
            $_SESSION['error_info'] = $info;
        });
        wp_safe_redirect(PUOCK_ABS_URI . '/error.php');
        exit;
    }
}

function pk_oauth_get_callback_url($type, $redirect = '')
{
    return pk_ajax_url('pk_oauth_callback', [
        'type' => $type,
        'redirect' => $redirect,
    ]);
}

function pk_oauth_url_page_ajax($type, $redirect = '')
{
    return pk_ajax_url('pk_oauth_start_redirect', [
        'type' => $type,
        'redirect' => $redirect,
    ]);
}

function pk_oauth_get_base($type, $redirect = '')
{
    $oauth_list = pk_oauth_list();
    if (!array_key_exists($type, $oauth_list)) {
        return null;
    }

    $oauth = $oauth_list[$type];
    if (!pk_oauth_is_enabled($type, $oauth)) {
        return null;
    }

    if (isset($oauth['oauth_id']) || isset($oauth['oauth_key'])) {
        $oauth_id = trim((string)($oauth['oauth_id'] ?? ''));
        $oauth_key = trim((string)($oauth['oauth_key'] ?? ''));
    } else {
        $optionPrefix = $oauth['option_prefix'] ?? ('oauth_' . $type);
        $oauth_id = trim((string)pk_get_option($optionPrefix . '_' . (empty($oauth['id_field']) ? 'id' : $oauth['id_field'])));
        $oauth_key = trim((string)pk_get_option($optionPrefix . '_' . (empty($oauth['secret_field']) ? 'secret' : $oauth['secret_field'])));
    }

    if ($oauth_id === '' || $oauth_key === '') {
        $label = (string)($oauth['label'] ?? $type);
        throw new InvalidArgumentException(sprintf(__('第三方登录配置不完整，请检查「%s」配置', PUOCK), $label));
    }

    if (isset($oauth['callback_url'])) {
        if (is_callable($oauth['callback_url'])) {
            $callbackUrl = call_user_func($oauth['callback_url'], $type, $redirect);
        } else {
            $callbackUrl = (string)$oauth['callback_url'];
        }
    } else {
        $callbackUrl = pk_oauth_get_callback_url($type, $redirect);
    }

    return new PkOAuthBase($oauth, new $oauth['class']($oauth_id, $oauth_key, $callbackUrl));
}

// 第三方授权登录开始跳转
function pk_oauth_start_redirect()
{
    $type = sanitize_key($_GET['type'] ?? '');
    $redirect = $_GET['redirect'] ?? '';
    if (empty($redirect)) {
        $redirect = home_url('/');
    }
    $oauth = pk_oauth_get_base($type, $redirect);
    if (!$oauth) {
        oauth_redirect_page(false, __('不支持的第三方授权请求', PUOCK), $redirect);
        exit;
    }
    try {
        $url = $oauth->base->getAuthUrl();
    } catch (Throwable $e) {
        oauth_redirect_page(false, sprintf(__('授权跳转失败：%s', PUOCK), $e->getMessage()), $redirect);
        exit;
    }

    if (empty($url)) {
        oauth_redirect_page(false, __('获取授权地址失败', PUOCK), $redirect);
        exit;
    }

    pk_session_call(function () use ($oauth, $type) {
        $_SESSION['oauth_state_' . $type] = $oauth->base->state;
    });
    wp_redirect($url);
    exit;
}

pk_ajax_register('pk_oauth_start_redirect', 'pk_oauth_start_redirect', true);

function pk_oauth_callback()
{
    $type = sanitize_key($_GET['type'] ?? '');
    // GitHub 等平台授权成功后可能不带 redirect，这里提供首页兜底，避免停留在 admin-ajax 返回 "0" 的空白页
    $redirect = $_GET['redirect'] ?? '';
    if (empty($redirect)) {
        $redirect = home_url('/');
    }
    pk_oauth_callback_execute($type, $redirect);
    // 兜底阻止 admin-ajax 后续输出默认的 "0"
    wp_die();
}

function pk_oauth_callback_execute($type, $redirect)
{
    if (!empty($redirect)) {
        $redirect = urldecode($redirect);
    }
    $oauth = pk_oauth_get_base($type, $redirect);
    if (!$oauth) {
        oauth_redirect_page(false, __('无效授权请求', PUOCK), $redirect);
        exit;
    }
    $oauth_state = null;
    pk_session_call(function () use (&$oauth_state, $type) {
        $oauth_state = $_SESSION['oauth_state_' . $type];
    });
    if (empty($oauth_state)) {
        oauth_redirect_page(false, __('无效的授权状态', PUOCK), $redirect);
        exit;
    }
    $oauthBase = $oauth->base;
    try {
        $oauthBase->getAccessToken($oauth_state);
        $userInfo = $oauthBase->getUserInfo();
    } catch (Exception $e) {
        oauth_redirect_page(false, sprintf(__('授权失败：%s', PUOCK), $e->getMessage()), $redirect);
        exit;
    }
    if (is_user_logged_in()) {
        $bind_users = get_users(array('meta_key' => $type . '_oauth', 'meta_value' => $oauthBase->openid, 'exclude' => get_current_user_id()));
        if ($bind_users && count($bind_users) > 0) {
            oauth_redirect_page(false, sprintf(__('绑定失败：此授权%s账户已被其他账户使用', PUOCK), $oauth->oauth['label']), $redirect);
            exit;
        }
        if (!empty(get_user_meta(get_current_user_id(), $type . "_oauth"))) {
            oauth_redirect_page(false, sprintf(__('绑定失败：此账户已绑定其他%s授权账户', PUOCK), $oauth->oauth['label']), $redirect);
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
                oauth_redirect_page(false, sprintf(__('您的%s账号未绑定本站账户，当前已关闭自动注册，请手动注册后再进入个人资料中进行绑定', PUOCK), $oauth->oauth['label']), $redirect);
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
        if (pk_oauth_is_enabled($key, $val)) {
            $out .= '<a style="margin-right:5px;margin-bottom:10px" href="' . pk_oauth_url_page_ajax($key, admin_url()) . '" class="button button-large">' . sprintf(__('%s登录', PUOCK), $val['label']) . '</a>';
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
        if (pk_oauth_is_enabled($key, $val)) {
            $count++;
        }
    }
    return apply_filters('pk_oauth_platform_count', $count);
}
