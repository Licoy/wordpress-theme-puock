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
        if (!isset($val['system']) || !$val['system'] || pk_oauth_is_enabled($key, $val)) {
            $url = $val['url'] ?? pk_oauth_url_page_ajax($key, $redirect);
            $icon = isset($val['icon']) ? str_starts_with($val['icon'], 'http') ? "<img src='{$val['icon']}' width='15' class='mr-1' alt='{$val['label']}'/>":"<i class='{$val['icon']} mr-1'></i>" : '';
            $color_type = $val['color_type'] ?? 'primary';
            $out .= "<a class='btn btn-{$color_type} btn-ssm mr5 mb5 d-flex align-items-center'
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
