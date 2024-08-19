<?php

namespace Puock\Theme\setting\options;

class OptionAi
{
    function get_fields(): array
    {
        return [
            'key' => 'ai',
            'label' => __('智能AI助手', PUOCK),
            'icon' => 'czs-robot',
            'fields' => [
                [
                    'id' => 'ai_chat_enable',
                    'label' => __('启用AI助手', PUOCK),
                    'type' => 'switch',
                    'tips' => __('启用后去<a href="/wp-admin/post-new.php?post_type=page">创建页面</a>选择<code>AI助手</code>模板即可使用', PUOCK)
                ],
                [
                    'id' => 'ai_chat_platform',
                    'label' => __('API 提供商', PUOCK),
                    'type' => 'radio',
                    'sdt' => 'gptnb',
                    'options' => [
                        [
                            'value' => 'gptnb',
                            'label' => __('GPTNB', PUOCK),
                        ],
                        [
                            'value' => 'openai',
                            'label' => __('OpenAI', PUOCK),
                        ],
                        [
                            'value' => 'custom',
                            'label' => __('自定义', PUOCK),
                        ],
                    ],
                    'tips' => __('<a href="https://goapi.gptnb.ai" target="_blank"><code>GPTNB（第三方中转）<i class="fa-solid fa-arrow-up-right-from-square"></i></code></a> | <a href="https://platform.openai.com" target="_blank"><code>OpenAI（官方）<i class="fa-solid fa-arrow-up-right-from-square"></i></code></a>（注意：第三方中转由第三方服务商提供，本程序不承诺任何担保，若有任何疑问请咨询对应的网站客服）', PUOCK),
                ],
                [
                    'id' => 'ai_chat_key',
                    'label' => __('API KEY', PUOCK),
                    'type' => 'text',
                    'sdt' => pk_get_option('openai_api_key'),
                    'tips' => __('请在上方选择的对应的服务商申请获取API Key，然后在此填入', PUOCK),
                ],
                [
                    'id' => 'ai_chat_agent',
                    'label' => __('自定义 API 代理域', PUOCK),
                    'type' => 'text',
                    'sdt' => '',
                    'showRefId'=>'func:(function(args){return args.data.ai_chat_platform==="custom"})(args)',
                    'tips' => __('如果您要使用其他平台请自行配置代理域名，例如您自己的反向代理等，其API规范必须符合OpenAI API', PUOCK),
                ],
                [
                    'id' => 'ai_chat_models',
                    'label' => __('对话模型', PUOCK),
                    'type' => 'dynamic-list',
                    'sdt' => [
                        ['name'=>'gpt-3.5-turbo','alias'=>'GPT-3.5-TURBO','enable'=>true],
                        ['name'=>'gpt-3.5-turbo-16k','alias'=>'GPT-3.5-TURBO-16K','enable'=>true],
                        ['name'=>'gpt-4','alias'=>'GPT-4'],
                        ['name'=>'gpt-4-32k','alias'=>'GPT-4-32K'],
                    ],
                    'draggable' => true,
                    'dynamicModel' => [
                        ['id' => 'name', 'label' => __('模型名称', PUOCK), 'std' => '','tips' => __('用于传递给平台的模型名称', PUOCK)],
                        ['id' => 'alias', 'label' => __('模型别名', PUOCK), 'std' => '','tips' => __('用于展示给用户的名称', PUOCK)],
                        ['id' => 'max_tokens', 'label' => __('模型最大Tokens', PUOCK), 'std' => 0, 'tips' => __('为0则无限制', PUOCK), 'type'=>'number'],
                        ['id' => 'enable', 'label' => __('启用', PUOCK), 'type' => 'switch'],
                    ],
                ],
                [
                    'id' => 'ai_chat_model_sys_prompt',
                    'label' => __('模型系统预设', PUOCK),
                    'type' => 'textarea',
                    'sdt' => pk_get_option('openai_model_sys_content'),
                    'tips' => __('模型系统预设，可让AI主动进行一些违规话题的屏蔽，不懂勿轻易填充', PUOCK),
                ],
                [
                    'id' => 'ai_chat_stream',
                    'label' => __('使用Stream(实时输出)模式', PUOCK),
                    'type' => 'switch',
                    'sdt' => pk_is_checked('openai_stream'),
                    'tips' => __('启用后请关闭nginx的<code>gzip</code>模式', PUOCK),
                ],
                [
                    'id' => 'ai_chat_welcome',
                    'label' => __('默认欢迎对话', PUOCK),
                    'type' => 'textarea',
                    'sdt' => pk_get_option('openai_default_welcome_chat', '您好，欢迎使用智能AI助理'),
                    'tips' => '支持HTML代码',
                ],
                [
                    'id' => 'ai_draw_dall_e',
                    'label' => __('AI绘画支持', PUOCK),
                    'type' => 'switch',
                    'sdt' => pk_is_checked('openai_dall_e'),
                    'tips' => __('启用后前端界面<code>勾选绘画模式</code>即可绘画', PUOCK),
                ],
                [
                    'id' => 'ai_draw_dall_e_model',
                    'label' => __('AI绘画模型', PUOCK),
                    'type' => 'select',
                    'sdt' => pk_get_option('ai_draw_dall_e_model', 'dall-e-2'),
                    'options' => [
                        ['label' => 'DallE-2', 'value' => 'dall-e-2'],
                        ['label' => 'DallE-3', 'value' => 'dall-e-3'],
                    ],
                ],
                [
                    'id' => 'ai_draw_dall_e_size',
                    'label' => __('AI绘画图片大小', PUOCK),
                    'type' => 'select',
                    'sdt' => pk_get_option('openai_dall_e_size', '512x512'),
                    'options' => [
                        ['label' => '256x256', 'value' => '256x256'],
                        ['label' => '512x512', 'value' => '512x512'],
                        ['label' => '1024x1024', 'value' => '1024x1024'],
                        ['label' => '1792x1024（仅DallE-3支持）', 'value' => '1792x1024'],
                        ['label' => '1024x1792（仅DallE-3支持）', 'value' => '1024x1792'],
                    ],
                ],
                [
                    'id' => 'ai_guest_use',
                    'label' => __('允许游客使用', PUOCK),
                    'type' => 'switch',
                    'sdt' => pk_is_checked('openai_guest_use'),
                    'tips' => __('是否在未登录状态下也可以使用', PUOCK),
                ],
            ]
        ];
    }
}
