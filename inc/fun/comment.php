<?php
function pk_comment_callback($comment, $args, $depth)
{
    global $authordata;
    $GLOBALS['comment'] = $comment;
    $author_cat_comment = get_post_meta($comment->comment_post_ID, 'author_cat_comment', true) == 'true';
    $is_author = $authordata->ID == get_current_user_id();
    if ($comment->comment_parent == 0) {
        $pccci_key = 'pk_comment_callback_cur_id';
        $pccci = @$GLOBALS[$pccci_key];
        if (!empty($pccci)) {
            echo '</div>';
        }
        $GLOBALS[$pccci_key] = $comment->comment_ID;
    }
    ?>
<div id="comment-<?php comment_ID() ?>" class="post-comment">
    <div class="info clearfix">
        <?php if (pk_open_show_comment_avatar()): ?>
            <div class="float-left">
                <?php if (pk_is_checked('basic_img_lazy_a')): ?>
                    <img src="<?php echo pk_get_lazy_pl_img() ?>"
                         data-src="<?php echo get_avatar_url($comment->comment_author_email, 64); ?>"
                         class="avatar avatar-64 photo md-avatar lazyload" width="60" height="60">
                <?php else: ?>
                    <?php echo get_avatar($comment, 64, '', '', array('class' => 'md-avatar')) ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="float-left <?php if (pk_open_show_comment_avatar()) {
            echo 'ml-3';
        } ?> two-info">
            <div class="puock-text ta3b">
                <span class="t-md puock-links"><?php pk_comment_author_url() ?></span>
                <?php if (pk_is_checked('comment_level')) {
                    pk_the_author_class();
                } ?>
            </div>
            <div class="t-sm c-sub">
                <span><?php comment_date('Y-m-d H:i:s') ?></span>
                <?php if ($comment->comment_approved == '1' && (!$author_cat_comment || $is_author)) : ?>
                    <a id="comment-reply-<?php comment_ID() ?>" data-id="<?php comment_ID() ?>"
                       class="hide-info animated bounceIn c-sub-a t-sm ml-1 comment-reply"
                       href="javascript:void(0)" title="回复此评论"><i class="czs-share"></i>
                        <span class="comment-reply-text">回复</span></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="content-text t-sm mt10 puock-text" <?php if (!pk_open_show_comment_avatar()) {
            echo 'style="margin-left:0"';
        } ?>>
            <?php if (!$author_cat_comment || $is_author): comment_text();; else: ?>
                <?php echo "<span><i class='czs-lock-l'></i>&nbsp;此评论仅对作者可见</span>";endif; ?>
            <?php if ($comment->comment_approved == '0') : ?>
                <p class="c-sub mt-1"><i class="czs-warning-l mr-1"></i>您的评论正在等待审核！</p>
            <?php endif; ?>

            <div class="comment-os c-sub">
                <?php
                $commentUserAgent = parse_user_agent($comment->comment_agent);
                $commentOsLink = pk_get_comment_os_images($commentUserAgent['platform']);
                $commentBrowserLink = pk_get_comment_os_images($commentUserAgent['browser']);
                echo "<img class='dont-view' alt='${commentUserAgent['platform']}' title='${commentUserAgent['platform']}' src='$commentOsLink'/><span>${commentUserAgent['platform']}</span>";
                echo "<img class='dont-view' alt='${commentUserAgent['browser']}' title='${commentUserAgent['browser']}' src='$commentBrowserLink'/><span>${commentUserAgent['browser']}</span>";
                echo "<span>${commentUserAgent['version']}</span>"
                ?>
            </div>
        </div>
        <div class="comment-box-reply d-none" id="comment-box-<?php comment_ID() ?>"></div>
    </div>
    <?php
}