<?php

if (is_single()):

    $relevant_cats = get_the_category();

    if ($relevant_cats && count($relevant_cats) > 0) {

        $relevant_cat_ids = '';

        foreach ($relevant_cats as $relevant_cat) {
            $relevant_cat_ids .= $relevant_cat->term_id . ',';
        }

        $relevant_cat_ids = substr($relevant_cat_ids, 0, strlen($relevant_cat_ids) - 1);

        $_cache_key = sprintf(PKC_CAT_RELEVANT_POSTS, $relevant_cat_ids);
        $relevants = pk_cache_get($_cache_key);
        if (!$relevants) {
            $relevants = query_posts(array('cat' => $relevant_cat_ids, 'ignore_sticky_posts' => true,
                'posts_per_page' => 4, 'orderby' => 'rand'));
            pk_cache_set($_cache_key, $relevants);
        }

        if ($relevants && count($relevants) > 0): ?>

            <div class="p-block pb-0">
                <div class="row puock-text post-relevant">
                    <?php foreach ($relevants as $post): setup_postdata($post) ?>
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
        endif;
        wp_reset_query();
    }


endif;
