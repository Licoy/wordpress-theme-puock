<?php

namespace Puock\Theme\setting\options;

class OptionAd extends BaseOptionItem{

    function get_fields(): array
    {
        return [
            'key' => 'ad',
            'label' => '广告配置',
            'icon'=>'dashicons-megaphone',
            'fields' => [
                [
                    'id' => 'ad_g_top_c',
                    'label' => '全站顶部广告',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'ad_g_top',
                    'label' => '全站顶部广告内容',
                    'type' => 'textarea',
                    'sdt' => '',
                    'showRefId' => 'ad_g_top_c',
                ],
                [
                    'id' => 'ad_g_bottom_c',
                    'label' => '全站底部广告',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'ad_g_bottom',
                    'label' => '全站底部广告内容',
                    'type' => 'textarea',
                    'sdt' => '',
                    'showRefId' => 'ad_g_bottom_c',
                ],
                [
                    'id' => 'ad_page_t_c',
                    'label' => '文章内顶部广告',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'ad_page_t',
                    'label' => '文章内顶部广告内容',
                    'type' => 'textarea',
                    'sdt' => '',
                    'showRefId' => 'ad_page_t_c',
                    'tips'=>'显示在面包屑导航下'
                ],
                [
                    'id' => 'ad_page_c_b_c',
                    'label' => '文章内容底部广告',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'ad_page_c_b',
                    'label' => '文章内容底部广告内容',
                    'type' => 'textarea',
                    'sdt' => '',
                    'showRefId' => 'ad_page_c_b_c',
                    'tips'=>'会显示在文章结尾处'
                ],
                [
                    'id' => 'ad_comment_t_c',
                    'label' => '评论上方广告',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'ad_comment_t',
                    'label' => '评论上方广告内容',
                    'type' => 'textarea',
                    'sdt' => '',
                    'showRefId' => 'ad_comment_t_c',
                ],
            ],
        ];
    }
}
