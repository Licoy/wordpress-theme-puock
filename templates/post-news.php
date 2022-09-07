<?php

if (is_home()):


    if (pk_is_checked('company_news_open')) {

        $company_news_cid = pk_get_option('company_news_cid', null);

        if ($company_news_cid):

            $news = query_posts(array('cat' => (is_array($company_news_cid) ? join(',', $company_news_cid) : $company_news_cid), 'ignore_sticky_posts' => true,
                'posts_per_page' => pk_get_option('company_news_max_num', 4), 'orderby' => 'DESC'));

            if ($news && count($news) > 0): ?>

                <h4 class="text-center"><?php echo pk_get_option('company_news_title') ?></h4>

                <div class="pb-0 mt30">
                    <div class="row puock-text post-relevant">
                        <?php foreach ($news as $post): setup_postdata($post) ?>
                            <a <?php pk_link_target() ?> href="<?php the_permalink() ?>"
                                                         class="col-6 col-md-3 post-relevant-item">
                                <!--                    --><?php //the_title() ?>
                                <div style="background:url('<?php echo pk_get_img_thumbnail_src(get_post_images(), 160, 140); ?>')">
                                    <div class="title"><?php the_title(); ?></div>
                                </div>
                            </a>
                            <?php wp_reset_postdata();endforeach; ?>
                    </div>
                </div>

            <?php
            endif; endif;
        wp_reset_query();
    }

endif;
