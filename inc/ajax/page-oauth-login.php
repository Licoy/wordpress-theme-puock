<?php

pk_ajax_register('pk_oauth_quick_page', 'pk_oauth_quick_page_callback', true);


function pk_oauth_quick_page_callback()
{
    $redirect = $_GET['redirect'] ?? get_edit_profile_url();
    pk_oauth_quick_buttons(true, $redirect);
    wp_die();
}

function pk_oauth_quick_buttons($echo = false, $redirect = '')
{
    $oauth_list = pk_oauth_list();
    $out = "<div class='d-flex justify-content-center wh100 flex-wrap'>";
    foreach ($oauth_list as $key => $val) {
        if (!isset($val['system']) || !$val['system'] || pk_is_checked('oauth_' . $key)) {
            $url = $val['url'] ?? pk_oauth_url_page_ajax($key, $redirect);
            $icon = isset($val['icon']) ? "<i class='{$val['icon']}'></i>" : '';
            $color_type = $val['color_type'] ?? 'primary';
            $out .= "<a class='btn btn-{$color_type} btn-ssm mr5 mb5'
               data-no-instant
               href='{$url}'>
                {$icon}
                {$val['label']}
            </a>";
        }
    }
    $out .= "</div>";
    if ($echo) {
        echo $out;
    }
    return $out;
}
