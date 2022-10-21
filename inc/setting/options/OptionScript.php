<?php

namespace Puock\Theme\setting\options;

class OptionScript extends BaseOptionItem
{

    function get_fields(): array
    {
        return [
            'key' => 'script',
            'label' => '脚本及样式',
            'icon' => 'dashicons-shortcode',
            'fields' => [
                [
                    'id' => 'style_color_primary',
                    'label' => '站点主色调',
                    'type' => 'color',
                    'sdt' => '#1c60f3',
                ],
                [
                    'id' => 'block_not_tran',
                    'label' => '全站区块不透明度',
                    'type' => 'slider',
                    'sdt' => 100,
                    'step' => 1,
                    'max' => 100,
//                    'tips'=>"小于100则会进行透明显示，部分浏览器可能不兼容",
                    'tips' => "func:(function(args){
                        return args.h('div',[
                            args.h('span',null,'小于100则会进行透明显示，部分浏览器可能不兼容 '),
                            args.h(args.el.nTag,{type:'primary',size:'small',round:true},'当前不透明度为：'+args.data.block_not_tran+'%')
                        ])
                    })(args)"
                ],
                [
                    'id' => 'tj_code_header',
                    'label' => '头部流量统计代码',
                    'type' => 'textarea',
                    'sdt' => '',
                    'tips' => "用于在页头添加统计代码（PS：若开启无刷新加载，请在标签上加上<code>data-instant</code>属性）"
                ],
                [
                    'id' => 'css_code_header',
                    'label' => '头部自定义全局CSS样式',
                    'type' => 'textarea',
                    'placeholder' => '例如：#header{background-color:red !important}',
                    'sdt' => '',
                    'tips' => "用于在页头添加统自定义CSS样式"
                ],
                [
                    'id' => 'tj_code_footer',
                    'label' => '底部流量统计代码',
                    'type' => 'textarea',
                    'sdt' => '',
                ],
                [
                    'id' => 'footer_info',
                    'label' => '底部页脚信息',
                    'type' => 'textarea',
                    'sdt' => 'Copyright Puock',
                ],
                [
                    'id' => 'footer_about_me_title',
                    'label' => '底部关于我们说明标题',
                    'sdt' => '关于我们',
                    'tips' => "若为空则不显示此栏目"
                ],
                [
                    'id' => 'footer_about_me',
                    'label' => '底部关于我们说明',
                    'type' => 'textarea',
                    'sdt' => '底部关于我们说明',
                ],
                [
                    'id' => 'footer_copyright_title',
                    'label' => '底部版权说明标题',
                    'sdt' => '版权说明',
                    'tips' => "若为空则不显示此栏目"
                ],
                [
                    'id' => 'footer_copyright',
                    'label' => '底部版权说明',
                    'type' => 'textarea',
                    'sdt' => '底部版权说明',
                ],
                [
                    'id' => 'down_tips',
                    'label' => '下载说明/申明',
                    'type' => 'textarea',
                    'sdt' => '本站部分资源来自于网络收集，若侵犯了你的隐私或版权，请及时联系我们删除有关信息。',
                ],
            ],
        ];
    }
}
