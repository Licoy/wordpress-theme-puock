<!--文章列表-->
<div id="posts">
    <div class="<?php if(!pk_post_style_list()){echo 'row';} ?> mr-0 ml-0">
        <?php while(have_posts()) : the_post(); ?>
            <?php get_template_part('templates/module','post') ?>
        <?php endwhile; ?>
    </div>
    <?php if(!(pk_get_option('index_mode','')=='cms' && is_home())): ?>
    <?php pk_paging(); ?>
    <?php endif; ?>
</div>
