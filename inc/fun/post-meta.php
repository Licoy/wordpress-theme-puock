<?php


//文章meta信息设置
function post_meta_set($boxes)
{
    global $post;
    foreach ($boxes as $meta_box) {
        $meta_box_value = get_post_meta($post->ID, $meta_box['name'] . '', true);
        if ($meta_box_value != "")
            $meta_box['std'] = $meta_box_value;
        echo '<input type="hidden" name="' . $meta_box['name'] . '_noncename" id="' . $meta_box['name'] . '_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';
        //选择类型输出不同的html代码
        switch ($meta_box['type']) {
            case 'title':
                echo '<h4>' . $meta_box['title'] . '</h4>';
                break;
            case 'des':
                echo '<p>' . $meta_box['std'] . '</p>';
                break;
            case 'text':
                echo '<h4>' . $meta_box['title'] . '</h4>';
                echo '<span class="form-field"><input type="text" size="40" name="' . $meta_box['name'] . '" value="' . $meta_box['std'] . '" /></span><br />';
                break;
            case 'textarea':
                echo '<h4>' . $meta_box['title'] . '</h4>';
                echo '<textarea id="seo-excerpt" cols="40" rows="2" name="' . $meta_box['name'] . '">' . $meta_box['std'] . '</textarea><br />';
                break;
            case 'radio':
                echo '<h4>' . $meta_box['title'] . '</h4>';
                $counter = 1;
                foreach ($meta_box['buttons'] as $radiobutton) {
                    $checked = "";
                    if (isset($meta_box['std']) && $meta_box['std'] == $counter) {
                        $checked = 'checked = "checked"';
                    }
                    echo '<input ' . $checked . ' type="radio" class="kcheck" value="' . $counter . '" name="' . $meta_box['name'] . '_value"/>' . $radiobutton;
                    $counter++;
                }
                break;
            case 'checkbox':
                if (isset($meta_box['std']) && $meta_box['std'] == 'true')
                    $checked = 'checked = "checked"';
                else
                    $checked = '';
                echo '<br /><input type="checkbox" name="' . $meta_box['name'] . '" value="true"  ' . $checked . ' />';
                echo '<label>' . $meta_box['title'] . '</label><br />';
                break;
        }
    }
}

//保存meta数据
function save_post_meta_data($post_id, $boxes)
{
    foreach ($boxes as $meta_box) {
        if (!wp_verify_nonce(@$_POST[$meta_box['name'] . '_noncename'], plugin_basename(__FILE__))) {
            return $post_id;
        }

        if ('page' == $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id))
                return $post_id;
        } else {
            if (!current_user_can('edit_post', $post_id))
                return $post_id;
        }

        $data = @$_POST[$meta_box['name'] . ''];

        if (get_post_meta($post_id, $meta_box['name'] . '') == "")
            add_post_meta($post_id, $meta_box['name'] . '', $data, true);
        elseif ($data != get_post_meta($post_id, $meta_box['name'] . '', true))
            update_post_meta($post_id, $meta_box['name'] . '', $data);
        elseif ($data == "")
            delete_post_meta($post_id, $meta_box['name'] . '', get_post_meta($post_id, $meta_box['name'] . '', true));
    }
}

$seo_metas = array(
    "seo_keywords" => array(
        "name" => "seo_keywords",
        "std" => "",
        "title" => "自定义SEO关键词，多个关键词之间使用','分隔，默认为设置的标签",
        "type" => "text"
    ),
    "seo_desc" => array(
        "name" => "seo_desc",
        "std" => "",
        "title" => "自定义SEO描述，默认为文章前150个字符（推荐不超过150个字符）",
        "type" => "text"
    )
);

function puock_seo_post_metas()
{
    global $seo_metas;
    post_meta_set($seo_metas);
}

function puock_seo_post_metas_save($post_id)
{
    global $seo_metas;
    save_post_meta_data($post_id, $seo_metas);
}

function puock_seo_post_meta_box()
{
    if (function_exists('add_meta_box')) {
        add_meta_box('puock_seo_post_meta_box', 'SEO设置', 'puock_seo_post_metas', 'post', 'normal', 'high');
    }
}

add_action('admin_menu', 'puock_seo_post_meta_box');
add_action('save_post', 'puock_seo_post_metas_save');

$basic_metas = array(
    "hide_side" => array(
        "name" => "hide_side",
        "std" => "",
        "title" => "隐藏侧边栏",
        "type" => "checkbox"
    ),
    "author_cat_comment" => array(
        "name" => "author_cat_comment",
        "std" => "",
        "title" => "评论仅对作者可见",
        "type" => "checkbox"
    ),
    "origin_author" => array(
        "name" => "origin_author",
        "std" => "",
        "title" => "文章出处名称（若非原创则填写此值，包括其下一栏）",
        "type" => "text"
    ),
    "origin_url" => array(
        "name" => "origin_url",
        "std" => "",
        "title" => "文章出处链接",
        "type" => "text"
    )
);

function puock_basic_post_metas()
{
    global $basic_metas;
    post_meta_set($basic_metas);
}

function puock_basic_post_metas_save($post_id)
{
    global $basic_metas;
    save_post_meta_data($post_id, $basic_metas);
}

function puock_basic_post_meta_box()
{
    if (function_exists('add_meta_box')) {
        add_meta_box('puock_basic_post_meta_box', '基本设置', 'puock_basic_post_metas', 'post', 'normal', 'high');
    }
}

add_action('admin_menu', 'puock_basic_post_meta_box');
add_action('save_post', 'puock_basic_post_metas_save');

function pk_post_meta_basic(){
    $link_cats = get_all_category_id('link_category');
    return array(
        "hide_side" => array(
            "name" => "hide_side",
            "std" => "",
            "title" => "隐藏侧边栏",
            "type" => "checkbox"
        ),
        "author_cat_comment" => array(
            "name" => "author_cat_comment",
            "std" => "",
            "title" => "评论仅对作者可见",
            "type" => "checkbox"
        ),
        "page_links_cids" => array(
            "name" => 'page_links_cids',
            "std" => '<b>链接分类ID对照列表：</b>' . $link_cats,
            "title" => "",
            "type" => "des"
        ),
        "page_links_id" => array(
            "name" => "page_links_id",
            "std" => "",
            "title" => "链接显示分类目录ID列表（仅为\"友情链接\"及\"网址导航\"模板时有效，每个ID之前用\",\"进行分隔，为空则不显示）",
            "type" => "text"
        ),
        "page_books_id" => array(
            "name" => "page_books_id",
            "std" => "",
            "title" => "书籍显示分类目录ID列表（仅为\"书籍推荐\"模板时有效，每个ID之前用\",\"进行分隔，为空则不显示）",
            "type" => "text"
        )
    );
}



function puock_basic_page_metas()
{
    post_meta_set(pk_post_meta_basic());
}

function puock_basic_page_metas_save($post_id)
{
    save_post_meta_data($post_id, pk_post_meta_basic());
}

function puock_basic_page_meta_box()
{
    if (function_exists('add_meta_box')) {
        add_meta_box('puock_basic_page_meta_box', '基本设置', 'puock_basic_page_metas', 'page', 'normal', 'high');
    }
}

add_action('admin_menu', 'puock_basic_page_meta_box');
add_action('save_post', 'puock_basic_page_metas_save');
