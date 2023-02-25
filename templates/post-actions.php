<div class="mt15 post-action-panel">
    <div class="post-action-content">
        <div class="d-flex justify-content-center w-100 c-sub">
            <div class="circle-button puock-bg text-center <?php if (puock_post_is_like()): ?>bg-primary text-light<?php endif; ?>"
                 id="post-like" data-id="<?php the_ID() ?>">
                <i class="fa-regular fa-thumbs-up t-md"></i>&nbsp;<span
                        class="t-sm"><?php echo get_post_meta($post->ID, 'puock_like', true) ?></span></div>
            <?php if (pk_is_checked('post_poster_open')): ?>
                <div class="circle-button puock-bg text-center pk-modal-toggle"
                     title="海报" data-no-title data-no-padding data-once-load="true"
                     data-url="<?php echo pk_ajax_url('pk_poster', ['id' => $post->ID]) ?>"
                ><i class="fa-regular fa-images"></i></div>
            <?php endif; ?>
            <?php if (pk_is_checked('post_reward')): ?>
                <div class="circle-button puock-bg text-center pk-modal-toggle" title="赞赏" data-once-load="true"
                     data-url="<?php echo pk_ajax_url('pk_ajax_dialog_reward') ?>">
                    <i class="fa fa-sack-dollar"></i></div>
            <?php endif; ?>
            <div class="circle-button puock-bg text-center pk-modal-toggle"
                 title="分享" data-once-load="true"
                 data-url="<?php echo pk_ajax_url('pk_ajax_dialog_post_share', ['id' => $post->ID]) ?>">
                <i class="fa fa-share-from-square t-md"></i></div>
            <div class="ls">
                <div class="circle-button puock-bg text-center post-menu-toggle post-menus-box"><i
                            class="fa fa-bars t-md"></i></div>
            </div>
        </div>
    </div>
</div>
