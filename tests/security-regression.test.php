<?php

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

function file_text($path)
{
    return file_get_contents(__DIR__ . '/../' . $path);
}

$ai = file_text('inc/ajax/ai.php');
assert_contains('pk_ai_rate_limited((int)$uid)', $ai, 'AI endpoint should rate-limit callers.');
assert_contains('pk_ai_get_enabled_model($model)', $ai, 'AI endpoint should enforce enabled model selection.');
assert_contains('strlen($json_body) > 65536', $ai, 'AI endpoint should cap request body size.');
assert_contains('set_time_limit(120)', $ai, 'AI streaming should have a bounded execution time.');

$shortcodes = file_text('inc/fun/short-code.php');
assert_contains('$wpdb->prepare($query, $query_args)', $shortcodes, 'Reply shortcode query should be parameterized.');
assert_contains('pk_get_comment_proof_cookie()', $shortcodes, 'Anonymous reply shortcode access should require signed proof.');
assert_contains('esc_attr(pk_sc_safe_class', $shortcodes, 'Shortcode class attributes should be sanitized.');
assert_contains('wp_json_encode(esc_url_raw($url))', $shortcodes, 'DPlayer URL should be encoded for JavaScript context.');

$core = file_text('inc/fun/core.php');
assert_contains('pk_comment_proof_sign', $core, 'Comment proof cookie should be signed.');
assert_contains('hash_equals(pk_comment_proof_sign($payload), $sig)', $core, 'Comment proof cookie signature should use constant-time comparison.');
assert_contains('var_export($sites, true)', $core, 'Thumbnail allowlist file generation should not interpolate raw PHP.');

$opt = file_text('inc/fun/opt.php');
assert_contains('wp_verify_nonce($nonce, \'puock_like_\' . $id)', $opt, 'Like endpoint should verify nonce.');
assert_contains('is_post_publicly_viewable($post)', $opt, 'Like endpoint should reject non-public posts.');
assert_contains("min(300, max(40, absint(\$_GET['w'] ?? 100)))", $opt, 'Captcha width should be bounded.');
assert_contains("min(120, max(20, absint(\$_GET['h'] ?? 40)))", $opt, 'Captcha height should be bounded.');

$poster = file_text('inc/ajax/page-poster.php');
assert_contains('is_post_publicly_viewable($post)', $poster, 'Poster endpoint should reject non-public posts.');
assert_contains('esc_html($title)', $poster, 'Poster title should be escaped in HTML context.');
assert_contains('esc_js($title)', $poster, 'Poster title should be escaped in JavaScript context.');

$oauth = file_text('inc/oauth/RainbowOAuth.php');
assert_contains('parse_url($apiBase, PHP_URL_SCHEME) !== \'https\'', $oauth, 'CCY OAuth API base should require HTTPS.');
assert_contains('wp_remote_post($apiUrl', $oauth, 'CCY OAuth AppKey should not be sent in the URL query string.');
assert_not_contains('wp_remote_get($url', $oauth, 'CCY OAuth should not use GET with AppKey in the URL.');

$timthumb = file_text('timthumb.php');
assert_contains('isSafeRemoteHost', $timthumb, 'TimThumb should validate remote hosts against private/reserved IPs.');
assert_contains('CURLOPT_FOLLOWLOCATION, false', $timthumb, 'TimThumb should not follow redirects to unvalidated hosts.');
assert_contains('pathIsWithin($real, $this->docRoot)', $timthumb, 'TimThumb local file checks should use directory-boundary containment.');

$pageAi = file_text('assets/js/page-ai.js');
assert_contains('sanitizeAiHtml(html)', $pageAi, 'AI page should sanitize rendered Markdown HTML.');
assert_contains('allowedTags', $pageAi, 'AI page sanitizer should use an allowlist.');

$puockJs = file_text('assets/js/puock.js');
assert_contains("_wpnonce: nonce", $puockJs, 'Like request should send the per-post nonce.');

echo "security regression tests passed\n";
