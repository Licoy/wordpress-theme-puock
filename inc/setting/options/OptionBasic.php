<?php

class OptionBasic extends BaseOptionItem{

    function get_fields(): array
    {
        return [
            'key' => 'basic',
            'label' => '基础设置',
            'icon'=>'dashicons-admin-generic',
            'fields' => [
                [
                    'id' => 'basic_img_lazy_s',
                    'label' => '图片延迟加载',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'basic_img_lazy_z',
                    'label' => '正文图片延迟加载',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'basic_img_lazy_a',
                    'label' => '留言头像延迟加载',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'post_content_indent',
                    'label' => '正文内容首行缩进',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'link_blank_content',
                    'label' => '正文内容链接新标签页打开',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'use_post_menu',
                    'label' => '正文内容侧边目录菜单生成',
                    'type' => 'switch',
                    'sdt' => 'false',
                    'tips'=>'勾选此项会在正文目录显示文章目录'
                ],
                [
                    'id' => 'comment_ajax',
                    'label' => '评论AJAX翻页',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'page_ajax_load',
                    'label' => '页面无刷新加载',
                    'type' => 'switch',
                    'sdt' => 'false',
                    'tips'=> "新标签页打开的链接除外"
                ],
                [
                    'id' => 'async_view',
                    'label' => '异步浏览量统计',
                    'type' => 'switch',
                    'sdt' => 'false',
                    'tips'=>'此选项为开启缓存后浏览量不自增问题解决方案'
                ],
                [
                    'id' => 'page_animate',
                    'label' => '页面模块载入动画',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'comment_level',
                    'label' => '显示评论等级',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'comment_mail_notify',
                    'label' => '评论回复邮件通知',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'comment_has_at',
                    'label' => '评论内容显示@',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'page_copy_right',
                    'label' => '显示正文版权说明',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'page_b_recommend',
                    'label' => '显示正文底部相关推荐',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'link_page',
                    'label' => '友情链接页面',
                    'type' => 'select',
                    'options' => self::get_pages(),
                ],
                [
                    'id' => 'index_link_id',
                    'label' => '首页友情链接目录',
                    'type' => 'select',
                    'options' => self::get_link_category(),
                ],
                [
                    'id' => 'gravatar_url',
                    'label' => 'Gravatar加载源',
                    'type' => 'radio',
                    'sdt' => 'cravatar',
                    'radioType' => 'radio',
                    'options' => [
                        [
                            'value' => 'wp',
                            'label' => '官方默认加载节点',
                        ],
                        [
                            'value' => 'cn',
                            'label' => '官方提供的CN节点',
                        ],
                        [
                            'value' => 'cn_ssl',
                            'label' => '官方提供SSL的CN节点',
                        ],
                        [
                            'value' => 'cravatar',
                            'label' => 'Cravatar公共头像',
                        ],
                        [
                            'value' => 'v2ex',
                            'label' => 'v2ex提供的SSL节点',
                        ],
                    ],
                ],
                [
                    'id' => 'post_reward',
                    'label' => '文章赞赏',
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'post_reward_alipay',
                    'label' => '文章赞赏支付宝二维码',
                    'type' => 'img',
                    'showRefId' => 'post_reward',
                    'tips'=>'请选择宽高比例为1:1的图片'
                ],
                [
                    'id' => 'post_reward_wx',
                    'label' => '文章赞赏微信二维码',
                    'type' => 'img',
                    'showRefId' => 'post_reward',
                    'tips'=>'请选择宽高比例为1:1的图片'
                ],
                [
                    'id' => 'post_foot_qrcode_open',
                    'label' => '文章正文底部二维码',
                    'type' => 'switch',
                    'sdt' => 'false',
                    'tips'=>'请选择宽高比例为1:1的图片'
                ],
                [
                    'id' => 'post_foot_qrcode_title',
                    'label' => '文章正文底部二维码标题',
                    'sdt' => '',
                    'showRefId' => 'post_foot_qrcode_open',
                ],
                [
                    'id' => 'post_foot_qrcode_img',
                    'label' => '文章正文底部二维码',
                    'type' => 'img',
                    'showRefId' => 'post_foot_qrcode_open',
                ],
                [
                    'id' => 'post_reprint_note',
                    'label' => '文章转载说明',
                    'type' => 'textarea',
                    'sdt' => '除特殊说明外本站文章皆由CC-4.0协议发布，转载请注明出处。',
                ],
            ],
        ];
    }
}
