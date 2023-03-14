<?php
/*
 Template Name: 站点地图
*/
$posts = get_posts('numberposts=-1&orderby=post_date&order=DESC');

get_header();

?>

<div id="page" class="container mt20">
    <?php get_template_part('templates/box', 'global-top') ?>
    <?php echo pk_breadcrumbs();?>
    <div id="page-sitemap">
        <div id="page-<?php the_ID() ?>" class="row row-cols-1">
            <div id="posts" class="col-lg-<?php pk_hide_sidebar_out('12','8') ?> col-md-12 <?php pk_open_box_animated('animated fadeInLeft') ?> ">
                <div class="p-block" id="sitemap-posts">
                    <h2 class="t-lg puock-text">全部文章</h2>
                    <div class="row">
                        <?php foreach($posts as $post): setup_postdata($post) ?>
                            <div class="col-12 col-lg-6">
                                <div class="media-link mt20">
                                    <h2 class="t-lg t-line-1" title="<?php the_title() ?>">
                                        <i class="fa fa-angle-right t-sm c-sub mr-1"></i>
                                        <a class="a-link t-w-400 t-md puock-text" title="<?php the_title() ?>" <?php pk_link_target() ?> href="<?php the_permalink() ?>"><?php the_title() ?></a>
                                    </h2>
                                </div>
                            </div>
                        <?php endforeach;wp_reset_postdata() ?>
                    </div>
                </div>
                <div class="p-block" id="sitemap-cats">
                    <h2 class="t-lg puock-text">分类目录</h2>
                    <div class="pd-links t-md no-style li-style-line mt20">
                        <ul>
                            <?php wp_list_categories('title_li='); ?>
                        </ul>
                    </div>
                </div>
                <div class="p-block" id="sitemap-pages">
                    <h2 class="t-lg puock-text">所有单页</h2>
                    <div class="pd-links t-md no-style li-style-line mt20">
                        <ul class="pl-0">
                            <?php wp_page_menu( array() ); ?>
                        </ul>
                    </div>
                </div>
                <?php comments_template() ?>
            </div>
            <?php get_sidebar() ?>
        </div>
    </div>
    <?php get_template_part('templates/box', 'global-bottom') ?>
</div>


<?php get_footer() ?>
