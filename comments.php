<?php if (get_comments_number() == 0 && !comments_open() || pk_post_comment_is_closed()):echo ''; else: ?>
    <?php get_template_part('ad/comment', 'top') ?>
    <div class="p-block" id="comments">
        <div>
            <span class="t-lg border-bottom border-primary puock-text pb-2"><i
                        class="czs-write-l mr-1"></i><?php _e('评论', PUOCK) ?>（<?php comments_number() ?>）</span>
        </div>
        <?php if (comments_open()): ?>
            <?php if (get_option('comment_registration', '0') == '1' && !is_user_logged_in()): //登录后才可以评论 ?>
                <div class="mt20 clearfix" id="comment-form-box">
                    <form class="mt10" id="comment-form" method="post">
                        <div class="form-group">
                            <textarea placeholder="<?php _e('您必须要登录之后才可以进行评论', PUOCK) ?>" disabled id="comment"
                                      name="comment" class="form-control form-control-sm t-sm" rows="1"></textarea>
                        </div>
                    </form>
                    <?php if (pk_is_checked('oauth_qq')): ?>
                        <div>
                            <a data-no-instant href="<?php echo pk_oauth_url_page_ajax('qq', get_the_permalink()) ?>"
                               class="btn btn-danger btn-ssm ahfff"><i
                                        class="czs-qq"></i>&nbsp;<?php _e('QQ登录', PUOCK) ?></a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="mt20 clearfix" id="comment-form-box">
                    <form class="mt10" id="comment-form" method="post"
                          action="<?php echo admin_url() . 'admin-ajax.php?action=comment_ajax' ?>">
                        <div class="form-group">
                            <textarea placeholder="<?php _e('世界这么大发表一下你的看法~', PUOCK) ?>" id="comment" name="comment"
                                      class="form-control form-control-sm t-sm" rows="4"></textarea>
                        </div>
                        <div class="row row-cols-1 comment-info">
                            <?php $commentInfoCol = pk_is_checked('vd_comment') ? 3 : 4; ?>
                            <?php if (!is_user_logged_in()): ?>
                                <input type="text" value="0" hidden name="comment-logged" id="comment-logged">
                                <div class="col-12 col-sm-<?php echo $commentInfoCol ?>"><input type="text" id="author"
                                                                                                name="author"
                                                                                                class="form-control form-control-sm t-sm"
                                                                                                placeholder="<?php _e('昵称（必填）', PUOCK) ?>">
                                </div>
                                <div class="col-12 col-sm-<?php echo $commentInfoCol ?>"><input type="email" id="email"
                                                                                                name="email"
                                                                                                class="form-control form-control-sm t-sm"
                                                                                                placeholder="<?php _e('邮箱（必填）', PUOCK) ?>">
                                </div>
                                <div class="col-12 col-sm-<?php echo $commentInfoCol ?>"><input type="text" id="url"
                                                                                                name="url"
                                                                                                class="form-control form-control-sm t-sm"
                                                                                                placeholder="<?php _e('网站', PUOCK) ?>">
                                </div>

                            <?php endif; ?>
                            <?php if (pk_is_checked('vd_comment')): ?>
                                <div class="col-12 col-sm-3">
                                    <div class="row flex-row justify-content-end">
                                        <div class="col-8 col-sm-7 text-right pl15">
                                            <input type="text" value="" placeholder="验证码" maxlength="4"
                                                   class="form-control form-control-sm t-sm" name="comment-vd"
                                                   autocomplete="off"
                                                   id="comment-vd">
                                        </div>
                                        <div class="col-4 col-sm-5 pr15">
                                            <img class="comment-captcha lazyload"
                                                 data-path="<?php echo get_admin_url() . 'admin-ajax.php?action=puock_comment_captcha&w=200&h=70' ?>"
                                                 data-src=""
                                                 src="<?php echo pk_get_lazy_pl_img() ?>"
                                                 alt="验证码">
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <input type="text" hidden name="comment_post_ID" value="<?php echo $post->ID ?>">
                        <input type="text" hidden id="comment_parent" name="comment_parent" value="">
                        <div class="p-flex-sbc mt10">
                            <div>
                                <?php if (is_user_logged_in()): $user = wp_get_current_user(); ?>
                                    <div class="puock-text t-sm">
                                        <input type="text" value="1" hidden name="comment-logged" id="comment-logged">
                                        <span><strong><?php echo $user->data->display_name ?></strong>，<a
                                                    data-no-instant class="ta3 a-link"
                                                    href="<?php echo wp_logout_url(get_the_permalink()) ?>"><?php _e('登出', PUOCK) ?></a></span>
                                    </div>
                                <?php endif; ?>
                                <?php if (pk_is_checked('oauth_qq') && !is_user_logged_in()): ?>
                                    <div class="d-inline-block">
                                        <a data-no-instant
                                           href="<?php echo pk_oauth_url_page_ajax('qq', get_the_permalink()) ?>"
                                           class="btn btn-danger btn-ssm ahfff"><i
                                                    class="czs-qq"></i>&nbsp;<?php _e('QQ登录', PUOCK) ?></a>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div>
                                <button id="comment-cancel" type="button"
                                        class="btn btn-outline-dark d-none btn-ssm"><?php _e('取消', PUOCK) ?></button>
                                <button id="comment-smiley" class="btn btn-outline-secondary btn-ssm" type="button"><i
                                            class="czs-face-l t-md"></i></button>
                                <button id="comment-submit" type="submit" class="btn btn-primary btn-ssm"><i
                                            class="czs-paper-plane-l"></i>&nbsp;<?php _e('发布评论', PUOCK) ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <?php if (pk_is_checked('comment_ajax')): ?>
            <div id="comment-ajax-load" class="text-center mt20 d-none">
                <div class="spinner-grow text-primary" role="status">
                    <span class="sr-only"><?php _e('载入中...', PUOCK) ?></span>
                </div>
            </div>
        <?php endif; ?>
        <div id="post-comments">
            <?php
            if (get_comments_number() > 0):
                wp_list_comments(array(
                    'type' => 'comment',
                    'callback' => 'pk_comment_callback',
                ));
                echo '</div>';
            endif;
            ?>

            <div class="mt20 p-flex-s-right" <?php echo pk_is_checked('comment_ajax') ? 'data-no-instant' : '' ?>>
                <ul class="pagination comment-ajax-load">
                    <?php
                    paginate_comments_links(array(
                        'prev_text' => '&laquo;',
                        'next_text' => '&raquo;',
                        'format' => '<li>%1</li>'
                    ));
                    ?>
                </ul>
            </div>

        </div>
    </div>
<?php endif; ?>
