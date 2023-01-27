<?php

function pk_ajax_url($action,$args=[]){
    $url = admin_url('admin-ajax.php?action='.$action);
    if(!empty($args)){
        $url .= '&'.http_build_query($args);
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

function pk_ajax_result_page($success = true, $info = '', $from_redirect = '')
{
    if ($success && !empty($from_redirect)) {
        wp_redirect($from_redirect);
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
        wp_send_json_error('权限不足');
    }
}

pk_ajax_register('get_theme_options', 'pk_ajax_get_theme_options');

function pk_ajax_update_theme_options()
{
    if (current_user_can('edit_theme_options')) {
        $body = pk_ajax_get_req_body();
        update_option(PUOCK_OPT, $body);
        do_action('pk_option_updated', $body);
        flush_rewrite_rules();
        wp_send_json_success();
    } else {
        wp_send_json_error('权限不足');
    }
}

pk_ajax_register('update_theme_options', 'pk_ajax_update_theme_options');
