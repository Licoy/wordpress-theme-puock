<!--轮播图-->
<?php
if (pk_is_checked('index_carousel')):
    $b_posts = query_posts(array('meta_key' => pk_get_option('index_mode') == 'company' ? 'banner_company' : 'banner',
        'meta_value' => 'true', 'meta_compare' => '=',
        'ignore_sticky_posts' => true, 'posts_per_page' => pk_get_option('index_carousel_mn', 3)));
    if ($b_posts && count($b_posts) > 0):
        ?>
        <div id="banners" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                <?php for ($i = 0; $i < count($b_posts); $i++): ?>
                    <li data-target="#banners" data-slide-to="<?php echo $i ?>"
                        class="<?php echo $i == 0 ? 'active' : '' ?>"></li>
                <?php endfor; ?>
            </ol>
            <div class="carousel-inner">
                <?php $i = 0;
                foreach ($b_posts as $post): setup_postdata($post);
                    $custom_banner_to_url = get_post_meta(get_the_ID(), 'banner_to', true); ?>
                    <a href="<?php if (empty($custom_banner_to_url)) {
                        the_permalink();
                    } else {
                        echo $custom_banner_to_url;
                    } ?>" class="carousel-item <?php echo $i == 0 ? 'active' : '' ?>">
                        <img class="w-100" src="<?php echo get_post_meta(get_the_ID(), 'banner_url', true) ?>"
                             title="<?php the_title() ?>" alt="<?php the_title() ?>">
                        <div class="carousel-caption d-none d-md-block">
                            <p class="mb-0"><?php the_title() ?></p>
                        </div>
                    </a>
                    <?php $i++;endforeach;
                unset($i); ?>
            </div>
            <a class="carousel-control-prev" href="#banners" role="button" data-slide="prev">
                <i class="czs-angle-left-l"></i>
            </a>
            <a class="carousel-control-next" href="#banners" role="button" data-slide="next">
                <i class="czs-angle-right-l"></i>
            </a>
        </div>
    <?php endif;
    wp_reset_query();endif; ?>