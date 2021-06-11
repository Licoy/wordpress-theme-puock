<?php

get_header();

?>

<div id="page" class="container mt20">
    <?php echo pk_breadcrumbs(); while (have_posts()):the_post();?>
        <div id="page-empty">
            <div id="page-<?php the_ID() ?>" class="row row-cols-1">
                <div id="post-main" class="col-lg-<?php pk_hide_sidebar_out('12','8') ?> col-md-12 <?php pk_open_box_animated('animated fadeInLeft') ?> ">
                    <div class="p-block">
                        <div><h2 id="post-title" class="mb-0 puock-text t-xxl"><?php the_title() ?></h2></div>
                        <div class="options clearfix mt20">
                            <div class="float-left">
                                <?php if (!pk_is_checked('hide_post_views')): ?>
                                <div class="option puock-bg ta3 t-sm float-left mr-1"><i class="czs-eye-l mr-1"></i><span id="post-views"><?php pk_get_post_views(); _e('次阅读', PUOCK) ?></span></div>
                                <?php endif; ?>
                                <a href="#comments"><div class="option puock-bg ta3 t-sm float-left mr-1"><i class="czs-comment-l mr-1"></i><?php comments_number() ?></div></a>
                            </div>
                            <div class="float-right">
                                <div class="option puock-bg ta3 t-sm float-left mr-1 d-none d-lg-inline-block post-main-size"><i class="czs-bevel"></i></div>
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
