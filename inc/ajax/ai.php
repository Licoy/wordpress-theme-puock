<?php

use Orhanerday\OpenAi\OpenAi;
use Rahul900day\Gpt3Encoder\Encoder;

function pk_openai_token_len($text): int
{
    return count(Encoder::encode($text));
}

function pk_ajax_ai_ask()
{
    if (!pk_is_checked('openai_enable')) {
        wp_die('<code>未启用AI问答</code>');
    }
    $uid = get_current_user_id();
    if (!$uid && !pk_is_checked('openai_guest_use')) {
        wp_die('<code>游客不允许使用AI问答</code>');
    }
    $text = $_POST['text'];
    if (empty($text)) {
        wp_die('<code>请输入问题</code>');
    }
    $openai_url = pk_get_option('openai_api_agent', 'https://api.openai.com');
    $openai_api_key = pk_get_option('openai_api_key');
    if (empty($openai_api_key)) {
        wp_die('<code>请先配置OpenAI API Key</code>');
    }
    $openaiClient = new OpenAi($openai_api_key);
    $openaiClient->setBaseURL($openai_url);
    $sys_content = pk_get_option('openai_model_sys_content');
    $messages = [];
    $use_total_token = pk_openai_token_len($text);
    if (!empty($sys_content)) {
        $messages[] = ['role' => 'system', 'content' => $sys_content];
        $use_total_token += pk_openai_token_len($sys_content);
    }
    $messages[] = ['role' => 'user', 'content' => $text];
    $max_tokens = pk_get_option('openai_max_tokens', 0);
    $temperature = pk_get_option('openai_temperature', 0.9);
    $args = [
        'model' => 'gpt-3.5-turbo',
        'messages' => $messages,
        'temperature' => $temperature,
    ];
    if ($max_tokens > 0) {
        $args['max_tokens'] = $max_tokens - $use_total_token;
    }
    try {
        $chat_res = $openaiClient->chat($args);
        $res = json_decode($chat_res);
        if(!$res){
            wp_die('<code>AI问答解析：' . $chat_res . '</code>');
        }
        if(isset($res->error)){
            wp_die('<code>AI问答异常：' . $res->error . '</code>');
        }
        $answer = $res->choices[0]->message->content;
        wp_die($answer);
    } catch (Exception $e) {
        wp_die('<code>AI问答出错：' . $e->getMessage() . '</code>');
    }
}

pk_ajax_register('pk_ai_ask', 'pk_ajax_ai_ask', true);
