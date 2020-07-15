<?php if(pk_hide_sidebar()==false): ?>
<div id="sidebar" class="<?php pk_open_box_animated('animated fadeInRight') ?> col-lg-4 d-none d-lg-block">
    <div class="sidebar-main">
    <?php

    function sidebar_check_has($name){
        if(!dynamic_sidebar($name)){
            dynamic_sidebar('sidebar_not');
        }
    }
    if(is_home()):
        sidebar_check_has('sidebar_home');
    elseif(is_single()):
        sidebar_check_has('sidebar_single');
    elseif(is_search()):
        sidebar_check_has('sidebar_search');
    elseif(is_category() || is_tag()):
        sidebar_check_has('sidebar_cat');
    elseif(is_page()):
        sidebar_check_has('sidebar_page');
    else:
        sidebar_check_has('sidebar_other');
    endif;

    ?>
    </div>
</div>
<?php endif; ?>