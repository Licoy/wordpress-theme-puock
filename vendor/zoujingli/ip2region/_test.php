<?php

require 'Ip2Region.php';

$ip2region = new Ip2Region();

$ip = '116.22.59.36';
echo PHP_EOL;
echo "查询IP：{$ip}" . PHP_EOL;
$info = $ip2region->btreeSearch($ip);
var_export($info);

echo PHP_EOL;
$info = $ip2region->memorySearch($ip);
var_export($info);
echo PHP_EOL;

// array (
//     'city_id' => 1713,
//     'region' => '中国|0|广东省|广州市|电信',
// )