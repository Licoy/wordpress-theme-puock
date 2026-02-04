<?php

function pk_ext_moments_init()
{
    $name = __('时光圈', PUOCK);
    $labels = array(
        'name' => $name,
        'singular_name' => $name,
        'add_new' => sprintf(__('发表%s', PUOCK), $name),
        'add_new_item' => sprintf(__('发表%s', PUOCK), $name),
        'edit_item' => sprintf(__('编辑%s', PUOCK), $name),
        'new_item' => sprintf(__('新%s', PUOCK), $name),
        'view_item' => sprintf(__('查看%s', PUOCK), $name),
        'search_items' => sprintf(__('搜索%s', PUOCK), $name),
        'not_found' => sprintf(__('暂无%s', PUOCK), $name),
        'not_found_in_trash' => sprintf(__('没有已遗弃的%s', PUOCK), $name),
        'parent_item_colon' => '',
        'menu_name' => $name
    );
    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => true,
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'menu_icon'=>'dashicons-format-status',
        'supports' => array(
            'title',
            'editor',
            'author',
            'comments'
        )
    );
    register_post_type('moments', $args);
}
add_action('init', 'pk_ext_moments_init');

function pk_ext_moments_rewrites_init()
{
    add_rewrite_rule(
        'moments/([0-9]+)?.html$',
        'index.php?post_type=moments&p=$matches[1]',
        'top'
    );
}
add_action( 'init', 'pk_ext_moments_rewrites_init' );

function pk_ext_moments_link( $link, $post ){
    if ( $post->post_type == 'moments' ){
        return home_url( 'moments/' . $post->ID .'.html' );
    } else {
        return $link;
    }
}
add_filter('post_type_link', 'pk_ext_moments_link', 1, 2);
