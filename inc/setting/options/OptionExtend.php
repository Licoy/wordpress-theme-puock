<?php

namespace Puock\Theme\setting\options;

class OptionExtend extends BaseOptionItem
{

    function get_fields(): array
    {
        return [
            'key' => 'extend',
            'label' => __('扩展及兼容', PUOCK),
            'icon' => 'dashicons-admin-plugins',
            'fields' => [
                [
                    'id' => 'office_mp_support',
                    'label' => __('Puock官方小程序支持', PUOCK),
                    'type' => 'switch',
                    'value' => defined('PUOCK_MP_VERSION'),
                    'tips' => __('Puock官方小程序支持，此选项安装小程序插件后会自动开启，如需关闭请在小程序插件中关闭', PUOCK) . " （<a target='_blank' href='https://licoy.cn/puock-mp.html'>" . __('了解小程序?', PUOCK) . "</a>）",
                    'disabled' => true,
                    'badge' => ['value' => '🔥 ' . __('热门 & 推荐', PUOCK)]
                ],
                [
                    'id' => 'user_center',
                    'label' => __('用户中心', PUOCK),
                    'type' => 'switch',
                    'sdt' => false,
                    'badge' => ['value' => 'New'],
                    'tips' => __('关闭后主题用户中心 /uc 将不可访问，已登录用户将进入 WordPress 个人资料/后台。前台登录按钮还需在「登录与授权」中开启「快捷登录」，评论区在需要登录时也会显示登录入口。使用前请先配置 WordPress 伪静态规则：', PUOCK) . '<code>try_files $uri $uri/ /index.php?$args</code>'
                ],
                [
                    'id' => 'user_center_entry',
                    'label' => __('用户中心入口', PUOCK),
                    'type' => 'select',
                    'sdt' => '',
                    'options' => [
                        ['value' => 'theme', 'label' => __('主题用户中心', PUOCK)],
                        ['value' => 'wp', 'label' => __('WordPress个人资料', PUOCK)],
                        ['value' => 'erphp', 'label' => __('ErphpDown用户中心', PUOCK)],
                    ],
                    'tips' => __('仅在开启「用户中心」后生效；关闭「用户中心」后主题 /uc 不可用，登录用户将进入 WordPress 个人资料/后台。未设置时默认使用主题用户中心。', PUOCK)
                ],
                [
                    'id' => 'erphpdown_user_center_url',
                    'label' => __('ErphpDown用户中心URL', PUOCK),
                    'type' => 'text',
                    'sdt' => '/erphpdown/user',
                    'showRefId' => 'func:(function(args){return args.data.user_center_entry==="erphp"})(args)',
                    'tips' => __('ErphpDown 用户中心地址，可填写完整URL或站内路径', PUOCK)
                ],
                [
                    'id' => 'strawberry_icon',
                    'label' => __('Strawberry图标库', PUOCK),
                    'type' => 'switch',
                    'sdt' => false,
                    'tips' => __('开启之后会在前台加载Strawberry图标库支持', PUOCK)
                ],
                [
                    'id' => 'dplayer',
                    'label' => 'DPlayer' . ' ' . __('支持', PUOCK),
                    'type' => 'switch',
                    'sdt' => false,
                    'tips' => __('开启之后会在前台加载DPlayer支持', PUOCK)
                ],
            ],
        ];
    }
}
