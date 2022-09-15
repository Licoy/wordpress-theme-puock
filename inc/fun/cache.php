<?php

const PKC_WIDGET_NEW_POSTS = 'widget_new_posts';
const PKC_WIDGET_HOT_POSTS = 'widget_hot_posts';
const PKC_WIDGET_HOT_COMMENTS = 'widget_hot_comments';
const PKC_WIDGET_NEW_COMMENTS = 'widget_new_comments';
const PKC_WIDGET_READ_PERSONS = 'widget_read_persons';
const PKC_WIDGET_CATEGORIES = 'widget_categories';
const PKC_WIDGET_TAGS = 'widget_tags';
const PKC_CMS_2BOX_POSTS = 'cms_2box_posts';
const PKC_FOOTER_LINKS = 'footer_links';
const PKC_MENU_PRIMARY = 'menu_primary';
const PKC_CAT_RELEVANT_POSTS = 'cat_relevant_posts';

// 缓存获取
function pk_cache_get($key, $force = false, &$found = null)
{
    return wp_cache_get($key, PUOCK, $force, $found);
}

// 缓存设置
function pk_cache_set($key, $value, $expiration = 0)
{
    return wp_cache_set($key, $value, PUOCK, $expiration);
}

// 缓存删除
function pk_cache_delete($key, $time = 0)
{
    return wp_cache_delete($key, PUOCK, $time);
}
