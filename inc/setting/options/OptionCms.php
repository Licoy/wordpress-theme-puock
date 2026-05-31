<?php

namespace Puock\Theme\setting\options;

class OptionCms extends BaseOptionItem{

    function get_fields(): array
    {
        return [
            'key' => 'cms',
            'label' => __('CMS布局', PUOCK),
            'icon'=>'czs-layers',
            'fields' => [
                [
                    'id' => 'cms_show_pagination',
                    'label' => __('显示分页', PUOCK),
                    'type' => 'switch',
                    'sdt' => false,
                ],
                [
                    'id' => 'cms_show_load_more',
                    'label' => __('显示加载更多', PUOCK),
                    'type' => 'switch',
                    'sdt' => false,
                ],
                [
                    'id' => 'cms_show_new',
                    'label' => __('显示最新文章', PUOCK),
                    'type' => 'switch',
                    'sdt' => true,
                ],
                [
                    'id' => 'cms_new_sort',
                    'label' => __('最新文章排序规则', PUOCK),
                    'type' => 'radio',
                    'options'=>[
                        ['label'=>__('发布时间', PUOCK),'value'=>'published'],
                        ['label'=>__('更新时间', PUOCK),'value'=>'updated'],
                    ],
                    'sdt' => 'published',
                ],
                [
                    'id' => 'cms_show_new_num',
                    'label' => __('最新文章数量', PUOCK),
                    'type' => 'number',
                    'sdt' => 6,
                    'showRefId' => 'cms_show_new',
                ],
                [
                    'id' => 'cms_new_exclude_cats',
                    'label' => __('最新文章排除分类', PUOCK),
                    'type' => 'select',
                    'sdt' => '',
                    'multiple' => true,
                    'showRefId' => 'cms_show_new',
                    'options' => self::get_category(),
                    'tips' => __('选择需要从最新文章列表中排除的分类', PUOCK),
                ],
                [
                    'id' => 'category_posts_per_page',
                    'label' => __('分类页每页文章数', PUOCK),
                    'type' => 'number',
                    'sdt' => 0,
                    'tips' => __('设置分类页面每页显示的文章数量（0为使用WordPress默认设置）', PUOCK),
                ],
                [
                    'id' => 'cms_card_columns',
                    'label' => __('CMS卡片列数', PUOCK),
                    'type' => 'select',
                    'sdt' => 2,
                    'options' => [
                        ['label' => '2', 'value' => 2],
                        ['label' => '3', 'value' => 3],
                        ['label' => '4', 'value' => 4],
                    ],
                    'tips' => __('适用于文章列表的卡片风格（首页/分类/标签/作者/搜索等）；当页面显示侧边栏时最大仅2列；默认2列', PUOCK),
                ],
                [
                    'id' => 'cms_show_four_grid',
                    'label' => __('显示首页四宫格', PUOCK),
                    'type' => 'switch',
                    'sdt' => false,
                    'tips' => __('仅在CMS布局首页第一页显示，位置在首页幻灯片下方', PUOCK),
                ],
                [
                    'id' => 'cms_four_grid_list',
                    'label' => __('首页四宫格列表', PUOCK),
                    'type' => 'dynamic-list',
                    'sdt' => [],
                    'draggable' => true,
                    'showRefId' => 'cms_show_four_grid',
                    'dynamicModel' => [
                        ['id' => 'title', 'label' => __('标题', PUOCK), 'std' => '', 'tips' => __('用于图片替代文本', PUOCK)],
                        ['id' => 'img', 'label' => __('图片', PUOCK), 'std' => '', 'type' => 'img', 'tips' => __('建议四张图片尺寸比例一致', PUOCK)],
                        ['id' => 'link', 'label' => __('指向链接', PUOCK), 'std' => ''],
                        ['id' => 'blank', 'label' => __('新标签打开', PUOCK), 'std' => false, 'type' => 'switch'],
                        ['id' => 'hide', 'label' => __('隐藏', PUOCK), 'type' => 'switch', 'sdt' => false, 'tips' => __('隐藏后将不会显示', PUOCK)],
                    ],
                    'tips' => __('最多显示前4个未隐藏且已设置图片的项目', PUOCK),
                ],
                [
                    'id' => 'cms_show_2box',
                    'label' => __('显示CMS两栏布局', PUOCK),
                    'type' => 'switch',
                    'sdt' => true,
                ],
                [
                    'id' => 'cms_show_2box_id',
                    'label' => __('CMS两栏布局分类ID', PUOCK),
                    'type' => 'select',
                    'sdt' => '',
                    'multiple' => true,
                    'showRefId' => 'cms_show_2box',
                    'options' => self::get_category(),
                ],
                [
                    'id' => 'cms_show_2box_num',
                    'label' => __('CMS两栏布局每栏数量', PUOCK),
                    'type' => 'number',
                    'sdt' => 6,
                    'showRefId' => 'cms_show_2box',
                ],
            ],
        ];
    }
}
