<?php

namespace Puock\Theme\setting\options;

class OptionCarousel extends BaseOptionItem
{

    function get_fields(): array
    {
        return [
            'key' => 'carousel',
            'label' => '幻灯片设置',
            'icon' => 'dashicons-format-gallery',
            'fields' => [
                [
                    'id' => '-',
                    'type' => 'info',
                    'tips' => '说明：幻灯片尺寸建议统一为2:1的比例，例如800*400，另外所有的幻灯片尺寸必须完全一致，否则会出现高度不一的情况！',
                ],
                [
                    'id' => 'index_carousel',
                    'label' => '首页幻灯片',
                    'type' => 'switch',
                    'sdt' => true,
                ],
                [
                    'id' => 'index_carousel_list',
                    'label' => '首页幻灯片列表',
                    'type' => 'dynamic-list',
                    'sdt' => [],
                    'draggable' => true,
                    'dynamicModel' => [
                        ['id' => 'title', 'label' => '幻灯标题', 'std' => ''],
                        ['id' => 'img', 'label' => '幻灯图片', 'std' => '', 'type' => 'img'],
                        ['id' => 'link', 'label' => '指向链接', 'std' => ''],
                        ['id' => 'blank', 'label' => '新标签打开', 'std' => false, 'type' => 'switch'],
                    ],
                    'showRefId' => 'index_carousel',
                ],
            ],
        ];
    }
}
