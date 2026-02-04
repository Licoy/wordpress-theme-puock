<?php

use Puock\Theme\classes\meta\PuockAbsMeta;

PuockAbsMeta::newPostMeta('pk-post-seo', [
    'title' => __('SEO设置', PUOCK),
    'options' => [
        array(
            "id" => "seo_keywords",
            "title" => __("自定义SEO关键词", PUOCK),
            'desc' => __('多个关键词之间使用", "分隔，默认为设置的标签', PUOCK),
            "type" => "text"
        ),
        array(
            "id" => "seo_desc",
            "title" => __("自定义SEO描述", PUOCK),
            'desc' => __('默认为文章前150个字符（推荐不超过150个字符）', PUOCK),
            "type" => "text"
        )
    ]
]);

PuockAbsMeta::newPostMeta('pk-post-basic', [
    'title' => __('基本设置', PUOCK),
    'options' => [
        array(
            "id" => "hide_side",
            "title" => __("隐藏侧边栏", PUOCK),
            "type" => "checkbox"
        ),
        array(
            "id" => "author_cat_comment",
            "title" => __("评论仅对作者可见", PUOCK),
            "type" => "checkbox"
        ),
        array(
            "id" => "origin_author",
            "title" => __("文章出处名称", PUOCK),
            "desc" => __("若非原创则填写此值，包括其下一栏", PUOCK),
            "type" => "text"
        ),
        array(
            "id" => "origin_url",
            "title" => __("文章出处链接", PUOCK),
            "type" => "text"
        )
    ]
]);

function pk_page_meta_basic()
{
    $link_cats = get_all_category_id_row('link_category');
    PuockAbsMeta::newPostMeta('pk-page-basic', [
        'title' => __('基本设置', PUOCK),
        'post_type' => 'page',
        'options' => [
            array(
                "id" => "hide_side",
                "title" => __("隐藏侧边栏", PUOCK),
                "type" => "checkbox"
            ),
            array(
                "id" => "author_cat_comment",
                "title" => __("评论仅对作者可见", PUOCK),
                "type" => "checkbox"
            ),
            array(
                "id" => "use_theme_link_forward",
                "std" => "0",
                "title" => __("内部链接使用主题链接跳转页", PUOCK),
                "type" => "checkbox"
            ),
            array(
                "id" => "page_links_id",
                "std" => "",
                "title" => __("链接显示分类目录ID列表", PUOCK),
                'desc' => __("（仅为<b>友情链接</b>及<b>网址导航</b>模板时有效，为空则不显示，可多选）", PUOCK),
                "type" => "select",
                'multiple' => true,
                "options" => $link_cats
            ),
            array(
                "id" => "page_books_id",
                "std" => "",
                "title" => __("书籍显示分类目录ID列表", PUOCK),
                "desc" => __("（仅为<b>书籍推荐</b>模板时有效，为空则不显示，可多选）", PUOCK),
                "type" => "select",
                'multiple' => true,
                "options" => $link_cats
            )
        ]
    ]);
}

pk_page_meta_basic();
