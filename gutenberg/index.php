<?php

function create_block_puock_block_init()
{
    pk_gutenberg_block_reg("alert");
}

function pk_gutenberg_block_reg($name)
{
    $prefix = 'puock-block-' . $name;
    $assets_prefix = '/gutenberg/components/' . $name;
    wp_register_script($prefix . '-js', get_template_directory_uri() . $assets_prefix . '/index.js', array('wp-server-side-render', 'wp-block-editor', 'wp-blocks', 'wp-element', 'wp-i18n', 'wp-polyfill'));
//    wp_register_style($prefix . '-editor-style', get_template_directory_uri() . $assets_prefix . '/index.css');
    wp_register_style($prefix . '-style', get_template_directory_uri() . $assets_prefix . '/style-index.css');
    register_block_type(__DIR__ . '/components/' . $name, [
        'editor_script' => $prefix . '-js',
//        'editor_style' => $prefix . '-editor-style',
        'style' => $prefix . '-style',
    ]);
}

add_action('init', 'create_block_puock_block_init');
