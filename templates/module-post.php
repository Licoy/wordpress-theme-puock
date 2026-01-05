<!--文章ID：<?php the_ID() ?> -->
<?php if (pk_post_style_list()): ?>
    <article class="block card-plain post-item p-block post-item-list">
        <div class="thumbnail">
            <a class="t-sm  ww" <?php pk_link_target() ?> href="<?php the_permalink() ?>">
                <img title="<?php the_title() ?>"
                     alt="<?php the_title() ?>" <?php echo pk_get_lazy_img_info(get_post_images(), '', 280, 160) ?> />
            </a>
        </div>
        <div class="post-info">
            <div class="info-top">
                <h2 class="info-title">
                    <?php if (is_sticky()): ?><span class="badge bg-danger"><i
                                class="fa fa-bolt-lightning"></i>置顶</span><?php endif; ?>
                    <?php echo get_post_category_link('badge d-none d-md-inline-block bg-' . pk_get_color_tag(['danger', 'warning', 'dark']) . ' ahfff') ?>
                    <a class="a-link" title="<?php the_title() ?>" <?php pk_link_target() ?>
                       href="<?php the_permalink() ?>"><?php the_title() ?></a>
                </h2>
                <div class="info-meta c-sub text-2line d-none d-md-block">
                    <?php the_excerpt() ?>
                </div>
            </div>
            <div class="info-footer w-100">
                <div>
                    <span class="t-sm c-sub">
                            <?php if (!pk_is_checked('hide_post_views')): ?>
                                <span class="mr-2"><i
                                            class="fa-regular fa-eye mr-1"></i><?php pk_get_post_views() ?><span
                                            class="t-sm d-none d-sm-inline-block"><?php _e('次阅读', PUOCK) ?></span></span>
                            <?php endif; ?>
                        <?php if (!pk_post_comment_is_closed()): ?>
                            <a class="c-sub-a" <?php pk_link_target() ?> href="<?php the_permalink() ?>#comments">
                                <i class="fa-regular fa-comment mr-1"></i>
                                <?php echo get_comments_number() ?><span
                                        class="t-sm d-none d-sm-inline-block"><?php _e('个评论', PUOCK) ?></span></a>
                        <?php endif; ?>
                    </span>
                </div>
                <div>
                    <?php echo get_post_category_link('c-sub-a t-sm ml-md-2 line-h-20 d-inline-block d-md-none') ?>
                    <span class="t-sm ml-md-2 c-sub line-h-20 d-none d-md-inline-block"><i
                                class="fa-regular fa-clock"></i> <?php pk_get_post_date() ?></span>
                </div>
            </div>
        </div>
        <span class="title-l-c bg-primary"></span>
    </article>
<?php else: ?>
    <?php
    $card_cols = pk_cms_card_columns();
    $card_col_class = 'col-md-6 col-12';
    if ($card_cols == 3) {
        $card_col_class = 'col-lg-4 col-md-6 col-sm-6 col-12';
    } elseif ($card_cols == 4) {
        $card_col_class = 'col-lg-3 col-md-4 col-sm-6 col-12';
    }
    ?>
    <article class="block card-plain post-item <?php echo $card_col_class; ?> post-item-card">
        <div class="p-block post-item-block">
            <div class="thumbnail position-relative">
                <a class="t-sm ww" <?php pk_link_target() ?> href="<?php the_permalink() ?>">
                    <img title="<?php the_title() ?>"
                         alt="<?php the_title() ?>" <?php echo pk_get_lazy_img_info(get_post_images(), '', 400, 200) ?> />
                </a>
                <div class="post-tags">
                    <?php if (is_sticky()): ?><span class="badge bg-danger"><i
                                class="fa fa-bolt-lightning"></i>置顶</span><?php endif; ?>
                </div>
            </div>
            <div class="post-info">
                <h2 class="info-title">
                    <?php echo get_post_category_link('badge d-none d-md-inline-block bg-' . pk_get_color_tag(['danger', 'warning', 'dark']) . ' ahfff') ?>
                    <a class="a-link puock-text" title="<?php the_title() ?>" <?php pk_link_target() ?>
                       href="<?php the_permalink() ?>"><?php the_title() ?></a>
                </h2>
                <div class="info-meta c-sub">
                    <div class="text-2line">
                        <?php the_excerpt() ?>
                    </div>
                </div>
                <div class="info-footer w-100">
                    <div>
                   <span class="t-sm c-sub">
                            <?php if (!pk_is_checked('hide_post_views')): ?>
                                <span class="mr-2">
                                <i class="fa-regular fa-eye mr-1"></i><?php pk_get_post_views() ?><span
                                            class="t-sm d-none d-sm-inline-block"><?php _e('次阅读', PUOCK) ?></span></span>
                            <?php endif; ?>
                       <?php if (!pk_post_comment_is_closed()): ?>
                           <a class="c-sub-a" <?php pk_link_target() ?> href="<?php the_permalink() ?>#comments">
                                <i class="fa-regular fa-comment mr-1"></i>
                                <?php echo get_comments_number() ?><span
                                       class="t-sm d-none d-sm-inline-block"><?php _e('个评论', PUOCK) ?></span></a>
                       <?php endif; ?>
                    </span>
                    </div>
                    <div>
                        <?php echo get_post_category_link('c-sub-a t-sm ml-md-2 line-h-20 d-inline-block d-md-none') ?>
                        <span class="t-sm ml-md-2 c-sub line-h-20 d-none d-md-inline-block"><i
                                    class="fa-regular fa-clock"></i> <?php pk_get_post_date() ?></span>
                    </div>
                </div>
            </div>
        </div>
    </article>
<?php endif; ?>
