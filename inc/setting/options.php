<?php

function pk_theme_option_fields_class_include()
{
    $classes = [];
    $classes[] = ['class' => dirname(__FILE__) . '/options/BaseOptionItem.php'];
    $classes[] = ['name' => 'OptionGlobal', 'class' => dirname(__FILE__) . '/options/OptionGlobal.php'];
    $classes[] = ['name' => 'OptionBasic', 'class' => dirname(__FILE__) . '/options/OptionBasic.php'];
    $classes[] = ['name' => 'OptionCarousel', 'class' => dirname(__FILE__) . '/options/OptionCarousel.php'];
    $classes[] = ['name' => 'OptionCms', 'class' => dirname(__FILE__) . '/options/OptionCms.php'];
    $classes[] = ['name' => 'OptionCompany', 'class' => dirname(__FILE__) . '/options/OptionCompany.php'];
    $classes[] = ['name' => 'OptionOAuth', 'class' => dirname(__FILE__) . '/options/OptionOAuth.php'];
    $classes[] = ['name' => 'OptionValidate', 'class' => dirname(__FILE__) . '/options/OptionValidate.php'];
    $classes[] = ['name' => 'OptionAd', 'class' => dirname(__FILE__) . '/options/OptionAd.php'];
    $classes[] = ['name' => 'OptionEmail', 'class' => dirname(__FILE__) . '/options/OptionEmail.php'];
    $classes[] = ['name' => 'OptionSeo', 'class' => dirname(__FILE__) . '/options/OptionSeo.php'];
    $classes[] = ['name' => 'OptionScript', 'class' => dirname(__FILE__) . '/options/OptionScript.php'];
    $classes[] = ['name' => 'OptionCache', 'class' => dirname(__FILE__) . '/options/OptionCache.php'];
    $classes[] = ['name' => 'OptionDebug', 'class' => dirname(__FILE__) . '/options/OptionDebug.php'];
    $classes[] = ['name' => 'OptionResource', 'class' => dirname(__FILE__) . '/options/OptionResource.php'];
    do_action('pk_theme_option_fields_class_include', $classes);
    return $classes;
}

function pk_get_theme_option_fields()
{
    if (!current_user_can('edit_theme_options')) {
        wp_send_json_error('权限不足');
        wp_die();
    }
    $classes = pk_theme_option_fields_class_include();
    $fields = [];
    foreach ($classes as $class) {
        require_once $class['class'];
        if (isset($class['name'])) {
            $class_name = $class['name'];
            $fields[] = (new $class['name']())->get_fields();
        }
    }
    do_action('pk_get_theme_option_fields', $fields);
    wp_send_json_success($fields);
    wp_die();
}

add_action('admin_init', function () {
    pk_ajax_register('get_theme_option_fields', 'pk_get_theme_option_fields');
});
