<?php
/*
 Template Name: 文章归档
*/
$posts = get_posts('numberposts=-1&orderby=post_date&order=DESC');

$dates = array();

foreach ($posts as $post){
    setup_postdata($post);
    $ym = get_the_date('Y-m');
    if(!array_key_exists($ym, $dates)){
        $dates[$ym] = array();
    }
    array_push($dates[$ym], $post);
}
wp_reset_postdata();

get_header();

?>

    <div id="page" class="container mt20">
        <?php get_template_part('templates/box', 'global-top') ?>
        <?php echo pk_breadcrumbs();?>
        <div id="page-archives">
            <div id="page-<?php the_ID() ?>" class="w-100">
                <div id="posts" class="<?php pk_open_box_animated('animated fadeInLeft') ?> ">
                    <div class="p-block puock-text">
                        <div class="<?php get_entry_content_class() ?> mb15">
                            <?php the_content(); ?>
                        </div>
                        <div class="timeline no-style">
                            <?php foreach ($dates as $date=>$posts): ?>
                            <div class="timeline-item">
                                <div class="timeline-location"></div>
                                <div class="timeline-content">
                                    <h4><?php echo $date ?></h4>
                                    <ul class="pd-links pl-0">
                                        <?php foreach ($posts as $post): setup_postdata($post) ?>
                                        <li>
                                            <a title="<?php the_title() ?>" <?php pk_link_target() ?> href="<?php the_permalink() ?>
                                                "><?php the_title() ?>&nbsp;（&nbsp;<?php echo date_i18n(__('d日', PUOCK), strtotime($post->post_date)) ?>）</a>
                                        </li>
                                        <?php endforeach;wp_reset_postdata(); ?>
                                    </ul>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php comments_template() ?>
                </div>
            </div>
        </div>
        <?php get_template_part('templates/box', 'global-bottom') ?>
    </div>

<?php get_footer() ?>
