<?php if(comments_open()): ?>
<div class="modal fade" id="twemoji" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title puock-text">选择表情</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="czs-close-l t-md"></i></span>
                </button>
            </div>
            <div class="modal-body puock-text t-md">
                <div id="smiley" class="animate bounce">
                    <?php foreach (get_smiley_codes() as $key=>$val): ?>
                        <?php
                        $imgKey = get_smiley_image($key);
                        ?>
                        <div class="smiley-item">
                            <img data-id="<?php echo $key ?>"
                                <?php echo pk_get_lazy_img_info(pk_get_static_url().'/assets/img/smiley/'.$imgKey.'.png','smiley-img',null,null,false) ?>
                                 alt="<?php echo $key.'-'.$val ?>" title="<?php echo $val ?>" /></div>
                    <?php endforeach; ?>
                    <div class="mt10">
                        <small class="c-sub">此表情来源于<a href="https://twemoji.twitter.com" target="_blank" rel="nofollow">twemoji</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>