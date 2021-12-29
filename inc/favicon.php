<?php

function pk_get_website_favicon_ico($url, $cache_time, $default_ico, $basename = "favicon.ico")
{
    $cache_file = dirname(__FILE__) . '/../cache/' . md5($url) . '.ico';
    if (is_file($cache_file)) {
        if (time() - filemtime($cache_file) <= $cache_time) {
            pk_favicon_output(file_get_contents($cache_file));
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
        pk_favicon_put_default_and_output($cache_file, $default_ico);
        return;
    }
    $mimeArray = explode('/', curl_getinfo($ch, CURLINFO_CONTENT_TYPE));
    curl_close($ch);
    if (count($mimeArray) == 0 || $mimeArray[0] != 'image') {
        @unlink($cache_file);
        pk_favicon_put_default_and_output($cache_file, $default_ico);
        return;
    }
    pk_favicon_output(file_get_contents($cache_file));
}

function pk_favicon_put_default_and_output($cache_file, $default_ico)
{
    $data = file_get_contents($default_ico);
    $f = fopen($cache_file, 'w');
    fwrite($f, $data);
    fclose($f);
    pk_favicon_output($data);
}

function pk_favicon_output($data)
{
    header('Cache-Control: public, max-age=0, must-revalidate');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    header("content-type: image/png");
    echo $data;
}

$url = @$_GET['url'];

if (empty($url)) {
    die('website url is empty');
}

pk_get_website_favicon_ico($url, 86400 * 3, dirname(__FILE__) . '/../assets/img/favicon.ico');
