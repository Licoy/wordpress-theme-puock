<?php
add_action('widgets_init', 'pk_widgets_init');
function pk_widgets_init()
{
    pk_common_sidebar_register('sidebar_single', '正文内容 - 侧边栏', '文章正文内容侧边栏');
    pk_common_sidebar_register('sidebar_home', '首页 - 侧边栏', '首页侧边栏');
    pk_common_sidebar_register('sidebar_search', '搜索页 - 侧边栏', '搜索页侧边栏');
    pk_common_sidebar_register('sidebar_cat', '分类/标签页 - 侧边栏', '分类/标签页侧边栏');
    pk_common_sidebar_register('sidebar_page', '单页面 - 侧边栏', '单页面侧边栏');
    pk_common_sidebar_register('sidebar_other', '其他页面 - 侧边栏', '包括作者/404等其他页面');
    pk_common_sidebar_register('sidebar_not', '通用 - 侧边栏', '若指定页面未配置任何栏目，则显示此栏目下的数据');
    pk_common_sidebar_register('post_content_author_top', '正文 - 作者上方栏目', '显示在正文作者栏上方的栏目');
    pk_common_sidebar_register('post_content_author_bottom', '正文 - 作者下方栏目', '显示在正文作者栏下方的栏目');
    pk_common_sidebar_register('index_bottom', '首页 - 底部栏目', '显示在首页内容最底部（友情链接上方的通栏项）');
    pk_common_sidebar_register('index_cms_layout_top', 'CMS布局 - 分类栏上方栏目', 'CMS布局下显示在分类栏之上的栏目');
    pk_common_sidebar_register('index_cms_layout_bottom', 'CMS布局 - 分类栏下方栏目', 'CMS布局下显示在分类栏之下的栏目');
    pk_common_sidebar_register('post_content_comment_top', '正文 - 评论上方栏目', '显示在正文评论上方的栏目');
    pk_common_sidebar_register('post_content_comment_bottom', '正文 - 评论下方栏目', '显示在正文评论下方的栏目');
    pk_common_sidebar_register('page_content_comment_top', '页面 - 评论上方栏目', '显示在页面评论上方的栏目');
    pk_common_sidebar_register('page_content_comment_bottom', '页面 - 评论下方栏目', '显示在页面评论下方的栏目');
}

function pk_common_sidebar_register($id, $name, $description = '')
{
    register_sidebar(array(
        'name' => __($name, PUOCK),
        'id' => $id,
        'description' => __($description, PUOCK),
        'before_widget' => '<div class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
    ));
}
