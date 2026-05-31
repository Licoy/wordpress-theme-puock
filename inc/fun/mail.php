<?php

function pk_smtp_is_enabled_value($value)
{
    return in_array($value, [true, 1, '1', 'true'], true);
}

function pk_smtp_sanitize_text($value)
{
    $value = trim((string)$value);
    if (function_exists('sanitize_text_field')) {
        return sanitize_text_field($value);
    }
    return trim(strip_tags($value));
}

function pk_smtp_sanitize_email_value($value)
{
    $value = trim((string)$value);
    if (function_exists('sanitize_email')) {
        return sanitize_email($value);
    }
    return filter_var($value, FILTER_SANITIZE_EMAIL);
}

function pk_smtp_is_valid_email($email)
{
    if (function_exists('is_email')) {
        return (bool)is_email($email);
    }
    return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
}

function pk_smtp_normalize_config(array $settings)
{
    return [
        'open' => pk_smtp_is_enabled_value($settings['smtp_open'] ?? false),
        'ssl' => pk_smtp_is_enabled_value($settings['smtp_ssl'] ?? false),
        'from' => pk_smtp_sanitize_email_value($settings['smtp_form'] ?? ''),
        'from_name' => pk_smtp_sanitize_text($settings['smtp_form_n'] ?? ''),
        'host' => pk_smtp_sanitize_text($settings['smtp_host'] ?? ''),
        'port' => (int)($settings['smtp_port'] ?? 0),
        'username' => pk_smtp_sanitize_text($settings['smtp_u'] ?? ''),
        'password' => trim((string)($settings['smtp_p'] ?? '')),
    ];
}

function pk_smtp_get_option_config()
{
    return pk_smtp_normalize_config([
        'smtp_open' => pk_get_option('smtp_open', false),
        'smtp_ssl' => pk_get_option('smtp_ssl', false),
        'smtp_form' => pk_get_option('smtp_form', ''),
        'smtp_form_n' => pk_get_option('smtp_form_n', ''),
        'smtp_host' => pk_get_option('smtp_host', ''),
        'smtp_port' => pk_get_option('smtp_port', ''),
        'smtp_u' => pk_get_option('smtp_u', ''),
        'smtp_p' => pk_get_option('smtp_p', ''),
    ]);
}

function pk_smtp_missing_required_fields(array $config)
{
    $missing = [];
    if (!$config['open']) {
        $missing[] = 'smtp_open';
    }
    if (empty($config['from']) || !pk_smtp_is_valid_email($config['from'])) {
        $missing[] = 'smtp_form';
    }
    if (empty($config['host'])) {
        $missing[] = 'smtp_host';
    }
    if (empty($config['port'])) {
        $missing[] = 'smtp_port';
    }
    if (empty($config['username'])) {
        $missing[] = 'smtp_u';
    }
    if (empty($config['password'])) {
        $missing[] = 'smtp_p';
    }
    return $missing;
}

function pk_smtp_resolve_test_recipient($type, $custom_email, $admin_email, $current_user_email)
{
    $type = pk_smtp_sanitize_text($type);
    if ($type === 'admin') {
        $email = $admin_email;
    } elseif ($type === 'current') {
        $email = $current_user_email;
    } elseif ($type === 'custom') {
        $email = $custom_email;
    } else {
        return '';
    }

    $email = pk_smtp_sanitize_email_value($email);
    if (!$email || !pk_smtp_is_valid_email($email)) {
        return '';
    }
    return $email;
}

function pk_smtp_apply_config($phpmailer, array $config)
{
    $phpmailer->From = $config['from'];
    $phpmailer->FromName = $config['from_name'];
    $phpmailer->Host = $config['host'];
    $phpmailer->Port = $config['port'];
    $phpmailer->SMTPSecure = $config['ssl'] ? 'ssl' : '';
    $phpmailer->Username = $config['username'];
    $phpmailer->Password = $config['password'];
    $phpmailer->IsSMTP();
    $phpmailer->SMTPAuth = true;
}

function pk_smtp_build_error_message($fallback, $mail_error = null, $phpmailer = null)
{
    $details = [];
    if (function_exists('is_wp_error') && is_wp_error($mail_error) && $mail_error->get_error_message()) {
        $details[] = $mail_error->get_error_message();
    } elseif (is_object($mail_error) && method_exists($mail_error, 'get_error_message')) {
        $error_message = $mail_error->get_error_message();
        if (!empty($error_message)) {
            $details[] = $error_message;
        }
    } elseif (is_string($mail_error) && trim($mail_error) !== '') {
        $details[] = trim($mail_error);
    }

    if (is_object($phpmailer) && !empty($phpmailer->ErrorInfo)) {
        $details[] = trim((string)$phpmailer->ErrorInfo);
    }

    $details = array_values(array_unique(array_filter($details)));
    if (empty($details)) {
        return $fallback;
    }
    return $fallback . '：' . implode('；', $details);
}
