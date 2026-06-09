<?php

use Orhanerday\OpenAi\OpenAi;
use Rahul900day\Gpt3Encoder\Encoder;

function pk_openai_token_len($text): int
{
    return count(Encoder::encode($text));
}

function pk_ai_die($msg)
{
    wp_die('<code>' . esc_html($msg) . '</code>');
}

function pk_ai_rate_limited(int $uid): bool
{
    $client = $uid > 0 ? 'u:' . $uid : 'ip:' . pk_get_client_ip();
    $key = 'pk_ai_ask_' . md5($client);
    $limit = $uid > 0 ? 12 : 6;
    $count = (int)get_transient($key);
    if ($count >= $limit) {
        return true;
    }
    set_transient($key, $count + 1, MINUTE_IN_SECONDS);
    return false;
}

function pk_ai_get_enabled_model(string $model): ?array
{
    $models = pk_get_option('ai_chat_models', []);
    if (!is_array($models)) {
        return null;
    }
    foreach ($models as $item) {
        if (!is_array($item) || empty($item['enable']) || empty($item['name'])) {
            continue;
        }
        if ((string)$item['name'] === $model) {
            return $item;
        }
    }
    return null;
}

function pk_ai_request_max_tokens(array $model_config, int $used_tokens): int
{
    $model_limit = max(0, (int)($model_config['max_tokens'] ?? 0));
    $site_limit = max(0, (int)pk_get_option('openai_max_tokens', 0));
    $limit = $model_limit > 0 ? $model_limit : 4096;
    if ($site_limit > 0) {
        $limit = min($limit, $site_limit);
    }
    if ($used_tokens >= $limit) {
        return 0;
    }
    return min(1024, $limit - $used_tokens);
}

function pk_ajax_ai_ask()
{
    if (!pk_is_checked('ai_chat_enable')) {
        pk_ai_die(__('未启用AI助手', PUOCK));
    }
    $uid = get_current_user_id();
    if (!$uid && !pk_is_checked('ai_guest_use')) {
        pk_ai_die(__('游客不允许使用AI助手', PUOCK));
    }
    if (pk_ai_rate_limited((int)$uid)) {
        pk_ai_die(__('请求过于频繁，请稍后再试', PUOCK));
    }
    $json_body = file_get_contents('php://input');
    if (strlen($json_body) > 65536) {
        pk_ai_die(__('请求内容过长', PUOCK));
    }
    $body = json_decode($json_body, true);
    if (!is_array($body)) {
        pk_ai_die(__('请求格式错误', PUOCK));
    }
    $text = trim((string)($body['text'] ?? ''));
    $model = (string)($body['model'] ?? '');
    if (empty($text)) {
        pk_ai_die(__('请输入描述', PUOCK));
    }
    if (strlen($text) > 20000) {
        pk_ai_die(__('描述内容过长', PUOCK));
    }
    $ai_platform = pk_get_option('ai_chat_platform','gptnb');
    switch ($ai_platform){
        case 'gptnb': $openai_url='https://goapi.gptnb.ai';break;
        case 'openai': $openai_url='https://api.openai.com';break;
        default:$openai_url=pk_get_option('ai_chat_agent', 'https://api.openai.com');
    }
    $openai_api_key = pk_get_option('ai_chat_key');
    if (empty($openai_api_key)) {
        pk_ai_die(__('请先配置OpenAI API Key', PUOCK));
    }
    $openaiClient = new OpenAi($openai_api_key);
    $openaiClient->setBaseURL($openai_url);
    $use_img_mode = $body['imgMode'] ?? false;
    if ($use_img_mode) {
        if (!pk_is_checked('ai_draw_dall_e')) {
            pk_ai_die(__('暂未启用AI绘图', PUOCK));
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
                pk_ai_die(__('AI绘图失败：解析响应错误', PUOCK));
            }
            $answer = esc_url_raw($res->data[0]->url ?? '');
            if (empty($answer)) {
                pk_ai_die(__('AI绘图失败：响应图片地址无效', PUOCK));
            }
            wp_die('![img](' . $answer . ')');
        } catch (Exception $e) {
            pk_ai_die(sprintf(__('AI绘图失败：%s', PUOCK), $e->getMessage()));
        }
    }
    $model_config = pk_ai_get_enabled_model($model);
    if (!$model_config) {
        pk_ai_die(__('模型不可用或未启用', PUOCK));
    }
    $use_stream = pk_is_checked('ai_chat_stream');
    if ($use_stream) {
        set_time_limit(120);
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
    $max_tokens = pk_ai_request_max_tokens($model_config, $use_total_token);
    if ($max_tokens <= 0) {
        pk_ai_die(__('输入内容超过模型可用 Tokens 限制', PUOCK));
    }
    $temperature = pk_get_option('openai_temperature', 0.9);
    $args = [
        'model' => $model,
        'messages' => $messages,
        'temperature' => $temperature,
        'max_tokens' => $max_tokens,
    ];
    if ($use_stream) {
        $args['stream'] = true;
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
                pk_ai_die(sprintf(__('AI问答解析：%s', PUOCK), $chat_res));
            }
            if (isset($res->error)) {
                pk_ai_die(sprintf(__('AI问答异常：%s', PUOCK), is_string($res->error) ? $res->error : wp_json_encode($res->error)));
            }
            $answer = $res->choices[0]->message->content;
            echo $answer;
        }
        wp_die();
    } catch (Exception $e) {
        pk_ai_die(sprintf(__('AI问答出错：%s', PUOCK), $e->getMessage()));
    }
}

pk_ajax_register('pk_ai_ask', 'pk_ajax_ai_ask', true);
