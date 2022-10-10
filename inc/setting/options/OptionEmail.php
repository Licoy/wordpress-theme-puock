<?php

namespace Puock\Theme\setting\options;

class OptionEmail extends BaseOptionItem{

    function get_fields(): array
    {
        return [
            'key' => 'email',
            'label' => 'SMTP邮件',
            'icon'=>'dashicons-email-alt',
            'fields' => [
                [
                    'id' => 'smtp_open',
                    'label' => '自定义SMTP邮件',
                    'type' => 'switch',
                    'sdt' => 'false',
                    'tips'=>'开启会覆盖WordPress默认配置'
                ],
                [
                    'id' => 'smtp_ssl',
                    'label' => '启用SSL安全模式',
                    'type' => 'switch',
                    'sdt' => 'false',
                    'showRefId' => 'smtp_open',
                ],
                [
                    'id' => 'smtp_form',
                    'label' => '发件人地址',
                    'sdt' => '',
                    'showRefId' => 'smtp_open',
                ],
                [
                    'id' => 'smtp_host',
                    'label' => 'SMTP服务器',
                    'sdt' => '',
                    'showRefId' => 'smtp_open',
                    'tips'=>'如163邮箱的为：smtp.163.com'
                ],
                [
                    'id' => 'smtp_port',
                    'label' => 'SMTP服务器端口',
                    'sdt' => '',
                    'showRefId' => 'smtp_open',
                ],
                [
                    'id' => 'smtp_u',
                    'label' => '邮箱账号',
                    'sdt' => '',
                    'showRefId' => 'smtp_open',
                ],
                [
                    'id' => 'smtp_p',
                    'label' => '邮箱密码',
                    'sdt' => '',
                    'showRefId' => 'smtp_open',
                    'tips'=>'一般非邮箱账号直接密码，而是对应的平台的POP3/SMTP授权码'
                ],
            ],
        ];
    }
}
