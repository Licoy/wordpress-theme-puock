[![Latest Stable Version](https://poser.pugx.org/zoujingli/ip2region/v/stable)](https://packagist.org/packages/zoujingli/ip2region)
[![Total Downloads](https://poser.pugx.org/zoujingli/ip2region/downloads)](https://packagist.org/packages/zoujingli/ip2region)
[![Monthly Downloads](https://poser.pugx.org/zoujingli/ip2region/d/monthly)](https://packagist.org/packages/zoujingli/ip2region)
[![Daily Downloads](https://poser.pugx.org/zoujingli/ip2region/d/daily)](https://packagist.org/packages/zoujingli/ip2region)
[![PHP Version Require](http://poser.pugx.org/zoujingli/ip2region/require/php)](https://packagist.org/packages/ip2region)
[![License](https://poser.pugx.org/zoujingli/ip2region/license)](https://packagist.org/packages/zoujingli/ip2region)

本库基于 [ip2region](https://github.com/lionsoul2014/ip2region) 简单整合，方便 `PHP` 项目使用 `Composer` 来安装。

# 通过 Composer 安装

```shell
composer require zoujingli/ip2region
```

# 在项目中快速调用

```php
$ip2region = new \Ip2Region();
$result = $ip2region->simple('8.8.8.8');
var_dump($result);

// 高级用法可以直接调用 XdbSearcher 类。
```

# Ip2region 是什么

ip2region v2.0 - 是一个离线IP地址定位库和IP定位数据管理框架，10微秒级别的查询效率，提供了众多主流编程语言的 `xdb` 数据生成和查询客户端实现。

# Ip2region 特性

### 1、标准化的数据格式

每个 ip 数据段的 region 信息都固定了格式：`国家|区域|省份|城市|ISP`，只有中国的数据绝大部分精确到了城市，其他国家部分数据只能定位到国家，后前的选项全部是0。

### 2、数据去重和压缩

`xdb` 格式生成程序会自动去重和压缩部分数据，默认的全部 IP 数据，生成的 ip2region.xdb 数据库是 11MiB，随着数据的详细度增加数据库的大小也慢慢增大。

### 3、极速查询响应

即使是完全基于 `xdb` 文件的查询，单次查询响应时间在十微秒级别，可通过如下两种方式开启内存加速查询：

1. `vIndex` 索引缓存 ：使用固定的 `512KiB` 的内存空间缓存 vector index 数据，减少一次 IO 磁盘操作，保持平均查询效率稳定在10-20微秒之间。
2. `xdb` 整个文件缓存：将整个 `xdb` 文件全部加载到内存，内存占用等同于 `xdb` 文件大小，无磁盘 IO 操作，保持微秒级别的查询效率。

### 4、IP 数据管理框架

v2.0 格式的 `xdb` 支持亿级别的 IP 数据段行数，region 信息也可以完全自定义，例如：你可以在 region 中追加特定业务需求的数据，例如：GPS信息/国际统一地域信息编码/邮编等。也就是你完全可以使用 ip2region 来管理你自己的 IP 定位数据。

# `xdb` 数据查询

API 介绍，使用文档和测试程序请参考对应 `searcher` 查询客户端下的 ReadMe 介绍，全部查询 binding 实现情况如下：

| Ok?                | 状态  | 编程语言                                                                              | 描述                   | 贡献者                                       |
|:-------------------|:----|:----------------------------------------------------------------------------------|:---------------------|:------------------------------------------|
| :white_check_mark: | 已完成 | [golang](https://github.com/lionsoul2014/ip2region/blob/master/binding/golang)    | golang xdb 查询客户端实现   | [Lion](https://github.com/lionsoul2014)   |
| :white_check_mark: | 已完成 | [php](https://github.com/lionsoul2014/ip2region/blob/master/binding/php)          | php xdb 查询客户端实现      | [Lion](https://github.com/lionsoul2014)   |
| :white_check_mark: | 已完成 | [java](https://github.com/lionsoul2014/ip2region/blob/master/binding/java)        | java xdb 查询客户端实现     | [Lion](https://github.com/lionsoul2014)   |
| :white_check_mark: | 已完成 | [lua](https://github.com/lionsoul2014/ip2region/blob/master/binding/lua)          | 纯 lua xdb 查询客户端实现    | [Lion](https://github.com/lionsoul2014)   |
| :white_check_mark: | 已完成 | [c](https://github.com/lionsoul2014/ip2region/blob/master/binding/c)              | ANSC c xdb 查询客户端实现   | [Lion](https://github.com/lionsoul2014)   |
| :white_check_mark: | 已完成 | [lua_c](https://github.com/lionsoul2014/ip2region/blob/master/binding/lua_c)      | lua c 扩展 xdb 查询客户端实现 | [Lion](https://github.com/lionsoul2014)   |
| &nbsp;&nbsp;&nbsp; | 待开始 | [rust](https://github.com/lionsoul2014/ip2region/blob/master/binding/rust)        | rust xdb 查询客户端实现     | [Lion](https://github.com/lionsoul2014)   |
| :white_check_mark: | 已完成 | [python](https://github.com/lionsoul2014/ip2region/blob/master/binding/python)    | python xdb 查询客户端实现   | [厉害的花花](https://github.com/luckydog6132)  |
| :white_check_mark: | 已完成 | [nodejs](https://github.com/lionsoul2014/ip2region/blob/master/binding/nodejs)    | nodejs xdb 查询客户端实现   | [Wu Jian Ping](https://github.com/wujjpp) |
| :white_check_mark: | 已完成 | [csharp](https://github.com/lionsoul2014/ip2region/blob/master/binding/csharp)    | csharp xdb 查询客户端实现   | [Alen Lee](https://github.com/malus2077)  |
| &nbsp;&nbsp;&nbsp; | 待开始 | [php_ext](https://github.com/lionsoul2014/ip2region/blob/master/binding/php7_ext) | php c 扩展 xdb 查询客户端实现 | 待确定                                       |
| &nbsp;&nbsp;&nbsp; | 待开始 | [nginx](https://github.com/lionsoul2014/ip2region/blob/master/binding/nginx)      | nginx 扩展 xdb 查询客户端实现 | 待确定                                       |

# `xdb` 数据生成

API 介绍，使用文档和测试程序请参考对应 `maker` 生成程序下的 ReadMe 介绍，全部生成 maker 实现情况如下：

| Ok?                | 状态  | 编程语言                                                                         | 描述                | 贡献者                                      |
|:-------------------|:----|:-----------------------------------------------------------------------------|:------------------|:-----------------------------------------|
| :white_check_mark: | 已完成 | [golang](https://github.com/lionsoul2014/ip2region/blob/master/maker/golang) | golang xdb 生成程序实现 | [Lion](https://github.com/lionsoul2014)  |
| :white_check_mark: | 已完成 | [java](https://github.com/lionsoul2014/ip2region/blob/master/maker/java)     | java xdb 生成程序实现   | [Lion](https://github.com/lionsoul2014)  |
| &nbsp;&nbsp;&nbsp; | 待开始 | [c](https://github.com/lionsoul2014/ip2region/blob/master/maker/c)           | ANSC c xdb 生成程序实现 | [Lion](https://github.com/lionsoul2014)  |
| :white_check_mark: | 已完成 | [python](https://github.com/lionsoul2014/ip2region/blob/master/maker/python) | python xdb 生成程序实现 | [leolin49](https://github.com/leolin49)  |
| :white_check_mark: | 已完成 | [csharp](https://github.com/lionsoul2014/ip2region/blob/master/maker/csharp) | csharp xdb 生成程序实现 | [Alan Lee](https://github.com/malus2077) |

# 并发查询必读

全部查询客户端的 search 接口都 <b>不是</b> 并发安全的实现，不同进程/线程/协程需要通过创建不同的查询对象来安全使用，并发量很大的情况下，基于文件查询的方式可能会导致打开文件数过多的错误，请修改内核的最大允许打开文件数(fs.file-max=一个更高的值)，或者将整个xdb加载到内存进行安全并发使用。

# 相关备注

### 1、使用声明

ip2region 重点在于<b>研究 IP 定位数据的存储设计和各种语言的查询实现</b>，并没有原始 IP 数据的支撑，本项目不保证及时的数据更新，没有也不会有商用版本，你可以使用自定义的数据导入 ip2region 进行管理。

### 2、技术交流

ip2region 微信交流群，请先加微信：lionsoul2014 (请备注 ip2region)

### 3、数据更新

基于检测算法的数据更新方式视频分享：[数据更新实现视频分享 - part1](https://www.bilibili.com/video/BV1934y1E7Q5/)，[数据更新实现视频分享 - part2](https://www.bilibili.com/video/BV1pF411j7Aw/)

### 4、数据结构

1. xdb 数据结构分析：[“ip2region xdb 数据结构和查询过程详解“](https://mp.weixin.qq.com/s?__biz=MzU4MDc2MzQ5OA==&mid=2247483696&idx=1&sn=6e9e138e86cf18245656c54ff4be3129&chksm=fd50ab35ca2722239ae7c0bb08efa44f499110c810227cbad3a16f36ebc1c2afc58eb464a57c#rd)
2. xdb 查询过程分析：[“ip2region xdb 数据结构和查询过程详解”](https://mp.weixin.qq.com/s?__biz=MzU4MDc2MzQ5OA==&mid=2247483696&idx=1&sn=6e9e138e86cf18245656c54ff4be3129&chksm=fd50ab35ca2722239ae7c0bb08efa44f499110c810227cbad3a16f36ebc1c2afc58eb464a57c#rd)
3. xdb 生成过程分析：[“ip2region xdb 二进制数据生成过程详解”](https://mp.weixin.qq.com/s?__biz=MzU4MDc2MzQ5OA==&mid=2247483718&idx=1&sn=92e552f3bba44a97ca661da244f35574&chksm=fd50ab43ca2722559733ed4e1082f239f381aaa881f9dbeb479174936145522696d9d200531e#rd)

# 关于 ip2region v2.0 的 PHP 用法

### 完全基于文件的查询

```php
$dbFile = "ip2region.xdb file path";
try {
    $searcher = XdbSearcher::newWithFileOnly($dbFile);
} catch (Exception $e) {
    printf("failed to create searcher with '%s': %s\n", $dbFile, $e);
    return;
}

$ip = '1.2.3.4';
$sTime = XdbSearcher::now();
$region = $searcher->search($ip);
if ($region === null) {
    // something is wrong
    printf("failed search(%s)\n", $ip);
    return;
}

printf("{region: %s, took: %.5f ms}\n", $region, XdbSearcher::now() - $sTime);

// 备注：并发使用，每个线程或者协程需要创建一个独立的 searcher 对象。
```

### 缓存 `VectorIndex` 索引

如果你的 php 母环境支持，可以预先加载 vectorIndex 缓存，然后做成全局变量，每次创建 Searcher 的时候使用全局的 vectorIndex，可以减少一次固定的 IO 操作从而加速查询，减少 io 压力。

```php
// 1、从 dbPath 加载 VectorIndex 缓存，把下述的 vIndex 变量缓存到内存里面。
$vIndex = XdbSearcher::loadVectorIndexFromFile($dbPath);
if ($vIndex === null) {
    printf("failed to load vector index from '%s'\n", $dbPath);
    return;
}

// 2、使用全局的 vIndex 创建带 VectorIndex 缓存的查询对象。
try {
    $searcher = XdbSearcher::newWithVectorIndex($dbFile, $vIndex);
} catch (Exception $e) {
    printf("failed to create vectorIndex cached searcher with '%s': %s\n", $dbFile, $e);
    return;
}

// 3、查询
$sTime = XdbSearcher::now();
$region = $searcher->search('1.2.3.4');
if ($region === null) {
    printf("failed search(1.2.3.4)\n");
    return;
}

printf("{region: %s, took: %.5f ms}\n", $region, XdbSearcher::now() - $sTime);

// 备注：并发使用，每个线程或者协程需要创建一个独立的 searcher 对象，但是都共享统一的只读 vectorIndex。
```

### 缓存整个 `xdb` 数据

如果你的 PHP 母环境支持，可以预先加载整个 `xdb` 的数据到内存，这样可以实现完全基于内存的查询，类似之前的 memory search 查询。

```php
// 1、从 dbPath 加载整个 xdb 到内存。
$cBuff = XdbSearcher::loadContentFromFile($dbPath);
if ($cBuff === null) {
    printf("failed to load content buffer from '%s'\n", $dbPath);
    return;
}

// 2、使用全局的 cBuff 创建带完全基于内存的查询对象。
try {
    $searcher = XdbSearcher::newWithBuffer($cBuff);
} catch (Exception $e) {
    printf("failed to create buffer cached searcher: %s\n", $dbFile, $e);
    return;
}

// 3、查询
$sTime = XdbSearcher::now();
$region = $searcher->search('1.2.3.4');
if ($region === null) {
    printf("failed search(1.2.3.4)\n");
    return;
}

printf("{region: %s, took: %.5f ms}\n", $region, XdbSearcher::now() - $sTime);

// 备注：并发使用，用整个 xdb 缓存创建的 searcher 对象可以安全用于并发。
```

# 查询测试

通过 `search_test.php` 脚本来进行查询测试：

```bash
➜  php git:(v2.0_xdb) ✗ php ./search_test.php
php ./search_test.php [command options]
options:
 --db string             ip2region binary xdb file path
 --cache-policy string   cache policy: file/vectorIndex/content
```

例如：使用默认的 data/ip2region.xdb 进行查询测试：

```bash
➜  php git:(v2.0_xdb) ✗ php ./search_test.php --db=../../data/ip2region.xdb --cache-policy=vectorIndex
ip2region xdb searcher test program, cachePolicy: vectorIndex
type 'quit' to exit
ip2region>> 1.2.3.4
{region: 美国|0|华盛顿|0|谷歌, ioCount: 7, took: 0.04492 ms}
ip2region>> 
```

输入 ip 即可进行查询测试。也可以分别设置 `cache-policy` 为 file/vectorIndex/content 来测试三种不同缓存实现的效率。

# bench 测试

通过 `bench_test.php` 脚本来进行自动 bench 测试，一方面确保 `xdb` 文件没有错误，另一方面通过大量的查询测试平均查询性能：

```bash
➜  php git:(v2.0_xdb) ✗ php ./bench_test.php
php ./bench_test.php [command options]
options:
 --db string             ip2region binary xdb file path
 --src string            source ip text file path
 --cache-policy string   cache policy: file/vectorIndex/content
```

例如：通过默认的 data/ip2region.xdb 和 data/ip.merge.txt 来进行 bench 测试：

```bash
➜  php git:(v2.0_xdb) ✗ php ./bench_test.php --db=../../data/ip2region.xdb --src=../../data/ip.merge.txt --cache-policy=vectorIndex
Bench finished, {cachePolicy: vectorIndex, total: 3417955, took: 15s, cost: 0.005 ms/op}
```

可以通过设置 `cache-policy` 参数来分别测试 file/vectorIndex/content 三种不同的缓存实现的的性能。
@Note：请注意 bench 使用的 src 文件需要是生成对应的 xdb 文件的相同的源文件。