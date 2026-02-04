<?php

function pk_load_more_posts()
{
    global $wp_query;
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
    $paged++;

    $args = [
        'post_type' => 'post',
        'post_status' => 'publish',
        'paged' => $paged,
    ];

    query_posts($args);
    ob_start();

    if (have_posts()) {
        while (have_posts()) : the_post();
            get_template_part('templates/module', 'post');
        endwhile;
    }

    $html = ob_get_clean();

    $has_more = $paged < $wp_query->max_num_pages;

    wp_reset_query();

    echo pk_ajax_resp([
        'html' => $html,
        'paged' => $paged,
        'has_more' => $has_more,
    ], __('加载成功', PUOCK));

    wp_die();
}

pk_ajax_register('pk_load_more_posts', 'pk_load_more_posts', true);