<?php

namespace Puock\Theme\classes;

class PuockMetaBox
{
    private static $args = array();

    public static function newPostMeta($id, $args = array())
    {
        self::$args['post'][$id] = $args;
    }

    public static function newTaxonomyMeta($id, $args = array())
    {
        self::$args['taxonomy'][$id] = $args;
    }

    public static function newSection($id, $args = array())
    {
        self::$args['section'][$id] = $args;
    }

    public static function load(){
    }
}
