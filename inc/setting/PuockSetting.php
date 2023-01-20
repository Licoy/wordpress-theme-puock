<?php

namespace Puock\Theme\setting;

use Puock\Theme\setting\options\OptionAbout;
use Puock\Theme\setting\options\OptionAd;
use Puock\Theme\setting\options\OptionBasic;
use Puock\Theme\setting\options\OptionCache;
use Puock\Theme\setting\options\OptionCarousel;
use Puock\Theme\setting\options\OptionCms;
use Puock\Theme\setting\options\OptionCompany;
use Puock\Theme\setting\options\OptionDebug;
use Puock\Theme\setting\options\OptionEmail;
use Puock\Theme\setting\options\OptionExtend;
use Puock\Theme\setting\options\OptionGlobal;
use Puock\Theme\setting\options\OptionAuth;
use Puock\Theme\setting\options\OptionResource;
use Puock\Theme\setting\options\OptionScript;
use Puock\Theme\setting\options\OptionSeo;
use Puock\Theme\setting\options\OptionValidate;

class PuockSetting
{
    public function init()
    {
        add_action("admin_menu", array($this, '__wp_reg_menu'));
        add_action('admin_init', array($this, '__wp_admin_init'));
    }

    public function option_menus_register()
    {
        $classes = [];
        $classes[] = ['class' => OptionGlobal::class, 'sort' => 1];
        $classes[] = ['class' => OptionBasic::class, 'sort' => 2];
        $classes[] = ['class' => OptionCarousel::class, 'sort' => 3];
        $classes[] = ['class' => OptionCms::class, 'sort' => 4];
        $classes[] = ['class' => OptionCompany::class, 'sort' => 5];
        $classes[] = ['class' => OptionAuth::class, 'sort' => 6];
        $classes[] = ['class' => OptionValidate::class, 'sort' => 7];
        $classes[] = ['class' => OptionAd::class, 'sort' => 8];
        $classes[] = ['class' => OptionEmail::class, 'sort' => 9];
        $classes[] = ['class' => OptionSeo::class, 'sort' => 10];
        $classes[] = ['class' => OptionExtend::class, 'sort' => 10];
        $classes[] = ['class' => OptionScript::class, 'sort' => 11];
        $classes[] = ['class' => OptionCache::class, 'sort' => 12];
        $classes[] = ['class' => OptionDebug::class, 'sort' => 13];
        $classes[] = ['class' => OptionResource::class, 'sort' => 14];
        $classes[] = ['class' => OptionAbout::class, 'sort' => 99];
        $classes = apply_filters('pk_theme_option_menus_register', $classes, 10, 1);
        array_multisort(array_column($classes, 'sort'), SORT_ASC, $classes);
        return $classes;
    }

    public function __wp_admin_init()
    {
        pk_ajax_register('get_theme_option_fields', array($this, '__wp_get_settings_ajax'));
    }

    public function __wp_reg_menu()
    {
        add_menu_page(
            __('Puock主题配置', PUOCK),
            __('Puock主题配置', PUOCK),
            "manage_options",
            "puock-options",
            array($this, 'setting_page'),
            'dashicons-buddicons-topics',
        );
    }

    function __wp_get_settings_ajax()
    {
        $menus = $this->option_menus_register();
        if (!current_user_can('edit_theme_options')) {
            wp_send_json_error(__('权限不足', PUOCK));
        }
        $fields = [];
        foreach ($menus as $menu) {
            $fields[] = (new $menu['class']())->get_fields();
        }
        do_action('pk_get_theme_option_fields', $fields);
        wp_send_json_success($fields);
    }

    function setting_page()
    {
        require_once dirname(__FILE__) . '/template.php';
    }
}
