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
        if (!self::$_category) {
            self::$_category = get_all_category_id_row('category');
        }
        return self::$_category;
    }

    protected static function get_link_category(): ?array
    {
        if (!self::$_link_category) {
            self::$_link_category = get_all_category_id_row('link_category');
            array_unshift(self::$_link_category, ['label' => __('无', PUOCK), 'value' => '']);
        }
        return self::$_link_category;
    }

    /**
     * 获取友情链接排序顺序。
     *
     * 升序 (ASC)、降序(DESC)，默认为升序 (ASC)。
     * https://developer.wordpress.org/reference/functions/get_bookmarks/#parameters
     *
     */
    protected static function get_link_order()
    {
        return [
            ["label" => __("升序 (ASC)", PUOCK), "value" => "ASC"],
            ["label" => __("降序(DESC)", PUOCK), "value" => "DESC"]
        ];
    }

    /**
     * 获取友情链接排序字段。
     *
     * 下面仅为部分字段，所支持的全部字段请查看官方文档`orderby`部分
     * https://developer.wordpress.org/reference/functions/get_bookmarks/#parameters
     *
     */
    protected static function get_link_order_by(): array
    {
        return [
            ["label" => __("ID排序", PUOCK), "value" => "link_id"],
            ["label" => __("链接排序", PUOCK), "value" => "url"],
            ["label" => __("名字排序", PUOCK), "value" => "name"],
            ["label" => __("评级排序", PUOCK), "value" => "rating"],
            ["label" => __("长度排序", PUOCK), "value" => "length"],
            ["label" => __("随机排序", PUOCK), "value" => "rand"]
        ];
    }

    protected static function get_pages()
    {
        if (!self::$_pages) {
            $pages = array();
            $pages[] = ['label' => __('无', PUOCK), 'value' => ''];
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
