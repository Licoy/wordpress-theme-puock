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
                ],
                [
                    'id' => 'vd_kwd_access_reject',
                    'label' => __('恶意统计关键字访问屏蔽', PUOCK),
                    'type' => 'switch',
                    'tips' => __('开启后，将会使含有指定关键字的query参数请求得到403拒绝访问，防止站点统计的恶意刷量', PUOCK),
                    'sdt' => false,
                ],
                [
                    'id' => 'vd_kwd_access_reject_list',
                    'label' => __('恶意统计关键字访问屏蔽参数', PUOCK),
                    'tips' => __('多个之间使用半角<code>,</code>进行分隔', PUOCK),
                    'sdt' => 'wd,str',
                ],
            ],
        ];
    }
}
