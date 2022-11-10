<?php

pk_ajax_register('pk_oauth_quick_page', 'pk_oauth_quick_page_callback', true);


function pk_oauth_quick_page_callback()
{
    $redirect = $_GET['redirect'] ?? get_edit_profile_url();
    $oauth_list = pk_oauth_list();
    echo "<div class='d-flex justify-content-center wh100'>";
    foreach ($oauth_list as $key => $val) {
        if (pk_is_checked('oauth_' . $key)) { ?>
            <a class="btn btn-<?php echo $val['color_type'] ?? 'primary'; ?> btn-ssm mr5 mb5"
               data-no-instant
               href="<?php echo pk_oauth_url_page_ajax($key, $redirect) ?>">
                <?php if ($val['icon'] ?? '') {
                    echo "<i class='{$val['icon']}'></i>";
                } ?>
                <?php echo $val['label'] ?>
            </a>
        <?php }
    }
    echo "</div>";
    wp_die();
}
