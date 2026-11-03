<?php

function pk_ajax_url($action, $args = [])
{
    $url = admin_url('admin-ajax.php?action=' . $action);
    if (!empty($args)) {
        $url .= '&' . http_build_query($args);
    }
    return $url;
}

/**
 * @param $name
 * @param $callback callable
 * @param $public
 * @return void
 */
function pk_ajax_register($name, $callback, $public = false)
{
    add_action('wp_ajax_' . $name, $callback);
    if ($public) {
        add_action('wp_ajax_nopriv_' . $name, $callback);
    }
}

function pk_ajax_get_req_body()
{
    $body = @file_get_contents('php://input');
    return json_decode($body, true);
}

function pk_ajax_check_theme_options_nonce()
{
    check_ajax_referer('pk_theme_options', 'nonce');
}

function pk_ajax_result_page($success = true, $info = '', $from_redirect = '')
{
    if ($success && !empty($from_redirect)) {
        wp_redirect($from_redirect);
        exit;
    } else {
        pk_session_call(function () use ($info) {
            $_SESSION['error_info'] = $info;
        });
        wp_redirect(PUOCK_ABS_URI . '/error.php');
        wp_die();
    }
}

function pk_ajax_get_theme_options()
{
    if (current_user_can('edit_theme_options')) {
        wp_send_json_success([
            'settings' => get_option(PUOCK_OPT),
        ]);
    } else {
        wp_send_json_error(__('权限不足', PUOCK));
    }
}

pk_ajax_register('get_theme_options', 'pk_ajax_get_theme_options');

/**
 * 没有 unfiltered_html 权限的用户（如多站点子站点管理员）不能写入脚本等不安全的 HTML：
 * 被修改的字段若无法通过 wp_kses_post 则保留原值，没有原值时使用过滤后的内容
 */
function pk_theme_options_kses($value, $old)
{
    if (is_array($value)) {
        foreach ($value as $key => $item) {
            $value[$key] = pk_theme_options_kses($item, is_array($old) ? ($old[$key] ?? null) : null);
        }
        return $value;
    }
    if (!is_string($value) || $value === $old) {
        return $value;
    }
    $filtered = wp_kses_post($value);
    // 只比较标签层面的差异，忽略 & 等实体规范化，避免普通链接参数被误判
    if ($filtered === wp_kses_normalize_entities($value)) {
        return $value;
    }
    return is_string($old) ? $old : $filtered;
}

function pk_ajax_update_theme_options()
{
    if (current_user_can('edit_theme_options')) {
        pk_ajax_check_theme_options_nonce();
        $body = pk_ajax_get_req_body();
        if (!is_array($body)) {
            wp_send_json_error(__('请求格式错误', PUOCK));
        }
        if (!current_user_can('unfiltered_html')) {
            $body = pk_theme_options_kses($body, get_option(PUOCK_OPT));
        }
        update_option(PUOCK_OPT, $body);
        do_action('pk_option_updated', $body);
        flush_rewrite_rules();
        wp_send_json_success();
    } else {
        wp_send_json_error(__('权限不足', PUOCK));
    }
}

pk_ajax_register('update_theme_options', 'pk_ajax_update_theme_options');

/**
 * 获取主题设置默认值
 */
function pk_ajax_get_theme_options_defaults()
{
    if (current_user_can('edit_theme_options')) {
        $setting = new \Puock\Theme\setting\PuockSetting();
        $defaults = $setting->get_default_options();
        wp_send_json_success([
            'defaults' => $defaults,
        ]);
    } else {
        wp_send_json_error(__('权限不足', PUOCK));
    }
}

pk_ajax_register('get_theme_options_defaults', 'pk_ajax_get_theme_options_defaults');

/**
 * 重置主题设置为默认值
 */
function pk_ajax_reset_theme_options()
{
    if (current_user_can('edit_theme_options')) {
        pk_ajax_check_theme_options_nonce();
        $setting = new \Puock\Theme\setting\PuockSetting();
        $defaults = $setting->get_default_options();
        update_option(PUOCK_OPT, $defaults);
        do_action('pk_option_updated', $defaults);
        flush_rewrite_rules();
        wp_send_json_success([
            'message' => __('设置已重置为默认值', PUOCK),
            'data' => $defaults,
        ]);
    } else {
        wp_send_json_error(__('权限不足', PUOCK));
    }
}

pk_ajax_register('reset_theme_options', 'pk_ajax_reset_theme_options');
