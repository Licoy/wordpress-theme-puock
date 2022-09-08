<?php

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

function pk_ajax_get_theme_options()
{
    if (current_user_can('edit_theme_options')) {
        $pages = array();
        $pageObjects = get_pages('sort_column=post_parent,menu_order');
        foreach ($pageObjects as $page) {
            $pages[] = ['label' => $page->post_title, 'value' => $page->ID];
        }
        wp_send_json_success([
            'settings' => get_option(PUOCK_OPT),
            'options' => [
                'pages' => $pages,
                'categories' => get_all_category_id_row('category'),
                'link_categories' => get_all_category_id_row('link_category'),
                'home_url'=>home_url(),
                'admin_url'=>admin_url(),
            ]
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
        wp_send_json_success();
    } else {
        wp_send_json_error('权限不足');
    }
}

pk_ajax_register('update_theme_options', 'pk_ajax_update_theme_options');
