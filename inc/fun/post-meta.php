<?php

use Puock\Theme\classes\meta\PuockAbsMeta;

PuockAbsMeta::newPostMeta('pk-post-seo', [
    'title' => 'SEO设置',
    'options' => [
        array(
            "id" => "seo_keywords",
            "title" => "自定义SEO关键词",
            'desc' => '多个关键词之间使用", "分隔，默认为设置的标签',
            "type" => "text"
        ),
        array(
            "id" => "seo_desc",
            "title" => "自定义SEO描述",
            'desc' => '默认为文章前150个字符（推荐不超过150个字符）',
            "type" => "text"
        )
    ]
]);

PuockAbsMeta::newPostMeta('pk-post-basic', [
    'title' => '基本设置',
    'options' => [
        array(
            "id" => "hide_side",
            "title" => "隐藏侧边栏",
            "type" => "checkbox"
        ),
        array(
            "id" => "author_cat_comment",
            "title" => "评论仅对作者可见",
            "type" => "checkbox"
        ),
        array(
            "id" => "origin_author",
            "title" => "文章出处名称",
            "desc" => "若非原创则填写此值，包括其下一栏",
            "type" => "text"
        ),
        array(
            "id" => "origin_url",
            "title" => "文章出处链接",
            "type" => "text"
        )
    ]
]);

function pk_page_meta_basic()
{
    $link_cats = get_all_category_id_row('link_category');
    PuockAbsMeta::newPostMeta('pk-page-basic', [
        'title' => '基本设置',
        'post_type' => 'page',
        'options' => [
            array(
                "id" => "hide_side",
                "title" => "隐藏侧边栏",
                "type" => "checkbox"
            ),
            array(
                "id" => "author_cat_comment",
                "title" => "评论仅对作者可见",
                "type" => "checkbox"
            ),
            array(
                "id" => "use_theme_link_forward",
                "std" => "0",
                "title" => "内部链接使用主题链接跳转页",
                "type" => "checkbox"
            ),
            array(
                "id" => "page_links_id",
                "std" => "",
                "title" => "链接显示分类目录ID列表",
                'desc' => "（仅为<b>友情链接</b>及<b>网址导航</b>模板时有效，为空则不显示，可多选）",
                "type" => "select",
                'multiple'=>true,
                "options" => $link_cats
            ),
            array(
                "id" => "page_books_id",
                "std" => "",
                "title" => "书籍显示分类目录ID列表",
                "desc" => "（仅为<b>书籍推荐</b>模板时有效，为空则不显示，可多选）",
                "type" => "select",
                'multiple'=>true,
                "options" => $link_cats
            )
        ]
    ]);
}

pk_page_meta_basic();
