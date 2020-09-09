<?php

/**
 * WordPress 显示访客 UA 信息：Show UserAgent
 * 感谢 Phower 提供原始版本数据（精简版）
 * 由 凝神长老 更新于 2020-07-20
 */
function show_ua_scripts()
{
    wp_enqueue_style('ua_scripts', get_template_directory_uri() . '/assets/css/ua.css');
}

add_action('wp_enqueue_scripts', 'show_ua_scripts');

/* 将 IP 处理为国家 */
function CID_get_country($ip)
{
    require_once(dirname(__FILE__) . '/ip2c.php');
    if (isset($GLOBALS['ip2c'])) {
        global $ip2c;
    } else {
        $ip2c = new ip2country(dirname(__FILE__) . '/ip-to-country.bin');
        $GLOBALS['ip2c'] = $ip2c;
    }

    return $ip2c->get_country($ip);
}

/* 将国家对应为国旗，并显示悬浮文字 */
function CID_get_flag($ip)
{
    if ($ip == '127.0.0.1') {
        // localhost 单独处理
        $code = 'WordPress';
        $name = 'localhost';
    } else {
        // 获取国家信息
        $country = CID_get_country($ip);

        // 如果无法匹配
        if (!$country) {
            return "";
        }

        $code = strtolower($country['id2']);
        $name = $country['name'];
    }

    // 特别行政区
    if ($code == 'hk') {
        $name = '中国香港';
    } elseif ($code == 'mo') {
        $name = '中国澳门';
    } elseif ($code == 'tw') {
        $name = '中国台湾';
    }

    // 特殊处理 ECNU
    if ($name == 'China') {
        $url = 'https://ip.ecnu.edu.cn/getIpInfo.php?ip=' . $ip;
        $html = file_get_contents($url);
        $ip_obj = json_decode($html);
        if (($ip_obj->data->edu_data != null) && (strpos($ip_obj->data->edu_data->school, '华东师范大学') !== false)) {
            $code = 'ecnu';
            $name = '华东师范大学' . ($ip_obj->{'data'}->{'edu_data'}->{'campus'}) . ($ip_obj->{'data'}->{'edu_data'}->{'building'});
        }
    }

    // 将名字对应为中文
    if ($name == 'China') {
        $name = '中国';
    } elseif ($name == 'United States') {
        $name = '美国';
    } elseif ($name == 'India') {
        $name = '印度';
    } elseif ($name == 'France') {
        $name = '法国';
    } elseif ($name == 'Brazil') {
        $name = '巴西';
    } elseif ($name == 'Russian Federation') {
        $name = '俄罗斯';
    } elseif ($name == 'United Kingdom') {
        $name = '英国';
    } elseif ($name == 'Germany') {
        $name = '德国';
    } elseif ($name == 'Philippines') {
        $name = '菲律宾';
    } elseif ($name == 'Netherlands') {
        $name = '荷兰';
    } elseif ($name == 'Canada') {
        $name = '加拿大';
    } elseif ($name == 'Italy') {
        $name = '意大利';
    } elseif ($name == 'Japan') {
        $name = '日本';
    } elseif ($name == 'Australia') {
        $name = '澳大利亚';
    } elseif ($name == 'Singapore') {
        $name = '新加坡';
    }

    if ($name == 'Reserved') {
        $name = '未知国家';
    }

    // 输出 HTML
    $output = stripslashes('<span class="country-flag" data-toggle="tooltip" data-placement="auto top" title="" data-original-title="%COUNTRY_NAME%"><img src="%IMAGE_BASE%/%COUNTRY_CODE%.png"/></span>');

    if (!$output) {
        return "";
    }

    $output = str_replace("%COUNTRY_CODE%", $code, $output);
    $output = str_replace("%COUNTRY_NAME%", $name, $output);
    $output = str_replace("%COMMENTER_IP%", $ip, $output);
    $output = str_replace("%IMAGE_BASE%", get_stylesheet_directory_uri() . '/assets/img/flags', $output);

    return $output;
}

