<?php

require_once dirname(__DIR__) . '/../../../wp-load.php';

function pk_get_website_favicon_ico($url, $cache_time, $default_ico, $basename = "favicon.ico")
{
    $cache_filename = 'icon-'.md5($url) . '.ico';
    $cache_file = dirname(__FILE__) . '/../cache/' . $cache_filename;
    if (is_file($cache_file)) {
        if (time() - filemtime($cache_file) <= $cache_time) {
            pk_favicon_get_ico_contents($cache_file, $cache_filename);
            return;
        }
    }
    // SSRF protection: block requests to private/internal IPs
    $parsed_url = parse_url($url);
    $host = $parsed_url['host'] ?? '';
    if (!in_array($parsed_url['scheme'] ?? '', ['http', 'https'], true) || !pk_favicon_is_public_host($host)) {
        pk_favicon_put_default_and_output($cache_file, $cache_filename, $default_ico);
        return;
    }

    $ch = curl_init(rtrim($url, '/') . '/' . $basename);
    $ico_file = fopen($cache_file, 'w');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_FILE, $ico_file);
    curl_setopt($ch, CURLOPT_MAXFILESIZE, 1048576);
    curl_exec($ch);
    fclose($ico_file);
    if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
        curl_close($ch);
        @unlink($cache_file);
        pk_favicon_put_default_and_output($cache_file, $cache_filename, $default_ico);
        return;
    }
    $mimeArray = explode('/', curl_getinfo($ch, CURLINFO_CONTENT_TYPE));
    curl_close($ch);
    if (count($mimeArray) == 0 || $mimeArray[0] != 'image') {
        @unlink($cache_file);
        pk_favicon_put_default_and_output($cache_file, $cache_filename, $default_ico);
        return;
    }
    pk_favicon_get_ico_contents($cache_file, $cache_filename);
}

function pk_favicon_get_ico_contents($cache_file, $cache_filename)
{
    if (pk_favicon_validate($cache_file)) {
        pk_favicon_http_redirect(301, 'cache/' . $cache_filename);
        return;
    }
    pk_favicon_http_redirect(302, 'assets/img/favicon.ico');
}

function pk_favicon_http_redirect($code, $cache_filename)
{
    $path = str_replace('inc/favicon.php', '', $_SERVER['SCRIPT_NAME']) . ltrim($cache_filename, '/');
    wp_safe_redirect(home_url($path), $code);
    exit;
}

function pk_favicon_validate($cache_file)
{
    if(file_exists($cache_file)){
        if(!getimagesize($cache_file)) return 0;
        return true;
    }else {
        return false;
    }
}

function pk_favicon_is_public_host($host): bool
{
    $host = strtolower(trim((string)$host, " \t\n\r\0\x0B."));
    if ($host === '' || !preg_match('/^[a-z0-9.-]+$/i', $host)) {
        return false;
    }
    $ips = filter_var($host, FILTER_VALIDATE_IP) ? [$host] : gethostbynamel($host);
    if (!$ips) {
        return false;
    }
    foreach ($ips as $ip) {
        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            return false;
        }
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return false;
        }
    }
    return true;
}

function pk_favicon_put_default_and_output($cache_file, $cache_filename, $default_ico)
{
    $data = file_get_contents($default_ico);
    if ($data === false) {
        die(esc_html__('default favicon not found', PUOCK));
    }
    $f = fopen($cache_file, 'w');
    fwrite($f, $data);
    fclose($f);
    pk_favicon_http_redirect(301, 'cache/' . $cache_filename);
}

$url = isset($_GET['url']) ? esc_url_raw(wp_unslash($_GET['url']), ['http', 'https']) : '';

if (empty($url)) {
    die(esc_html__('website url is empty', PUOCK));
}

$exists = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(1) FROM $wpdb->links WHERE link_url LIKE %s", '%' . $wpdb->esc_like( $url ) . '%') );

if(!$exists){
    die(esc_html(sprintf('invalid url: %s', $url)));
}

pk_get_website_favicon_ico($url, 86400 * 3, dirname(__FILE__) . '/../assets/img/favicon.ico');
