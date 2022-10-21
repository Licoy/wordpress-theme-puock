<!--轮播图-->
<?php
if (pk_is_checked('index_carousel')):
    $index_carousel_list = pk_get_option('index_carousel_list', []);
    if (is_array($index_carousel_list) && count($index_carousel_list) > 0):
        ?>
        <div id="banners" class="carousel slide mb15" data-ride="carousel">
            <ol class="carousel-indicators">
                <?php for ($i = 0; $i < count($index_carousel_list); $i++): ?>
                    <li data-target="#banners" data-slide-to="<?php echo $i ?>"
                        class="<?php echo $i == 0 ? 'active' : '' ?>"></li>
                <?php endfor; ?>
            </ol>
            <div class="carousel-inner">
                <?php $i = 0;
                foreach ($index_carousel_list as $index_carousel_item): ?>
                    <a <?php if ($index_carousel_item['blank'] ?? false) {
                        echo "target=\"_blank\"";
                    } ?> href="<?php echo $index_carousel_item['link'] ?? '/' ?>"
                         class="carousel-item <?php echo $i == 0 ? 'active' : '' ?>">
                        <img class="w-100" src="<?php echo $index_carousel_item['img'] ?>"
                             title="<?php echo $index_carousel_item['title'] ?>"
                             alt="<?php echo $index_carousel_item['title'] ?>">
                        <div class="carousel-caption d-none d-md-block">
                            <p class="mb-0"><?php echo $index_carousel_item['title'] ?></p>
                        </div>
                    </a>
                    <?php $i++;endforeach;
                unset($i); ?>
            </div>
            <a class="carousel-control-prev" href="#banners" role="button" data-slide="prev">
                <i class="fa fa-angle-left"></i>
            </a>
            <a class="carousel-control-next" href="#banners" role="button" data-slide="next">
                <i class="fa fa-angle-right"></i>
            </a>
        </div>
    <?php endif;endif; ?>
