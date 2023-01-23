<?php
/*
Template Name: 时光圈
*/

get_header();

?>

<div id="page" class="container mt20">
    <?php get_template_part('templates/box', 'global-top') ?>
    <?php echo pk_breadcrumbs(); ?>
    <?php
    $page = get_query_var('paged') ? get_query_var('paged') : 1;
    $cache_key = sprintf(PKC_MOMENTS, $page);
    $posts = pk_cache_get($cache_key);
    if(!$posts){
        $posts = query_posts(array(
            'post_type' => 'moments',
            'posts_per_page' => 10,
            'paged' => get_query_var('paged') ? get_query_var('paged') : 1
        ));
        pk_cache_set($cache_key, $posts);
    }
    ?>
    <div id="page-moments">
        <div id="page-<?php the_ID() ?>" class="row">
            <div id="posts"
                 class="col-lg-<?php pk_hide_sidebar_out('12', '8') ?> col-md-12 <?php pk_open_box_animated('animated fadeInLeft') ?> ">
                <?php foreach ($posts as $post):setup_postdata($post);
                    the_post(); ?>
                    <div class="mt20 p-block puock-text moments-item">
                        <div class="meta p-flex-sc">
                            <div class="avatar mr10">
                                <img <?php echo pk_get_lazy_img_info(get_avatar_url(get_the_author_meta('email')),'md-avatar mt-1') ?>
                                     alt="<?php the_author_meta('display_name') ?>"
                                     title="<?php the_author_meta('display_name') ?>">
                            </div>
                            <div class="info fs12">
                                <div><?php the_author_meta('display_name') ?></div>
                                <div class="c-sub"><?php echo get_the_date('Y-m-d'); ?></div>
                            </div>
                        </div>
                        <div class="mt10 moment-content <?php get_entry_content_class() ?>">
                            <?php the_content(); ?>
                        </div>
                        <div class="mt10 moment-footer p-flex-s-right">
                            <span class="t-sm c-sub">
                            <?php if (!pk_is_checked('hide_post_views')): ?>
                                <span class="mr-2"><i
                                            class="fa-regular fa-eye mr-1"></i><?php pk_get_post_views() ?></span>
                            <?php endif; ?>
                                <?php if (!pk_post_comment_is_closed()): ?>
                                    <a class="c-sub-a" <?php pk_link_target() ?> href="<?php the_permalink() ?>#comments">
                                <i class="fa-regular fa-comment mr-1"></i>
                                <?php echo get_comments_number() ?></a>
                                <?php endif; ?>
                    </span>
                        </div>
                    </div>
                <?php endforeach;
                wp_reset_postdata(); ?>
                <?php pk_paging(); ?>
            </div>
            <?php wp_reset_query();
            get_sidebar() ?>
        </div>
    </div>
    <?php get_template_part('templates/box', 'global-bottom') ?>
</div>

<?php get_footer() ?>
