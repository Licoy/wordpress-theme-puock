<?php

namespace Puock\Theme\setting\options;

/**
 * @type = 'string' | 'number' | 'select' | 'switch' | 'date' | 'img' | 'textarea' | 'color' | 'upload' | 'radio' | 'info' | 'slider' | 'dynamic-list'
 * @ruleType = 'string' | 'number' | 'boolean' | 'method' | 'regexp' | 'integer' | 'float' | 'array' | 'object' | 'enum' | 'date' | 'url' | 'hex' | 'email' | 'pattern' | 'any'
 * @textType = 'text' | 'textarea' | string
 * @radioType = 'button' | 'radio'
 * @infoType = 'info' | 'warning' | 'error' | 'success'
 */
abstract class BaseOptionItem
{

    protected static $_category = null;
    protected static $_link_category = null;
    protected static $_pages = null;

    protected static function get_category()
    {
        if(!self::$_category){
            self::$_category = get_all_category_id_row('category');
        }
        return self::$_category;
    }

    protected static function get_link_category()
    {
        if(!self::$_link_category){
            self::$_link_category = get_all_category_id_row('link_category');
        }
        return self::$_link_category;
    }

    protected static function get_pages()
    {
        if(!self::$_pages){
            $pages = array();
            $pageObjects = get_pages('sort_column=post_parent,menu_order');
            foreach ($pageObjects as $page) {
                $pages[] = ['label' => $page->post_title, 'value' => $page->ID];
            }
            self::$_pages = $pages;
        }
        return self::$_pages;
    }

    abstract function get_fields(): array;
}
