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
        if(!empty(pk_get_option('index_carousel_switch_effect'))){
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
            'label' => '幻灯与公告',
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
                            'label' => '启用幻灯片',
                            'type' => 'switch',
                            'sdt' => true,
                        ],
                        [
                            'id' => 'index_carousel_mousewheel',
                            'label' => '鼠标左右滚动',
                            'type' => 'switch',
                            'sdt' => true,
                        ],
                        [
                            'id' => 'index_carousel_hide_title',
                            'label' => '隐藏标题',
                            'type' => 'switch',
                            'sdt' => false,
                        ],
                        [
                            'id' => 'index_carousel_loop',
                            'label' => '循环播放',
                            'type' => 'switch',
                            'sdt' => true,
                        ],
                        [
                            'id' => 'index_carousel_autoplay_speed',
                            'label' => '自动播放速度（毫秒）',
                            'tips' => '填写0则不自动播放',
                            'type' => 'number',
                            'sdt' => 3000,
                        ],
                        [
                            'id'=>'index_carousel_switch_effect',
                            'label' => '切换效果',
                            'type' => 'select',
                            'sdt' => '',
                            'options'=>[
                                ['label'=>'默认','value'=>''],
                                ['label'=>'淡入淡出','value'=>'fade'],
                                ['label'=>'立方体','value'=>'cube'],
                                ['label'=>'快速翻转','value'=>'flip'],
                                ['label'=>'覆盖流','value'=>'coverflow'],
                                ['label'=>'卡片','value'=>'cards']
                            ]
                        ],
                        [
                            'id' => 'index_carousel_list',
                            'label' => '幻灯片列表',
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
                [
                    'id' => '-',
                    'type' => 'panel',
                    'label' => '全局公告',
                    'open' => pk_is_checked('global_notice'),
                    'children' => [
                        [
                            'id' => 'global_notice',
                            'label' => '开启全局公告',
                            'type' => 'switch',
                            'sdt' => false,
                        ],
                        [
                            'id' => 'global_notice_autoplay_speed',
                            'label' => '自动播放速度（毫秒）',
                            'tips' => '填写0则不自动播放',
                            'type' => 'number',
                            'sdt' => 3000,
                        ],
                        [
                            'id' => 'global_notice_list',
                            'label' => '公告列表',
                            'type' => 'dynamic-list',
                            'sdt' => [],
                            'draggable' => true,
                            'dynamicModel' => [
                                ['id' => 'title', 'label' => '公告标题(支持HTML)', 'type'=>'textarea', 'std' => ''],
                                ['id' => 'link', 'label' => '指向链接(可空)', 'std' => ''],
                                ['id' => 'icon', 'label' => '图标class(可空)', 'std' => ''],
                                ['id' => 'hide', 'label' => '隐藏', 'type' => 'switch', 'sdt' => false, 'tips' => '隐藏后将不会显示'],
                            ],
                        ],
                    ]
                ]
            ],
        ];
    }
}
