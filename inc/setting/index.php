<?php

function pk_register_options_setting_menu() {
    add_menu_page(
        "Puock主题配置",
        "Puock主题配置",
        "manage_options",
        "puock-options",
        'pk_options_page',
        'dashicons-art'
    );
}

add_action( "admin_menu", "pk_register_options_setting_menu");

function pk_options_page(){
    require_once dirname(__FILE__).'/template.php';
}

