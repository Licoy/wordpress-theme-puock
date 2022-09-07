<?php if (in_array(pk_get_option('index_mode', ''), array('cms', 'company'))
    && (pk_is_checked('cms_show_2box') || pk_is_checked('company_show_2box'))): ?>
    <div class="row row-cols-1 <?php pk_open_box_animated('animated fadeInUp') ?> " id="magazines">
        <?php
        $cms_mode = pk_get_option('index_mode', '') == 'cms' ? 'cms' : 'company';
        $cms_cats_str = pk_get_option($cms_mode . '_show_2box_id', '');
        if (!empty($cms_cats_str)) {
            $cms_cats = is_array($cms_cats_str) ? $cms_cats_str : explode(",", $cms_cats_str);
            if (count($cms_cats) > 0) {
                $cms_cats_num = pk_get_option($cms_mode . '_show_2box_num', '6');
                foreach ($cms_cats as $catId):
                    query_posts(array(
                        'cat' => $catId,
                        'posts_per_page' => $cms_cats_num,
                        'orderby' => 'DESC'
                    ));
                    $post_index = 0;
                    ?>
                    <?php if (have_posts()) : ?>
                    <div class="col-md-6 pr-0 magazine">
                        <div class="p-block">
                            <div>
                                <span class="t-lg puock-text pb-2 d-inline-block border-bottom border-primary"><?php echo get_post_category_link('ta3 a-link', '<i class="czs-layers"></i>&nbsp;', $catId) ?></span>
                            </div>
                            <?php while (have_posts()) : the_post(); ?>
                                <?php if ($post_index == 0): ?>
                                    <div class="media row mt10">
                                        <div class="col-4">
                                            <figure class="thumbnail">
                                                <a <?php pk_link_target() ?> href="<?php the_permalink() ?>">
                                                    <img title="<?php the_title() ?>"
                                                         alt="<?php the_title() ?>" <?php echo pk_get_lazy_img_info(get_post_images(), 'w-100', 280, 180) ?>/>
                                                </a>
                                            </figure>
                                        </div>
                                        <div class="media-body col-8 pl-0">
                                            <h2 class="t-lg t-line-1"><a class="a-link"
                                                                         title="<?php the_title() ?>" <?php pk_link_target() ?>
                                                                         href="<?php the_permalink() ?>"><?php the_title() ?></a>
                                            </h2>
                                            <div class="t-md c-sub d-none d-md-block"><?php the_excerpt() ?></div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="media-link media-row-2">
                                        <div class="t-lg t-line-1 row">
                                            <div class="col-lg-9 col-12 text-nowrap text-truncate">
                                                <i class="czs-angle-right-l t-sm c-sub mr-1"></i>
                                                <a class="a-link t-w-400 t-md"
                                                   title="<?php the_title() ?>" <?php pk_link_target() ?>
                                                   href="<?php the_permalink() ?>"><?php the_title() ?></a>
                                            </div>
                                            <div class="col-lg-3 text-right d-none d-lg-block">
                                                <span class="c-sub t-sm"><?php pk_get_post_date() ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif;
                                $post_index++; ?>
                            <?php endwhile; ?>
                        </div>
                    </div>
                <?php endif;
                    wp_reset_query();endforeach;
            }
        } ?>
    </div>
<?php endif; ?>
