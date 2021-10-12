<?php
$headMetas = [
    'home' => home_url(),
    'vd_comment' => pk_is_checked('vd_comment') ? 'on' : 'off',
    'vd_vid' => pk_get_option('vd_vaptcha_id', ''),
    'use_post_menu' => pk_is_checked('use_post_menu') ? 'on' : 'off',
];
?>
<meta name="puock-params" content='<?php echo json_encode($headMetas) ?>'>