<?php

namespace Puock\Theme\setting\options;

class OptionCompany extends BaseOptionItem
{

    function get_fields(): array
    {
        return [
            'key' => 'company',
            'label' => __('企业布局', PUOCK),
            'icon' => 'dashicons-building',
            'fields' => [
                [
                    'id' => 'company_product_title',
                    'label' => __('产品介绍-大标题', PUOCK),
                    'sdt' => __('产品介绍', PUOCK),
                ],
                [
                    'id' => 'company_products',
                    'label' => __('产品列表', PUOCK),
                    'type' => 'dynamic-list',
                    'sdt' => [],
                    'draggable' => true,
                    'dynamicModel' => [
                        ['id' => 'title', 'label' => __('标题', PUOCK), 'std' => ''],
                        ['id' => 'img', 'label' => __('图片', PUOCK), 'std' => '', 'type' => 'img'],
                        ['id' => 'desc', 'label' => __('描述', PUOCK), 'std' => ''],
                        ['id' => 'link', 'label' => __('链接', PUOCK), 'std' => ''],
                    ],
                ],
                [
                    'id' => 'company_do_title',
                    'label' => __('做什么-大标题', PUOCK),
                    'sdt' => __('做什么', PUOCK),
                ],
                [
                    'id' => 'company_dos',
                    'label' => __('做什么-列表', PUOCK),
                    'type' => 'dynamic-list',
                    'sdt' => [],
                    'draggable' => true,
                    'dynamicModel' => [
                        ['id' => 'title', 'label' => __('标题', PUOCK), 'std' => ''],
                        ['id' => 'icon', 'label' => __('图标', PUOCK), 'std' => ''],
                        ['id' => 'desc', 'label' => __('描述', PUOCK), 'std' => ''],
                    ],
                ],
                [
                    'id' => 'company_do_img',
                    'label' => __('做什么-左侧展示图', PUOCK),
                    'type' => 'img',
                    'sdt' => '',
                ],
                [
                    'id' => 'company_news_open',
                    'label' => __('显示新闻', PUOCK),
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'company_news_title',
                    'label' => __('新闻模块标题', PUOCK),
                    'sdt' => __('新闻动态', PUOCK),
                    'showRefId' => 'company_news_open',
                ],
                [
                    'id' => 'company_news_cid',
                    'label' => __('新闻分类目录', PUOCK),
                    'type' => 'select',
                    'sdt' => '',
                    'multiple' => true,
                    'showRefId' => 'company_news_open',
                    'options' => self::get_category(),
                ],
                [
                    'id' => 'company_news_max_num',
                    'label' => __('新闻显示数量', PUOCK),
                    'type' => 'number',
                    'sdt' => 4,
                    'showRefId' => 'company_news_open',
                ],
                [
                    'id' => 'company_show_2box',
                    'label' => __('企业两栏CMS分类', PUOCK),
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'company_show_2box_id',
                    'label' => __('企业两栏CMS分类项', PUOCK),
                    'type' => 'select',
                    'sdt' => '',
                    'multiple' => true,
                    'showRefId' => 'company_show_2box',
                    'options' => self::get_category(),
                ],
                [
                    'id' => 'company_show_2box_num',
                    'label' => __('企业两栏CMS分类每栏显示数量', PUOCK),
                    'type' => 'number',
                    'sdt' => 6,
                    'showRefId' => 'company_show_2box',
                ],
            ],
        ];
    }
}
