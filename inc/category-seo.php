<?php

// 分类添加字段
function category_seo_field()
{
    echo '<div class="form-field">  
                        <label for="seo-cat-keywords">' . esc_html__('SEO关键字', PUOCK) . '</label>  
            <input name="seo-cat-keywords" id="seo-cat-keywords" type="text" value="" size="40">  
                        <p>' . esc_html__('SEO关键字，多个关键字之间使用","分隔，默认显示该分类名称', PUOCK) . '</p>  
          </div>';
    echo '<div class="form-field">  
                        <label for="seo-cat-desc">' . esc_html__('SEO描述', PUOCK) . '</label>  
            <input name="seo-cat-desc" id="seo-cat-desc" type="text" value="" size="40">  
                        <p>' . esc_html__('SEO描述，默认显示该分类名称', PUOCK) . '</p>
          </div>';

}

add_action('category_add_form_fields', 'category_seo_field', 10, 2);

// 分类编辑字段
function edit_category_seo_field($tag)
{
    echo '<tr class="form-field">  
            <th scope="row"><label for="seo-cat-keywords">' . esc_html__('SEO关键字', PUOCK) . '</label></th>  
            <td>  
                <input name="seo-cat-keywords" id="seo-cat-keywords" type="text" value="';
    echo get_option('seo-cat-keywords-' . $tag->term_id) . '" size="40"/><br>  
                <span class="seo-cat-keywords">' . esc_html__('SEO关键字，多个关键字之间使用","分隔，默认显示该分类名称', PUOCK) . '</span>  
            </td>  
        </tr>';
    echo '<tr class="form-field">  
            <th scope="row"><label for="seo-cat-desc">' . esc_html__('SEO描述', PUOCK) . '</label></th>  
            <td>  
                <input name="seo-cat-desc" id="seo-cat-desc" type="text" value="';
    echo get_option('seo-cat-desc-' . $tag->term_id) . '" size="40"/><br>  
                <span class="seo-cat-desc">' . esc_html__('SEO描述，默认显示该分类名称', PUOCK) . '</span>  
            </td>  
        </tr>';
}

add_action('category_edit_form_fields', 'edit_category_seo_field', 10, 2);

// 保存数据
function cat_seo_taxonomy_save_data($term_id)
{
    if (isset($_POST['seo-cat-keywords']) && isset($_POST['seo-cat-desc'])) {
        if (!current_user_can('manage_categories')) {
            return $term_id;
        }
        update_option('seo-cat-keywords-' . $term_id, $_POST['seo-cat-keywords']);
        update_option('seo-cat-desc-' . $term_id, $_POST['seo-cat-desc']);
    }
}

add_action('created_category', 'cat_seo_taxonomy_save_data', 10, 1);
add_action('edited_category', 'cat_seo_taxonomy_save_data', 10, 1);