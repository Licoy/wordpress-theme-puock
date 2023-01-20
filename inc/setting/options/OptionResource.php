<?php

namespace Puock\Theme\setting\options;

class OptionResource extends BaseOptionItem{

    function get_fields(): array
    {
        return [
            'key' => 'resource',
            'label' => __('资源与更新', PUOCK),
            'icon'=>'dashicons-cloud-saved',
            'fields' => [
                [
                    'id' => 'static_load_origin',
                    'label' => __('主题静态资源加载源', PUOCK),
                    'type' => 'radio',
                    'sdt' => 'self',
                    'options' => [
                        [
                            'value' => 'self',
                            'label' => __('本地', PUOCK),
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
                            'label' => __('自定义（在下方一栏中填入）', PUOCK),
                        ],
                    ],
                ],
                [
                    'id' => 'custom_static_load_origin',
                    'label' => __('自定义静态资源加载URI', PUOCK),
                    'sdt' => '',
                    'tips'=>__('需填写完整地址，如<code>https://example.com/puock</code>，路径需要指向到可以访问主题根目录为准', PUOCK)
                ],
                [
                    'id' => 'update_server',
                    'label' => __('主题在线更新源', PUOCK),
                    'type' => 'radio',
                    'sdt' => 'worker',
                    'options' => [
                        [
                            'value' => 'worker',
                            'label' => __('官方代理', PUOCK),
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
                    'label' => __('主题更新检测频率', PUOCK),
                    'type' => 'number',
                    'sdt' => 6,
                    'tips'=>__('单位为小时', PUOCK),
                ],
            ],
        ];
    }
}
