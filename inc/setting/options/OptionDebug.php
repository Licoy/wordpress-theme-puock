<?php

namespace Puock\Theme\setting\options;

class OptionDebug extends BaseOptionItem{

    function get_fields(): array
    {
        return [
            'key' => 'debug',
            'label' => '调试与开发',
            'icon'=>'dashicons-code-standards',
            'fields' => [
                [
                    'id' => 'debug_sql_count',
                    'label' => '显示SQL查询统计',
                    'type' => 'switch',
                    'sdt' => 'false',
                    'tips'=>'此数据会显示在<code>console</code>，需<code>F12</code>打开控制台查看'
                ],
                [
                    'id' => 'debug_sql_detail',
                    'label' => '显示SQL查询详情',
                    'type' => 'switch',
                    'sdt' => 'false',
                    'tips'=>"此数据会显示在<code>console</code>，需<code>F12</code>打开控制台查看,，需要在<code>wp-config.php</code>中加入<code>define('SAVEQUERIES', true);</code>"
                ],
            ],
        ];
    }
}
