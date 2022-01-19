<?php
$headMetas = [
    'home' => home_url(),
    'vd_comment' => pk_is_checked('vd_comment'),
    'use_post_menu' => pk_is_checked('use_post_menu'),
    'is_single' => is_single(),
    'is_pjax' => pk_is_checked('page_ajax_load', false),
    'main_lazy_img' => pk_is_checked('basic_img_lazy_z', false),
    'link_blank_open' => pk_is_checked('link_blank_content'),
];
?>
<meta name="puock-params" content='<?php echo json_encode($headMetas) ?>'>
