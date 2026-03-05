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
