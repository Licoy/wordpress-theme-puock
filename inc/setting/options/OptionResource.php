<?php

namespace Puock\Theme\setting\options;

class OptionResource extends BaseOptionItem{

    function get_fields(): array
    {
        return [
            'key' => 'resource',
            'label' => '资源或更新',
            'icon'=>'dashicons-cloud-saved',
            'fields' => [
                [
                    'id' => 'static_load_origin',
                    'label' => '主题静态资源加载源',
                    'type' => 'radio',
                    'sdt' => 'self',
                    'options' => [
                        [
                            'value' => 'self',
                            'label' => '本地',
                        ],
                        [
                            'value' => 'jsdelivr',
                            'label' => 'JSDelivrCDN',
                        ],
                        [
                            'value' => 'jsdelivr-fastly',
                            'label' => 'JSDelivrFastly',
                        ],
                        [
                            'value' => 'jsdelivr-testingcf',
                            'label' => 'JSDelivrTestingcf',
                        ],
                        [
                            'value' => 'jsdelivr-gcore',
                            'label' => 'JSDelivrGcore',
                        ],
                        [
                            'value' => 'custom',
                            'label' => '自定义（在下方一栏中填入）',
                        ],
                    ],
                ],
                [
                    'id' => 'custom_static_load_origin',
                    'label' => '自定义静态资源加载URI',
                    'sdt' => '',
                    'tips'=>'需填写完整地址，如https://example.com/puock，路径需要指向到可以访问主题根目录为准'
                ],
                [
                    'id' => 'update_server',
                    'label' => '主题在线更新源',
                    'type' => 'radio',
                    'sdt' => 'worker',
                    'options' => [
                        [
                            'value' => 'worker',
                            'label' => '官方代理',
                        ],
                        [
                            'value' => 'github',
                            'label' => 'Github',
                        ],
                        [
                            'value' => 'fastgit',
                            'label' => 'fastgit',
                        ]
                    ],
                ],
                [
                    'id' => 'update_server_check_period',
                    'label' => '主题更新检测频率',
                    'type' => 'number',
                    'sdt' => 6,
                    'tips'=>'单位为小时'
                ],
            ],
        ];
    }
}
