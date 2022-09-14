<?php get_header() ?>

<?php while (have_posts()) : the_post(); ?>
    <div id="post" class="container mt20">
        <?php get_template_part('ad/global', 'top') ?>
        <?php echo pk_breadcrumbs() ?>
        <?php get_template_part('ad/page', 'top') ?>
        <div class="row row-cols-1">
            <div id="post-main"
                 class="col-lg-<?php pk_hide_sidebar_out('12', '8') ?> col-md-12 <?php pk_open_box_animated('animated fadeInLeft') ?> ">
                <div class="p-block">
                    <div><h1 id="post-title" class="mb-0 puock-text t-xxl"><?php the_title() ?></h1></div>
                    <div class="options clearfix mt20">
                        <div class="float-left">
                            <?php if (!pk_is_checked('hide_post_views')): ?>
                                <div class="option puock-bg ta3 t-sm float-left mr-1"><i
                                            class="czs-eye-l mr-1"></i>
                                    <span id="post-views"><?php pk_get_post_views(); ?></span><span><?php _e('次阅读', PUOCK) ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if (!pk_post_comment_is_closed()): ?>
                                <a href="#comments">
                                    <div class="option puock-bg ta3 t-sm float-left mr-1"><i
                                                class="czs-comment-l mr-1"></i><?php comments_number() ?></div>
                                </a>
                            <?php endif; ?>
                            <?php if (is_user_logged_in() && current_user_can('edit_post', $post->ID)): ?>
                                <a target="_blank" href="<?php echo get_edit_post_link() ?>">
                                    <div class="option puock-bg ta3 t-sm float-left mr-1"><i
                                                class="czs-web-edit-l mr-1"></i><?php _e('编辑', PUOCK) ?></div>
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="float-right">
                            <div class="option puock-bg ta3 t-sm float-left mr-1 d-none d-lg-inline-block post-main-size">
                                <i class="czs-bevel"></i></div>
                        </div>
                    </div>
                    <div class="entry-content mt20">
                        <div class="content-main puock-text <?php pk_checked_out('post_content_indent', 'p-indent') ?>" id="post-main-content">
                            <?php the_content(); ?>
                            <?php if (pk_is_checked('post_foot_qrcode_open')): ?>
                                <div class="post-foot-qrcode">
                                    <div class="title"><?php echo pk_get_option('post_foot_qrcode_title', '无说明') ?></div>
                                    <img src="<?php echo pk_get_option('post_foot_qrcode_img', '') ?>"
                                         alt="post-qrcode">
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php
                        $link_pages = wp_link_pages(array(
                            'before' => '<li>',
                            'after' => '</li>',
                            'prev_text' => '&laquo;',
                            'next_text' => '&raquo;',
                            'format' => '<li>%1</li>',
                            'echo' => false
                        ));
                        if (!empty($link_pages)):
                            ?>
                            <div class="mt20 clearfix text-center">
                                <ul class="pagination float-right">
                                    <?php echo $link_pages ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <div class="footer-info puock-text mt20">
                            <div class="clearfix mt20 t-sm">
                                <div class="float-left">
                                    <span><?php _e('发表于：', PUOCK) ?></span><?php echo get_post_category_link_exec(true) ?>
                                </div>
                                <div class="float-right">
                                    <span class="c-sub"><?php pk_get_post_date() ?></span>
                                </div>
                            </div>
                            <div class="clearfix">
                                <div class="float-left">
                                    <?php echo get_post_tags('mt20 tags') ?>
                                </div>
                                <div class="float-right mt20">
                                    <span class="badge badge-secondary copy-post-link curp"><i
                                                class="czs-list-clipboard-l"></i><span><?php _e('复制链接', PUOCK) ?></span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php get_template_part('templates/post', 'options') ?>
                </div>
                <?php if (pk_is_checked('page_copy_right')): ?>
                    <div class="p-block clearfix">
                        <div class="float-left mr-3 d-none d-md-block">
                            <img class="md-avatar mt-1"
                                 src="<?php echo pk_get_gravatar(get_the_author_meta('email')) ?>"
                                 alt="<?php the_author_meta('display_name') ?>"
                                 title="<?php the_author_meta('display_name') ?>">
                        </div>
                        <div class="float-left puock-text t-md">
                            <?php $origin_author = get_post_meta(get_the_ID(), 'origin_author', true);
                            if (empty($origin_author)): ?>
                                <div>
                                    <span class="font-weight-bold"><?php _e('版权声明：', PUOCK) ?></span><span><?php _e('本站原创文章，由', PUOCK) ?></span>
                                    <a class="a-link"
                                       href="<?php global $authordata;
                                       if ($authordata) {
                                           echo get_author_posts_url($authordata->ID,
                                               $authordata->user_nicename);
                                       } ?>"><?php the_author() ?> </a><?php the_date('Y-m-d') ?><?php
                                    _e('发表，', PUOCK) ?><?php _e('共计', PUOCK) ?><?php echo count_words() ?><?php _e('字。', PUOCK) ?>
                                </div>
                                <div class="mt-2">
                                    <span class="font-weight-bold c-sub"><?php _e('转载说明：', PUOCK) ?></span><span
                                            class="c-sub"><?php echo pk_get_option('post_reprint_note', pk_get_option('footer_copyright')) ?></span>
                                </div>
                            <?php else: ?>
                                <div>
                                    <span class="font-weight-bold"><?php _e('版权声明：', PUOCK) ?></span><?php _e('本文于', PUOCK) ?><?php the_date('Y-m-d')
                                    ?><?php _e('转载自', PUOCK) ?><a target="_blank"
                                                                  href="<?php echo get_post_meta(get_the_ID(), 'origin_url', true) ?>"
                                                                  class="a-link" rel="nofollow"><?php
                                        echo $origin_author ?></a><?php _e('，共计', PUOCK) ?><?php echo count_words('') ?><?php _e('字。', PUOCK) ?>
                                </div>
                                <div class="mt-2">
                                    <span class="font-weight-bold c-sub"><?php _e('转载提示：', PUOCK) ?></span><span
                                            class="c-sub"><?php _e('此文章非本站原创文章，若需转载请联系原作者获得转载授权。', PUOCK) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php get_template_part('ad/page', 'innerb') ?>
                <?php if (pk_is_checked('page_b_recommend')): ?>
                    <?php get_template_part('templates/post', 'relevant') ?>
                <?php endif; ?>
                <?php get_template_part('templates/module', 'andb') ?>
                <?php comments_template() ?>
            </div>
            <?php get_sidebar() ?>
        </div>
        <?php get_template_part('ad/global', 'bottom') ?>
    </div>

    <?php get_template_part('templates/module', 'smiley') ?>
    <?php get_template_part('templates/module', 'reward') ?>
    <!-- 分享至第三方 -->
    <div class="modal fade" id="shareModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title puock-text"><?php _e('分享至', PUOCK) ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="czs-close-l t-md"></i></span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-center w-100 share-to">
                        <div data-id="wb" class="circle-button circle-sm circle-hb text-center bg-danger text-light"><i
                                    class="czs-weibo t-md"></i></div>
                        <div data-id="wx" id="wx-share" data-toggle="tooltip" data-html="true"
                             data-url="<?php echo PUOCK_ABS_URI . pk_post_qrcode(get_permalink()) ?>"
                             class="circle-button circle-sm circle-hb text-center bg-success text-light"><i
                                    class="czs-weixin t-md"></i></div>
                        <div data-id="qzone" class="circle-button circle-sm circle-hb text-center bg-yellow text-light">
                            <i class="czs-qzone t-md"></i></div>
                        <div data-id="tw" class="circle-button circle-sm circle-hb text-center bg-info text-light"><i
                                    class="czs-twitter t-md"></i></div>
                        <div data-id="fb" class="circle-button circle-sm circle-hb text-center bg-primary text-light"><i
                                    class="czs-facebook t-md"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php endwhile; ?>

<?php get_footer() ?>
