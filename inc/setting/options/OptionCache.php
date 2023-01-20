<?php

namespace Puock\Theme\setting\options;

class OptionCache extends BaseOptionItem{

    function get_fields(): array
    {
        return [
            'key' => 'cache',
            'label' => __('缓存与性能', PUOCK),
            'icon'=>'dashicons-superhero',
            'fields' => [
                [
                    'id' => 'cache_expire_second',
                    'label' => __('缓存过期秒数', PUOCK),
                    'type' => 'number',
                    'sdt' => 0,
                    'tips'=>__('0为不过期', PUOCK),
                ],
            ],
        ];
    }
}
