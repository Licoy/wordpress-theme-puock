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
            $label = (string)($val['label'] ?? $key);
            $raw_icon = (string)($val['icon'] ?? '');
            $icon = '';
            if ($raw_icon !== '') {
                if (strpos($raw_icon, 'http') === 0) {
                    $icon = "<img src='" . esc_url($raw_icon) . "' width='15' class='me-1' alt='" . esc_attr($label) . "'/>";
                } else {
                    $icon = "<i class='" . esc_attr(pk_sc_safe_class($raw_icon . ' me-1')) . "'></i>";
                }
            }
            $color_type = $val['color_type'] ?? 'primary';
            $out .= "<a class='btn btn-" . esc_attr(sanitize_html_class($color_type)) . " btn-ssm mr5 mb5 d-flex align-items-center'
               data-no-instant
               href='" . esc_url($url) . "'>
                {$icon}
                " . esc_html($label) . "
            </a>";
        }
    }
    $out .= "</div>";
    if ($echo) {
        echo $out;
    }
    return $out;
}
