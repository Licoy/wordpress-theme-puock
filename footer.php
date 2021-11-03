<!-- 消息提示框 -->
<div class="modal fade" id="infoToast" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title puock-text" id="infoToastTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="czs-close-l t-md"></i></span>
                </button>
            </div>
            <div class="modal-body puock-text t-md" id="infoToastText">
            </div>
        </div>
    </div>
</div>
<?php if(pk_is_checked('use_post_menu')) get_template_part('templates/module', 'menus') ?>
<!--返回顶部和底部-->
<div id="return-top-bottom">
    <div data-to="top" class="p-block"><i class="czs-arrow-up-l puock-text"></i></div>
    <div data-to="bottom" class="p-block"><i class="czs-arrow-down-l puock-text"></i></div>
</div>
<footer id="footer">
    <div class="container">
        <div class="row row-cols-md-1">
            <div class="col-md-6">
                <div><span class="t-md pb-2 d-inline-block border-bottom border-primary"><i class="czs-about-l"></i> <?php _e('关于我们', PUOCK) ?></span></div>
                <p class="mt20 t-md"><?php echo pk_get_option('footer_about_me','') ?></p>
            </div>
            <div class="col-md-6">
                <div><span class="t-md pb-2 d-inline-block border-bottom border-primary"><i class="czs-link-l"></i> <?php _e('友情链接', PUOCK) ?></span></div>
                <div class="more-link mt20 t-md">
                    <?php
                    $link_cid = pk_get_option('index_link_id','');
                    if(!empty($link_cid)){
                        preg_match_all('/<a .*?>.*?<\/a>/', wp_list_bookmarks(array(
                            'category'=>$link_cid,
                            'category_before'=>'',
                            'title_li'=>'',
                            'echo'=>0
                        )), $links);
                        foreach ($links[0] as $link){
                            echo $link;
                        }
                    }
                    $link_page_id = pk_get_option('link_page','');
                    if(!empty($link_page_id)){
                        echo '<a target="_blank" href="'.get_page_link($link_page_id).'">'.__('更多链接', PUOCK).'</a>';
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="mt20 text-center t-md">
            <div class="info">
                <?php echo pk_get_option('footer_info') ?>
                <p>Theme by <a target="_blank" href="https://github.com/Licoy/wordpress-theme-puock">Puock</a></p>
            </div>
        </div>
    </div>
</footer>
</div>
<script data-no-instant src="<?php echo pk_get_static_url(); ?>/assets/js/libs.min.js?ver=<?php echo PUOCK_CUR_VER ?>"></script>
<?php if(is_single()): ?>
<script data-instant src="<?php echo pk_get_static_url(); ?>/assets/js/libs/qrcode.min.js?ver=<?php echo PUOCK_CUR_VER ?>"></script>
<?php endif; ?>
<?php if(!empty(pk_get_option('tj_code_footer',''))): ?>
<?php echo pk_get_option('tj_code_footer',''); ?>
<?php endif; ?>
<!--<script data-instant src="--><?php //echo pk_get_static_url(); ?><!--/assets/js/pages.js?ver=--><?php //echo PUOCK_CUR_VER ?><!--"></script>-->
<!--<script data-no-instant src="--><?php //echo pk_get_static_url(); ?><!--/assets/js/pages-once.js?ver=--><?php //echo PUOCK_CUR_VER ?><!--"></script>-->
<!--<script data-no-instant src="--><?php //echo pk_get_static_url(); ?><!--/assets/js/inc.js?ver=--><?php //echo PUOCK_CUR_VER ?><!--"></script>-->
<script data-no-instant src="<?php echo pk_get_static_url(); ?>/assets/dist/puock.min.js?ver=<?php echo time() ?>"></script>
<?php wp_footer();  ?>
<?php get_template_part('templates/async','views') ?>
</body>
</html>