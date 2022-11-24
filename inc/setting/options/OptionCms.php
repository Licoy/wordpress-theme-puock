<?php

namespace Puock\Theme\setting\options;

class OptionCms extends BaseOptionItem{

    function get_fields(): array
    {
        return [
            'key' => 'cms',
            'label' => 'CMS布局',
            'icon'=>'czs-layers',
            'fields' => [
                [
                    'id' => 'cms_show_new',
                    'label' => '显示最新文章',
                    'type' => 'switch',
                    'sdt' => true,
                ],
                [
                    'id' => 'cms_show_new_num',
                    'label' => '最新文章显示数量',
                    'type' => 'number',
                    'sdt' => 6,
                    'showRefId' => 'cms_show_new',
                ],
                [
                    'id' => 'cms_show_2box',
                    'label' => '两栏CMS分类列表',
                    'type' => 'switch',
                    'sdt' => true,
                ],
                [
                    'id' => 'cms_show_2box_id',
                    'label' => '两栏CMS分类ID列表',
                    'type' => 'select',
                    'sdt' => '',
                    'multiple' => true,
                    'showRefId' => 'cms_show_2box',
                    'options' => self::get_category(),
                ],
                [
                    'id' => 'cms_show_2box_num',
                    'label' => '两栏CMS分类每栏显示数量',
                    'type' => 'number',
                    'sdt' => 6,
                    'showRefId' => 'cms_show_2box',
                ],
            ],
        ];
    }
}
