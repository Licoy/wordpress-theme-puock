<?php

pk_ajax_register('pk_ajax_dialog_post_share', 'pk_ajax_dialog_post_share', true);
function pk_ajax_dialog_post_share()
{
    $post_id = $_REQUEST['id'] ?? 0;
    if (empty($post_id)) {
        wp_die(sprintf(__('无效的文章ID: %s', PUOCK), $post_id));
    }
    $post_link = get_permalink($post_id);
    ?>
    <!-- 分享至第三方 -->
    <div class="d-flex justify-content-center w-100">
        <div data-id="wb"
             class="share-to circle-button circle-sm circle-hb text-center bg-danger text-light"><i
                    class="fa-brands fa-weibo t-md"></i></div>
        <div data-id="wx" id="wx-share" data-bs-toggle="tooltip" data-bs-html="true"
             data-bs-title="<p class='text-center t-sm mb-1 mt-1'><?php esc_attr_e('使用微信扫一扫', PUOCK) ?></p><img width='120' height='120' class='mb-1' alt='<?php esc_attr_e('微信二维码', PUOCK) ?>' src='<?php echo PUOCK_ABS_URI . pk_post_qrcode($post_link) ?>'/>"
             class="share-to circle-button circle-sm circle-hb text-center bg-success text-light"><i
                    class="fa-brands fa-weixin t-md"></i></div>
        <div data-id="qzone"
             class="share-to circle-button circle-sm circle-hb text-center bg-warning text-light">
            <i class="fa-brands fa-qq t-md"></i></div>
        <div data-id="tw"
             class="share-to circle-button circle-sm circle-hb text-center bg-info text-light"><i
                    class="fa-brands fa-twitter t-md"></i></div>
        <div data-id="fb"
             class="share-to circle-button circle-sm circle-hb text-center bg-primary text-light"><i
                    class="fa-brands fa-facebook t-md"></i></div>
        <div data-id="copy-link" data-cp-val="<?php echo $post_link ?>"
             class="circle-button circle-sm circle-hb text-center bg-dark text-light pk-copy"><i
                    class="fa fa-copy t-md"></i></div>
    </div>

    <?php

    wp_die();
}

