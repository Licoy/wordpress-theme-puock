<?php

namespace Puock\Theme\setting\options;

class OptionSeo extends BaseOptionItem{

    function get_fields(): array
    {
        return [
            'key' => 'seo',
            'label' => __('SEO搜索优化', PUOCK),
            'icon'=>'dashicons-google',
            'fields' => [
                [
                    'id' => 'seo_open',
                    'label' => __('开启SEO', PUOCK),
                    'type' => 'switch',
                    'sdt' => true,
                    'tips'=>__('若您正在使用其它的SEO插件，请取消勾选', PUOCK),
                ],
                [
                    'id' => 'web_title',
                    'label' => __('网站标题', PUOCK),
                    'sdt' => '',
                    'showRefId' => 'seo_open',
                ],
                [
                    'id' => 'web_title_2',
                    'label' => __('网站副标题', PUOCK),
                    'sdt' => '',
                    'showRefId' => 'seo_open',
                ],
                [
                    'id' => 'title_conn',
                    'label' => __('标题连接符', PUOCK),
                    'sdt' => '-',
                    'showRefId' => 'seo_open',
                    'tips'=>__('Title连接符号，例如 "-"、"|"', PUOCK),
                ],
                [
                    'id' => 'description',
                    'label' => __('网站描述', PUOCK),
                    'type' => 'textarea',
                    'sdt' => '',
                    'showRefId' => 'seo_open',
                ],
                [
                    'id' => 'keyword',
                    'label' => __('网站关键词', PUOCK),
                    'type' => 'textarea',
                    'sdt' => '',
                    'showRefId' => 'seo_open',
                ],
                [
                    'id' => 'no_category',
                    'label' => __('不显示分类链接中的<code>category</code>', PUOCK),
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'open_baidu_submit',
                    'label' => __('发布文章主动推送至百度', PUOCK),
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'baidu_submit_url',
                    'label' => __('百度推送接口地址', PUOCK),
                    'sdt' => '',
                    'showRefId' => 'open_baidu_submit',
                    'tips'=>__('百度推送接口地址，如：', PUOCK)."http://data.zz.baidu.com/urls?site=https://xxx.com&token=XXXXXX"
                ],
            ],
        ];
    }
}
