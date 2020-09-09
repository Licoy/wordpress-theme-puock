<?php get_header() ?>

<?php while(have_posts()) : the_post(); ?>
<div id="post" class="container mt20">
    <?php get_template_part('ad/global','top') ?>
    <?php echo pk_breadcrumbs() ?>
    <div class="row row-cols-1">
        <div id="post-main" class="col-lg-<?php pk_hide_sidebar_out('12','8') ?> col-md-12 <?php pk_open_box_animated('animated fadeInLeft') ?> ">
            <div class="p-block">
                <div><h2 id="post-title" class="mb-0 puock-text t-xxl"><?php the_title() ?></h2></div>
                <div class="options clearfix mt20">
                    <div class="float-left">
                        <div class="option puock-bg ta3 t-sm float-left mr-1"><i class="czs-eye-l mr-1"></i><span id="post-views"><?php pk_get_post_views() ?></span></div>
                        <a href="#comments"><div class="option puock-bg ta3 t-sm float-left mr-1"><i class="czs-comment-l mr-1"></i><?php comments_number() ?></div></a>
                    </div>
                    <div class="float-right">
                        <div class="option puock-bg ta3 t-sm float-left mr-1 d-none d-lg-inline-block post-main-size"><i class="czs-bevel"></i></div>
                    </div>
                </div>
                <div class="entry-content mt20">
                    <div class="content-main puock-text" id="post-main-content">
                        <?php get_template_part('ad/page','top') ?>
                        <?php the_content(); ?>
                        <?php 
                            if(pk_is_checked('content_bottom_open')){
                                echo pk_get_option('content_bottom');
                            }
                        ?>
                        <?php get_template_part('ad/page','bottom') ?>
                    </div>
                    <?php
                        $link_pages = wp_link_pages(array(
                            'before'=> '<li>',
                            'after'=> '</li>',
                            'prev_text'=>'&laquo;',
                            'next_text'=>'&raquo;',
                            'format'=>'<li>%1</li>',
                            'echo'=>false
                        ));
                        if(!empty($link_pages)):
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
                                <span>发表于：</span><?php echo get_post_category_link() ?>
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
                                <span class="badge badge-secondary copy-post-link curp"><i class="czs-list-clipboard-l"></i><span>复制链接</span></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php get_template_part('templates/post','options') ?>
            </div>
            <?php if(pk_is_checked('page_copy_right')): ?>
            <div class="p-block clearfix">
                <div class="float-left mr-3 d-none d-md-block">
                    <img class="md-avatar mt-1" src="<?php echo pk_get_gravatar(get_the_author_meta('email')) ?>" alt="<?php the_author_meta('display_name') ?>"
                         title="<?php the_author_meta('display_name') ?>">
                </div>
                <div class="float-left puock-text t-md">
                    <?php $origin_author = get_post_meta(get_the_ID(),'origin_author',true);if(empty($origin_author)): ?>
                    <div><span class="font-weight-bold">版权声明：</span>本站原创文章，由<a class="a-link"
                           href="<?php global $authordata;if($authordata){echo get_author_posts_url( $authordata->ID, $authordata->user_nicename );} ?>"><?php the_author() ?></a>于<?php the_date('Y年m月d日') ?>发表，共计<?php echo count_words('') ?>字。</div>
                        <div class="mt-2">
                            <span class="font-weight-bold c-sub">转载提示：</span><span class="c-sub">除特殊说明外本站文章皆由CC-4.0协议发布，转载请注明出处。</span>
                        </div>
                    <?php else: ?>
                        <div><span class="font-weight-bold">版权声明：</span>本文于<?php the_date('Y年m月d日') ?>转载自<a target="_blank" href="<?php echo get_post_meta(get_the_ID(),'origin_url',true) ?>" class="a-link" rel="nofollow"><?php echo $origin_author ?></a>，共计<?php echo count_words('') ?>字。</div>
                        <div class="mt-2">
                            <span class="font-weight-bold c-sub">转载提示：</span><span class="c-sub">此文章非本站原创文章，若需转载请联系原作者获得转载授权。</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            <?php get_template_part('templates/post','relevant') ?>
            <?php get_template_part('templates/module','andb') ?>
            <?php comments_template() ?>
        </div>
        <?php get_sidebar() ?>
    </div>
    <?php get_template_part('ad/global','bottom') ?>
</div>

<?php get_template_part('templates/module', 'smiley') ?>
<?php get_template_part('templates/module', 'reward') ?>
<!-- 分享至第三方 -->
<div class="modal fade" id="shareModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title puock-text">分享至</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="czs-close-l t-md"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-center w-100 share-to">
                    <div data-id="wb" class="circle-button circle-sm circle-hb text-center bg-danger text-light"><i class="czs-weibo t-md"></i></div>
                    <div data-id="wx" id="wx-share" data-toggle="tooltip" data-html="true"
                         class="circle-button circle-sm circle-hb text-center bg-success text-light"><i class="czs-weixin t-md"></i></div>
                    <div data-id="qzone" class="circle-button circle-sm circle-hb text-center bg-yellow text-light"><i class="czs-qzone t-md"></i></div>
                    <div data-id="tw" class="circle-button circle-sm circle-hb text-center bg-info text-light"><i class="czs-twitter t-md"></i></div>
                    <div data-id="fb" class="circle-button circle-sm circle-hb text-center bg-primary text-light"><i class="czs-facebook t-md"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php endwhile; ?>

<?php get_footer() ?>
