<?php

use Orhanerday\OpenAi\OpenAi;
use Rahul900day\Gpt3Encoder\Encoder;

function pk_openai_token_len($text): int
{
    return count(Encoder::encode($text));
}

function pk_ajax_ai_ask()
{
    if (!pk_is_checked('ai_chat_enable')) {
        wp_die('<code>未启用AI助手</code>');
    }
    $uid = get_current_user_id();
    if (!$uid && !pk_is_checked('ai_guest_use')) {
        wp_die('<code>游客不允许使用AI助手</code>');
    }
    $json_body = file_get_contents('php://input');
    $body = json_decode($json_body, true);
    $text = $body['text'] ?? '';
    $model = $body['model'] ?? '';
    if (empty($text)) {
        wp_die('<code>请输入描述</code>');
    }
    $openai_url = pk_get_option('ai_chat_agent', 'https://api.openai.com');
    $openai_api_key = pk_get_option('ai_chat_key');
    if (empty($openai_api_key)) {
        wp_die('<code>请先配置OpenAI API Key</code>');
    }
    $openaiClient = new OpenAi($openai_api_key);
    $openaiClient->setBaseURL($openai_url);
    $use_img_mode = $body['imgMode'] ?? false;
    if ($use_img_mode) {
        if (!pk_is_checked('ai_draw_dall_e')) {
            wp_die('<code>暂未启用AI绘图</code>');
        }
        try {
            $chat_res = $openaiClient->image([
                'model'=> pk_get_option('ai_draw_dall_e_model', 'dall-e-2'),
                'prompt' => $text,
                'n' => 1,
                'size' => pk_get_option('ai_draw_dall_e_size', '512x512'),
                'response_format' => 'url',
            ]);
            $res = json_decode($chat_res);
            if (!$res) {
                wp_die('<code>AI绘图失败：解析响应错误</code>');
            }
            $answer = $res->data[0]->url;
            wp_die('![img](' . $answer . ')');
        } catch (Exception $e) {
            wp_die('<code>AI绘图失败：' . $e->getMessage() . '</code>');
        }
    }
    $use_stream = pk_is_checked('ai_chat_stream');
    if ($use_stream) {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        header('Content-Type: text/event-stream');
        header('X-Accel-Buffering: no');
    }
    $sys_content = pk_get_option('ai_chat_model_sys_prompt');
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
        'model' => $model,
        'messages' => $messages,
        'temperature' => $temperature,
    ];
    if ($use_stream) {
        $args['stream'] = true;
    }
    if ($max_tokens > 0) {
        $args['max_tokens'] = $max_tokens - $use_total_token;
    }
    try {
        if ($use_stream) {
            $openaiClient->chat($args, function ($curl_info, $data) {
                echo $data;
                ob_flush();
                flush();
                return strlen($data);
            });
        } else {
            $chat_res = $openaiClient->chat($args);
            $res = json_decode($chat_res);
            if (!$res) {
                wp_die('<code>AI问答解析：' . $chat_res . '</code>');
            }
            if (isset($res->error)) {
                wp_die('<code>AI问答异常：' . $res->error . '</code>');
            }
            $answer = $res->choices[0]->message->content;
            echo $answer;
        }
        wp_die();
    } catch (Exception $e) {
        wp_die('<code>AI问答出错：' . $e->getMessage() . '</code>');
    }
}

pk_ajax_register('pk_ai_ask', 'pk_ajax_ai_ask', true);
