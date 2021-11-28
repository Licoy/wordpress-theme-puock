<?php get_header() ?>

    <?php if(pk_get_option('index_mode')!='company'): ?>

    <div id="content" class="mt15 container">
        <?php get_template_part('ad/global','top') ?>
        <div class="row row-cols-1">
            <div class="col-lg-<?php pk_hide_sidebar_out('12','8') ?> col-md-12 <?php pk_open_box_animated('animated fadeInLeft') ?> ">
                <?php if(isset($paged) && $paged<=1): ?>
                    <div class="<?php pk_open_box_animated('animated fadeInLeft') ?>">
                        <?php get_template_part('templates/module','banners') ?>
                        <div class="mb15"></div>
                    </div>
                <?php endif; ?>
                <div class="<?php pk_open_box_animated('animated fadeInLeft') ?>
                <?php echo pk_post_style_list() ? '':'pr-0 pl-0' ?>">
                    <div>
                        <?php get_template_part('templates/module','posts') ?>
                    </div>
                </div>
            </div>
            <?php get_sidebar() ?>
        </div>

        <?php get_template_part('templates/module','cms') ?>

        <?php get_template_part('templates/module','links') ?>

        <?php get_template_part('ad/global','bottom') ?>
    </div>

    <?php else: get_template_part('templates/page','company'); endif; ?>



<?php get_footer() ?>