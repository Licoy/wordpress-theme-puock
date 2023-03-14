<?php

namespace Puock\Theme\setting\options;

class OptionAi
{
    function get_fields(): array
    {
        return [
            'key' => 'ai',
            'label' => __('ChatGPT问答', PUOCK),
            'icon' => 'fa-solid fa-robot',
            'fields' => [
                [
                    'id'=>'openai_enable',
                    'label'=>__('启用ChatGPT问答', PUOCK),
                    'type'=>'switch',
                    'sdt'=>false,
                ],
                [
                    'id'=>'openai_api_key',
                    'label'=>__('OpenAI API Key', PUOCK),
                    'type'=>'text',
                    'tips'=>__('请在<a href="https://platform.openai.com/" target="_blank">OpenAI - Platform</a>申请获取API Key', PUOCK),
                ],
                [
                    'id'=>'openai_api_agent',
                    'label'=>__('OpenAI API 代理域', PUOCK),
                    'type'=>'text',
                    'tips'=>__('默认为<code>https://api.openai.com/v1</code>，如果你的服务器无法访问该域名，请自行配置代理域名', PUOCK),
                ],
                [
                    'id'=>'openai_max_tokens',
                    'label'=>__('最大tokens', PUOCK),
                    'type'=>'number',
                    'sdt'=>0,
                    'tips'=>__('最大tokens，最大为4096，填0则不设置', PUOCK),
                ],
                [
                    'id'=>'openai_temperature',
                    'label'=>__('Temperature', PUOCK),
                    'type'=>'number',
                    'sdt'=>0.9,
                ],
                [
                    'id'=>'openai_model_sys_content',
                    'label'=>__('模型系统预设', PUOCK),
                    'type'=>'textarea',
                    'tips'=>__('模型系统预设，可让AI主动进行一些违规话题的屏蔽，不懂勿轻易填充', PUOCK),
                ],
                [
                    'id'=>'openai_guest_use',
                    'label'=>__('允许游客使用', PUOCK),
                    'type'=>'switch',
                    'tips'=>__('是否在未登录状态下也可以使用', PUOCK),
                ],
                [
                    'id'=>'openai_default_welcome_chat',
                    'label'=>__('默认欢迎对话', PUOCK),
                    'type'=>'textarea',
                    'sdt'=>'您好，欢迎使用智能AI助理',
                    'tips'=>'支持HTML代码',
                ]
            ]
        ];
    }
}
