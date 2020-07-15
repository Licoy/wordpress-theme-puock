<?php


register_sidebar(array(
    'name' => __( '正文内容侧边栏' ),
    'id' => 'sidebar_single',
    'description' => __( '文章正文内容侧边栏' ),
    'before_widget' => '<div class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h3 class="widget-title">',
    'after_title' => '</h3>'
) );

register_sidebar(array(
    'name' => __( '首页侧边栏' ),
    'id' => 'sidebar_home',
    'description' => __( '首页侧边栏' ),
    'before_widget' => '<div class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h3 class="widget-title">',
    'after_title' => '</h3>'
) );

register_sidebar(array(
    'name' => __( '搜索页侧边栏' ),
    'id' => 'sidebar_search',
    'description' => __( '搜索页侧边栏' ),
    'before_widget' => '<div class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h3 class="widget-title">',
    'after_title' => '</h3>'
) );

register_sidebar(array(
    'name' => __( '分类/标签页侧边栏' ),
    'id' => 'sidebar_cat',
    'description' => __( '分类/标签页侧边栏' ),
    'before_widget' => '<div class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h3 class="widget-title">',
    'after_title' => '</h3>'
) );

register_sidebar(array(
    'name' => __( '单页面侧边栏' ),
    'id' => 'sidebar_page',
    'description' => __( '单页面侧边栏' ),
    'before_widget' => '<div class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h3 class="widget-title">',
    'after_title' => '</h3>'
) );

register_sidebar(array(
    'name' => __( '其他页面侧边栏' ),
    'id' => 'sidebar_other',
    'description' => __( '包括作者/404等其他页面' ),
    'before_widget' => '<div class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h3 class="widget-title">',
    'after_title' => '</h3>'
) );

register_sidebar(array(
    'name' => __( '通用侧边栏' ),
    'id' => 'sidebar_not',
    'description' => __( '若指定页面未配置任何栏目，则显示此栏目下的数据' ),
    'before_widget' => '<div class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h3 class="widget-title">',
    'after_title' => '</h3>'
) );