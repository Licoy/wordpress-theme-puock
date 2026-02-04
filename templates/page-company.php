<div id="content" class="puock-text">
    <?php get_template_part('templates/module', 'banners') ?>
    <div id="index-company" class="puock-bg pt20">

        <div class="container mt50" id="prod-items">
            <h3 class="text-center"><?php echo pk_get_option('company_product_title', __('产品概览', PUOCK)) ?></h3>
            <div class="row">
                <?php $products = pk_get_option('company_products', []);
                foreach ($products as $product): ?>
                    <div class="col-lg-4 prod-item">
                        <div class="p-block bgimg" style='background-image: url("<?php echo $product['img'] ?>"'>
                            <a target="_blank" href="<?php echo $product['link'] ?>">
                                <h3 class="go"><?php echo $product['title'] ?></h3>
                            </a>
                            <p class="desc"><?php echo $product['desc'] ?></p>
                        </div>
                    </div>
                <?php endforeach;
                unset($products); ?>
            </div>
        </div>

        <div class="container mt50" id="do-items">
            <h3 class="text-center"><?php echo pk_get_option('company_do_title') ?></h3>
            <div class="row mt50">
                <div class="col-lg-5">
                    <div class="w-100 h-100 bgimg"
                         style='background-image: url("<?php echo pk_get_option('company_do_img') ?>");'></div>
                </div>
                <div class="col-lg-7">
                    <?php $dos = pk_get_option('company_dos', []);
                    foreach ($dos as $do): ?>
                        <div class="do-item">
                            <div class="icon">
                                <i class="<?php echo $do['icon'] ?>"></i>
                            </div>
                            <div class="info">
                                <h3 class="title"><?php echo $do['title'] ?></h3>
                                <p class="desc"><?php echo $do['desc'] ?></p>
                            </div>
                        </div>
                    <?php endforeach;
                    unset($dos); ?>
                </div>
            </div>
        </div>

        <?php if (pk_is_checked('company_news_open')): ?>
            <!--   新闻     -->
            <div class="mt50 container t-md">
                <?php get_template_part('templates/post', 'news') ?>
            </div>
        <?php endif; ?>
        <?php if (pk_is_checked('company_show_2box')): ?>
            <!--   两栏分类     -->
            <div class="mt50 container t-md">
                <?php get_template_part('templates/module', 'cms') ?>
            </div>
        <?php endif; ?>
    </div>


</div>
