<?php

class WP_Http
{
    public static function make_absolute_url($path, $base_url)
    {
        if (preg_match('/^[a-z][a-z0-9+.-]*:/i', $path)) {
            return $path;
        }
        if (strpos($path, '//') === 0) {
            return 'https:' . $path;
        }
        if (strpos($path, '/') === 0) {
            return 'https://example.test' . $path;
        }
        return rtrim($base_url, '/') . '/' . $path;
    }
}

function assert_same($expected, $actual, $message)
{
    if ($expected !== $actual) {
        fwrite(STDERR, $message . PHP_EOL);
        fwrite(STDERR, 'Expected: ' . var_export($expected, true) . PHP_EOL);
        fwrite(STDERR, 'Actual:   ' . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}

function get_post($value)
{
    return is_object($value) ? $value : null;
}

function has_post_thumbnail($post_id)
{
    return false;
}

function get_the_post_thumbnail_url($post_id, $size)
{
    return false;
}

function get_post_meta($post_id, $key, $single)
{
    return '';
}

function home_url($path = '')
{
    return 'https://example.test/blog' . $path;
}

function esc_url($url, $protocols = null)
{
    $url = str_replace(' ', '%20', $url);
    return preg_match('#^https?://#i', $url) ? $url : '';
}

function get_random_default_image($post_id = null)
{
    return 'DEFAULT-' . ($post_id === null ? 'none' : $post_id);
}

$source = file_get_contents(__DIR__ . '/../functions.php');
$start = strpos($source, 'function pk_get_post_cover_image');
$end = strpos($source, 'function pk_glob', $start);
if ($start === false || $end === false) {
    fwrite(STDERR, 'Unable to load post cover functions.' . PHP_EOL);
    exit(1);
}
eval(substr($source, $start, $end - $start));

$cases = [
    'absolute URL' => [
        'https://images.example/a.jpg',
        'https://images.example/a.jpg',
    ],
    'root-relative URL' => [
        '/wp-content/uploads/a.jpg',
        'https://example.test/wp-content/uploads/a.jpg',
    ],
    'protocol-relative URL' => [
        '//cdn.example/a.jpg',
        'https://cdn.example/a.jpg',
    ],
    'path-relative URL' => [
        'wp-content/uploads/a.jpg',
        'https://example.test/blog/wp-content/uploads/a.jpg',
    ],
];

foreach ($cases as $name => [$src, $expected]) {
    $post = (object)[
        'ID' => 560,
        'post_content' => '<p><img src="' . $src . '"></p>',
    ];
    assert_same($expected, pk_get_post_cover_image($post), $name . ' should be used as the real cover.');
    assert_same($expected, get_post_images($post), $name . ' should not fall back to the default cover.');
}

$unsafe_post = (object)[
    'ID' => 560,
    'post_content' => '<img src="javascript:alert(1)">',
];
assert_same('', pk_get_post_cover_image($unsafe_post), 'Unsafe image schemes should be rejected.');
assert_same('DEFAULT-560', get_post_images($unsafe_post), 'Rejected image schemes should use the default cover.');

echo "post cover image tests passed\n";
