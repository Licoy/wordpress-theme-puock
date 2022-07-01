<?php
if (@$_GET['settings-updated'] === 'true') {
    do_action('options-framework-saved');
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
