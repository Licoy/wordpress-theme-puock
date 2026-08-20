<?php

define('PUOCK', 'puock');

$GLOBALS['puc_api_errors'] = [];

class WP_Error
{
    private $code;

    public function __construct($code, $message = '', $data = null)
    {
        $this->code = $code;
    }

    public function get_error_code()
    {
        return $this->code;
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

function __($text, $domain = null)
{
    return $text;
}

function do_action($hook, ...$args)
{
    if ($hook === 'puc_api_error') {
        $GLOBALS['puc_api_errors'][] = $args;
    }
}

function wp_json_encode($value, $flags = 0)
{
    return json_encode($value, $flags);
}

$source = file_get_contents(__DIR__ . '/../functions.php');
assert_same(true, strpos($source, 'REQUIRE_RELEASE_ASSETS') !== false, 'GitHub updates must require a matching release asset.');
assert_same(true, strpos($source, '/^Puock-V\d+\.\d+\.\d+\.zip$/D') !== false, 'GitHub updates must require the exact case-sensitive asset filename.');
assert_same(false, strpos($source, 'puock-update.php?r=fastgit') !== false, 'Legacy FastGit settings must fall back to the worker source.');

$resource_options = file_get_contents(__DIR__ . '/../inc/setting/options/OptionResource.php');
assert_same(false, strpos($resource_options, "'value' => 'fastgit'") !== false, 'FastGit must not be offered to new installations.');

$start = strpos($source, 'function pk_update_package_url_is_safe');
$end = strpos($source, 'function pk_update()', $start);
if ($start === false || $end === false) {
    fwrite(STDERR, 'Unable to load update package validation functions.' . PHP_EOL);
    exit(1);
}
eval(substr($source, $start, $end - $start));

$version = '2.10.4';
$github_asset = 'https://github.com/Licoy/wordpress-theme-puock/releases/download/v2.10.4/Puock-V2.10.4.zip';
$worker_asset = 'https://gh.gitcdn.top/https://github.com/Licoy/wordpress-theme-puock/releases/download/v2.10.4/Puock-V2.10.4.zip';

assert_same(true, pk_update_package_url_is_safe($version, $github_asset), 'GitHub release asset should be accepted.');
assert_same(true, pk_update_package_url_is_safe($version, $worker_asset), 'Official proxy release asset should be accepted.');
assert_same(false, pk_update_package_url_is_safe($version, 'https://github.com/Licoy/wordpress-theme-puock/archive/refs/tags/v2.10.4.zip'), 'GitHub source archives must be rejected.');
assert_same(false, pk_update_package_url_is_safe($version, 'https://api.github.com/repos/Licoy/wordpress-theme-puock/zipball/v2.10.4'), 'GitHub zipball URLs must be rejected.');
assert_same(false, pk_update_package_url_is_safe($version, 'https://example.com/Puock-V2.10.4.zip'), 'Untrusted hosts must be rejected.');
assert_same(false, pk_update_package_url_is_safe($version, 'https://github.com/Licoy/wordpress-theme-puock/releases/download/v2.10.3/Puock-V2.10.3.zip'), 'Asset version must match update metadata.');
assert_same(false, pk_update_package_url_is_safe($version, 'https://github.com/Licoy/wordpress-theme-puock/releases/download/v2.10.4/puock-v2.10.4.zip'), 'Asset filename matching must be case-sensitive.');
assert_same(false, pk_update_package_url_is_safe('v2.10.4', $github_asset), 'Update metadata must use a stable numeric version.');
assert_same(false, pk_update_package_url_is_safe([], $github_asset), 'Malformed version metadata must be rejected.');

$safe_update = (object)['version' => $version, 'download_url' => $github_asset];
assert_same($safe_update, pk_filter_update_package($safe_update), 'Safe release asset should pass through unchanged.');

$unsafe_update = (object)[
    'version' => $version,
    'download_url' => 'https://github.com/Licoy/wordpress-theme-puock/archive/refs/tags/v2.10.4.zip',
];
$error_log = tempnam(sys_get_temp_dir(), 'puock-update-log-');
ini_set('error_log', $error_log);
assert_same(null, pk_filter_update_package($unsafe_update), 'Unsafe source archive should fail closed.');
assert_same(1, count($GLOBALS['puc_api_errors']), 'Unsafe package rejection should emit one PUC API error.');
assert_same('puock-unsafe-update-package', $GLOBALS['puc_api_errors'][0][0]->get_error_code(), 'Unsafe package error should use the stable error code.');
assert_same(true, strpos(file_get_contents($error_log), 'puock-unsafe-update-package') !== false, 'Unsafe package rejection should be written to the error log.');

$malformed_update = (object)['version' => [], 'download_url' => []];
assert_same(null, pk_filter_update_package($malformed_update), 'Malformed package metadata should fail closed.');
assert_same(2, count($GLOBALS['puc_api_errors']), 'Malformed package metadata should emit a PUC API error.');
unlink($error_log);

echo "update package tests passed\n";
