<?php if (pk_is_checked('use_post_menu')) get_template_part('templates/module', 'menus') ?>
<!--返回顶部和底部-->
<div id="rb-float-actions">
    <?php echo apply_filters('pk_rb_float_actions','') ?>
    <?php if (pk_is_checked('mobile_sidebar_enable')): ?>
    <div id="mobile-sidebar-toggle" class="p-block d-md-none"><i class="fa-solid fa-bars-progress puock-text"></i></div>
    <?php endif; ?>
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
            <?php echo apply_filters('pk_footer_info','') ?>
        </div>
    </div>
    </div>
</footer>
</div>
<div id="gt-validate-box"></div>
<?php get_template_part('inc/metas') ?>
<?php wp_footer(); ?>
<script>
// Bypass PJAX for WordPress core auth pages to avoid redirect issues
if (window.Puock && typeof window.Puock.goUrl === 'function') {
    (function(P){
        var origGo = P.goUrl;
        P.goUrl = function(url){
            var bypass = url.indexOf('/wp-login.php') !== -1 || url.indexOf('/wp-admin') !== -1;
            if (P.data && P.data.params && P.data.params.is_pjax && !bypass) {
                origGo.call(P, url);
            } else {
                window.location.href = url;
            }
        };
    })(window.Puock);
}
</script>
<?php if (!empty(pk_get_option('tj_code_footer', ''))): ?>
    <?php echo pk_get_option('tj_code_footer', ''); ?>
<?php endif; ?>
<?php pk_debug_print_sql_list(); ?>

<!-- 移动端侧边栏 -->
<div id="mobile-sidebar" class="mobile-sidebar">
    <div class="mobile-sidebar-content">
        <div class="mobile-sidebar-header">
            <h3 class="mobile-sidebar-title"><?php echo get_bloginfo('name'); ?></h3>
            <div id="mobile-sidebar-close" class="mobile-sidebar-close"><i class="fa-solid fa-circle-xmark puock-text"></i></div>
        </div>
        <div class="mobile-sidebar-body">
            <?php
            if (is_home()):
                pk_sidebar_check_has('sidebar_home');
            elseif (is_single()):
                pk_sidebar_check_has('sidebar_single');
            elseif (is_search()):
                pk_sidebar_check_has('sidebar_search');
            elseif (is_category() || is_tag()):
                pk_sidebar_check_has('sidebar_cat');
            elseif (is_page()):
                pk_sidebar_check_has('sidebar_page');
            else:
                pk_sidebar_check_has('sidebar_other');
            endif;
            ?>
        </div>
    </div>
    <div id="mobile-sidebar-overlay" class="mobile-sidebar-overlay"></div>
</div>

</body>
</html>
