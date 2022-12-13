<?php

namespace Puock\Theme\setting\options;

class OptionCarousel extends BaseOptionItem
{

    public static function getCarouselIndexArgs($encode = true)
    {
        $args = [];
        if (pk_is_checked('index_carousel_mousewheel')) {
            $args['mousewheel'] = ['invert' => true];
        }
        $speed = pk_get_option('index_carousel_autoplay_speed');
        if ($speed && $speed > 0) {
            $args['autoplay'] = ['delay' => $speed];
        }
        if (pk_is_checked('index_carousel_loop')) {
            $args['loop'] = true;
        }
        return $encode ? json_encode($args) : $args;
    }

    function get_fields(): array
    {
        return [
            'key' => 'carousel',
            'label' => '幻灯片设置',
            'icon' => 'dashicons-format-gallery',
            'fields' => [
                [
                    'id' => '-',
                    'type' => 'panel',
                    'label' => '首页幻灯片',
                    'open' => pk_is_checked('index_carousel'),
                    'children' => [
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
                            'id' => 'index_carousel_mousewheel',
                            'label' => '首页幻灯片鼠标左右滚动',
                            'type' => 'switch',
                            'sdt' => true,
                        ],
                        [
                            'id' => 'index_carousel_hide_title',
                            'label' => '隐藏幻灯片标题',
                            'type' => 'switch',
                            'sdt' => false,
                        ],
                        [
                            'id' => 'index_carousel_loop',
                            'label' => '首页幻灯片循环播放',
                            'type' => 'switch',
                            'sdt' => true,
                        ],
                        [
                            'id' => 'index_carousel_autoplay_speed',
                            'label' => '首页幻灯片自动播放速度（毫秒）',
                            'tips' => '填写0则不自动播放',
                            'type' => 'number',
                            'sdt' => 3000,
                        ],
                        [
                            'id' => 'index_carousel_list',
                            'label' => '首页幻灯片列表',
                            'type' => 'dynamic-list',
                            'sdt' => [],
                            'draggable' => true,
                            'dynamicModel' => [
                                ['id' => 'title', 'label' => '幻灯标题', 'std' => ''],
                                ['id' => 'img', 'label' => '幻灯图片', 'std' => '', 'type' => 'img', 'tips' => '建议尺寸2:1，所有图片大小必须一致'],
                                ['id' => 'link', 'label' => '指向链接', 'std' => ''],
                                ['id' => 'blank', 'label' => '新标签打开', 'std' => false, 'type' => 'switch'],
                                ['id' => 'hide', 'label' => '隐藏', 'type' => 'switch', 'sdt' => false, 'tips' => '隐藏后将不会显示'],
                            ],
                        ],
                    ]
                ],
            ],
        ];
    }
}
