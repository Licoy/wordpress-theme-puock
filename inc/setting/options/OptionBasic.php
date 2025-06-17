<?php

namespace Puock\Theme\setting\options;

class OptionBasic extends BaseOptionItem
{

    function get_fields(): array
    {
        return [
            'key' => 'basic',
            'label' => __('基础设置', PUOCK),
            'icon' => 'dashicons-admin-generic',
            'fields' => [
                [
                    'id' => 'basic_img_lazy_s',
                    'label' => __('图片懒加载', PUOCK),
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'basic_img_lazy_z',
                    'label' => __('正文图片懒加载', PUOCK),
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'basic_img_lazy_a',
                    'label' => __('留言头像懒加载', PUOCK),
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'off_img_viewer',
                    'label' => __('禁用正文图片灯箱预览', PUOCK),
                    'type' => 'switch',
                    'sdt' => false,
                ],
                [
                    'id' => 'off_code_highlighting',
                    'label' => __('禁用主题代码高亮', PUOCK),
                    'type' => 'switch',
                    'sdt' => false,
                ],
                [
                    'id' => 'post_content_indent',
                    'label' => __('正文内容首行缩进', PUOCK),
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'link_blank_content',
                    'label' => __('正文内容链接新标签页打开', PUOCK),
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'link_go_page',
                    'label' => __('正文内容链接加跳转', PUOCK),
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'use_post_menu',
                    'label' => __('正文内容侧边目录菜单生成', PUOCK),
                    'type' => 'switch',
                    'sdt' => 'false',
                    'tips' => __('勾选此项会在正文目录显示文章目录', PUOCK),
                ],
                [
                    'id' => 'comment_ajax',
                    'label' => __('评论ajax翻页', PUOCK),
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'page_ajax_load',
                    'label' => __('页面无刷新加载', PUOCK),
                    'type' => 'switch',
                    'sdt' => 'false',
                    'tips' => "新标签页打开的链接除外"
                ],
                [
                    'id' => 'async_view',
                    'label' => __('异步浏览量统计', PUOCK),
                    'type' => 'switch',
                    'sdt' => 'false',
                    'tips' => __('此选项为开启缓存后浏览量不自增问题解决方案', PUOCK)
                ],
                [
                    'id' => 'page_animate',
                    'label' => __('页面模块载入动画', PUOCK),
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'page_link_before_icon',
                    'label' => __('页面内容链接前显示图标', PUOCK),
                    'type' => 'switch',
                    'sdt' => false,
                ],
                [
                    'id' => '-',
                    'type' => 'panel',
                    'open' => pk_is_checked('post_expire_tips_open'),
                    'label' => __('文章超过时效提示', PUOCK),
                    'children'=>[
                        [
                            'id' => 'post_expire_tips_open',
                            'label' => __('启用', PUOCK),
                            'type' => 'switch',
                            'sdt' => 'false',
                        ],
                        [
                            'id' => 'post_expire_tips_day',
                            'label' => __('大于N天', PUOCK),
                            'type' => 'number',
                            'sdt' => 100,
                        ],
                        [
                            'id' => 'post_expire_tips',
                            'label' => __('提示内容', PUOCK),
                            'sdt' => __('<i class="fa fa-circle-exclamation me-1"></i>提醒：本文最后更新于{date}，文中所关联的信息可能已发生改变，请知悉！', PUOCK),
                            'tips' => __('{date}：文章最后更新时间', PUOCK),
                        ],
                    ]
                ],
                [
                    'id' => '-',
                    'type' => 'panel',
                    'open' => true,
                    'label' => __('评论相关', PUOCK),
                    'children' => [
                        [
                            'id' => 'comment_level',
                            'label' => __('评论等级', PUOCK),
                            'type' => 'switch',
                            'sdt' => 'false',
                        ],
                        [
                            'id' => 'comment_mail_notify',
                            'label' => __('评论回复邮件通知', PUOCK),
                            'type' => 'switch',
                            'sdt' => 'false',
                        ],
                        [
                            'id' => 'comment_has_at',
                            'label' => __('评论@功能', PUOCK),
                            'type' => 'switch',
                            'sdt' => 'false',
                        ],
                        [
                            'id' => 'comment_show_ua',
                            'label' => __('评论显示用户UA', PUOCK),
                            'type' => 'switch',
                            'sdt' => true,
                        ],
                        [
                            'id' => 'comment_show_ip',
                            'label' => __('评论显示IP归属地及运营商', PUOCK),
                            'type' => 'switch',
                            'sdt' => true,
                        ],
                        [
                            'id' => 'comment_dont_show_owner_ip',
                            'label' => __('不显示站长IP归属地及运营商', PUOCK),
                            'type' => 'switch',
                            'sdt' => false,
                        ],
                    ]
                ],
                [
                    'id' => 'post_poster_open',
                    'label' => __('文章海报生成', PUOCK),
                    'tips' => __('使用此功能如果出现图片无法生成，请检查图片是否符合跨域要求', PUOCK),
                    'type' => 'switch',
                    'sdt' => false,
                ],
                [
                    'id' => 'page_copy_right',
                    'label' => __('显示正文版权说明', PUOCK),
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'page_b_recommend',
                    'label' => __('显示正文底部相关推荐', PUOCK),
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'page_b_recommend_num',
                    'label' => __('正文底部相关推荐文章数量', PUOCK),
                    'tips' => __('建议是4的倍数，不然会出现空缺位置', PUOCK),
                    'type' => 'number',
                    'sdt' => 4,
                ],
                [
                    'id' => 'link_page',
                    'label' => __('友情链接页面', PUOCK),
                    'type' => 'select',
                    'options' => self::get_pages(),
                ],
                [
                    'id' => 'index_link_id',
                    'label' => __('首页友情链接目录ID', PUOCK),
                    'type' => 'select',
                    'options' => self::get_link_category(),
                ],
                [
                    'id' => 'index_link_order_by',
                    'label' => __('首页友情链接排序字段', PUOCK),
                    'tips' => __('根据链接字段进行排序，缺省默认值为ID排序', PUOCK),
                    'type' => 'select',
                    'options' => self::get_link_order_by(),
                ],
                [
                    'id' => 'index_link_order',
                    'label' => __('首页友情链接排序顺序', PUOCK),
                    'tips' => __('缺省默认值为升序 (ASC)', PUOCK),
                    'type' => 'select',
                    'options' => self::get_link_order(),
                ],
                [
                    'id' => 'gravatar_url',
                    'label' => __('Gravatar头像源', PUOCK),
                    'type' => 'radio',
                    'sdt' => 'cravatar',
                    'radioType' => 'radio',
                    'options' => [
                        [
                            'value' => 'wp',
                            'label' => __('WordPress默认', PUOCK),
                        ],
                        [
                            'value' => 'cn',
                            'label' => __('WordPress国内默认', PUOCK),
                        ],
                        [
                            'value' => 'cn_ssl',
                            'label' => __('WordPress国内默认SSL', PUOCK),
                        ],
                        [
                            'value' => 'cravatar',
                            'label' => 'Cravatar',
                        ],
                        [
                            'value' => 'v2ex',
                            'label' => 'V2EX',
                        ],
                        [
                            'value' => 'loli',
                            'label' => 'loli.net'
                        ],
                        [
                            'value' => 'custom',
                            'label' => __('自定义', PUOCK)
                        ]
                    ],
                ],
                [
                    'id'=>'gravatar_custom_url',
                    'label'=>__('自定义Gravatar源', PUOCK),
                    'tips'=>__('例如：',PUOCK).'<code>gravatar.example.com</code>',
                    'showRefId'=>'func:(function(args){return args.data.gravatar_url==="custom"})(args)'
                ],
                [
                    'id' => 'post_reward',
                    'label' => __('文章赞赏', PUOCK),
                    'type' => 'switch',
                    'sdt' => 'false',
                ],
                [
                    'id' => 'post_reward_alipay',
                    'label' => __('文章赞赏支付宝二维码', PUOCK),
                    'type' => 'img',
                    'showRefId' => 'post_reward',
                    'tips' => __('请选择宽高比例为1:1的图片', PUOCK)
                ],
                [
                    'id' => 'post_reward_wx',
                    'label' => __('文章赞赏微信二维码', PUOCK),
                    'type' => 'img',
                    'showRefId' => 'post_reward',
                    'tips' => __('请选择宽高比例为1:1的图片', PUOCK)
                ],
                [
                    'id' => 'post_foot_qrcode_open',
                    'label' => __('文章正文底部二维码', PUOCK),
                    'type' => 'switch',
                    'sdt' => 'false',
                    'tips' => __('请选择宽高比例为1:1的图片', PUOCK)
                ],
                [
                    'id' => 'post_foot_qrcode_title',
                    'label' => __('文章正文底部二维码标题', PUOCK),
                    'sdt' => '',
                    'showRefId' => 'post_foot_qrcode_open',
                ],
                [
                    'id' => 'post_foot_qrcode_img',
                    'label' => __('文章正文底部二维码', PUOCK),
                    'type' => 'img',
                    'showRefId' => 'post_foot_qrcode_open',
                ],
                [
                    'id' => 'post_reprint_note',
                    'label' => __('文章转载说明', PUOCK),
                    'type' => 'textarea',
                    'sdt' => __('除特殊说明外本站文章皆由CC-4.0协议发布，转载请注明出处。', PUOCK),
                ],
                [
                    'id' => 'post_read_time',
                    'label' => __('文章阅读时间', PUOCK),
                    'type' => 'switch',
                    'sdt' => false,
                ]
            ],
        ];
    }
}
