<!--轮播图-->
<?php

use Puock\Theme\setting\options\OptionCarousel;

if (pk_is_checked('index_carousel')):
    $index_carousel_list = pk_get_option('index_carousel_list', []);
    if (is_array($index_carousel_list) && count($index_carousel_list) > 0):
        ?>
        <div id="index-banners" data-swiper="init" data-swiper-class="index-banner-swiper"
             data-swiper-args='<?php echo OptionCarousel::getCarouselIndexArgs() ?>'
             class="mb15">
            <div class="swiper index-banner-swiper">
                <div class="swiper-wrapper">
                    <?php
                    $index_carousel_hide_title = pk_is_checked('index_carousel_hide_title');
                    foreach ($index_carousel_list as $item):
                        if (($item['hide'] ?? false) || empty($item['img'])) continue
                        ?>
                        <div class="swiper-slide swiper-lazy">
                            <a data-no-instant href="<?php echo $item['link'] ?? 'javascript:void(0);' ?>"
                                <?php if (isset($item['blank']) && $item['blank']) {
                                    echo ' target="_blank"';
                                } ?>>
                                <img class="w-100" src="<?php echo $item['img']; ?>"
                                     alt="<?php echo $item['title'] ?? ''; ?>">

                                <?php if (!$index_carousel_hide_title && isset($item['title']) && !empty($item['title'])): ?>
                                    <div class="swiper-title">
                                        <div class="swiper-title-text"><?php echo $item['title'] ?></div>
                                    </div>
                                <?php endif; ?>
                            </a>
                        </div>
                    <?php
                    endforeach;
                    ?>
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    <?php endif;endif; ?>
