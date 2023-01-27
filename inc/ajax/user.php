<?php


class Puock_Ajax_User
{
    public function __construct()
    {
        pk_ajax_register('pk_user_update_profile', array($this, 'update_profile'));
    }

    public function update_profile()
    {
        if (is_string($data = pk_get_req_data([
                'nickname' => ['name' => __('昵称', PUOCK), 'required' => true, 'remove_html' => true],
                'user_url' => ['name' => __('网站地址', PUOCK), 'remove_html' => true, 'empty' => true],
                'description' => ['name' => __('个人说明', PUOCK), 'remove_html' => true, 'empty' => true],
            ])) === true) {
            echo pk_ajax_resp_error($data);
            wp_die();
        }
        $data['ID'] = get_current_user_id();
        if (!empty($data['nickname'])) {
            $data['display_name'] = $data['nickname'];
        }
        if (!wp_update_user($data)) {
            echo pk_ajax_resp_error(__('保存失败', PUOCK));
            wp_die();
        }
        echo pk_ajax_resp(null, __('保存成功', PUOCK));
        wp_die();
    }
}

new Puock_Ajax_User();
