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
            array_unshift(self::$_link_category, ['label' => '无', 'value' => '']);
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
            ["label" => "升序 (ASC)", "value" => "ASC"],
            ["label" => "降序(DESC)", "value" => "DESC"]
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
            ["label" => "ID排序", "value" => "link_id"],
            ["label" => "链接排序", "value" => "url"],
            ["label" => "名字排序", "value" => "name"],
            ["label" => "评级排序", "value" => "rating"],
            ["label" => "长度排序", "value" => "length"],
            ["label" => "随机排序", "value" => "rand"]
        ];
    }

    protected static function get_pages()
    {
        if (!self::$_pages) {
            $pages = array();
            $pages[] = ['label' => '无', 'value' => ''];
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
