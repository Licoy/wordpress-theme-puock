<?php

if (!function_exists('pk_global_notice_html')){
    function pk_global_notice_html()
    {
        if (pk_is_checked('global_notice')) {
            $global_notice_list = pk_get_option('global_notice_list');
            if (is_array($global_notice_list) && count($global_notice_list) > 0) {
                $final_list = [];
                foreach ($global_notice_list as $item) {
                    if ($global_notice_list['hide'] ?? false) continue;
                    $final_list[] = $item;
                }
                if (count($final_list) > 0) { ?>
                    <div class="puock-text p-block t-md global-top-notice">
                        <div data-swiper="init" data-swiper-class="global-top-notice-swiper"
                             data-swiper-args='<?php echo json_encode([
                                 'direction' => 'vertical',
                                 'autoplay' => ['delay' => pk_get_option('global_notice_autoplay_speed', 3000), 'disableOnInteraction' => false],
                                 'loop' => true
                             ]) ?>'>
                            <div class="swiper global-top-notice-swiper">
                                <div class="swiper-wrapper">
                                    <?php
                                    foreach ($final_list as $item) { ?>
                                        <div class="swiper-slide t-line-1">
                                            <a class="ta3" data-no-instant
                                               href="<?php echo empty($item['link']) ? 'javascript:void(0)' : $item['link'] ?>">
                                            <span class="notice-icon"><i
                                                        class="<?php echo empty($item['icon']) ? 'fa-regular fa-bell' : $item['icon'] ?>"></i></span>
                                                <span><?php echo $item['title'] ?></span>
                                            </a>
                                        </div>
                                    <?php } ?>

                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
        }
    }
}


get_template_part('ad/global', 'top');

pk_global_notice_html();

?>
