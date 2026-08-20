<?php

define('PUOCK', 'puock');

$GLOBALS['wp_kses_post_calls'] = [];

function assert_contains($needle, $haystack, $message)
{
    if (strpos($haystack, $needle) === false) {
        fwrite(STDERR, $message . PHP_EOL);
        exit(1);
    }
}

function assert_not_contains($needle, $haystack, $message)
{
    if (strpos($haystack, $needle) !== false) {
        fwrite(STDERR, $message . PHP_EOL);
        exit(1);
    }
}

function assert_same($expected, $actual, $message)
{
    if ($expected !== $actual) {
        fwrite(STDERR, $message . PHP_EOL);
        exit(1);
    }
}

function __($text, $domain = null)
{
    return $text;
}

function add_shortcode($tag, $callback) {}
function remove_filter($hook, $callback) {}
function add_filter($hook, $callback, $priority = 10) {}

function pk_get_option($key)
{
    return 'download notice';
}

function esc_html($text)
{
    return htmlspecialchars((string)$text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function wp_kses_post($content)
{
    $GLOBALS['wp_kses_post_calls'][] = (string)$content;
    return strip_tags((string)$content, '<a>');
}

require __DIR__ . '/../inc/fun/short-code.php';

$link = '<a href="https://example.test/file.zip" target="_blank" rel="nofollow">download</a>';
$content = $link . '<script>alert(1)</script>';
$output = pk_download(['file' => 'file.zip', 'size' => '1MB'], $content);

assert_contains($link, $output, 'Download shortcode should preserve an allowed link.');
assert_not_contains('&lt;a href=', $output, 'Download shortcode should not render link markup as text.');
assert_not_contains('<script', $output, 'Download shortcode should filter unsafe HTML.');
assert_same(true, in_array($content, $GLOBALS['wp_kses_post_calls'], true), 'Download content should use wp_kses_post.');

echo "download shortcode tests passed\n";
