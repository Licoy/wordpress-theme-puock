<?php

class OptionCompany extends BaseOptionItem{

    function get_fields(): array
    {
        return [
            'key' => 'company',
            'label' => '企业布局',
            'icon'=>'dashicons-building',
            'fields' => [
                [
                    'id' => 'company_product_title',
                    'label' => '产品介绍-大标题',
                    'sdt' => '产品介绍',
                ],
                [
                    'id' => 'company_products',
                    'label' => '产品列表',
                    'type' => 'dynamic-list',
                    'sdt' => [],
                    'dynamicModel'=>[
                        ['id' => 'title', 'label' => '标题', 'std' => ''],
                        ['id' => 'img', 'label' => '图片', 'std' => '', 'type' => 'img'],
                        ['id' => 'desc', 'label' => '描述', 'std' => ''],
                        ['id' => 'link', 'label' => '链接', 'std' => ''],
                    ],
                ],
                [
                    'id' => 'company_do_title',
                    'label' => '做什么-大标题',
                    'sdt' => '做什么',
                ],
                [
                    'id' => 'company_dos',
                    'label' => '做什么-列表',
                    'type' => 'dynamic-list',
                    'sdt' => [],
                    'dynamicModel'=>[
                        ['id' => 'title', 'label' => '标题', 'std' => ''],
                        ['id' => 'icon', 'label' => '图标', 'std' => ''],
                        ['id' => 'desc', 'label' => '描述', 'std' => ''],
                    ],
                ],
                [
                    'id' => 'company_do_img',
                    'label' => '做什么-左侧展示图',
                    'type' => 'img',
                    'sdt' => '',
                ],
                [
                    'id' => 'company_news_open',
                    'label' => '显示新闻',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'company_news_title',
                    'label' => '新闻模块标题',
                    'sdt' => '新闻动态',
                    'showRefId' => 'company_news_open',
                ],
                [
                    'id' => 'company_news_cid',
                    'label' => '新闻分类目录',
                    'type' => 'select',
                    'sdt' => '',
                    'multiple' => true,
                    'showRefId' => 'company_news_open',
                    'options' => self::get_category(),
                ],
                [
                    'id' => 'company_news_max_num',
                    'label' => '新闻显示数量',
                    'type' => 'number',
                    'sdt' => 4,
                    'showRefId' => 'company_news_open',
                ],
                [
                    'id' => 'company_show_2box',
                    'label' => '两栏CMS分类',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'company_show_2box_id',
                    'label' => '两栏CMS分类项',
                    'type' => 'select',
                    'sdt' => '',
                    'multiple' => true,
                    'showRefId' => 'company_show_2box',
                    'options' => self::get_category(),
                ],
                [
                    'id' => 'company_show_2box_num',
                    'label' => '两栏CMS分类每栏显示数量',
                    'type' => 'number',
                    'sdt' => 6,
                    'showRefId' => 'company_show_2box',
                ],
            ],
        ];
    }
}
