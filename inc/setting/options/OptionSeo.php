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
