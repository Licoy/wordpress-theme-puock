<link rel="stylesheet" href="<?php echo get_template_directory_uri() ?>/assets/dist/setting/index.css?ver=<?php echo PUOCK_CUR_VER_STR ?>">
<style>
    #wpcontent {
        margin-left: 140px
    }

    @media screen and (max-width: 782px) {
        #wpcontent {
            padding-left: 0 !important;
        }
    }

    @media screen and (max-width: 960px) {
        #wpcontent {
            margin-left: 0 !important;
        }
    }

    .n-message-wrapper, .n-message-container, .n-modal-container {
        z-index: 999999 !important;
    }

    .n-input input:focus, .n-input textarea:focus {
        border: none !important;
        box-shadow: none !important;
    }

    .n-code {
        padding: 0;
        background: none !important;
    }
</style>
<div id="app"></div>
<script>
    jQuery(function () {
        var wpBodyContentEl = jQuery("#wpbody-content")
        var wpAdminBarEl = jQuery("#wpadminbar")
        var wpFooterEl = jQuery("#wpfooter")

        function loadWpContentHeight() {
            var h = window.innerHeight - wpAdminBarEl.height() - wpFooterEl.height() - 50
            wpBodyContentEl.attr("style", "height:" + h + "px")
        }

        window.onresize = loadWpContentHeight

        loadWpContentHeight()
    })

    window.puockSettingMetaInfo = {
        version: "V<?php echo PUOCK_CUR_VER_STR ?>",
        github: "https://github.com/Licoy/wordpress-theme-puock",
        qq: "https://licoy.cn/go/puock-update.php?r=qq_qun",
        license: "GPL V3",
        donate: "https://licoy.cn/go/zs",
        get_url: '<?php echo admin_url('admin-ajax.php') ?>?action=get_theme_options',
        update_url: '<?php echo admin_url('admin-ajax.php') ?>?action=update_theme_options',
    }
</script>
<?php
if (file_exists(dirname(__FILE__) . './template-script-dev.php')) {
    include_once dirname(__FILE__) . './template-script-dev.php';
} else { ?>

    <script type="module" crossorigin
            src="<?php echo get_template_directory_uri() ?>/assets/dist/setting/index.js?ver=<?php echo PUOCK_CUR_VER_STR ?>"></script>

    <?php
}
?>
