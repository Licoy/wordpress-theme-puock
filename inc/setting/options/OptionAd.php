<?php

namespace Puock\Theme\setting\options;

class OptionAd extends BaseOptionItem{

    function get_fields(): array
    {
        return [
            'key' => 'ad',
            'label' => __('广告设置', PUOCK),
            'icon'=>'dashicons-megaphone',
            'fields' => [
                [
                    'id' => 'ad_g_top_c',
                    'label' => __('全站顶部广告', PUOCK),
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'ad_g_top',
                    'label' => __('全站顶部广告内容', PUOCK),
                    'type' => 'textarea',
                    'sdt' => '',
                    'showRefId' => 'ad_g_top_c',
                ],
                [
                    'id' => 'ad_g_bottom_c',
                    'label' => __('全站底部广告', PUOCK),
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'ad_g_bottom',
                    'label' => __('全站底部广告内容', PUOCK),
                    'type' => 'textarea',
                    'sdt' => '',
                    'showRefId' => 'ad_g_bottom_c',
                ],
                [
                    'id' => 'ad_page_t_c',
                    'label' => __('文章内顶部广告', PUOCK),
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'ad_page_t',
                    'label' => __('文章内顶部广告内容', PUOCK),
                    'type' => 'textarea',
                    'sdt' => '',
                    'showRefId' => 'ad_page_t_c',
                    'tips'=>__('显示在面包屑导航下', PUOCK)
                ],
                [
                    'id' => 'ad_page_c_b_c',
                    'label' => __('文章内容底部广告', PUOCK),
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'ad_page_c_b',
                    'label' => __('文章内容底部广告内容', PUOCK),
                    'type' => 'textarea',
                    'sdt' => '',
                    'showRefId' => 'ad_page_c_b_c',
                    'tips'=>__('会显示在文章结尾处', PUOCK)
                ],
                [
                    'id' => 'ad_comment_t_c',
                    'label' => __('评论上方广告', PUOCK),
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'ad_comment_t',
                    'label' => __('评论上方广告内容', PUOCK),
                    'type' => 'textarea',
                    'sdt' => '',
                    'showRefId' => 'ad_comment_t_c',
                ],
            ],
        ];
    }
}
