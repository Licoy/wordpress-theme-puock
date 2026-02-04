<div id="content" class="puock-text">
    <?php get_template_part('templates/module','banners') ?>
    <div id="index-company" class="puock-bg pt20">
        <div class="container text-center">
            <h4><?php echo pk_get_option('company_about_t') ?></h4>
            <p class="tin2 mt30 t-md"><?php echo pk_get_option('company_about_c') ?></p>
            <?php if(!empty(pk_get_option('company_about_a',''))): ?><a
                href="<?php echo pk_get_option('company_about_a') ?>" class="btn btn-outline-secondary"><?php _e('查看更多', PUOCK) ?></a><?php endif; ?>
        </div>
        <div class="mt50 container t-md">
            <h4 class="text-center"><?php echo pk_get_option('company_product_title') ?></h4>
            <div class="card-deck mb-3 text-center mt30">
                <?php for ($i=1;$i<=4;$i++): if(pk_is_checked('company_product_open_'.$i)): ?>
                <div class="card mb-4 shadow-sm puock-bg">
                    <div class="card-header">
                        <h4 class="my-0 font-weight-normal"><?php echo pk_get_option('company_product_t_'.$i) ?></h4>
                    </div>
                    <div class="card-body">
                        <div>
                            <?php echo pk_get_option('company_product_c_'.$i) ?>
                        </div>
                        <?php if(!empty(pk_get_option('company_product_a_'.$i,''))): ?><a
                            href="<?php echo pk_get_option('company_product_a_'.$i) ?>" class="btn btn-ssm btn-outline-secondary"><?php _e('立即查看', PUOCK) ?></a><?php endif; ?>
                    </div>
                </div>
                <?php endif;endfor; ?>
            </div>
        </div>
        <div class="mt50 container t-md text-center">
            <h4><?php echo pk_get_option('company_soul_title') ?></h4>
            <div class="row mt30">
                <?php for ($i=1;$i<=3;$i++): if(pk_is_checked('company_soul_open_'.$i)): ?>
                <div class="col-lg-4">
                    <div>
                        <img src="<?php echo pk_get_option('company_soul_i_'.$i) ?>" class="xs-avatar">
                    </div>
                    <h4 class="mt10"><?php echo pk_get_option('company_soul_t_'.$i) ?></h4>
                    <p><?php echo pk_get_option('company_soul_c_'.$i) ?></p>
                    <p><?php if(!empty(pk_get_option('company_soul_a_'.$i,''))): ?><a
                            href="<?php echo pk_get_option('company_soul_a_'.$i) ?>"
                            class="btn btn-ssm btn-outline-secondary"><?php _e('立即查看', PUOCK) ?></a><?php endif; ?></p>
                </div>
                <?php endif;endfor; ?>
            </div>
        </div>
        <?php foreach (array('l','r') as $i): if(pk_is_checked('company_pr_open_'.$i)): ?>
        <div class="mt50 container t-md">
            <div class="row featurette">
                <div class="col-md-8 <?php if($i=='r'){echo 'order-md-2';} ?>">
                    <h2 class="featurette-heading"><?php echo pk_get_option('company_pr_t_'.$i) ?></h2>
                    <p class="lead"><?php echo pk_get_option('company_pr_c_'.$i) ?></p>
                </div>
                <div class="col-md-4 <?php if($i=='r'){echo 'order-md-1';} ?>">
                    <img src="<?php echo pk_get_option('company_pr_i_'.$i) ?>" width="400px" height="400px">
                </div>
            </div>
        </div>
        <?php endif;endforeach; ?>
        <?php if(pk_is_checked('company_news_open')): ?>
        <!--   新闻     -->
        <div class="mt50 container t-md">
            <?php get_template_part('templates/post','news') ?>
        </div>
        <?php endif; ?>
        <?php if(pk_is_checked('company_show_2box')): ?>
        <!--   两栏分类     -->
        <div class="mt50 container t-md">
            <?php get_template_part('templates/module','cms') ?>
        </div>
        <?php endif; ?>
    </div>

</div>