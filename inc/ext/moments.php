<?php

function pk_ext_moments_init()
{
    $name = "时光圈";
    $labels = array(
        'name' => $name,
        'singular_name' => $name,
        'add_new' => '发表'.$name,
        'add_new_item' => '发表'.$name,
        'edit_item' => '编辑'.$name,
        'new_item' => '新'.$name,
        'view_item' => '查看'.$name,
        'search_items' => '搜索'.$name,
        'not_found' => '暂无'.$name,
        'not_found_in_trash' => '没有已遗弃的'.$name,
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
