<!--文章列表-->
<div id="posts">
    <div class="<?php if(!pk_post_style_list()){echo 'row';} ?> mr-0 ml-0">
        <?php while(have_posts()) : the_post(); ?>
            <?php get_template_part('templates/module','post') ?>
        <?php endwhile; ?>
    </div>
    <?php
    $show_load_more = false;
    if (is_home()) {
        $index_mode = pk_get_option('index_mode', '');
        if ($index_mode == 'blog' && pk_is_checked('blog_show_load_more')) {
            $show_load_more = true;
        } elseif ($index_mode == 'cms' && pk_is_checked('cms_show_load_more')) {
            $show_load_more = true;
        }
    }

    $show_pagination = !(pk_get_option('index_mode','')=='cms' && is_home()) || pk_get_option('cms_show_pagination',false);
    ?>
    <?php if ($show_load_more): ?>
    <div class="text-center mt20 mb20">
        <button id="load-more-btn" class="btn btn-sm btn-outline-primary" data-paged="<?php echo isset($paged) ? $paged : 1; ?>">
            <i class="fa fa-plus"></i> <?php _e('加载更多', PUOCK) ?>
        </button>
    </div>
    <?php elseif ($show_pagination): ?>
    <?php pk_paging(); ?>
    <?php endif; ?>
</div>
