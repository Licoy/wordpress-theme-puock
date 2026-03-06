<?php

namespace Puock\Theme\setting\options;

class OptionEmail extends BaseOptionItem{

    function get_fields(): array
    {
        return [
            'key' => 'email',
            'label' => __('SMTP邮件', PUOCK),
            'icon'=>'dashicons-email-alt',
            'fields' => [
                [
                    'id' => 'smtp_open',
                    'label' => __('开启SMTP', PUOCK),
                    'type' => 'switch',
                    'sdt' => 'false',
                    'tips'=>__('开启会覆盖WordPress默认配置', PUOCK),
                ],
                [
                    'id' => 'smtp_ssl',
                    'label' => __('SMTP加密', PUOCK),
                    'type' => 'switch',
                    'sdt' => 'false',
                    'showRefId' => 'smtp_open',
                ],
                [
                    'id' => 'smtp_form',
                    'label' => __('发件人邮箱', PUOCK),
                    'sdt' => '',
                    'showRefId' => 'smtp_open',
                ],
                [
                    'id' => 'smtp_host',
                    'label' => __('SMTP服务器', PUOCK),
                    'sdt' => '',
                    'showRefId' => 'smtp_open',
                    'tips'=>__('如163邮箱的为：smtp.163.com', PUOCK)
                ],
                [
                    'id' => 'smtp_port',
                    'label' => __('SMTP端口', PUOCK),
                    'sdt' => '',
                    'showRefId' => 'smtp_open',
                ],
                [
                    'id' => 'smtp_u',
                    'label' => __('SMTP账户', PUOCK),
                    'sdt' => '',
                    'showRefId' => 'smtp_open',
                ],
                [
                    'id' => 'smtp_p',
                    'label' => __('SMTP密码', PUOCK),
                    'sdt' => '',
                    'showRefId' => 'smtp_open',
                    'tips'=>__('一般非邮箱账号直接密码，而是对应的平台的POP3/SMTP授权码', PUOCK),
                ],
                [
                    'id' => '-',
                    'type' => 'panel',
                    'label' => __('邮件通知模板', PUOCK),
                    'open' => false,
                    'children' => [
                        [
                            'id' => 'email_header_img',
                            'label' => __('邮件头图', PUOCK),
                            'type' => 'img',
                            'sdt' => '',
                            'tips' => __('设置邮件通知顶部的头图URL（留空不显示）', PUOCK),
                        ],
                        [
                            'id' => 'email_primary_color',
                            'label' => __('邮件主色调', PUOCK),
                            'type' => 'text',
                            'sdt' => '#007bff',
                            'tips' => __('邮件头部和链接的颜色，默认为 #007bff', PUOCK),
                        ],
                    ]
                ],
            ],
        ];
    }
}
