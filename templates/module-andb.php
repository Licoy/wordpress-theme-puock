
<div class="p-block p-lf-15">
    <div class="row text-center pd-links single-next-or-pre t-md ">
        <div class="col-6 p-border-r-1 p-0">
            <?php if(!empty(get_previous_post_link())): ?>
                <?php echo get_previous_post_link("%link","<div class='abhl puock-text'><p class='t-line-1'>%title</p><span>".__('上一篇', PUOCK) ."</span></div>") ?>
            <?php else:?>
                <a rel="prev">
                    <div class="abhl">
                        <p class="t-line-1"><?php _e('已是最早的文章', PUOCK) ?></p>
                        <span><?php _e('上一篇', PUOCK) ?></span>
                    </div>
                </a>
            <?php endif?>
        </div>
        <div class="col-6 p-0">
            <?php if(!empty(get_next_post_link())): ?>
                <?php echo get_next_post_link("%link","<div class='abhl puock-text'><p class='t-line-1'>%title</p><span>".__('下一篇', PUOCK) ."</span></div>") ?>
            <?php else:?>
                <a rel="prev">
                    <div class="abhl">
                        <p class="t-line-1"><?php _e('已是最新的文章', PUOCK) ?></p>
                        <span><?php _e('下一篇', PUOCK) ?></span>
                    </div>
                </a>
            <?php endif?>
        </div>
    </div>
</div>
