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

$init = file_text('inc/init.php');
assert_contains('function pk_rest_api_user_can_access', $init, 'close_rest_api should expose a named capability helper.');
assert_contains('function pk_rest_authentication_errors', $init, 'close_rest_api should use a named REST authentication callback.');
assert_contains("add_filter('rest_authentication_errors', 'pk_rest_authentication_errors')", $init, 'close_rest_api should register the named REST authentication callback.');
assert_contains("current_user_can('edit_posts')", $init, 'Logged-in editors with edit_posts should still access REST.');
assert_contains("current_user_can('edit_pages')", $init, 'Logged-in editors with edit_pages should still access REST.');
assert_contains('is_user_logged_in()', $init, 'REST bypass should require a logged-in user.');
assert_contains("true === \$access || is_wp_error(\$access)", $init, 'REST auth callback should preserve existing success or error results.');
assert_contains("pk_rest_api_user_can_access()", $init, 'REST auth callback should defer to the editor capability helper.');
assert_not_contains("add_filter('rest_enabled', '__return_false')", $init, 'close_rest_api must not disable REST globally, or Gutenberg cannot save featured images.');
assert_not_contains("add_filter('rest_authentication_errors', function (\$access) {\n            return new WP_Error", $init, 'close_rest_api must not return 403 for every requester.');

$gutenberg = file_text('gutenberg/index.php');
assert_contains("register_block_type(\$block_dir)", $gutenberg, 'Gutenberg blocks should register from block.json metadata.');
assert_contains("is_readable(\$block_dir . '/block.json')", $gutenberg, 'Block registration should skip missing block.json files.');
assert_not_contains("wp_register_script(\$prefix . '-js'", $gutenberg, 'Block assets should come from block.json instead of a hand-registered handle.');

$block = file_text('gutenberg/components/alert/block.json');
assert_contains('"apiVersion": 3', $block, 'Alert block should use Block API version 3 for WordPress 7 iframed editor.');

$opt = file_text('inc/fun/opt.php');
assert_contains('is_block_editor()', $opt, 'Classic media scripts should not be forced onto the block editor.');

echo "rest api regression tests passed\n";
