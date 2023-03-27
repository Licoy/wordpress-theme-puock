<?php
/*
Template Name: 读者墙
*/

$year = 2;
$sql = "SELECT count(comment_ID) as num, comment_author_email as mail,comment_author as `name`,comment_author_url as url
                FROM $wpdb->comments WHERE user_id !=1 AND comment_approved !=0 AND TO_DAYS(now()) - TO_DAYS(comment_date) < (".($year*365).")
                 group by comment_author_email order by num desc limit 0, 100";
$reads = $wpdb->get_results($sql);
get_header();
?>

<div id="page" class="container mt20">
    <?php get_template_part('templates/box', 'global-top') ?>
    <?php echo pk_breadcrumbs(); while (have_posts()):the_post();?>
        <div id="page-reads">
            <div id="page-<?php the_ID() ?>" class="row row-cols-1">
                <div id="posts" class="col-lg-<?php pk_hide_sidebar_out('12','8') ?> col-md-12 <?php pk_open_box_animated('animated fadeInLeft') ?> ">
                    <div class="p-block puock-text">
                        <h2 class="t-lg"><?php the_title() ?></h2>
                        <?php if(!empty(get_the_content())): ?>
                            <div class="mt20 <?php get_entry_content_class() ?>">
                                <?php the_content() ?>
                            </div>
                        <?php endif; ?>
                        <div class="mt20 row pd-links">
                            <?php foreach ($reads as $read): ?>
                                <div class="col col-6 col-md-4 col-lg-3 pl-0">
                                    <div class="p-2 text-truncate text-nowrap">
                                        <a href="<?php echo empty($read->url) ? 'javascript:void(0)':pk_go_link($read->url) ?>"
                                            <?php echo empty($read->url) ? '':'target="_blank"' ?> rel="nofollow">
                                            <img data-bs-toggle="tooltip" <?php echo pk_get_lazy_img_info(get_avatar_url($read->mail),'md-avatar') ?>
                                                 title="<?php echo $read->name?>" alt="<?php echo $read->name?>">
                                            <span class="t-sm"><span class="c-sub">+(<?php echo $read->num?>)&nbsp;</span><?php echo $read->name?></span>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach;wp_reset_postdata() ?>
                        </div>
                    </div>
                    <?php comments_template() ?>
                </div>
                <?php get_sidebar() ?>
            </div>
        </div>
    <?php endwhile; ?>
    <?php get_template_part('templates/box', 'global-bottom') ?>
</div>

<?php get_footer() ?>
