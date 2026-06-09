<?php

namespace Puock\Theme\setting {
    class PuockSetting
    {
        public static function get_config_debug_entry(): string
        {
            return '';
        }
    }
}

namespace {
    define('PUOCK', 'puock');
    define('PUOCK_CUR_VER_STR', '2.8.11');
    define('PUOCK_OPT', 'puock_options');

    global $_wp_admin_css_colors;
    $_wp_admin_css_colors = [];

    function assert_contains($needle, $haystack, $message)
    {
        if (strpos($haystack, $needle) === false) {
            fwrite(STDERR, $message . PHP_EOL);
            fwrite(STDERR, 'Expected to contain: ' . $needle . PHP_EOL);
            exit(1);
        }
    }

    function assert_not_contains($needle, $haystack, $message)
    {
        if (strpos($haystack, $needle) !== false) {
            fwrite(STDERR, $message . PHP_EOL);
            fwrite(STDERR, 'Expected not to contain: ' . $needle . PHP_EOL);
            exit(1);
        }
    }

    function get_user_option($key)
    {
        return '';
    }

    function __($text, $domain = null)
    {
        return $text;
    }

    function _e($text, $domain = null)
    {
        echo $text;
    }

    function get_template_directory()
    {
        return __DIR__ . '/missing-theme';
    }

    function get_template_directory_uri()
    {
        return 'http://example.test/wp-content/themes/puock';
    }

    function esc_attr($value)
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    function esc_js($value)
    {
        return addslashes((string)$value);
    }

    function esc_url($value)
    {
        return str_replace('&', '&#038;', (string)$value);
    }

    function wp_json_encode($value, $flags = 0)
    {
        return json_encode($value, $flags);
    }

    function get_user_locale()
    {
        return 'zh_CN';
    }

    function admin_url($path = '')
    {
        return 'http://example.test/wp-admin/' . ltrim($path, '/');
    }

    function wp_create_nonce($action)
    {
        return 'nonce-value';
    }

    function get_option($key, $default = false)
    {
        return $default;
    }

    $fields = [];
    ob_start();
    require __DIR__ . '/../inc/setting/template.php';
    $output = ob_get_clean();

    assert_contains(
        'smtp_test_url: "http://example.test/wp-admin/admin-ajax.php?action=pk_smtp_test_mail&nonce=nonce-value"',
        $output,
        'SMTP test URL in settings JS should keep nonce in the query string.'
    );
    assert_not_contains(
        'smtp_test_url: \'http://example.test/wp-admin/admin-ajax.php?action=pk_smtp_test_mail&#038;nonce=nonce-value\'',
        $output,
        'SMTP test URL should not be injected with HTML-escaped ampersands in script context.'
    );
    assert_contains(
        'update_url: "http://example.test/wp-admin/admin-ajax.php?action=update_theme_options&nonce=nonce-value"',
        $output,
        'Theme option update URL should include a nonce.'
    );
    assert_contains(
        'reset_url: "http://example.test/wp-admin/admin-ajax.php?action=reset_theme_options&nonce=nonce-value"',
        $output,
        'Theme option reset URL should include a nonce.'
    );
    assert_not_contains(
        'update_url: \'http://example.test/wp-admin/admin-ajax.php?action=update_theme_options&#038;nonce=nonce-value\'',
        $output,
        'Theme option update URL should not use HTML-escaped ampersands in script context.'
    );
    assert_not_contains(
        'reset_url: \'http://example.test/wp-admin/admin-ajax.php?action=reset_theme_options&#038;nonce=nonce-value\'',
        $output,
        'Theme option reset URL should not use HTML-escaped ampersands in script context.'
    );

    echo "setting template tests passed\n";
}
