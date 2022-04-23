<?php
if (isset($_GET['exec'])) {

    function pk_admin_msg_bar($msg, $type = "success")
    {
        return '<div id="message" class="notice notice-' . $type . '  is-dismissible">
				<p><strong>' . $msg . '</strong></p></div>';
    }

    function pk_check_github_tag_version()
    {
        $res = wp_remote_request("https://api.github.com/repos/Licoy/wordpress-theme-puock/tags", array(
            'method' => "GET",
            'headers' => array('Content-Type' => 'application/json;charset=UTF-8'),
            'timeout' => 10
        ));
        if (is_array($res) && !is_wp_error($res) && $res['response']['code'] == 200) {
            $tags = json_decode($res['body'], true);
            if (count($tags) > 0) {
                $latest_version = (float)str_replace('v', '', $tags[0]['name']);
                if ($latest_version > PUOCK_CUR_VER) {
                    return pk_admin_msg_bar("检测到新版本V" . $latest_version .
                        "&nbsp;<a target='_blank' 
                        href='https://github.com/Licoy/wordpress-theme-puock/releases/tag/v${latest_version}'>点此进入手动下载更新</a>");
                }
            }
            return pk_admin_msg_bar("当前版本已是最新版本");
        }
        return pk_admin_msg_bar("检测更新失败", "warning");
    }

    $get_exec = $_GET['exec'];
    if ($get_exec == "update_check") {
        echo pk_check_github_tag_version();
    }
}

?>
<h2>
    <?php $menu = $this->menu_settings(); ?>
    <?php echo esc_html($menu['page_title']); ?>
    <img style="cursor: pointer" onclick="window.open('https://github.com/Licoy/wordpress-theme-puock', 'blank')"
         src="https://img.shields.io/badge/当前版本-V<?php echo PUOCK_CUR_VER_STR; ?>-CC3333.svg?logo=git" alt="当前版本">
    <img style="cursor: pointer"
         onclick="window.open('https://licoy.cn/go/zs', 'blank')"
         src="https://img.shields.io/badge/赞赏-开发不易-FFCC33.svg?logo=Buy-Me-A-Coffee" alt="赞赏支持">
    <img style="cursor: pointer"
         onclick="window.open('https://github.com/Licoy/wordpress-theme-puock/blob/master/LICENSE', 'blank')"
         src="https://img.shields.io/badge/开源协议-GPL-ff69b4.svg?logo=github" alt="开源协议">
    <img style="cursor: pointer"
         onclick="window.open('https://licoy.cn/go/puock-update.php?r=qq_qun', 'blank')"
         src="https://img.shields.io/badge/QQ群-加入讨论-bule.svg?logo=tencent-qq" alt="QQ群">
</h2>
