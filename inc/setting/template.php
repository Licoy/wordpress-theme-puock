<?php
global $_wp_admin_css_colors;
$puock_admin_color_key = get_user_option('admin_color');
if (empty($puock_admin_color_key) || !is_array($_wp_admin_css_colors) || !isset($_wp_admin_css_colors[$puock_admin_color_key])) {
    $puock_admin_color_key = 'modern';
}
$puock_admin_color_info = is_array($_wp_admin_css_colors) && isset($_wp_admin_css_colors[$puock_admin_color_key])
    ? $_wp_admin_css_colors[$puock_admin_color_key]
    : null;
$puock_admin_color_scheme = [
    'key' => $puock_admin_color_key,
    'name' => $puock_admin_color_info->name ?? __('默认', PUOCK),
    'colors' => array_values($puock_admin_color_info->colors ?? ['#1d2327', '#2c3338', '#72aee6', '#2271b1']),
    'iconColors' => $puock_admin_color_info->icon_colors ?? (object)[],
];
$puock_admin_primary_color = $puock_admin_color_scheme['colors'][3] ?? ($puock_admin_color_scheme['colors'][2] ?? '#2271b1');
$puock_config_debug_entry = \Puock\Theme\setting\PuockSetting::get_config_debug_entry();
?>
<?php if ($puock_config_debug_entry === ''): ?>
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
        adminColorScheme: <?php echo wp_json_encode($puock_admin_color_scheme, JSON_UNESCAPED_SLASHES); ?>,
        language:"<?php echo get_user_locale() ?>",
        description:"<?php _e('简单/方便/高颜值', PUOCK) ?>",
        tag: {text: "<?php _e('主题', PUOCK) ?>", color: '<?php echo esc_js($puock_admin_primary_color); ?>'},
        github: "https://github.com/Licoy/wordpress-theme-puock",
        qq: "https://licoy.cn/go/puock-update.php?r=qq_qun",
        license: "GPL V3",
        donate: "https://licoy.cn/puock-theme-sponsor.html",
        update_url: '<?php echo admin_url('admin-ajax.php') ?>?action=update_theme_options',
        reset_url: '<?php echo admin_url('admin-ajax.php') ?>?action=reset_theme_options',
        fields:<?php echo json_encode($fields); ?>,
        data:<?php echo json_encode(get_option(PUOCK_OPT)); ?>,
    }

</script>
<script type="text/javascript" crossorigin src="<?php echo get_template_directory_uri() ?>/assets/dist/setting/language/<?php echo get_user_locale() ?>.js?ver=<?php echo PUOCK_CUR_VER_STR ?>"></script>
<?php if ($puock_config_debug_entry !== ''): ?>
    <script type="module" src="<?php echo esc_url($puock_config_debug_entry . '/@vite/client'); ?>"></script>
    <script type="module" src="<?php echo esc_url($puock_config_debug_entry . '/src/main.ts'); ?>"></script>
<?php else: ?>
    <script type="module" crossorigin
            src="<?php echo get_template_directory_uri() ?>/assets/dist/setting/index.js?ver=<?php echo PUOCK_CUR_VER_STR ?>"></script>
<?php endif; ?>