/* 输出国旗 */
function CID_print_comment_flag()
{
    $ip = get_comment_author_IP();
    echo CID_get_flag($ip);
}

/* 返回指定评论 ID 的国旗 HTML，用于 WPDiscuz 5.X 的输出 */
function CID_return_comment_flag_by_id($id)
{
    $ip = get_comment_author_IP($id);
    return CID_get_flag($ip);
}

/* 返回指定评论 ID 的国旗 HTML，用于 WPDiscuz 7.X 的输出 */
function CID_return_comment_flag_by_id_wpdiscuz_7($id)
{
    $ip = get_comment_author_IP($id);
    $raw = CID_get_flag($ip);
    return str_replace("data-original-title=", "wpd-tooltip=", $raw);
}

/* Windows 版本 */
function CID_windows_detect_os($ua)
{
    $os_name = $os_code = $os_ver = null;
    $os_before = null;

    if (preg_match('/Windows 95/i', $ua) || preg_match('/Win95/', $ua)) {
        $os_name = "Windows";
        $os_code = "windows";
        $os_ver = "95";
    } elseif (preg_match('/Windows NT 5.0/i', $ua) || preg_match('/Windows 2000/i', $ua)) {
        $os_name = "Windows";
        $os_code = "windows";
        $os_ver = "2000";
    } elseif (preg_match('/Win 9x 4.90/i', $ua) || preg_match('/Windows ME/i', $ua)) {
        $os_name = "Windows";
        $os_code = "windows";
        $os_ver = "ME";
    } elseif (preg_match('/Windows.98/i', $ua) || preg_match('/Win98/i', $ua)) {
        $os_name = "Windows";
        $os_code = "windows";
        $os_ver = "98";
    } elseif (preg_match('/Windows NT 6.0/i', $ua)) {
        $os_name = "Windows";
        $os_code = "windows_vista";
        $os_ver = "Vista";
    } elseif (preg_match('/Windows NT 6.1/i', $ua)) {
        $os_name = "Windows";
        $os_code = "windows_win7";
        $os_ver = "7";
    } elseif (preg_match('/Windows NT 6.2/i', $ua)) {
        $os_name = "Windows";
        $os_code = "windows_win8";
        $os_ver = "8";
    } elseif (preg_match('/Windows NT 6.3/i', $ua)) {
        $os_name = "Windows";
        $os_code = "windows_win8";
        $os_ver = "8.1";
    } elseif (preg_match('/Windows NT 6.4/i', $ua)) {
        $os_name = "Windows";
        $os_code = "windows_win8";
        $os_ver = "10";
    } elseif (preg_match('/Windows NT 10.0/i', $ua)) {
        $os_name = "Windows";
        $os_code = "windows_win8";
        $os_ver = "10";
    } elseif (preg_match('/Windows NT 5.1/i', $ua)) {
        $os_name = "Windows";
        $os_code = "windows";
        $os_ver = "XP";
    } elseif (preg_match('/Windows NT 5.2/i', $ua)) {
        $os_name = "Windows";
        $os_code = "windows";
        if (preg_match('/Win64/i', $ua)) {
            $os_ver = "XP 64 bit";
        } else {
            $os_ver = "Server 2003";
        }
    } elseif (preg_match('/Mac_PowerPC/i', $ua)) {
        $os_name = "Mac OS";
        $os_code = "macos";
    } elseif (preg_match('/Windows Phone/i', $ua)) {
        $matches = explode(';', $ua);
        $os_name = $matches[2];
        $os_code = "windows_phone7";
    } elseif (preg_match('/Windows NT 4.0/i', $ua) || preg_match('/WinNT4.0/i', $ua)) {
        $os_name = "Windows";
        $os_code = "windows";
        $os_ver = "NT 4.0";
    } elseif (preg_match('/Windows NT/i', $ua) || preg_match('/WinNT/i', $ua)) {
        $os_name = "Windows";
        $os_code = "windows";
        $os_ver = "NT";
    } else {
        $os_name = '未知操作系统';
        $os_code = 'other';
    }

    $os_before = '<span class="os os_win"><i class="fa fa-windows"></i>';

    // 微信电脑版
    // 可能需要将数据表中 UA 字段的长度（varchar 255）改为 300 左右才能显示
    if (preg_match('/WindowsWechat/i', $ua)) {
        $os_name = '微信电脑版';
        $os_code = 'wechat';
        $os_ver = '';
        $os_before = '<span class="os os_wechat"><i class="fa fa-weixin"></i>';
    }

    return array($os_name, $os_code, $os_ver, $os_before);
}

