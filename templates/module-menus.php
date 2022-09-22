<?php
/**
 * 文章目录
 */
?>

<?php if(is_single()):?>
    <div id="post-menus" class="post-menus-box">
        <div id="post-menu-state" class="post-menu-toggle" title="打开或关闭文章目录">
            <i class="puock-text ta3 czs-menu-l"></i>
        </div>
        <div id="post-menu-content" class="animated slideInRight mini-scroll">
            <div id="post-menu-head">
            </div>
            <div id="post-menu-content-items"></div>
        </div>
    </div>
<?php endif; ?>
