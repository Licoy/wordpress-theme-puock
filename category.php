<?php get_header() ?>

    <div id="content">

        <div class="cat-top-info">
            <div class="container">
                <?php echo pk_breadcrumbs() ?>
                <?php $cats = get_category_child($cat);if($cats && count($cats)>0): ?>
                    <div class="mt10 row pl-3 child-cat">
                        <?php foreach ($cats as $catItem): ?>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 pl-0 text-center">
                                <a href="<?php echo $catItem['url'] ?>" class="a-link t-md ta3 p-block pt-2 pb-2 d-inline-block w-100 abhl ww">
                                    <span><?php echo $catItem['item']->name ?></span>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="container mt15">
            <?php get_template_part('templates/box', 'global-top') ?>
            <div class="row row-cols-1">
                <div class="col-lg-<?php pk_hide_sidebar_out('12','8') ?> col-md-12 <?php pk_open_box_animated('animated fadeInLeft') ?> ">

                    <?php get_template_part('templates/module','posts') ?>

                </div>
                <?php get_sidebar() ?>
            </div>
            <?php get_template_part('templates/box', 'global-bottom') ?>
        </div>


    </div>



<?php get_footer() ?>
