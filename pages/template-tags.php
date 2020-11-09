<?php
/*
Template Name: 标签概览
*/

get_header();

?>

<div id="page" class="container mt20">
    <?php echo pk_breadcrumbs(); while (have_posts()):the_post();?>
        <div id="page-tags">
            <div id="page-<?php the_ID() ?>" class="row row-cols-1">
                <div id="posts" class="col-lg-<?php pk_hide_sidebar_out('12','8') ?> col-md-12 <?php pk_open_box_animated('animated fadeInLeft') ?> ">
                    <?php if(!empty(get_the_content())): ?>
                        <div class="mt20 p-block puock-text entry-content">
                            <?php the_content() ?>
                        </div>
                    <?php endif ?>
                    <div class="puock-text p-block no-style pb-2">
                        <?php specs_show_tags() ?>
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
