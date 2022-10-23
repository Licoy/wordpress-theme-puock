<?php

namespace Puock\Theme\setting\options;

class OptionOAuth extends BaseOptionItem
{

    function get_fields(): array
    {
        return [
            'key' => 'oauth',
            'label' => '第三方登录',
            'icon' => 'dashicons-facebook',
            'fields' => [
                [
                    'id' => '-',
                    'label' => '第三方登录回调地址提示',
                    'type' => 'info',
                    'infoType' => 'info',
                    'tips' => '通用回调地址（callback url）为: <code>' . home_url() . '/wp-admin/admin-ajax.php</code>'
                ],
                [
                    'id' => 'oauth_close_register',
                    'label' => '关闭第三方登录直接注册',
                    'type' => 'switch',
                    'tips' => '开启后，若用户未绑定过账户进行第三方登录时则不会自动创建新的账户',
                    'std' => false
                ],
                [
                    'id' => '-',
                    'label' => 'QQ登录配置',
                    'type' => 'panel',
                    'open' => pk_is_checked('oauth_qq'),
                    'tips' => '<a target="_blank" href="https://wiki.connect.qq.com/%E7%BD%91%E7%AB%99%E6%8E%A5%E5%85%A5%E6%B5%81%E7%A8%8B">申请步骤及说明</a>',
                    'children' => [
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
                    ]
                ],
                [
                    'id' => '-',
                    'label' => 'Github登录配置',
                    'type' => 'panel',
                    'open' => pk_is_checked('oauth_github'),
                    'tips' => '<a target="_blank" href="https://www.ruanyifeng.com/blog/2019/04/github-oauth.html">申请步骤及说明</a>',
                    'children' => [
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
                    ]
                ],
                [
                    'id' => '-',
                    'label' => '微博登录配置',
                    'type' => 'panel',
                    'open' => pk_is_checked('oauth_weibo'),
                    'tips' => '<a target="_blank" href="https://open.weibo.com/wiki/%E7%BD%91%E7%AB%99%E6%8E%A5%E5%85%A5%E4%BB%8B%E7%BB%8D">申请步骤及说明</a>',
                    'children' => [
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
                    ]
                ],
                [
                    'id' => '-',
                    'label' => 'Gitee登录配置',
                    'type' => 'panel',
                    'open' => pk_is_checked('oauth_gitee'),
                    'tips' => '<a target="_blank" href="https://gitee.com/api/v5/oauth_doc#/list-item-3">申请步骤及说明</a>',
                    'children' => [
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
                    ]
                ],
            ],
        ];
    }
}
