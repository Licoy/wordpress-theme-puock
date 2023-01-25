<?php

namespace Puock\Theme\setting\options;

class OptionExtend extends BaseOptionItem
{

    function get_fields(): array
    {
        return [
            'key' => 'extend',
            'label' => __('扩展及兼容', PUOCK),
            'icon' => 'dashicons-admin-plugins',
            'fields' => [
                [
                    'id' => 'office_mp_support',
                    'label' => __('Puock官方小程序支持', PUOCK),
                    'type' => 'switch',
                    'value' => defined('PUOCK_MP_VERSION'),
                    'tips' => __('Puock官方小程序支持，此选项安装小程序插件后会自动开启，如需关闭请在小程序插件中关闭', PUOCK) . " （<a target='_blank' href='https://licoy.cn/puock-mp.html'>" . __('了解小程序?', PUOCK) . "</a>）",
                    'disabled' => true,
                    'badge' => ['value' => '🔥 ' . __('热门 & 推荐', PUOCK)]
                ],
                [
                    'id' => 'strawberry_icon',
                    'label' => __('Strawberry图标库', PUOCK),
                    'type' => 'switch',
                    'sdt' => false,
                    'tips' => __('开启之后会在前台加载Strawberry图标库支持', PUOCK)
                ],
                [
                    'id' => 'dplayer',
                    'label' => 'DPlayer' . ' ' . __('支持', PUOCK),
                    'type' => 'switch',
                    'sdt' => false,
                    'tips' => __('开启之后会在前台加载DPlayer支持', PUOCK)
                ],
            ],
        ];
    }
}
