<?php

namespace Puock\Theme\setting\options;

class OptionExtend extends BaseOptionItem
{

    function get_fields(): array
    {
        return [
            'key' => 'extend',
            'label' => '扩展及兼容',
            'icon' => 'dashicons-admin-plugins',
            'badge' => ['dot'=>true,'processing'=>true],
            'fields' => [
                [
                    'id' => 'office_mp_support',
                    'label' => 'Puock官方小程序支持',
                    'type' => 'switch',
                    'value' => defined('PUOCK_MP_VERSION'),
                    'tips' => "Puock官方小程序支持，此选项安装小程序插件后会自动开启，如需关闭请在小程序插件中关闭 （<a target='_blank' href='https://licoy.cn/puock-mp.html'>了解小程序？</a>）",
                    'disabled' => true,
                    'badge' => ['value'=>'🔥 热门 & 推荐']
                ],
                [
                    'id' => 'strawberry_icon',
                    'label' => '草莓图标库',
                    'type' => 'switch',
                    'sdt' => false,
                    'tips' => "开启之后会在前台加载草莓图标库支持"
                ],
                [
                    'id' => 'dplayer',
                    'label' => 'DPlayer支持',
                    'type' => 'switch',
                    'sdt' => false,
                    'tips' => "开启之后会将视频播放器替换为dplayer"
                ],
            ],
        ];
    }
}
