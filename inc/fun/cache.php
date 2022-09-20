<?php

const PKC_WIDGET_NEW_POSTS = 'widget_new_posts';
const PKC_WIDGET_HOT_POSTS = 'widget_hot_posts';
const PKC_WIDGET_HOT_COMMENTS = 'widget_hot_comments';
const PKC_WIDGET_NEW_COMMENTS = 'widget_new_comments';
const PKC_WIDGET_READ_PERSONS = 'widget_read_persons';
const PKC_WIDGET_CATEGORIES = 'widget_categories';
const PKC_WIDGET_TAGS = 'widget_tags';
const PKC_TOTAL_VIEWS = 'total_views';
const PKC_TOTAL_COMMENTS = 'total_comments';
const PKC_CMS_2BOX_POSTS = 'cms_2box_posts_%d';
const PKC_FOOTER_LINKS = 'footer_links';
const PKC_CAT_RELEVANT_POSTS = 'cat_relevant_posts_%d';
const PKC_POST_VIEWS = 'post_views_%d';
const PKC_AUTHOR_COMMENTS = 'author_comments_%s';
const PKC_MOMENTS = 'moments_%s';

function pk_cache_key_load($key){
    return "wp:puock:".$key;
}

// 缓存获取
function pk_cache_get($key, $force = false, &$found = null)
{
    return wp_cache_get($key, PUOCK, $force, $found);
}

// 缓存设置
function pk_cache_set($key, $value, $expiration = null)
{
    $expiration = $expiration == null ? pk_get_option('cache_expire_second', 0) : $expiration;
    return wp_cache_set($key, $value, PUOCK, $expiration);
}

// 缓存删除
function pk_cache_delete($key, $time = 0)
{
    return wp_cache_delete($key, PUOCK, $time);
}

// 缓存删除
function pk_cache_delete_multiple($keys)
{
    return wp_cache_delete_multiple($keys, PUOCK);
}

// 缓存删除
function pk_cache_delete_find_keys($find_key){
    pk_cache_roc_call(function($redis) use($find_key){
        $keys = $redis->keys($find_key);
        if($keys && is_array($keys)){
            foreach ($keys as $key){
                $redis->del($key);
            }
        }
    });
}

// 清除缓存注册
function pk_cache_del_register()
{
    add_action('comment_post', 'pk_cache_del_comments_post', 10, 3);
    add_action('transition_comment_status', 'pk_cache_del_comment', 10, 3);
    add_action('pk_option_updated', 'pk_cache_del_options_updated', 10, 1);
    add_action('save_post', 'pk_cache_del_save_post', 10, 3);
}

add_action('init', 'pk_cache_del_register');

function pk_cache_del_comments_post($comment_ID, $comment_approved, $commentdata)
{
    if ($comment_approved) {
        pk_cache_delete(sprintf(PKC_AUTHOR_COMMENTS, md5($commentdata['comment_author_email'])));
        pk_cache_delete(PKC_TOTAL_COMMENTS);
        pk_cache_delete(PKC_WIDGET_NEW_COMMENTS);
    }
}

function pk_cache_del_comment($new_status, $old_status, $comment)
{
    pk_cache_delete(sprintf(PKC_AUTHOR_COMMENTS, $comment->comment_author_email));
    pk_cache_delete(PKC_TOTAL_COMMENTS);
    pk_cache_delete(PKC_WIDGET_NEW_COMMENTS);
}

function pk_cache_del_options_updated($opts)
{
    wp_cache_flush();
}

function pk_cache_del_save_post($post_id, $post, $is_update)
{
    if ($post->post_type == 'moments') {
        pk_cache_delete_find_keys(pk_cache_key_load(sprintf(PKC_MOMENTS, '*')));
    }
}


function pk_cache_roc_call($function){
    if(function_exists('redis_object_cache')){
        if(redis_object_cache()->get_status()=='Connected'){
            global $wp_object_cache;
            $function($wp_object_cache->redis_instance());
        }
    }
}
