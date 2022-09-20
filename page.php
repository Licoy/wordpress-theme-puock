<?php get_header(); ?>

<div id="page" class="container mt20">
    <?php echo pk_breadcrumbs(); while (have_posts()):the_post();?>
        <div id="page-empty">
            <div id="page-<?php the_ID() ?>" class="row row-cols-1">
                <div id="post-main" class="col-lg-<?php pk_hide_sidebar_out('12','8') ?> col-md-12 <?php pk_open_box_animated('animated fadeInLeft') ?> ">
                    <div class="p-block">
                        <div><h1 id="post-title" class="mb-0 puock-text t-xxl"><?php the_title() ?></h1></div>
                        <div class="options p-flex-sbc mt20">
                            <div>
                                <?php if (!pk_is_checked('hide_post_views')): ?>
                                <div class="option puock-bg ta3 t-sm mr-1"><i class="czs-eye-l mr-1"></i><span id="post-views"><?php pk_get_post_views();  ?></span><span><?php _e('次阅读', PUOCK) ?></span></div>
                                <?php endif; ?>
                                <a href="#comments"><div class="option puock-bg ta3 t-sm mr-1"><i class="czs-comment-l mr-1"></i><?php comments_number() ?></div></a>
                                <?php if (is_user_logged_in() && current_user_can('edit_post', $post->ID)): ?>
                                    <a target="_blank" href="<?php echo get_edit_post_link() ?>">
                                        <div class="option puock-bg ta3 t-sm mr-1"><i
                                                    class="czs-web-edit-l mr-1"></i><?php _e('编辑', PUOCK) ?></div>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="option puock-bg ta3 t-sm mr-1 d-none d-lg-inline-block post-main-size"><i class="czs-bevel"></i></div>
                            </div>
                        </div>
                        <div class="mt20 puock-text entry-content">
                            <?php the_content() ?>
                            <?php
                            $link_pages = wp_link_pages(array(
                                'before'=> '<li>',
                                'after'=> '</li>',
                                'prev_text'=>'&laquo;',
                                'next_text'=>'&raquo;',
                                'format'=>'<li>%1</li>',
                                'echo'=>false
                            ));
                            if(!empty($link_pages)):
                                ?>
                                <div class="mt20 clearfix text-center">
                                    <ul class="pagination float-right">
                                        <?php echo $link_pages ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php comments_template() ?>
                </div>
                <?php get_sidebar() ?>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<?php get_template_part('templates/module', 'smiley') ?>

<?php get_footer() ?>
