<?php

namespace Puock\Theme\setting\options;

class OptionCarousel extends BaseOptionItem
{

    public static function getCarouselIndexArgs($encode = true)
    {
        $args = [
            'navigation' => [
                'nextEl' => '.index-banner-swiper .swiper-button-next',
                'prevEl' => '.index-banner-swiper .swiper-button-prev',
            ],
            'pagination' => [
                'el' => '.index-banner-swiper .swiper-pagination',
                'clickable' => true,
                'dynamicBullets' => true,
            ],
        ];
        if (!empty(pk_get_option('index_carousel_switch_effect'))) {
            $args['effect'] = pk_get_option('index_carousel_switch_effect');
        }
        if (pk_is_checked('index_carousel_mousewheel')) {
            $args['mousewheel'] = ['invert' => true];
        }
        $speed = pk_get_option('index_carousel_autoplay_speed');
        if ($speed && $speed > 0) {
            $args['autoplay'] = ['delay' => $speed, 'disableOnInteraction' => false];
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
            'label' => __('幻灯与公告', PUOCK),
            'icon' => 'dashicons-format-gallery',
            'fields' => [
                [
                    'id' => '-',
                    'type' => 'panel',
                    'label' => __('首页幻灯片', PUOCK),
                    'open' => pk_is_checked('index_carousel'),
                    'children' => [
                        [
                            'id' => '-',
                            'type' => 'info',
                            'tips' => __('说明：幻灯片尺寸建议统一为2:1的比例，例如800*400，另外所有的幻灯片尺寸必须完全一致，否则会出现高度不一的情况！', PUOCK),
                        ],
                        [
                            'id' => 'index_carousel',
                            'label' => __('启用', PUOCK),
                            'type' => 'switch',
                            'sdt' => true,
                        ],
                        [
                            'id' => 'index_carousel_mousewheel',
                            'label' => __('鼠标滚轮切换', PUOCK),
                            'type' => 'switch',
                            'sdt' => true,
                        ],
                        [
                            'id' => 'index_carousel_hide_title',
                            'label' => __('隐藏标题', PUOCK),
                            'type' => 'switch',
                            'sdt' => false,
                        ],
                        [
                            'id' => 'index_carousel_loop',
                            'label' => __('循环播放', PUOCK),
                            'type' => 'switch',
                            'sdt' => true,
                        ],
                        [
                            'id' => 'index_carousel_autoplay_speed',
                            'label' => __('自动播放速度（毫秒）', PUOCK),
                            'tips' => __('0为不自动播放', PUOCK),
                            'type' => 'number',
                            'sdt' => 3000,
                        ],
                        [
                            'id' => 'index_carousel_switch_effect',
                            'label' => __('切换效果', PUOCK),
                            'type' => 'select',
                            'sdt' => '',
                            'options' => [
                                ['label' => __('默认', PUOCK), 'value' => ''],
                                ['label' => __('淡入淡出', PUOCK), 'value' => 'fade'],
                                ['label' => __('立方体', PUOCK), 'value' => 'cube'],
                                ['label' => __('快速翻转', PUOCK), 'value' => 'flip'],
                                ['label' => __('覆盖流', PUOCK), 'value' => 'coverflow'],
                                ['label' => __('卡片', PUOCK), 'value' => 'cards']
                            ]
                        ],
                        [
                            'id' => 'index_carousel_list',
                            'label' => __('幻灯片列表', PUOCK),
                            'type' => 'dynamic-list',
                            'sdt' => [],
                            'draggable' => true,
                            'dynamicModel' => [
                                ['id' => 'title', 'label' => __('幻灯标题', PUOCK), 'std' => ''],
                                ['id' => 'img', 'label' => __('幻灯图片', PUOCK), 'std' => '', 'type' => 'img', 'tips' => __('建议尺寸2:1，所有图片大小必须一致', PUOCK)],
                                ['id' => 'link', 'label' => __('指向链接', PUOCK), 'std' => ''],
                                ['id' => 'blank', 'label' => __('新标签打开', PUOCK), 'std' => false, 'type' => 'switch'],
                                ['id' => 'hide', 'label' => __('隐藏', PUOCK), 'type' => 'switch', 'sdt' => false, 'tips' => __('隐藏后将不会显示', PUOCK)],
                            ],
                        ],
                    ]
                ],
                [
                    'id' => '-',
                    'type' => 'panel',
                    'label' => __('全局公告', PUOCK),
                    'open' => pk_is_checked('global_notice'),
                    'children' => [
                        [
                            'id' => 'global_notice',
                            'label' => __('启用', PUOCK),
                            'type' => 'switch',
                            'sdt' => false,
                        ],
                        [
                            'id' => 'global_notice_autoplay_speed',
                            'label' => __('自动播放速度（毫秒）', PUOCK),
                            'tips' => __('0为不自动播放', PUOCK),
                            'type' => 'number',
                            'sdt' => 3000,
                        ],
                        [
                            'id' => 'global_notice_list',
                            'label' => __('公告列表', PUOCK),
                            'type' => 'dynamic-list',
                            'sdt' => [],
                            'draggable' => true,
                            'dynamicModel' => [
                                ['id' => 'title', 'label' => __('公告标题(支持HTML)', PUOCK), 'type' => 'textarea', 'std' => ''],
                                ['id' => 'link', 'label' => __('指向链接(可空)', PUOCK), 'std' => ''],
                                ['id' => 'icon', 'label' => __('图标class(可空)', PUOCK), 'std' => ''],
                                ['id' => 'hide', 'label' => __('隐藏', PUOCK), 'type' => 'switch', 'sdt' => false, 'tips' => __('隐藏后将不会显示', PUOCK)],
                            ],
                        ],
                    ]
                ]
            ],
        ];
    }
}
