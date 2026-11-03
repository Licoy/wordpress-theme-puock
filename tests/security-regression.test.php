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
assert_contains('/assets/dist/js/admin.min.js', $core, 'Admin scripts should use the packaged production JS path.');
assert_not_contains('/assets/dist/admin.min.js', $core, 'Admin scripts must not reference a path omitted from the production package.');

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
assert_contains('$url = add_query_arg($query, $apiUrl)', $oauth, 'CCY OAuth should follow the documented GET query contract.');
assert_contains('wp_safe_remote_get($url', $oauth, 'CCY OAuth should validate the configured remote endpoint.');
assert_not_contains('wp_remote_post($apiUrl', $oauth, 'CCY OAuth should not send documented query parameters as a POST body.');

$timthumb = file_text('timthumb.php');
assert_contains('isSafeRemoteHost', $timthumb, 'TimThumb should validate remote hosts against private/reserved IPs.');
assert_contains('CURLOPT_FOLLOWLOCATION, false', $timthumb, 'TimThumb should not follow redirects to unvalidated hosts.');
assert_contains('pathIsWithin($real, $this->docRoot)', $timthumb, 'TimThumb local file checks should use directory-boundary containment.');

$pageAi = file_text('assets/js/page-ai.js');
assert_contains('sanitizeAiHtml(html)', $pageAi, 'AI page should sanitize rendered Markdown HTML.');
assert_contains('allowedTags', $pageAi, 'AI page sanitizer should use an allowlist.');

$puockJs = file_text('assets/js/puock.js');
assert_contains("_wpnonce: nonce", $puockJs, 'Like request should send the per-post nonce.');

$frontLogin = file_text('inc/ajax/page-front-login.php');
assert_contains('get_password_reset_key($user)', $frontLogin, 'Forgot password should use core reset keys.');
assert_not_contains("'password' => \$data['password'], 'email'", $frontLogin, 'Forgot password must not accept the new password from the requester.');
assert_not_contains('pk_front_forget_password_reset_exec', $frontLogin, 'Legacy GET password reset endpoint must be removed.');
assert_contains("(int)get_transient('pk_login_try_' . \$ip) + 1", $frontLogin, 'Login attempt counter should start from zero.');

$meta = file_text('inc/classes/meta/PuockAbsMeta.php');
assert_contains("current_user_can('edit_post', \$post_id)", $meta, 'Meta box save should check edit_post capability.');
assert_contains('$this->sanitizeValue($option', $meta, 'Meta box values should be sanitized by type.');
assert_contains('esc_attr($value)', $meta, 'Meta box text values should be escaped on render.');
assert_contains('esc_textarea($value)', $meta, 'Meta box textarea values should be escaped on render.');

$init = file_text('inc/init.php');
assert_contains("add_filter(\$pk_kses_filter, 'wp_filter_post_kses')", $init, 'Term and link descriptions should keep a kses filter.');
assert_contains("add_filter('term_description', 'wp_kses_post')", $init, 'Term descriptions should be filtered on display.');

$functions = file_text('functions.php');
assert_contains('esc_attr(wp_strip_all_tags((string)@$post->post_title))', $functions, 'Content image title/alt should escape the post title.');
foreach (['templates/module-post.php', 'templates/module-cms.php', 'pages/template-archives.php', 'pages/thmplate-sitemap.php'] as $tpl) {
    assert_not_contains('="<?php the_title() ?>"', file_text($tpl), $tpl . ' should use the_title_attribute() in attributes.');
}

$typesetting = file_text('inc/lib/ChineseTypesetting.php');
assert_not_contains("'＜' => '<'", $typesetting, 'Full-width < must not be converted after kses.');
assert_not_contains("'＞' => '>'", $typesetting, 'Full-width > must not be converted after kses.');
assert_not_contains("'＆' => '&'", $typesetting, 'Full-width & must not be converted after kses.');

$widget = file_text('inc/fun/widget.php');
assert_contains("comment_approved = '1'", $widget, 'Reader wall widget should only count approved comments.');
assert_contains('esc_attr($author->name)', $widget, 'Reader wall widget should escape comment author names.');
assert_contains('esc_attr($comment->comment_author)', $widget, 'Recent comments widget should escape comment author names.');
assert_contains("'post_status' => 'publish'", $widget, 'Recent comments widget should only list comments on public posts.');
assert_contains('esc_attr($read->name)', file_text('pages/template-reads.php'), 'Reader wall page should escape comment author names.');

$ai = file_text('inc/ajax/ai.php');
assert_contains("'application/json') !== 0", $ai, 'AI endpoint should only accept JSON requests.');
assert_contains("header('Content-Type: text/plain; charset=utf-8')", $ai, 'AI answers must not be served as HTML.');

$core = file_text('inc/fun/core.php');
assert_contains("add_action('login_init', 'login_protection')", $core, 'Login protection should run on login_init.');
assert_contains('hash_equals($token, $cookie)', $core, 'Login protection should verify a signed cookie.');
assert_contains("array_filter(array_map('absint', \$link_cats))", $core, 'Link category IDs should be cast to integers before SQL.');

assert_contains("preg_quote(preg_replace('/^www\\./i', '', \$requestHost), '/')", $timthumb, 'TimThumb should escape the Host header before using it in a regex.');
assert_contains('CURLOPT_RESOLVE', $timthumb, 'TimThumb should pin curl to the validated IP.');
assert_contains("'100.64.0.0/10'", $timthumb, 'TimThumb should block CGNAT/metadata ranges.');
assert_not_contains('file_get_contents($url, false, $context', $timthumb, 'TimThumb should not fall back to unpinned remote fetching.');

assert_contains('$base->http->isVerifyCA = true', file_text('inc/oauth/oauth.php'), 'OAuth HTTP client should verify TLS certificates.');

assert_contains("add_filter('posts_search', 'pk_sc_hidden_content_search_sql')", $shortcodes, 'Hidden shortcode content should be excluded from search.');
assert_contains("hash_equals((string)\$pass, \$_REQUEST['pass'])", $shortcodes, 'Password shortcode should use strict comparison.');

$comment = file_text('inc/fun/comment.php');
assert_contains("apply_filters('pk_comment_author_view_is_author', \$author_id > 0 && \$author_id === get_current_user_id(), \$author_id)", $comment, 'Author-only comments should filter a boolean.');

assert_contains('<?php if (post_password_required()) return; ?>', file_text('comments.php'), 'Comments must be hidden on password-protected posts.');

$ajax = file_text('inc/fun/ajax.php');
assert_contains("pk_theme_options_kses(\$body, get_option(PUOCK_OPT))", $ajax, 'Theme options should be filtered for users without unfiltered_html.');
assert_contains("current_user_can('unfiltered_html')", file_text('inc/setting/PuockSetting.php'), 'Settings debug entry should require unfiltered_html.');

echo "security regression tests passed\n";
