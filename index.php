<?php get_header() ?>

<?php if (pk_get_option('index_mode') != 'company'): ?>

    <div id="content" class="mt15 container">
        <?php get_template_part('templates/box', 'global-top') ?>
        <div class="row row-cols-1">
            <div class="col-lg-<?php pk_hide_sidebar_out('12', '8') ?> col-md-12 <?php pk_open_box_animated('animated fadeInLeft') ?> ">
                <?php if (isset($paged) && $paged <= 1): $home_banner_ava = pk_ava_home_banners(); if(is_array($home_banner_ava) && count($home_banner_ava)>0):?>
                    <div class="<?php pk_open_box_animated('animated fadeInLeft') ?>">
                        <?php get_template_part('templates/module', 'banners') ?>
                    </div>
                <?php endif; ?>
                    <?php if (pk_get_option('index_mode') == 'cms' && pk_is_checked('cms_show_four_grid')):
                        $cms_four_grid_ava = pk_ava_cms_four_grid_items();
                        if (is_array($cms_four_grid_ava) && count($cms_four_grid_ava) > 0):
                            ?>
                    <div class="<?php pk_open_box_animated('animated fadeInLeft') ?>">
                        <?php get_template_part('templates/module', 'four-grid') ?>
                    </div>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
                <div class="<?php pk_open_box_animated('animated fadeInLeft') ?>
                <?php echo pk_post_style_list() ? '' : 'pe-0 ps-0' ?>">
                    <div>
                        <?php get_template_part('templates/module', 'posts') ?>
                    </div>
                </div>
            </div>
            <?php get_sidebar() ?>
        </div>

        <?php get_template_part('templates/module', 'cms') ?>

        <?php get_template_part('templates/module', 'links') ?>

        <?php get_template_part('templates/box', 'global-bottom') ?>

        <?php dynamic_sidebar('index_bottom') ?>

    </div>

<?php else: get_template_part('templates/page', 'company'); endif; ?>



<?php get_footer() ?>
