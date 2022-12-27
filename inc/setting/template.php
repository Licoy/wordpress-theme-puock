<?php if (!file_exists(dirname(__FILE__) . '/template-script-dev.php')): ?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri() ?>/assets/dist/setting/index.css?ver=<?php echo PUOCK_CUR_VER_STR ?>">
<?php endif; ?>
<style id="pk-options-style"></style>
<div id="app">
    <div style="padding: 40px;font-size: 30px">loading...</div>
</div>
<script>
    jQuery(function () {
        var wpAdminBarEl = jQuery("#wpadminbar")
        var wpFooterEl = jQuery("#wpfooter")
        var pkOptionsStyleEl = jQuery("#pk-options-style")

        function loadWpContentHeight() {
            var h = window.innerHeight - wpAdminBarEl.height() - wpFooterEl.height() - 50
            pkOptionsStyleEl.html("#wpbody-content{height:" + h + "px;padding:0;}#pk-options-box{height:" + (window.innerHeight - wpAdminBarEl.height()) + "px}")
        }

        window.addEventListener("resize", loadWpContentHeight)

        loadWpContentHeight()
    })

    window.puockSettingId = "puock-theme-options-global";
    window.puockSettingMetaInfo = {
        version: "V<?php echo PUOCK_CUR_VER_STR ?>",
        colors: {
            primaryColor: '#ae4af7',
            primaryColorHover: '#903eca',
            primaryColorPressed: '#8912e6',
            primaryColorSuppl: '#a537fb',
        },
        description:'简单/方便/高颜值',
        tag: {text: '主题', color: 'rgb(155,39,238)'},
        github: "https://github.com/Licoy/wordpress-theme-puock",
        qq: "https://licoy.cn/go/puock-update.php?r=qq_qun",
        license: "GPL V3",
        donate: "https://licoy.cn/go/zs",
        get_url: '<?php echo admin_url('admin-ajax.php') ?>?action=get_theme_options',
        update_url: '<?php echo admin_url('admin-ajax.php') ?>?action=update_theme_options',
        fields_url: '<?php echo admin_url('admin-ajax.php') ?>?action=get_theme_option_fields',
    }
</script>
<?php
if (file_exists(dirname(__FILE__) . '/template-script-dev.php')) {
    include_once dirname(__FILE__) . '/template-script-dev.php';
} else { ?>

    <script type="module" crossorigin
            src="<?php echo get_template_directory_uri() ?>/assets/dist/setting/index.js?ver=<?php echo PUOCK_CUR_VER_STR ?>"></script>

    <?php
}
?>
