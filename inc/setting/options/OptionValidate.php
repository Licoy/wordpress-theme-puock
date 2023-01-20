<?php

namespace Puock\Theme\setting\options;

class OptionValidate extends BaseOptionItem
{

    function get_fields(): array
    {
        return [
            'key' => 'validate',
            'label' => __('验证及防刷', PUOCK),
            'icon' => 'dashicons-shield',
            'fields' => [
                [
                    'id' => 'vd_type',
                    'label' => __('验证码类型', PUOCK),
                    'type' => 'radio',
                    'sdt' => 'img',
                    'radioType' => 'button',
                    'options' => [
                        [
                            'value' => 'img',
                            'label' => __('图形验证码', PUOCK),
                        ],
                        [
                            'value' => 'gt',
                            'label' => __('极验验证码', PUOCK),
                        ],
                    ],
                ],
                [
                    'id' => 'vd_comment',
                    'label' => __('启用评论验证', PUOCK),
                    'type' => 'switch',
                    'sdt' => false,
                ],
                [
                    'id' => '-',
                    'type' => 'panel',
                    'label' => __('极验验证码', PUOCK),
                    'open' => true,
                    'children' => [
                        [
                            'id' => 'vd_gt_id',
                            'label' => __('极验验证ID', PUOCK),
                            'sdt' => ''
                        ],
                        [
                            'id' => 'vd_gt_key',
                            'label' => __('极验验证Key', PUOCK),
                            'sdt' => ''
                        ]
                    ]
                ]
            ],
        ];
    }
}
