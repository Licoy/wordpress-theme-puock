<?php if (is_home()) : ?>
    <meta name="keywords" content="<?php echo pk_get_option('keyword') ?>"/>
    <meta name="description" content="<?php echo pk_get_option('description') ?>"/>
<?php endif; ?>
<?php if (is_single()) : while (have_posts()) : the_post(); ?>
    <meta name="keywords" content="<?php
    $single_seo_keywords = get_post_meta($post->ID, "seo_keywords", true);
    if ($single_seo_keywords != null && !empty(trim($single_seo_keywords))) {
        echo trim($single_seo_keywords);
    } else {
        $tags_list = get_the_tags();
        if ($tags_list != null && count($tags_list) > 0) {
            $tags_str = "";
            foreach (get_the_tags() as $tag_item) {
                $tags_str .= $tag_item->name . ',';
            };
            $tags_str = substr($tags_str, 0, strlen($tags_str) - 1);
            echo $tags_str;
        }
    }
    ?>"/>
    <meta name="description" content="<?php
    $single_seo_desc = get_post_meta($post->ID, "seo_desc", true);
    if ($single_seo_desc != null && !empty(trim($single_seo_desc))) {
        echo trim($single_seo_desc);
    } else {
        echo wp_trim_words(do_shortcode(get_the_content($post->ID)), 147, '...');
    }
    ?>"/>
<?php endwhile; endif; ?>
<?php if (is_category()) : ?>
    <?php
    $cat_seo_root_obj = get_category($cat);
    $cat_seo_keywords = get_option("seo-cat-keywords-" . $cat);
    if (!empty(trim($cat_seo_keywords))) {
        echo '<meta name="keywords" content="' . $cat_seo_keywords . '"/>';
    } else {
        echo '<meta name="keywords" content="' . $cat_seo_root_obj->name . '"/>';
    }
    $cat_seo_desc = get_option("seo-cat-desc-" . $cat);
    if (!empty(trim($cat_seo_desc))) {
        echo '<meta name="description" content="' . $cat_seo_desc . '"/>';
    } else {
        echo '<meta name="description" content="' . $cat_seo_root_obj->name . '"/>';
    }


    ?>
<?php endif; ?>

<?php if (is_author()) : ?>
    <meta name="description" content="<?php echo the_author_meta('description') ?>"/>
<?php endif; ?>

<?php if (is_tag()) : ?>
    <meta name="description" content="<?php echo preg_replace('/<[^>]*>/', '', tag_description()) ?>"/>
<?php endif; ?>

<?php
if (is_home()) {
    echo '<link rel="canonical" href="' . home_url() . '">';
} else {
    echo '<link rel="canonical" href="' . get_permalink() . '">';
}
?>