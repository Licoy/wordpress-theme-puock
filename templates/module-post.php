<!--文章ID：<?php the_ID() ?> -->
<?php if (pk_post_style_list()): ?>
    <article class="block card-plain post-item p-block">
        <div class="row">
            <div class="col-4 col-lg-3">
                <figure class="thumbnail">
                    <a class="t-sm" <?php pk_link_target() ?> href="<?php the_permalink() ?>">
                        <img title="<?php the_title() ?>"
                             alt="<?php the_title() ?>" <?php echo pk_get_lazy_img_info(get_post_images(), 'tsh', 280, 180) ?> />
                    </a>
                </figure>
            </div>
            <header class="post-info col-lg-9 col-8 d-block">
                <h2 class="info-title t-line-1">
                    <?php if (is_sticky()): ?><span class="badge bg-danger"><i
                                class="czs-lightning-l"></i>置顶</span><?php endif; ?>
                    <?php echo get_post_category_link('badge d-none d-md-inline-block bg-' . pk_get_color_tag(['danger', 'warning', 'dark']) . ' ahfff') ?>
                    <a class="a-link" title="<?php the_title() ?>" <?php pk_link_target() ?>
                       href="<?php the_permalink() ?>"><?php the_title() ?></a>
                </h2>
                <div class="info-meta d-none d-md-block c-sub">
                    <?php the_excerpt() ?>
                </div>
                <div class="info-footer w-100 clearfix d-none d-block">
                    <div class="float-left">
                                        <span class="t-sm c-sub">
                                                <?php if (!pk_is_checked('hide_post_views')): ?>
                                                    <span class="mr-2"><i
                                                                class="czs-eye-l mr-1"></i><?php pk_get_post_views() ?><span
                                                                class="t-sm d-none d-sm-inline-block">次阅读</span></span>
                                                <?php endif; ?>
                                            <?php if (!pk_post_comment_is_closed()): ?>
                                                <a class="c-sub-a" <?php pk_link_target() ?> href="<?php the_permalink() ?>#comments">
                                                    <i class="czs-comment-l mr-1"></i>
                                                    <?php echo get_comments_number() ?><span
                                                            class="t-sm d-none d-sm-inline-block">个评论</span></a>
                                            <?php endif; ?>
                                        </span>
                    </div>
                    <div class="float-right">
                        <?php echo get_post_category_link('c-sub-a t-sm ml-md-2 line-h-20 d-inline-block d-md-none', '<i class="czs-tag-l mr-1"></i>') ?>
                        <span class="t-sm ml-md-2 c-sub line-h-20 d-none d-md-inline-block"><?php pk_get_post_date() ?></span>
                    </div>
                </div>
            </header>
        </div>
        <span class="title-l-c bg-primary"></span>
    </article>
<?php else: ?>
    <article class="block card-plain post-item col-sm-6 col-12 post-item-card">
        <div class="p-block">
            <figure class="thumbnail">
                <a class="t-sm" <?php pk_link_target() ?> href="<?php the_permalink() ?>">
                    <img title="<?php the_title() ?>"
                         alt="<?php the_title() ?>" <?php echo pk_get_lazy_img_info(get_post_images(), 'tsh', 400, 200) ?> />
                </a>
            </figure>
            <header class="post-info d-block">
                <h2 class="info-title t-line-1">
                    <?php if (is_sticky()): ?><span class="badge bg-danger"><i
                                class="czs-lightning-l"></i>置顶</span><?php endif; ?>
                    <?php echo get_post_category_link('badge d-none d-md-inline-block bg-' . pk_get_color_tag(['danger', 'warning', 'dark']) . ' ahfff') ?>
                    <a class="a-link puock-text" title="<?php the_title() ?>" <?php pk_link_target() ?>
                       href="<?php the_permalink() ?>"><?php the_title() ?></a>
                </h2>
                <div class="info-meta d-block c-sub">
                    <p class="text-2line"><?php echo get_the_excerpt() ?></p>
                </div>
                <div class="info-footer w-100 clearfix d-none d-block">
                    <div class="float-left">
                                    <span class="t-sm c-sub">
                                            <?php if (!pk_is_checked('hide_post_views')): ?>
                                                <span class="mr-2">
                                                <i class="czs-eye-l mr-1"></i><?php pk_get_post_views() ?><span
                                                            class="t-sm d-none d-sm-inline-block">次阅读</span></span>
                                            <?php endif; ?>
                                        <?php if (!pk_post_comment_is_closed()): ?>
                                            <a class="c-sub-a" <?php pk_link_target() ?> href="<?php the_permalink() ?>#comments">
                                                <i class="czs-comment-l mr-1"></i>
                                                <?php echo get_comments_number() ?><span
                                                        class="t-sm d-none d-sm-inline-block">个评论</span></a>
                                        <?php endif; ?>
                                    </span>
                    </div>
                    <div class="float-right">
                        <?php echo get_post_category_link('c-sub-a t-sm ml-md-2 line-h-20 d-inline-block d-md-none', '<i class="czs-tag-l mr-1"></i>') ?>
                        <span class="t-sm ml-md-2 c-sub line-h-20 d-none d-md-inline-block"><?php pk_get_post_date() ?></span>
                    </div>
                </div>
            </header>
        </div>
    </article>
<?php endif; ?>
