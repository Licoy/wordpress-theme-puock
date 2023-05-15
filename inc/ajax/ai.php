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
    $json_body = file_get_contents('php://input');
    $body = json_decode($json_body, true);
    $text = $body['text'] ?? '';
    if (empty($text)) {
        wp_die('<code>请输入问题</code>');
    }
    $openai_url = pk_get_option('openai_api_agent', 'https://api.openai.com');
    $openai_api_key = pk_get_option('openai_api_key');
    if (empty($openai_api_key)) {
        wp_die('<code>请先配置OpenAI API Key</code>');
    }
    set_time_limit(0);
    ini_set('memory_limit', '-1');
    header('X-Accel-Buffering: no');
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
        "stream" => true,
    ];
    if ($max_tokens > 0) {
        $args['max_tokens'] = $max_tokens - $use_total_token;
    }
    $last_not_lines = '';
    $callback = function ($curl_info, $data) use (&$last_not_lines) {
        try{
            $try_json = json_decode($data, true);
            if($try_json && isset($try_json['error'])){
                echo $try_json['error']['message'];
                ob_flush();
                flush();
            }
        }catch (\Throwable $e){}
        $tmp_lines = explode("\n", $last_not_lines . $data);
        $last_not_lines = '';
        $use_lines = [];
        $not_lines = [];
        for ($i = 0; $i < count($tmp_lines); $i++) {
            $line = $tmp_lines[$i];
            if (strpos($line, 'data:') === 0) {
                $t = trim(str_replace('data:', '', $line));
                $resJson = json_decode($t, true);
                if ($resJson) {
                    $use_lines[] = $resJson;
                } else {
                    $not_lines[] = $line;
                }
            } else {
                $not_lines[] = $line;
            }
        }
        $last_not_lines .= join("\n", $not_lines);
        $resData = '';
        if (count($use_lines) > 0) {
            foreach ($use_lines as $line) {
                if (isset($line['error'])) {
                    $resData = $line['error']['message'];
                } else {
                    if (isset($line['choices']) && count($line['choices']) > 0) {
                        if (isset($line['choices'][0]['delta']['content'])) {
                            $resData = $line['choices'][0]['delta']['content'];
                        }
                    }
                }
                if ($resData) {
                    echo $resData;
                    $resData = '';
                    ob_flush();
                    flush();
                }
            }
        }
        return strlen($data);
    };
    try {
        $openaiClient->chat($args,$callback);
        wp_die();
    } catch (Exception $e) {
        wp_die('<code>AI问答出错：' . $e->getMessage() . '</code>');
    }
}

pk_ajax_register('pk_ai_ask', 'pk_ajax_ai_ask', true);
