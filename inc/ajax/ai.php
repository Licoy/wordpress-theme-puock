<?php


use GuzzleHttp\Client;
use Tectalic\OpenAi\Authentication;
use Tectalic\OpenAi\Manager;
use Tectalic\OpenAi\Models\ChatCompletions\CreateRequest;

function pk_ajax_ai_ask()
{
    if (!pk_is_checked('openai_enable')) {
        wp_die('<code>未启用AI问答</code>');
    }
    $uid = get_current_user_id();
    if(!$uid && !pk_is_checked('openai_guest_use')){
        wp_die('<code>游客不允许使用AI问答</code>');
    }
    $text = $_POST['text'];
    if (empty($text)) {
        wp_die('<code>请输入问题</code>');
    }
    $openai_url = pk_get_option('openai_api_agent', 'https://api.openai.com/v1');
    $openai_api_key = pk_get_option('openai_api_key');
    if (empty($openai_api_key)) {
        wp_die('<code>请先配置OpenAI API Key</code>');
    }
    $openaiClient = new \Tectalic\OpenAi\Client(new Client(), new Authentication($openai_api_key), $openai_url);
    $sys_content = pk_get_option('openai_model_sys_content');
    $messages = [];
    if (!empty($sys_content)) {
        $messages[] = ['role' => 'system', 'content' => $sys_content];
    }
    $messages[] = ['role' => 'user', 'content' => $text];
    $max_tokens = pk_get_option('openai_max_tokens', 0);
    $temperature = pk_get_option('openai_temperature', 0.9);
    $args = [
        'model' => 'gpt-3.5-turbo',
        'messages' => $messages,
        'temperature' => $temperature,
    ];
    if($max_tokens>0){
        $args['max_tokens'] = $max_tokens;
    }
    try {
        $res = $openaiClient->chatCompletions()->create(
            new CreateRequest($args)
        )->toModel();
        if ($res && count($res->choices) > 0) {
            wp_die($res->choices[0]->message->content);
        }
    } catch (Exception $e) {
        wp_die('<code>AI问答出错：' . $e->getMessage() . '</code>');
    }
}

pk_ajax_register('pk_ai_ask', 'pk_ajax_ai_ask', true);
