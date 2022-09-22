<?php

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
    $ch = curl_init($url . '/' . $basename);
    $ico_file = fopen($cache_file, 'w');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_FILE, $ico_file);
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
    $url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']
        . str_replace('inc/favicon.php', '', $_SERVER['SCRIPT_NAME']) . $cache_filename;
    if ($code == 301) {
        Header("HTTP/1.1 301 Moved Permanently");
    }
    Header("Location: " . $url);
}

function pk_favicon_validate($cache_file)
{
    return @exif_imagetype($cache_file);
}

function pk_favicon_put_default_and_output($cache_file, $cache_filename, $default_ico)
{
    $data = file_get_contents($default_ico);
    $f = fopen($cache_file, 'w');
    fwrite($f, $data);
    fclose($f);
    pk_favicon_http_redirect(301, 'cache/' . $cache_filename);
}

$url = @$_GET['url'];

if (empty($url)) {
    die('website url is empty');
}

pk_get_website_favicon_ico($url, 86400 * 3, dirname(__FILE__) . '/../assets/img/favicon.ico');
