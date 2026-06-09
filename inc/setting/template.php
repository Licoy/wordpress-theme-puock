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
$puock_setting_asset_version = static function ($relative_path) {
    $version = PUOCK_CUR_VER_STR;
    $file_path = get_template_directory() . $relative_path;
    if (is_readable($file_path)) {
        $version .= '-' . filemtime($file_path);
    }
    return esc_attr($version);
};
$puock_smtp_test_mail_labels = [
    'recipient' => __('收件人', PUOCK),
    'admin' => __('站点管理员邮箱', PUOCK),
    'current' => __('当前登录用户邮箱', PUOCK),
    'custom' => __('自定义邮箱', PUOCK),
    'customPlaceholder' => __('请输入收件人邮箱', PUOCK),
    'send' => __('发送测试邮件', PUOCK),
    'sending' => __('正在发送...', PUOCK),
    'success' => __('测试邮件发送成功，请检查收件箱', PUOCK),
    'failed' => __('测试邮件发送失败', PUOCK),
    'missingUrl' => __('缺少测试邮件接口地址', PUOCK),
    'requestFailed' => __('请求失败，请检查网络或控制台错误', PUOCK),
    'tips' => __('测试会使用当前表单中的 SMTP 配置，不会自动保存。测试通过后仍需点击右上角保存配置。', PUOCK),
];
$puock_theme_options_nonce = wp_create_nonce('pk_theme_options');
?>
<?php if ($puock_config_debug_entry === ''): ?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri() ?>/assets/dist/setting/index.css?ver=<?php echo $puock_setting_asset_version('/assets/dist/setting/index.css') ?>">
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
        update_url: <?php echo wp_json_encode(admin_url('admin-ajax.php?action=update_theme_options&nonce=' . $puock_theme_options_nonce), JSON_UNESCAPED_SLASHES); ?>,
        reset_url: <?php echo wp_json_encode(admin_url('admin-ajax.php?action=reset_theme_options&nonce=' . $puock_theme_options_nonce), JSON_UNESCAPED_SLASHES); ?>,
        smtp_test_url: <?php echo wp_json_encode(admin_url('admin-ajax.php?action=pk_smtp_test_mail&nonce=' . wp_create_nonce('pk_smtp_test_mail')), JSON_UNESCAPED_SLASHES); ?>,
        smtpTestMailLabels: <?php echo wp_json_encode($puock_smtp_test_mail_labels, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>,
        fields:<?php echo json_encode($fields); ?>,
        data:<?php echo json_encode(get_option(PUOCK_OPT)); ?>,
    }
</script>
<script type="text/javascript" crossorigin src="<?php echo get_template_directory_uri() ?>/assets/dist/setting/language/<?php echo get_user_locale() ?>.js?ver=<?php echo $puock_setting_asset_version('/assets/dist/setting/language/' . get_user_locale() . '.js') ?>"></script>
<?php if ($puock_config_debug_entry !== ''): ?>
    <script type="module" src="<?php echo esc_url($puock_config_debug_entry . '/@vite/client'); ?>"></script>
    <script type="module" src="<?php echo esc_url($puock_config_debug_entry . '/src/main.ts'); ?>"></script>
<?php else: ?>
    <script type="module" crossorigin
            src="<?php echo get_template_directory_uri() ?>/assets/dist/setting/index.js?ver=<?php echo $puock_setting_asset_version('/assets/dist/setting/index.js') ?>"></script>
<?php endif; ?>
