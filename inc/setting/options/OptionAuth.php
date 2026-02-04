<?php

namespace Puock\Theme\setting\options;

class OptionAuth extends BaseOptionItem
{

    function get_fields(): array
    {
        return [
            'key' => 'auth',
            'label' => __('登录与授权', PUOCK),
            'icon' => 'czs-qq',
            'fields' => [
                [
                    'id' => '-',
                    'label' => __('快捷登录', PUOCK),
                    'type' => 'panel',
                    'open' => true,
                    'children' => [
                        [
                            'id' => 'open_quick_login',
                            'label' => __('开启快捷登录', PUOCK),
                            'type' => 'switch',
                            'sdt' => false,
                        ],
                        [
                            'id' => 'only_quick_oauth',
                            'label' => __('仅允许第三方登录', PUOCK),
                            'type' => 'switch',
                            'sdt' => false,
                        ],
                        [
                            'id' => 'quick_login_try_max_open',
                            'label' => __('启用登录最大尝试次数限制', PUOCK),
                            'tips' => __('超过此次数后，对应的IP将会被禁止登录', PUOCK),
                            'type' => 'switch',
                            'sdt' => false,
                        ],
                        [
                            'id' => 'quick_login_try_max_num',
                            'label' => __('登录最大尝试次数', PUOCK),
                            'type' => 'number',
                            'sdt' => 3,
                        ],
                        [
                            'id' => 'quick_login_try_max_ban_time',
                            'label' => __('登录尝试次数达到后禁止时间（分）', PUOCK),
                            'type' => 'number',
                            'sdt' => 10,
                        ],
                        [
                            'id' => 'quick_login_forget_password',
                            'label' => __('启用忘记密码找回', PUOCK),
                            'type' => 'switch',
                            'sdt' => false,
                        ],
                    ]
                ],
                [
                    'id' => '-',
                    'type' => 'panel',
                    'label' => __('后台登录保护', PUOCK),
                    'open' => pk_is_checked('login_protection'),
                    'children' => [
                        [
                            'id' => 'login_protection',
                            'label' => __('启用后台登录保护', PUOCK),
                            'type' => 'switch',
                            'sdt' => 'false',
                            'tips' => 'func:(function(args){
                            const link = `' . home_url() . '/wp-login.php?${args.data.lp_user}=${args.data.lp_pass}`
                            return `<div>' . __('启用后则用', PUOCK) . ' <a href="${link}" target="_blank">${link}</a> ' . __('的方式访问后台入口', PUOCK) . '</div>`
                        })(args)'
                        ],
                        [
                            'id' => 'lp_user',
                            'label' => __('后台登录保护参数', PUOCK),
                            'sdt' => 'admin',
                            'showRefId' => 'login_protection',
                        ],
                        [
                            'id' => 'lp_pass',
                            'label' => __('后台登录保护密码', PUOCK),
                            'sdt' => 'admin',
                            'showRefId' => 'login_protection',
                        ],
                    ]
                ],
                [
                    'id' => '-',
                    'label' => __('第三方登录回调地址提示', PUOCK),
                    'type' => 'info',
                    'infoType' => 'info',
                    'tips' => sprintf(__('通用回调地址（callback url）为: <code>%s</code>', PUOCK), home_url() . '/wp-admin/admin-ajax.php')
                ],
                [
                    'id' => 'oauth_close_register',
                    'label' => __('关闭第三方登录直接注册', PUOCK),
                    'type' => 'switch',
                    'tips' => __('开启后，若用户未绑定过账户进行第三方登录时则不会自动创建新的账户', PUOCK),
                    'std' => false
                ],
                [
                    'id' => '-',
                    'label' => 'QQ ' . __('登录配置', PUOCK),
                    'type' => 'panel',
                    'open' => pk_is_checked('oauth_qq'),
                    'tips' => '<a target="_blank" href="https://wiki.connect.qq.com/%E7%BD%91%E7%AB%99%E6%8E%A5%E5%85%A5%E6%B5%81%E7%A8%8B">' . __('申请步骤及说明', PUOCK) . '</a>',
                    'children' => [
                        [
                            'id' => 'oauth_qq',
                            'label' => 'QQ ' . __('登录', PUOCK),
                            'type' => 'switch',
                            'sdt' => 'false',
                        ],
                        [
                            'id' => 'oauth_qq_id',
                            'label' => __('QQ互联', PUOCK) . ' APP ID',
                            'sdt' => '',
                            'showRefId' => 'oauth_qq',
                        ],
                        [
                            'id' => 'oauth_qq_key',
                            'label' => __('QQ互联', PUOCK) . ' APP KEY',
                            'sdt' => '',
                            'showRefId' => 'oauth_qq',
                        ],
                    ]
                ],
                [
                    'id' => '-',
                    'label' => 'Github ' . __('登录配置', PUOCK),
                    'type' => 'panel',
                    'open' => pk_is_checked('oauth_github'),
                    'tips' => '<a target="_blank" href="https://www.ruanyifeng.com/blog/2019/04/github-oauth.html">' . __('申请步骤及说明', PUOCK) . '</a>',
                    'children' => [
                        [
                            'id' => 'oauth_github',
                            'label' => 'Github ' . __('登录', PUOCK),
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
                    'label' => __('微博', PUOCK).' ' . __('登录配置', PUOCK),
                    'type' => 'panel',
                    'open' => pk_is_checked('oauth_weibo'),
                    'tips' => '<a target="_blank" href="https://open.weibo.com/wiki/%E7%BD%91%E7%AB%99%E6%8E%A5%E5%85%A5%E4%BB%8B%E7%BB%8D">' . __('申请步骤及说明', PUOCK) . '</a>',
                    'children' => [
                        [
                            'id' => 'oauth_weibo',
                            'label' => __('微博', PUOCK) . ' ' . __('登录', PUOCK),
                            'type' => 'switch',
                            'sdt' => 'false',
                        ],
                        [
                            'id' => 'oauth_weibo_key',
                            'label' => __('微博', PUOCK) . ' App Key',
                            'sdt' => '',
                            'showRefId' => 'oauth_weibo',
                        ],
                        [
                            'id' => 'oauth_weibo_secret',
                            'label' => __('微博', PUOCK) . ' App Secret',
                            'sdt' => '',
                            'showRefId' => 'oauth_weibo',
                        ],
                    ]
                ],
                [
                    'id' => '-',
                    'label' => 'Gitee ' . __('登录配置', PUOCK),
                    'type' => 'panel',
                    'open' => pk_is_checked('oauth_gitee'),
                    'tips' => '<a target="_blank" href="https://gitee.com/api/v5/oauth_doc#/list-item-3">' . __('申请步骤及说明', PUOCK) . '</a>',
                    'children' => [
                        [
                            'id' => 'oauth_gitee',
                            'label' => 'Gitee ' . __('登录', PUOCK),
                            'type' => 'switch',
                            'sdt' => 'false',
                        ],
                        [
                            'id' => 'oauth_gitee_id',
                            'label' => 'Gitee Client ID',
                            'sdt' => '',
                            'showRefId' => 'oauth_gitee',
                        ],
                        [
                            'id' => 'oauth_gitee_secret',
                            'label' => 'Gitee Client Secret',
                            'sdt' => '',
                            'showRefId' => 'oauth_gitee',
                        ],
                    ]
                ],
                [
                    'id' => '-',
                    'label' => 'LinuxDO ' . __('登录配置', PUOCK),
                    'type' => 'panel',
                    'open' => pk_is_checked('oauth_linuxdo'),
                    'tips' => '<a target="_blank" href="https://connect.linux.do">' . __('申请步骤及说明', PUOCK) . '</a>',
                    'children' => [
                        [
                            'id' => '-',
                            'label' => __('第三方登录回调地址提示', PUOCK),
                            'type' => 'info',
                            'infoType' => 'info',
                            'tips' => sprintf(__('通用回调地址（callback url）为: <code>%s</code>', PUOCK), PUOCK_ABS_URI . '/inc/oauth/callback/linuxdo.php')
                        ],
                        [
                            'id' => 'oauth_linuxdo',
                            'label' => 'LinuxDO ' . __('登录', PUOCK),
                            'type' => 'switch',
                            'sdt' => 'false',
                        ],
                        [
                            'id' => 'oauth_linuxdo_id',
                            'label' => 'LinuxDO Client ID',
                            'sdt' => '',
                            'showRefId' => 'oauth_linuxdo',
                        ],
                        [
                            'id' => 'oauth_linuxdo_secret',
                            'label' => 'LinuxDO Client Secret',
                            'sdt' => '',
                            'showRefId' => 'oauth_linuxdo',
                        ],
                    ]
                ],
                [
                    'id' => '-',
                    'label' => __('彩虹聚合登录', PUOCK) . ' ' . __('登录配置', PUOCK),
                    'type' => 'panel',
                    'open' => pk_is_checked('oauth_ccy'),
                    'tips' => '<a target="_blank" href="https://u.cccyun.cc/doc.php">' . __('申请步骤及说明', PUOCK) . '</a>',
                    'children' => [
                        [
                            'id' => '-',
                            'label' => __('第三方登录回调地址提示', PUOCK),
                            'type' => 'info',
                            'infoType' => 'info',
                            'tips' => sprintf(__('回调域名为: <code>%s</code>', PUOCK), str_replace(['http://','https://'],'',home_url()))
                        ],
                        [
                            'id' => 'oauth_ccy',
                            'label' => __('彩虹聚合登录', PUOCK) . ' ' . __('登录', PUOCK),
                            'type' => 'switch',
                            'sdt' => 'false',
                        ],
                        [
                            'id' => 'oauth_ccy_api',
                            'label' => __('接口地址', PUOCK),
                            'tips' => __('彩虹聚合登录接口地址（不含路径），例如：https://u.cccyun.cc', PUOCK),
                            'sdt' => 'https://u.cccyun.cc',
                            'showRefId' => 'oauth_ccy',
                        ],
                        [
                            'id' => 'oauth_ccy_appid',
                            'label' => __('彩虹聚合登录 AppID', PUOCK),
                            'sdt' => '',
                            'showRefId' => 'oauth_ccy',
                        ],
                        [
                            'id' => 'oauth_ccy_appkey',
                            'label' => __('彩虹聚合登录 AppKey', PUOCK),
                            'sdt' => '',
                            'showRefId' => 'oauth_ccy',
                        ],
                        [
                            'id' => 'oauth_ccy_types',
                            'label' => __('自定义登录方式类型', PUOCK),
                            'type' => 'dynamic-list',
                            'tips' => __('每项将生成一个登录按钮，provider key 固定前缀为：ccy_{value}', PUOCK),
                            'sdt' => [
                                ['label' => __('QQ', PUOCK), 'value' => 'qq', 'icon' => 'fa-brands fa-qq', 'color_type' => 'danger'],
                                ['label' => __('微信', PUOCK), 'value' => 'wx', 'icon' => 'fa-brands fa-weixin', 'color_type' => 'success'],
                            ],
                            'draggable' => true,
                            'dynamicModel' => [
                                ['id' => 'label', 'label' => __('名称', PUOCK), 'std' => '', 'tips' => __('显示在按钮上的名称', PUOCK)],
                                ['id' => 'value', 'label' => __('对应值', PUOCK), 'std' => '', 'tips' => __('彩虹登录方式 type 值，例如 qq/wx/alipay/sina/baidu', PUOCK)],
                                ['id' => 'icon', 'label' => __('图标', PUOCK), 'std' => '', 'tips' => __('FontAwesome class 或图片 URL', PUOCK)],
                                ['id' => 'color_type', 'label' => __('主题色', PUOCK), 'std' => 'primary', 'type' => 'select', 'options' => [
                                    ['label' => 'primary', 'value' => 'primary'],
                                    ['label' => 'success', 'value' => 'success'],
                                    ['label' => 'danger', 'value' => 'danger'],
                                    ['label' => 'info', 'value' => 'info'],
                                    ['label' => 'warning', 'value' => 'warning'],
                                    ['label' => 'dark', 'value' => 'dark'],
                                    ['label' => 'secondary', 'value' => 'secondary'],
                                ]],
                            ],
                            'showRefId' => 'oauth_ccy',
                        ],
                    ]
                ],
            ],
        ];
    }
}
