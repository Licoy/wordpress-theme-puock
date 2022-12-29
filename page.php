<?php get_header(); ?>

<div id="page" class="container mt20">
    <?php get_template_part('ad/global', 'top') ?>
    <?php echo pk_breadcrumbs(); while (have_posts()):the_post();?>
        <div id="page-empty">
            <div id="page-<?php the_ID() ?>" class="row row-cols-1">
                <div id="post-main" class="col-lg-<?php pk_hide_sidebar_out('12','8') ?> col-md-12 <?php pk_open_box_animated('animated fadeInLeft') ?> ">
                    <div class="p-block">
                        <div><h1 id="post-title" class="mb-0 puock-text t-xxl"><?php the_title() ?></h1></div>
                        <div class="options p-flex-sbc mt20">
                            <div>
                                <?php if (!pk_is_checked('hide_post_views')): ?>
                                <div class="option puock-bg ta3 t-sm mr-1"><i class="fa-regular fa-eye mr-1"></i><span id="post-views"><?php pk_get_post_views();  ?></span><span><?php _e('次阅读', PUOCK) ?></span></div>
                                <?php endif; ?>
                                <a href="#comments"><div class="option puock-bg ta3 t-sm mr-1"><i class="fa-regular fa-comment mr-1"></i><?php comments_number() ?></div></a>
                                <?php if (is_user_logged_in() && current_user_can('edit_post', $post->ID)): ?>
                                    <a target="_blank" href="<?php echo get_edit_post_link() ?>">
                                        <div class="option puock-bg ta3 t-sm mr-1"><i
                                                    class="fa-regular fa-pen-to-square mr-1"></i><?php _e('编辑', PUOCK) ?></div>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <?php if(!pk_is_checked("hide_global_sidebar")): ?>
                            <div>
                                <div class="option puock-bg ta3 t-sm mr-1 d-none d-lg-inline-block post-main-size"><i class="fa fa-up-right-and-down-left-from-center"></i></div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="mt20 puock-text <?php get_entry_content_class() ?>">
                            <?php the_content() ?>
                            <?php do_action('pk_page_content_footer'); ?>
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
                        <?php get_template_part('templates/post', 'actions') ?>
                    </div>
                    <?php dynamic_sidebar('page_content_comment_top'); ?>
                    <?php comments_template() ?>
                    <?php dynamic_sidebar('page_content_comment_bottom'); ?>
                </div>
                <?php get_sidebar() ?>
            </div>
            <?php get_template_part('ad/global', 'bottom') ?>
        </div>
    <?php endwhile; ?>
</div>

<?php get_footer() ?>
