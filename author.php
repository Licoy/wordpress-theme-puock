<?php get_header() ?>

<div id="content" class="mt15 container">
    <?php get_template_part('templates/box', 'global-top') ?>

    <?php echo pk_breadcrumbs() ?>

    <div id="author">
        <div class="bg"
             style="background-image: url('<?php echo PUOCK_ABS_URI.'/assets/img/show/author-bg.jpg' ?>')">
            <div class="avatar">
                <img <?php echo pk_get_lazy_img_info(get_avatar_url(get_the_author_meta('ID'),['size'=>200]),'avatar') ?>>
            </div>
        </div>
        <div class="info">
            <div class="fs18"><?php the_author_meta('display_name') ?></div>
            <div class="fs12 c-sub mt10"><?php the_author_meta('description') ?></div>
        </div>
        <div class="row row-cols-1 mt15">
            <div class="col-lg-<?php pk_hide_sidebar_out('12', '8') ?> col-md-12
            <?php pk_open_box_animated('animated fadeInLeft') ?> ">
                <div class="author-tab-data">
                    <div class="data-header">
                        <div class="item cur">
                            <?php _e('文章', PUOCK) ?> <span class="c-sub fs12"><?php echo count_user_posts(get_the_author_meta('ID')) ?></span>
                        </div>
<!--                        <div class="item">-->
<!--                            评论 <span class="c-sub fs12">--><?php //echo count_user_posts(get_the_author_meta('ID')) ?><!--</span>-->
<!--                        </div>-->
                    </div>
                    <div class="data-content">
                        <div class="posts">
                            <?php get_template_part('templates/module', 'posts') ?>
                        </div>
                    </div>
                </div>

            </div>
            <?php get_sidebar() ?>
        </div>

    </div>


    <?php get_template_part('templates/box', 'global-bottom') ?>
</div>


<?php get_footer() ?>