/* Unix 版本 */
function CID_unix_detect_os($ua)
{
    $os_name = $os_ver = $os_code = null;
    $os_before = null;

    if (preg_match('/Linux/i', $ua)) {
        $os_name = "Linux";
        $os_code = "linux";
        $os_before = '<span class="os os_linux"><i class="fa fa-linux"></i>';
        if (preg_match('#Debian#i', $ua)) {
            $os_code = "debian";
            $os_name = "Debian GNU/Linux";
        } elseif (preg_match('#Mandrake#i', $ua)) {
            $os_code = "mandrake";
            $os_name = "Mandrake Linux";
        } elseif (preg_match('#Kindle Fire#i', $ua)) { // Kindle Fire
            $matches = explode(';', $ua);
            $os_code = "kindle";
            $matches2 = explode(')', $matches[4]);
            $os_name = $matches[2] . $matches2[0];
        } elseif (preg_match('#Android#i', $ua)) { // Android
            $matches = explode(';', $ua);
            $os_code = "android";
            $android_version = '';
            $i = 0;
            while (preg_match('#Zhihu#i', $matches[$i])
                || !preg_match('#Android#i', $matches[$i])) {
                $i++;
            }
            $android_version = $matches[$i];
            $android_brand = '';
            $android_brand_raw = '';
            if (preg_match('#Build#i', $matches[$i + 1])) {
                $android_brand_raw = explode('Build', $matches[$i + 1]);
            } elseif (preg_match('#Build#i', $matches[$i + 2])) {
                $android_brand_raw = explode('Build', $matches[$i + 2]);
            }
            if ($android_brand_raw != '') {
                $android_brand = $android_brand_raw[0];
                // 华为的型号要处理一下
                if (preg_match('#HUAWEI#i', $android_brand_raw[1])) {
                    $android_brand = "HUAWEI " . $android_brand;
                }
            } else {
                $android_brand = explode(')', $matches[$i + 1]);
                $android_brand = $android_brand[0];
            }
            // 会错把“知乎”当做手机品牌，所以要处理掉
            if (preg_match('/ZhihuHybrid/i', $android_version, $zhihu)) {
                $android_version = str_replace('DefaultBrowser', '', $android_version);
                $android_version = str_replace('Futureve/', '', $android_version);
                $android_version = str_replace('Mozilla/5.0', '', $android_version);
                $android_version = str_replace('(Linux', '', $android_version);
            }
            $os_name = $android_version . ' | ' . $android_brand;
            $os_before = $debug . '<span class="os os_android"><i class="fa fa-android"></i>';
        } elseif (preg_match('#SuSE#i', $ua)) {
            $os_code = "suse";
            $os_name = "SuSE Linux";
        } elseif (preg_match('#Novell#i', $ua)) {
            $os_code = "novell";
            $os_name = "Novell Linux";
        } elseif (preg_match('#Ubuntu#i', $ua)) {
            $os_code = "ubuntu";
            $os_name = "Ubuntu Linux";
            $os_before = '<span class="os os_ubuntu"><i class="fa fa-ubuntu"></i>';
        } elseif (preg_match('#Red ?Hat#i', $ua)) {
            $os_code = "redhat";
            $os_name = "RedHat Linux";
            $os_before = '<span class="os os_redhat"><i class="fa fa-redhat"></i>';
        } elseif (preg_match('#Gentoo#i', $ua)) {
            $os_code = "gentoo";
            $os_name = "Gentoo Linux";
        } elseif (preg_match('#Fedora#i', $ua)) {
            $os_code = "fedora";
            $os_name = "Fedora Linux";
            $os_before = '<span class="os os_fedora"><i class="fa fa-defora"></i>';
        } elseif (preg_match('#MEPIS#i', $ua)) {
            $os_name = "MEPIS Linux";
        } elseif (preg_match('#Knoppix#i', $ua)) {
            $os_name = "Knoppix Linux";
        } elseif (preg_match('#Slackware#i', $ua)) {
            $os_code = "slackware";
            $os_name = "Slackware Linux";
        } elseif (preg_match('#Xandros#i', $ua)) {
            $os_name = "Xandros Linux";
        } elseif (preg_match('#Kanotix#i', $ua)) {
            $os_name = "Kanotix Linux";
        }

    } elseif (preg_match('/FreeBSD/i', $ua)) {
        $os_name = "FreeBSD";
        $os_code = "freebsd";
        $os_before = '<span class="os os_unix"><i class="fa fa-desktop"></i>';
    } elseif (preg_match('/NetBSD/i', $ua)) {
        $os_name = "NetBSD";
        $os_code = "netbsd";
        $os_before = '<span class="os os_unix"><i class="fa fa-desktop"></i>';
    } elseif (preg_match('/OpenBSD/i', $ua)) {
        $os_name = "OpenBSD";
        $os_code = "openbsd";
        $os_before = '<span class="os os_unix"><i class="fa fa-desktop"></i>';
    } elseif (preg_match('/IRIX/i', $ua)) {
        $os_name = "SGI IRIX";
        $os_code = "sgi";
        $os_before = '<span class="os os_unix"><i class="fa fa-desktop"></i>';
    } elseif (preg_match('/SunOS/i', $ua)) {
        $os_name = "Solaris";
        $os_code = "sun";
        $os_before = '<span class="os os_unix"><i class="fa fa-desktop"></i>';
    } elseif (preg_match('#iPod.*.CPU.([a-zA-Z0-9.( _)]+)#i', $ua, $matches)) {
        $os_name = "iPod";
        $os_code = "iphone";
        $os_ver = $matches[1];
        $os_before = '<span class="os os_mac"><i class="fa fa-apple"></i>';
    } elseif (preg_match('#iPhone.*.CPU.([a-zA-Z0-9.( _)]+)#i', $ua, $matches)) {
        $os_name = "iPhone";
        $os_code = "iphone";
        $os_ver = $matches[1];
        $os_before = '<span class="os os_mac"><i class="fa fa-apple"></i>';
    } elseif (preg_match('#iPad.*.CPU.([a-zA-Z0-9.( _)]+)#i', $ua, $matches)) {
        $os_name = "iPad";
        $os_code = "ipad";
        $os_ver = $matches[1];
        $os_before = '<span class="os os_mac"><i class="fa fa-apple"></i>';
    } elseif (preg_match('/Mac OS X.([0-9. _]+)/i', $ua, $matches)) {
        $os_name = "Mac OS";
        $os_code = "macos";
        if (count(explode(7, $matches[1])) > 1) {
            $matches[1] = 'Lion ' . $matches[1];
        } elseif (count(explode(8, $matches[1])) > 1) {
            $matches[1] = 'Mountain Lion ' . $matches[1];
        }
        $os_ver = "X " . $matches[1];
        $os_before = '<span class="os os_mac"><i class="fa fa-apple"></i>';
    } elseif (preg_match('/Macintosh/i', $ua)) {
        $os_name = "Mac OS";
        $os_code = "macos";
        $os_before = '<span class="os os_mac"><i class="fa fa-apple"></i>';
    } elseif (preg_match('/Unix/i', $ua)) {
        $os_name = "UNIX";
        $os_code = "unix";
        $os_before = '<span class="os os_unix"><i class="fa fa-desktop"></i>';
    } elseif (preg_match('/CrOS/i', $ua)) {
        $os_name = "Google Chrome OS";
        $os_code = "chromeos";
        $os_before = '<span class="os os_android"><i class="fa fa-android"></i>';
    } elseif (preg_match('/Fedor.([0-9. _]+)/i', $ua, $matches)) {
        $os_name = "Fedora";
        $os_code = "fedora";
        $os_ver = $matches[1];
        $os_before = '<span class="os os_linux"><i class="fa fa-linux"></i>';
    } elseif (preg_match('#Device/Apple([0-9. _a-zA-Z\(\)]+)#i', $ua, $matches)) {
        $os_name = "Apple";
        $os_code = "macos";
        $os_ver = $matches[1];
        $os_before = '<span class="os os_mac"><i class="fa fa-apple"></i>';
    } else {
        $os_name = '未知操作系统';
        $os_code = 'other';
        $os_before = '<span class="os os_other"><i class="fa fa-desktop"></i>';
    }

    return array($os_name, $os_code, $os_ver, $os_before);
}

