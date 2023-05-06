<?php


function pk_baidu_wd_reject()
{
    $key_list = array_map('trim', explode(',', pk_get_option('vd_kwd_access_reject_list', '')));
    if(count($key_list) > 0){
        foreach ($key_list as $key){
            if(isset($_GET[$key])){
                header('HTTP/1.1 403 Forbidden');
                exit;
            }
        }
    }
}
if(pk_is_checked('vd_kwd_access_reject')){
    add_action('init', 'pk_baidu_wd_reject');
}
