<?php

function create_block_puock_block_init()
{
    pk_gutenberg_block_reg("alert");
}

function pk_gutenberg_block_reg($name)
{
    if (!function_exists('register_block_type')) {
        return;
    }
    $block_dir = __DIR__ . '/components/' . $name;
    if (!is_readable($block_dir . '/block.json')) {
        return;
    }
    register_block_type($block_dir);
}

add_action('init', 'create_block_puock_block_init');
