<?php

function pk_smtp_test_mail_missing_label($field)
{
    $labels = [
        'smtp_open' => __('开启SMTP', PUOCK),
        'smtp_form' => __('发件人邮箱', PUOCK),
        'smtp_host' => __('SMTP服务器', PUOCK),
        'smtp_port' => __('SMTP端口', PUOCK),
        'smtp_u' => __('SMTP账户', PUOCK),
        'smtp_p' => __('SMTP密码', PUOCK),
    ];
    return $labels[$field] ?? $field;
}

function pk_smtp_test_mail_template()
{
    $blog_name = get_option('blogname');
    $home_url = home_url('/');
    $send_time = current_time('mysql');
    $title = sprintf(__('Puock 主题 SMTP 测试邮件 - %s', PUOCK), $blog_name);
    $primary_color = pk_get_option('email_primary_color', '#007bff');
    if (function_exists('sanitize_hex_color')) {
        $primary_color = sanitize_hex_color($primary_color) ?: '#007bff';
    } elseif (!preg_match('/^#[0-9a-fA-F]{3,6}$/', $primary_color)) {
        $primary_color = '#007bff';
    }
    $header_img = pk_get_option('email_header_img', '');
    $safe_blog_name = esc_html($blog_name);
    $safe_home_url = esc_url($home_url);
    $safe_send_time = esc_html($send_time);
    $safe_title = esc_html($title);
    $safe_primary_color = esc_attr($primary_color);

    $message = "
    <style>
        #p-smtp-test-mail{
            font-size: 14px;
            border:1px solid #dddddd;
            border-radius: 10px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }
        #p-smtp-test-mail a{
            color:{$safe_primary_color};
            text-decoration: none;
        }
        #p-smtp-test-mail .header{
            background-color: {$safe_primary_color};
            padding:15px;
            color:#fff;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        #p-smtp-test-mail .header-img img{
            width:100%;
            max-height:200px;
            object-fit:cover;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        #p-smtp-test-mail .header-img + .header{
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }
        #p-smtp-test-mail .main{
            padding:20px 15px;
            color:#343a40;
        }
        #p-smtp-test-mail .time{
            margin-top: 20px;
            font-weight: 600;
        }
        #p-smtp-test-mail .footer{
            padding:12px 15px;
            background-color: #8f969c;
            color:#fff;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
            font-size: 12px;
        }
    </style>
    <div id=\"p-smtp-test-mail\">";

    if (!empty($header_img)) {
        $message .= "
        <div class=\"header-img\">
            <img src=\"" . esc_url($header_img) . "\" alt=\"{$safe_blog_name}\">
        </div>";
    }

    $message .= "
        <div class=\"header\">
            {$safe_title}
        </div>
        <div class=\"main\">
            <p>" . __('如果您收到这封邮件，说明您的 SMTP 配置已成功生效！', PUOCK) . "</p>
            <p class=\"time\">" . sprintf(__('发送时间：%s', PUOCK), $safe_send_time) . "</p>
        </div>
        <div class=\"footer\">
            <span><a href=\"{$safe_home_url}\" target=\"_blank\">{$safe_blog_name}</a> · " . __('此邮件由系统发出，请勿直接回复。', PUOCK) . "</span>
        </div>
    </div>";

    return [$title, $message];
}

function pk_smtp_test_mail_callback()
{
    if (!current_user_can('edit_theme_options')) {
        wp_send_json_error(__('权限不足', PUOCK));
    }
    check_ajax_referer('pk_smtp_test_mail', 'nonce');

    $body = pk_ajax_get_req_body();
    if (!is_array($body)) {
        wp_send_json_error(__('请求参数错误', PUOCK));
    }

    $settings = isset($body['settings']) && is_array($body['settings']) ? $body['settings'] : [];
    $config = pk_smtp_normalize_config($settings);
    $missing = pk_smtp_missing_required_fields($config);
    if (!empty($missing)) {
        $labels = array_map('pk_smtp_test_mail_missing_label', $missing);
        wp_send_json_error(sprintf(__('请先完善 SMTP 配置：%s', PUOCK), implode('、', $labels)));
    }

    $current_user = wp_get_current_user();
    $recipient = pk_smtp_resolve_test_recipient(
        $body['recipient_type'] ?? 'admin',
        $body['recipient_email'] ?? '',
        get_option('admin_email'),
        $current_user->user_email ?? ''
    );
    if (!$recipient) {
        wp_send_json_error(__('收件人邮箱无效', PUOCK));
    }

    [$subject, $message] = pk_smtp_test_mail_template();
    $headers = ['Content-Type: text/html; charset=' . get_option('blog_charset')];
    $mail_error = null;
    $phpmailer_ref = null;
    $smtp_callback = function ($phpmailer) use ($config, &$phpmailer_ref) {
        $phpmailer_ref = $phpmailer;
        pk_smtp_apply_config($phpmailer, $config);
    };
    $failed_callback = function ($wp_error) use (&$mail_error) {
        $mail_error = $wp_error;
    };

    add_action('phpmailer_init', $smtp_callback, 999);
    add_action('wp_mail_failed', $failed_callback);
    try {
        $sent = wp_mail($recipient, $subject, $message, $headers);
    } finally {
        remove_action('phpmailer_init', $smtp_callback, 999);
        remove_action('wp_mail_failed', $failed_callback);
    }

    if (!$sent) {
        $error_message = pk_smtp_build_error_message(
            __('测试邮件发送失败', PUOCK),
            $mail_error,
            $phpmailer_ref,
            __('服务器未返回详细错误，请检查 SMTP 服务器、端口、加密方式、账号和授权码。', PUOCK)
        );
        wp_send_json_error($error_message);
    }

    wp_send_json_success([
        'message' => __('测试邮件发送成功', PUOCK),
        'recipient' => $recipient,
    ]);
}

pk_ajax_register('pk_smtp_test_mail', 'pk_smtp_test_mail_callback');
