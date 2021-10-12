<?php

//去除分类中的category

add_action('load-themes.php', 'no_category_base_refresh_rules');
add_action('created_category', 'no_category_base_refresh_rules');
add_action('edited_category', 'no_category_base_refresh_rules');
add_action('delete_category', 'no_category_base_refresh_rules');
add_action('init', 'no_category_base_permastruct');
add_filter('category_rewrite_rules', 'no_category_base_rewrite_rules');
add_filter('query_vars', 'no_category_base_query_vars');
add_filter('request', 'no_category_base_request');


function no_category_base_refresh_rules()
{
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
}

function no_category_base_permastruct()
{
    global $wp_rewrite, $wp_version;
    if (version_compare($wp_version, '3.4', '<')) {
        // For pre-3.4 support
        $wp_rewrite->extra_permastructs['category'][0] = '%category%';
    } else {
        $wp_rewrite->extra_permastructs['category']['struct'] = '%category%';
    }
}

function no_category_base_rewrite_rules($category_rewrite)
{
    $category_rewrite = array();
    $categories = get_categories(array('hide_empty' => false));
    foreach ($categories as $category) {
        $category_nicename = $category->slug;
        if ($category->parent == $category->cat_ID)
            $category->parent = 0;
        elseif ($category->parent != 0)
            $category_nicename = get_category_parents($category->parent, false, '/', true) . $category_nicename;
        $category_rewrite['(' . $category_nicename . ')/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
        $category_rewrite['(' . $category_nicename . ')/page/?([0-9]{1,})/?$'] = 'index.php?category_name=$matches[1]&paged=$matches[2]';
        $category_rewrite['(' . $category_nicename . ')/?$'] = 'index.php?category_name=$matches[1]';
    }
    $old_category_base = get_option('category_base') ? get_option('category_base') : 'category';
    $old_category_base = trim($old_category_base, '/');
    $category_rewrite[$old_category_base . '/(.*)$'] = 'index.php?category_redirect=$matches[1]';
    return $category_rewrite;
}

function no_category_base_query_vars($public_query_vars)
{
    $public_query_vars[] = 'category_redirect';
    return $public_query_vars;
}

function no_category_base_request($query_vars)
{
    if (isset($query_vars['category_redirect'])) {
        $catlink = trailingslashit(get_option('home')) . user_trailingslashit($query_vars['category_redirect'], 'category');
        status_header(301);
        header("Location: $catlink");
        exit();
    }
    return $query_vars;
}