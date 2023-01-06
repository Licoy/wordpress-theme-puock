<?php
/*
Template Name: 随机文章
*/
$posts = get_posts('numberposts=10&orderby=rand');
get_header();
?>

<div id="page" class="container mt15">
    <?php get_template_part('templates/box', 'global-top') ?>
    <?php echo pk_breadcrumbs();?>
    <div id="page-random">
        <div id="page-<?php the_ID() ?>" class="row">
            <div id="posts" class="col-lg-<?php pk_hide_sidebar_out('12','8') ?> col-md-12 <?php pk_open_box_animated('animated fadeInLeft') ?> ">
                <div class="row box-plr15">
                    <?php foreach($posts as $post): setup_postdata($post) ?>
                        <?php get_template_part('templates/module','post') ?>
                    <?php endforeach;wp_reset_postdata(); ?>
                </div>
                <?php comments_template() ?>
            </div>
            <?php get_sidebar() ?>
        </div>
    </div>
    <?php get_template_part('templates/box', 'global-bottom') ?>
</div>

<?php get_template_part('templates/module', 'smiley') ?>

<?php get_footer() ?>
