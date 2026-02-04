<?php

use Puock\Theme\classes\PuockUserCenter;

if (!is_user_logged_in()) {
    wp_redirect(home_url());
    exit;
}
$current_user_center_menu = get_query_var('id', 'profile');
if (empty($current_user_center_menu)) {
    $current_user_center_menu = 'profile';
}
$user_center_menus = PuockUserCenter::get_menus();
pk_set_custom_seo((isset($user_center_menus[$current_user_center_menu]) ? $user_center_menus[$current_user_center_menu]['title'].' - ' : '') . __('用户中心', PUOCK));
get_header();
?>

<div id="content" class="mt20 container min-height-container">
    <?php echo pk_breadcrumbs() ?>
    <div class="p-block" id="user-center">
        <div class="row row-cols-1">
            <div class="col-lg-3 col-md-12">
                <div class="list-group user-center-menus">
                    <?php foreach ($user_center_menus as $key => $menu): ?>
                        <a href="<?php echo home_url() . '/uc/' . $key; ?>"
                           class="ta3 list-group-item d-flex justify-content-between align-items-start <?php echo $current_user_center_menu === $key ? 'current' : ''; ?>">
                            <div class="ms-2 me-auto">
                                <div><?php echo $menu['title'] ?></div>
                                <span class="sub-title d-none d-md-inline-block"><?php echo $menu['subtitle'] ?></span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-lg-9 col-md-12 fs14">
                <?php call_user_func($user_center_menus[$current_user_center_menu]['call']) ?>
            </div>
        </div>
    </div>
</div>


<?php get_footer(); ?>
