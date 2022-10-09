<?php

class OptionOAuth extends BaseOptionItem{

    function get_fields(): array
    {
        return [
            'key' => 'oauth',
            'label' => '第三方登录',
            'icon'=>'dashicons-facebook',
            'fields' => [
                [
                    'id' => '-',
                    'label' => '第三方登录回调地址提示',
                    'type' => 'info',
                    'infoType' => 'info',
                    'tips'=>'通用回调地址（callback url）为: <code>'.home_url().'/wp-admin/admin-ajax.php</code>'
                ],
                [
                    'id' => 'oauth_qq',
                    'label' => 'QQ登录',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'oauth_qq_id',
                    'label' => 'QQ互联 APP ID',
                    'sdt' => '',
                    'showRefId' => 'oauth_qq',
                ],
                [
                    'id' => 'oauth_qq_key',
                    'label' => 'QQ互联 APP KEY',
                    'sdt' => '',
                    'showRefId' => 'oauth_qq',
                ],
                [
                    'id' => 'oauth_github',
                    'label' => 'Github登录',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'oauth_github_id',
                    'label' => 'Github Client ID',
                    'sdt' => '',
                    'showRefId' => 'oauth_github',
                ],
                [
                    'id' => 'oauth_github_secret',
                    'label' => 'Github Client Secret',
                    'sdt' => '',
                    'showRefId' => 'oauth_github',
                ],
                [
                    'id' => 'oauth_weibo',
                    'label' => '微博登录',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'oauth_weibo_key',
                    'label' => '微博 App Key',
                    'sdt' => '',
                    'showRefId' => 'oauth_weibo',
                ],
                [
                    'id' => 'oauth_weibo_secret',
                    'label' => '微博 App Secret',
                    'sdt' => '',
                    'showRefId' => 'oauth_weibo',
                ],
                [
                    'id' => 'oauth_gitee',
                    'label' => 'Gitee（码云）登录',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'oauth_gitee_id',
                    'label' => 'Gitee（码云）Client ID',
                    'sdt' => '',
                    'showRefId' => 'oauth_gitee',
                ],
                [
                    'id' => 'oauth_gitee_secret',
                    'label' => 'Gitee（码云）Client Secret',
                    'sdt' => '',
                    'showRefId' => 'oauth_gitee',
                ],
            ],
        ];
    }
}
