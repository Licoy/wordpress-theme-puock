<?php $titleConn = " " . pk_get_option("title_conn") . " " ?>
<?php $blog_name = pk_get_web_title();
$pkSeoPageInfo = ''; ?>
<?php global $other_page_title;
if(get_query_var('paged')){
    $pkSeoPageInfo = $titleConn . '第' . get_query_var('paged') . '页';
}
if (isset($other_page_title)) { ?>
    <title><?php echo $other_page_title . $pkSeoPageInfo . $titleConn . $blog_name; ?></title>
<?php } else if (is_home()) { ?>
    <title><?php echo $blog_name . $pkSeoPageInfo . $titleConn . pk_get_option('web_title_2'); ?></title>
<?php } else if (is_search()) { ?><title>搜索"<?php echo $_REQUEST['s'] ?>
    "的结果<?php echo $titleConn . $pkSeoPageInfo . $blog_name ?></title>
<?php } else if (is_single() || is_page()) { ?>
    <title><?php echo trim(wp_title('', 0)); ?><?php echo $titleConn . $blog_name; ?></title>
<?php } else if (is_year()) { ?><title><?php the_time('Y年'); ?>
    的所有文章 <?php echo $pkSeoPageInfo . $titleConn . $blog_name ?></title>
<?php } else if (is_month()) { ?><title><?php the_time('m'); ?>
    份的所有文章 <?php echo $pkSeoPageInfo . $titleConn . $blog_name ?></title>
<?php } else if (is_day()) { ?><title><?php the_time('Y年m月d日'); ?>
    的所有文章 <?php echo $pkSeoPageInfo . $titleConn . $blog_name ?></title>
<?php } else if (is_author()) { ?><title><?php the_author(); ?>
    发表的所有文章 <?php echo $pkSeoPageInfo . $titleConn . $blog_name ?></title>
<?php } else if (is_category()) { ?>
    <title><?php single_cat_title(); ?><?php echo $pkSeoPageInfo . $titleConn . $blog_name ?> </title>
<?php } else if (is_tag()) { ?>
    <title><?php single_tag_title("", true); ?><?php echo $pkSeoPageInfo . $titleConn . $blog_name; ?></title>
<?php } else if (is_404()) { ?> <title>你访问的资源不存在<?php echo $pkSeoPageInfo . $titleConn . $blog_name; ?></title>
<?php } else { ?> <title><?php echo $blog_name . $pkSeoPageInfo . $titleConn . $blog_name; ?></title><?php } ?>
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
<?php
if(is_home()){
    echo '<link rel="canonical" href="'.home_url().'">';
}else{
    echo '<link rel="canonical" href="'.get_permalink().'">';
}
?>

