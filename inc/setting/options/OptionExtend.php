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
            'fields' => [
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
