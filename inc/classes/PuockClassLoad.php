<?php

namespace Puock\Theme\classes;

class PuockClassLoad
{

    private static array $classes = [];

    public function __construct()
    {
        if (pk_is_checked('user_center')) {
            self::$classes[] = PuockUserCenter::class;
        }
        add_action('init', array($this, '__wp_init'));
    }

    public function __wp_init()
    {
        $this->load_class();
    }

    public static function add_class(IPuockClassLoad $class)
    {
        self::$classes[] = $class;
    }

    private static function load_class()
    {
        foreach (self::$classes as $class) {
            $class::load();
        }
    }
}

interface IPuockClassLoad
{
    public static function load();
}
