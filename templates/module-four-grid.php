<!--CMS首页四宫格-->
<?php
global $cms_four_grid_ava;
$cms_four_grid_items = pk_is_checked('cms_show_four_grid')
    ? (is_array($cms_four_grid_ava) ? $cms_four_grid_ava : pk_ava_cms_four_grid_items())
    : false;
$cms_four_grid_count = is_array($cms_four_grid_items) ? count($cms_four_grid_items) : 0;
if ($cms_four_grid_count > 0):
    $cms_four_grid_columns = max(1, min(4, $cms_four_grid_count));
    $cms_four_grid_mobile_columns = max(1, min(2, $cms_four_grid_count));
    ?>
    <div id="cms-four-grid" class="mb15"
         style="<?php echo esc_attr(sprintf('--cms-four-grid-columns:%d;--cms-four-grid-mobile-columns:%d;', $cms_four_grid_columns, $cms_four_grid_mobile_columns)); ?>">
        <?php foreach ($cms_four_grid_items as $item):
            $title = $item['title'] ?? '';
            $img = $item['img'] ?? '';
            $link = trim($item['link'] ?? '');
            $open_blank = filter_var($item['blank'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $content = '<img alt="' . esc_attr($title) . '" ' . pk_get_lazy_img_info($img, 'cms-four-grid-img', 320, 128) . ' />';
            ?>
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
        <?php endforeach; ?>
    </div>
<?php endif; ?>
