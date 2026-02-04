<?php
add_action('widgets_init', 'pk_widgets_init');
function pk_widgets_init()
{
    pk_common_sidebar_register('sidebar_single', __('正文内容 - 侧边栏', PUOCK), __('文章正文内容侧边栏', PUOCK));
    pk_common_sidebar_register('sidebar_home', __('首页 - 侧边栏', PUOCK), __('首页侧边栏', PUOCK));
    pk_common_sidebar_register('sidebar_search', __('搜索页 - 侧边栏', PUOCK), __('搜索页侧边栏', PUOCK));
    pk_common_sidebar_register('sidebar_cat', __('分类/标签页 - 侧边栏', PUOCK), __('分类/标签页侧边栏', PUOCK));
    pk_common_sidebar_register('sidebar_page', __('单页面 - 侧边栏', PUOCK), __('单页面侧边栏', PUOCK));
    pk_common_sidebar_register('sidebar_other', __('其他页面 - 侧边栏', PUOCK), __('包括作者/404等其他页面', PUOCK));
    pk_common_sidebar_register('sidebar_not', __('通用 - 侧边栏', PUOCK), __('若指定页面未配置任何栏目，则显示此栏目下的数据', PUOCK));
    pk_common_sidebar_register('post_content_author_top', __('正文 - 作者上方栏目', PUOCK), __('显示在正文作者栏上方的栏目', PUOCK));
    pk_common_sidebar_register('post_content_author_bottom', __('正文 - 作者下方栏目', PUOCK), __('显示在正文作者栏下方的栏目', PUOCK));
    pk_common_sidebar_register('index_bottom', __('首页 - 底部栏目', PUOCK), __('显示在首页内容最底部（友情链接上方的通栏项）', PUOCK));
    pk_common_sidebar_register('index_cms_layout_top', __('CMS布局 - 分类栏上方栏目', PUOCK), __('CMS布局下显示在分类栏之上的栏目', PUOCK));
    pk_common_sidebar_register('index_cms_layout_bottom', __('CMS布局 - 分类栏下方栏目', PUOCK), __('CMS布局下显示在分类栏之下的栏目', PUOCK));
    pk_common_sidebar_register('post_content_comment_top', __('正文 - 评论上方栏目', PUOCK), __('显示在正文评论上方的栏目', PUOCK));
    pk_common_sidebar_register('post_content_comment_bottom', __('正文 - 评论下方栏目', PUOCK), __('显示在正文评论下方的栏目', PUOCK));
    pk_common_sidebar_register('page_content_comment_top', __('页面 - 评论上方栏目', PUOCK), __('显示在页面评论上方的栏目', PUOCK));
    pk_common_sidebar_register('page_content_comment_bottom', __('页面 - 评论下方栏目', PUOCK), __('显示在页面评论下方的栏目', PUOCK));
}

function pk_common_sidebar_register($id, $name, $description = '')
{
    register_sidebar(array(
        'name' => $name,
        'id' => $id,
        'description' => $description,
        'before_widget' => '<div class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
    ));
}
