<?php

class OptionSeo extends BaseOptionItem{

    function get_fields(): array
    {
        return [
            'key' => 'seo',
            'label' => 'SEO搜索优化',
            'icon'=>'dashicons-google',
            'fields' => [
                [
                    'id' => 'seo_open',
                    'label' => 'SEO功能',
                    'type' => 'switch',
                    'sdt' => true,
                    'tips'=>"若您正在使用其它的SEO插件，请取消勾选"
                ],
                [
                    'id' => 'web_title',
                    'label' => '网站Title',
                    'sdt' => '',
                    'showRefId' => 'seo_open',
                ],
                [
                    'id' => 'web_title_2',
                    'label' => '网站首页副标题',
                    'sdt' => '',
                    'showRefId' => 'seo_open',
                ],
                [
                    'id' => 'title_conn',
                    'label' => 'Title连接符',
                    'sdt' => '-',
                    'showRefId' => 'seo_open',
                    'tips'=>'Title连接符号，例如 "-"、"|"'
                ],
                [
                    'id' => 'description',
                    'label' => '网站描述',
                    'type' => 'textarea',
                    'sdt' => '',
                    'showRefId' => 'seo_open',
                ],
                [
                    'id' => 'keyword',
                    'label' => '网站关键词',
                    'type' => 'textarea',
                    'sdt' => '',
                    'showRefId' => 'seo_open',
                ],
                [
                    'id' => 'no_category',
                    'label' => '不显示分类链接中的"category"',
                    'type' => 'switch',
                    'sdt' => 'false',
                    'showRefId' => 'seo_open',
                ],
                [
                    'id' => 'open_baidu_submit',
                    'label' => '发布文章主动推送至百度',
                    'type' => 'switch',
                    'sdt' => 'false',
                    'showRefId' => 'seo_open',
                ],
                [
                    'id' => 'baidu_submit_url',
                    'label' => '百度推送接口地址',
                    'sdt' => '',
                    'showRefId' => 'open_baidu_submit',
                    'tips'=>"百度推送接口地址，如：http://data.zz.baidu.com/urls?site=https://xxx.com&token=XXXXXX"
                ],
            ],
        ];
    }
}
