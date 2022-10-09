<?php

class OptionGlobal extends BaseOptionItem{

    function get_fields(): array
    {
        return [
            'key' => 'global',
            'label' => '全局设置',
            'icon'=>'dashicons-admin-site',
            'fields' => [
                [
                    'id' => 'index_mode',
                    'label' => '首页布局',
                    'type' => 'radio',
                    'sdt' => 'blog',
                    'radioType' => 'button',
                    'options' => [
                        [
                            'value' => 'blog',
                            'label' => '博客风格',
                        ],
                        [
                            'value' => 'cms',
                            'label' => 'CMS风格',
                        ],
                        [
                            'value' => 'company',
                            'label' => '企业风格',
                        ],
                    ],
                ],
                [
                    'id' => 'post_style',
                    'label' => '文章样式',
                    'type' => 'radio',
                    'sdt' => 'list',
                    'radioType' => 'button',
                    'options' => [
                        [
                            'value' => 'list',
                            'label' => '列表式',
                        ],
                        [
                            'value' => 'card',
                            'label' => '卡片式',
                        ],
                    ],
                ],
                [
                    'id' => 'theme_mode',
                    'label' => '默认主题模式',
                    'type' => 'radio',
                    'sdt' => 'light',
                    'radioType' => 'button',
                    'options' => [
                        [
                            'value' => 'light',
                            'label' => '高亮模式',
                        ],
                        [
                            'value' => 'dark',
                            'label' => '暗黑模式',
                        ],
                    ],
                ],
                [
                    'id' => 'theme_mode_s',
                    'label' => '允许切换主题模式',
                    'type' => 'switch',
                    'sdt' => true,
                ],
                [
                    'id' => 'show_login_url',
                    'label' => '导航栏显示登录入口',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'nav_blur',
                    'label' => '导航栏毛玻璃效果',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'html_page_permalink',
                    'label' => '页面使用.html后缀',
                    'type' => 'switch',
                    'sdt' => 'false',
                    'tips'=>'更改后需要重新保存<strong>固定链接</strong>'
                ],
                [
                    'id' => 'chinese_format',
                    'label' => '开启中文格式化（文案排版）',
                    'type' => 'switch',
                    'sdt' => 'false',
                    'tips'=>"参考：<a href='https://github.com/sparanoid/chinese-copywriting-guidelines' target='_blank'>https://github.com/sparanoid/chinese-copywriting-guidelines</a>"
                ],
                [
                    'id' => 'on_txt_logo',
                    'label' => '使用文字LOGO',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'light_logo',
                    'label' => '高亮模式下LOGO',
                    'type' => 'img',
                    'sdt' => '',
                    'tips'=>'若不上传则显示文字LOGO，比例：500*125，请尽量选择png无底色图片'
                ],
                [
                    'id' => 'dark_logo',
                    'label' => '暗黑模式下LOGO',
                    'type' => 'img',
                    'sdt' => '',
                    'tips'=>"比例：500*125，请尽量选择png无底色图片",
                ],
                [
                    'id' => 'favicon',
                    'label' => '网站favicon',
                    'type' => 'img',
                    'sdt' => '',
                    'tips'=>"比例：1:1"
                ],
                [
                    'id' => 'stop5x_editor',
                    'label' => '禁用Gutenberg编辑器',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'use_widgets_block',
                    'label' => '使用区块小工具',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'upload_webp',
                    'label' => '允许上传webp',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'hide_post_views',
                    'label' => '隐藏文章阅读量',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'close_post_comment',
                    'label' => '关闭全站评论功能',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'hide_footer_wp_t',
                    'label' => '隐藏底部"感谢使用WordPress进行创作"和左上角标识',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'grey',
                    'label' => '全站变灰',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'compress_html',
                    'label' => '将HTML压缩成一行',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'hide_global_sidebar',
                    'label' => '关闭全局侧边栏显示',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'login_protection',
                    'label' => '启用后台登录保护',
                    'type' => 'switch',
                    'sdt' => 'false',
                    'tips'=>'func:(function(args){
                            const link = `'.home_url().'/wp-login.php?${args.data.lp_user}=${args.data.lp_pass}`
                            return `<div>启用后则用 <a href="${link}" target="_blank">${link}</a> 的方式访问后台入口</div>`
                        })(args)'
                ],
                [
                    'id' => 'lp_user',
                    'label' => '后台登录保护参数',
                    'sdt' => 'admin',
                    'showRefId' => 'login_protection',
                ],
                [
                    'id' => 'lp_pass',
                    'label' => '后台登录保护值',
                    'sdt' => 'admin',
                    'showRefId' => 'login_protection',
                ],
                [
                    'id' => 'thumbnail_allows',
                    'label' => '缩略图白名单',
                    'type' => 'textarea',
                    'sdt' => '',
                    'tips'=>"<strong>若使用了其他外链图片须在此处添加外链域名以允许</strong>：一行一个，不要带 <code>http://</code> 或 <code>https://</code> 协议头，例如：<code>blog.example.com</code>"
                ],
            ],
        ];
    }
}
