<?php

class OptionCache extends BaseOptionItem{

    function get_fields(): array
    {
        return [
            'key' => 'cache',
            'label' => '缓存与性能',
            'icon'=>'dashicons-superhero',
            'fields' => [
                [
                    'id' => 'cache_expire_second',
                    'label' => '缓存过期秒数',
                    'type' => 'number',
                    'sdt' => 0,
                    'tips'=>'0为不过期'
                ],
            ],
        ];
    }
}
