<div class="p-block index-links">
    <div>
        <span class="t-lg puock-text pb-2 d-inline-block border-bottom border-primary">
            <i class="czs-link-l"></i>&nbsp;友情链接
        </span>
    </div>
    <div class="mt20 t-md">
        <?php
        $link_cid = pk_get_option('index_link_id', '');
        if (!empty($link_cid)) {
            $links = get_bookmarks(array(
                'category' => $link_cid,
                'category_before' => '',
                'title_li' => '',
                'echo' => 0,
                'class' => 'aa'
            ));
            foreach ($links as $link) {
                if ($link->link_visible != 'Y') {
                    continue;
                }
                echo "<a href='$link->link_url' title='$link->link_name'
                    class='badge bg-secondary ahfff'
                    rel='$link->link_rel' target='$link->link_target'>$link->link_name</a>";
            }
        }
        $link_page_id = pk_get_option('link_page', '');
        if (!empty($link_page_id)) {
            echo '<a target="_blank" href="' . get_page_link($link_page_id) . '">' . __('更多链接', PUOCK) . '</a>';
        }
        ?>
    </div>

</div>