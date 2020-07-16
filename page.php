<?php

get_header();

?>

<meta name="referrer" content="never">

<div id="page" class="container mt20">
    <?php echo pk_breadcrumbs(); while (have_posts()):the_post();?>
        <div id="page-empty">
            <div id="page-<?php the_ID() ?>" class="row row-cols-1">
                <div id="posts" class="col-lg-<?php pk_hide_sidebar_out('12','8') ?> col-md-12 <?php pk_open_box_animated('animated fadeInLeft') ?> ">
                    <?php if(!empty(get_the_content())): ?>
                        <div class="mt20 p-block puock-text entry-content">
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
                    <?php endif;comments_template() ?>
                </div>
                <?php get_sidebar() ?>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<?php get_template_part('templates/module', 'smiley') ?>

<?php get_footer() ?>
