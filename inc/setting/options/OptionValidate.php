<?php

namespace Puock\Theme\setting\options;

class OptionValidate extends BaseOptionItem
{

    function get_fields(): array
    {
        return [
            'key' => 'validate',
            'label' => '验证及防刷',
            'icon' => 'dashicons-shield',
            'fields' => [
                [
                    'id' => 'vd_comment',
                    'label' => '启用评论防刷验证',
                    'type' => 'switch',
                    'sdt' => 'false',
                    'tips' => '图形验证码'
                ]
            ],
        ];
    }
}
