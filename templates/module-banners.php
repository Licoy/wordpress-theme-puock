<!--轮播图-->
<?php
if (pk_is_checked('index_carousel')):
    $index_carousel_list = pk_get_option('index_carousel_list', []);
    if (is_array($index_carousel_list) && count($index_carousel_list) > 0):
        ?>
        <div id="index-banners" data-swiper="init" data-swiper-class="index-banner-swiper"
             data-swiper-args='{"autoplay":{"delay":3000}}' class="mb15">
            <div class="swiper index-banner-swiper">
                <div class="swiper-wrapper">
                    <?php
                    foreach ($index_carousel_list as $item):
                        ?>
                        <div class="swiper-slide swiper-lazy">
                            <a data-no-instant href="<?php echo $item['link'] ?? 'javascript:void(0);' ?>"
                                <?php if (isset($item['blank']) && $item['blank']) {
                                    echo ' target="_blank"';
                                } ?>>
                                <img class="w-100" src="<?php echo $item['img']; ?>"
                                     alt="<?php echo $item['title'] ?? ''; ?>">

                                <?php if (isset($item['title']) && !empty($item['title'])): ?>
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