/* 浏览器 */
function CID_detect_browser($ua)
{
    $browser_name = $browser_code = $browser_ver = $os_name = $os_code = $os_ver = null;
    $browser_before = null;
    $os_before = null;
    $ua = preg_replace("/FunWebProducts/i", "", $ua);
    if (preg_match('#com.zhihu.android/Futureve/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
        // 知乎
        $browser_name = '知乎';
        $browser_code = 'zhihu';
        $browser_ver = $matches[1];
        $browser_before = '<span class="ua ua_zhihu"><i class="fa fa-zhihu"></i>';
    } elseif (preg_match('#(Firefox|Phoenix|Firebird|BonEcho|GranParadiso|Minefield|Iceweasel)/4([a-zA-Z0-9.]+)#i', $ua, $matches)) {
        $browser_name = 'Firefox';
        $browser_code = 'firefox';
        $browser_ver = '4' . $matches[2];
        $browser_before = '<span class="ua ua_firefox"><i class="fa fa-firefox"></i>';
    } elseif (preg_match('#(Firefox|Phoenix|Firebird|BonEcho|GranParadiso|Minefield|Iceweasel)/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
        $browser_name = 'Firefox';
        $browser_code = 'firefox';
        $browser_ver = $matches[2];
        $browser_before = '<span class="ua ua_firefox"><i class="fa fa-firefox"></i>';
    } elseif (preg_match('#(Edge|Edg)/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
        // 旧版 Edge，新版 Edg
        $browser_name = 'Edge';
        $browser_code = 'edge';
        $browser_ver = $matches[2];
        $browser_before = '<span class="ua ua_edge"><i class="fa fa-edge"></i>';
    } elseif (preg_match('#SE 2([a-zA-Z0-9.]+)#i', $ua, $matches)) {
        $browser_name = '搜狗浏览器';
        $browser_code = 'sogou';
        $browser_ver = '2' . $matches[1];
        $browser_before = '<span class="ua ua_sogou"><i class="fa fa-globe"></i>';
    } elseif (preg_match('#baidubrowser ([a-zA-Z0-9.]+)#i', $ua, $matches)) {
        $browser_name = '百度浏览器';
        $browser_code = 'baidubrowser';
        $browser_ver = $matches[1];
        $browser_before = '<span class="ua ua_ucweb"><i class="fa fa-globe"></i>';
    } elseif (preg_match('#360([a-zA-Z0-9.]+)#i', $ua, $matches)) {
        $browser_name = '360浏览器';
        $browser_code = '360se';
        $browser_ver = $matches[1];
        $browser_before = '<span class="ua ua_ucweb"><i class="fa fa-globe"></i>';
    } elseif (preg_match('#QQBrowser/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
        $browser_name = 'QQ浏览器';
        $browser_code = 'qqbrowser';
        $browser_ver = $matches[1];
        $browser_before = '<span class="ua ua_qqbrowser"><i class="fa fa-globe"></i>';
    } elseif (preg_match('#Chrome/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
        $browser_name = 'Chrome';
        $browser_code = 'chrome';
        $browser_ver = $matches[1];
        $browser_before = '<span class="ua ua_chrome"><i class="fa fa-chrome"></i>';
    } elseif (preg_match('#Arora/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
        $browser_name = 'Arora';
        $browser_code = 'arora';
        $browser_ver = $matches[1];
        $browser_before = '<span class="ua ua_other"><i class="fa fa-globe"></i>';
    } elseif (preg_match('#Maxthon( |\/)([a-zA-Z0-9.]+)#i', $ua, $matches)) {
        $browser_name = '傲游浏览器';
        $browser_code = 'maxthon';
        $browser_ver = $matches[2];
        $browser_before = '<span class="ua ua_other"><i class="fa fa-globe"></i>';
    } elseif (preg_match('#CriOS/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
        $browser_name = 'Chrome #iOS';
        $browser_code = 'crios';
        $browser_ver = $matches[1];
        $browser_before = '<span class="ua ua_chrome"><i class="fa fa-chrome"></i>';
    } elseif (preg_match('#Safari/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
        $browser_name = 'Safari';
        $browser_code = 'safari';
        $browser_ver = $matches[1];
        $browser_before = '<span class="ua ua_apple"><i class="fa fa-safari"></i>';
    } elseif (preg_match('#opera mini#i', $ua)) {
        $browser_name = 'Opera Mini';
        $browser_code = 'opera';
        preg_match('#Opera/([a-zA-Z0-9.]+)#i', $ua, $matches);
        $browser_ver = $matches[1];
        $browser_before = '<span class="ua ua_opera"><i class="fa fa-opera"></i>';
    } elseif (preg_match('#Opera.(.*)Version[ /]([a-zA-Z0-9.]+)#i', $ua, $matches)) {
        $browser_name = 'Opera';
        $browser_code = 'opera';
        $browser_ver = $matches[2];
        $browser_before = '<span class="ua ua_opera"><i class="fa fa-opera"></i>';
    } elseif (preg_match('#Opera/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
        $browser_name = 'Opera Mini';
        $browser_code = 'opera';
        $browser_ver = $matches[1];
        $browser_before = '<span class="ua ua_opera"><i class="fa fa-opera"></i>';
    } elseif (preg_match('#UCWEB([a-zA-Z0-9.]+)#i', $ua, $matches)) {
        $browser_name = 'UC';
        $browser_code = 'ucweb';
        $browser_ver = $matches[1];
        $browser_before = '<span class="ua ua_ucweb"><i class="fa fa-globe"></i>';
    } elseif (preg_match('#MSIE ([a-zA-Z0-9.]+)#i', $ua, $matches)) {
        $browser_name = 'Internet Explorer';
        $browser_ver = '';
        $browser_before = '<span class="ua ua_ie"><i class="fa fa-internet-explorer"></i>';
        if (strpos($browser_ver, '7') !== false || strpos($browser_ver, '8') !== false) {
            $browser_code = 'ie8';
        } elseif (strpos($browser_ver, '9') !== false) {
            $browser_code = 'ie9';
        } elseif (strpos($browser_ver, '10') !== false) {
            $browser_code = 'ie10';
        } else {
            $browser_code = 'ie';
        }
    } elseif (preg_match('#^Mozilla/5.0#i', $ua) && preg_match('#rv:([a-zA-Z0-9.]+)#i', $ua, $matches)) {
        $browser_name = 'Firefox 5.0';
        $browser_code = 'mozilla';
        $browser_ver = $matches[1];
        $browser_before = '<span class="ua ua_firefox"><i class="fa fa-firefox"></i>';
    } else {
        $browser_name = '未知浏览器';
        $browser_code = 'null';
        $browser_before = '<span class="ua ua_other"><i class="fa fa-globe"></i>';
    }

    // 从 QQ 或者 微信 访问
    if (preg_match('#QQ/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
        $browser_name = 'QQ';
        $browser_code = 'qq';
        $browser_ver = $matches[1];
        $browser_before = '<span class="ua ua_qq"><i class="fa fa-qq"></i>';
    } else if (preg_match('#MicroMessenger/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
        $browser_name = '微信';
        $browser_code = 'wechat';
        $browser_ver = $matches[1];
        $browser_before = '<span class="ua ua_wechat"><i class="fa fa-weixin"></i>';
    }

    // 操作系统
    if (preg_match('/Windows/i', $ua)) {
        list($os_name, $os_code, $os_ver, $os_before) = CID_windows_detect_os($ua);
    } else {
        list($os_name, $os_code, $os_ver, $os_before) = CID_unix_detect_os($ua);
    }

    if (!$os_name) {
        $os_name = '未知操作系统';
        $os_code = 'other';
        $os_before = '<span class="os os_other"><i class="fa fa-desktop"></i>';
    }

    return array(
        $browser_name,
        $browser_code,
        $browser_ver,
        $browser_before,
        $os_name,
        $os_code,
        $os_ver,
        $os_before,
    );
}

/* 原始信息 */
function CID_friendly_string_without_template($browser_name = '', $browser_code = '', $browser_ver = '', $browser_before = '', $os_name = '', $os_code = '', $os_ver = '', $os_before = '')
{
    $browser_name = htmlspecialchars($browser_name);
    $browser_code = htmlspecialchars($browser_code);
    $browser_ver = htmlspecialchars($browser_ver);
    $os_name = htmlspecialchars($os_name);
    $os_code = htmlspecialchars($os_code);
    $os_ver = htmlspecialchars($os_ver);

    $text1 = '';
    $text2 = '';

    if ($browser_name && $os_name) {
        $text1 = "$browser_name $browser_ver ";
        $text2 = "$os_name $os_ver";
    } elseif ($browser_name) {
        $text1 = "$browser_name $browser_ver";
    } elseif ($os_name) {
        $text1 = "$os_name $os_ver";
    }

    return $browser_before . ' ' . $text1 . ' </span>' . $os_before . ' ' . $text2 . '</span>';
}

function CID_get_comment_browser_without_template()
{
    global $comment;
    if (!$comment->comment_agent) {
        return;
    }
    list ($browser_name, $browser_code, $browser_ver, $browser_before, $os_name, $os_code, $os_ver, $os_before) = CID_detect_browser($comment->comment_agent);
    $string = CID_friendly_string_without_template($browser_name, $browser_code, $browser_ver, $browser_before, $os_name, $os_code, $os_ver, $os_before);

    return $string;
}

/* 返回指定评论 ID 的浏览器和操作系统，用于 WPDiscuz 的输出 */
function CID_get_comment_browser_without_template_by_id($id)
{
    $comment = get_comment($id);
    if (!$comment->comment_agent) {
        return;
    }
    list ($browser_name, $browser_code, $browser_ver, $browser_before, $os_name, $os_code, $os_ver, $os_before) = CID_detect_browser($comment->comment_agent);
    $string = CID_friendly_string_without_template($browser_name, $browser_code, $browser_ver, $browser_before, $os_name, $os_code, $os_ver, $os_before);

    return $string;
}

/* 输出 HTML 到 PHP 页面中的评论部分 */
function CID_print_comment_browser()
{
    echo CID_get_comment_browser_without_template();
}

/* 返回指定评论 ID 的 UA 信息，用于 WPDiscuz 的输出 */
function CID_return_comment_browser_by_id($id)
{
    return CID_get_comment_browser_without_template_by_id($id);
}

?>
