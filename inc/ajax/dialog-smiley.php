<?php

pk_ajax_register('pk_ajax_dialog_smiley', 'pk_ajax_dialog_smiley', true);
function pk_ajax_dialog_smiley()
{
    ?>
    <div id="smiley" class="animate bounce" style="max-width: 290px">
        <?php foreach (get_smiley_codes() as $key => $val): ?>
            <?php
            $imgKey = get_smiley_image($key);
            ?>
            <div class="smiley-item">
                <img data-id="<?php echo $key ?>"
                     src="<?php echo pk_get_static_url() . '/assets/img/smiley/' . $imgKey . '.png'?>"
                     class="smiley-img"
                     alt="<?php echo $key . '-' . $val ?>" title="<?php echo $val ?>"/></div>
        <?php endforeach; ?>
        <div class="mt10">
            <small class="c-sub">此表情来源于: <a href="https://twemoji.twitter.com" target="_blank"
                                            rel="nofollow">twemoji</a></small>
        </div>
    </div>
    <?php wp_die();
} ?>
