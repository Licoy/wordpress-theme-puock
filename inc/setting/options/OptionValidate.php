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
                    'id' => 'vd_type',
                    'label' => '验证码类型',
                    'type' => 'radio',
                    'sdt' => 'img',
                    'radioType' => 'button',
                    'options' => [
                        [
                            'value' => 'img',
                            'label' => '图形验证码',
                        ],
                        [
                            'value' => 'gt',
                            'label' => '极验验证码',
                        ],
                    ],
                ],
                [
                    'id' => 'vd_comment',
                    'label' => '启用评论验证',
                    'type' => 'switch',
                    'sdt' => false,
                ],
                [
                    'id' => '-',
                    'type' => 'panel',
                    'label' => '极验验证码',
                    'open' => true,
                    'children' => [
                        [
                            'id' => 'vd_gt_id',
                            'label' => '极验验证ID',
                            'sdt' => ''
                        ],
                        [
                            'id' => 'vd_gt_key',
                            'label' => '极验验证Key',
                            'sdt' => ''
                        ]
                    ]
                ]
            ],
        ];
    }
}
