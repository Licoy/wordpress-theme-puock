<div class="mt15">
    <div class="d-flex justify-content-center w-100 c-sub">
        <div class="circle-button puock-bg text-center <?php if(puock_post_is_like()): ?>bg-primary text-light<?php endif; ?>"
             id="post-like" data-id="<?php the_ID() ?>">
            <i class="czs-thumbs-up-l t-md"></i><span class="t-sm"><?php echo get_post_meta($post->ID,'puock_like',true) ?></span></div>
        <?php if(pk_is_checked('post_reward')): ?>
            <div class="circle-button puock-bg text-center" data-toggle="modal" data-target="#rewardModal"><span>赏</span></div>
        <?php endif; ?>
        <div class="circle-button puock-bg text-center" data-toggle="modal" data-target="#shareModal"><i class="czs-share t-md"></i></div>
    </div>
</div>