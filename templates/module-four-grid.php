<!--CMS首页四宫格-->
<?php
global $cms_four_grid_ava;
$cms_four_grid_items = pk_is_checked('cms_show_four_grid')
    ? (is_array($cms_four_grid_ava) ? $cms_four_grid_ava : pk_ava_cms_four_grid_items())
    : false;
if (is_array($cms_four_grid_items) && count($cms_four_grid_items) > 0):
    ?>
    <div id="cms-four-grid" class="row g-2 g-md-3 mb15">
        <?php foreach ($cms_four_grid_items as $item):
            $title = $item['title'] ?? '';
            $img = $item['img'] ?? '';
            $link = trim($item['link'] ?? '');
            $open_blank = filter_var($item['blank'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $content = '<img alt="' . esc_attr($title) . '" ' . pk_get_lazy_img_info($img, 'cms-four-grid-img', 320, 128) . ' />';
            ?>
            <div class="col-6 col-md-3">
                <?php if (!empty($link)): ?>
                    <a data-no-instant class="cms-four-grid-item cms-four-grid-link"
                       href="<?php echo esc_url($link); ?>"
                        <?php echo $open_blank ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
                        <?php echo $content; ?>
                    </a>
                <?php else: ?>
                    <div class="cms-four-grid-item">
                        <?php echo $content; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
