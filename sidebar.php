<?php if (!pk_hide_sidebar()): ?>
    <div id="sidebar" class="<?php pk_open_box_animated('animated fadeInRight') ?> col-lg-4 d-none d-lg-block">
        <div class="sidebar-main">
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
<?php endif; ?>
