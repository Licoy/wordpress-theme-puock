<!-- 消息提示框 -->
<div class="modal fade" id="infoToast" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title puock-text" id="infoToastTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa fa-close t-md"></i></span>
                </button>
            </div>
            <div class="modal-body puock-text t-md" id="infoToastText">
            </div>
        </div>
    </div>
</div>
<?php if (pk_is_checked('use_post_menu')) get_template_part('templates/module', 'menus') ?>
<!--返回顶部和底部-->
<div id="return-top-bottom">
    <div data-to="top" class="p-block"><i class="fa fa-arrow-up puock-text"></i></div>
    <div data-to="bottom" class="p-block"><i class="fa fa-arrow-down puock-text"></i></div>
</div>
<footer id="footer">
    <div class="container">
        <div class="row row-cols-md-1">
            <?php if (pk_is_checked('footer_about_me_open')): ?>
                <div class="col-md-6">
                    <div><span class="t-md pb-2 d-inline-block border-bottom border-primary"><i
                                    class="fa-regular fa-bell"></i> <?php echo pk_get_option('footer_about_me_title', '') ?></span>
                    </div>
                    <p class="mt20 t-md"><?php echo pk_get_option('footer_about_me', '') ?></p>
                </div>
            <?php endif; ?>
            <?php if (pk_is_checked('footer_copyright_open')): ?>
                <div class="col-md-6">
                    <div><span class="t-md pb-2 d-inline-block border-bottom border-primary"><i
                                    class="fa-regular fa-copyright"></i> <?php echo pk_get_option('footer_copyright_title', '') ?></span>
                    </div>
                    <p class="mt20 t-md"><?php echo pk_get_option('footer_copyright', '') ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="mt20 text-center t-md">
        <div class="info">
            <?php echo pk_get_option('footer_info') ?>
            <?php /* 请遵守开源协议，保留主题底部（此处）的署名，以支持本主题更好的发展，谢谢合作 */ ?>
            <div class="fs12 mt10 c-sub">
                <span>
                    <i class="fa-brands fa-wordpress"></i>&nbsp;Theme by <a target="_blank" class="c-sub" title="Puock v<?php echo PUOCK_CUR_VER_STR; ?>"
                                                                            href="https://github.com/Licoy/wordpress-theme-puock">Puock</a>
                </span>
            </div>
        </div>
    </div>
    </div>
</footer>
</div>
<?php get_template_part('inc/metas') ?>
<?php wp_footer(); ?>
<?php if (!empty(pk_get_option('tj_code_footer', ''))): ?>
    <?php echo pk_get_option('tj_code_footer', ''); ?>
<?php endif; ?>
<?php pk_debug_print_sql_list(); ?>
</body>
</html>
