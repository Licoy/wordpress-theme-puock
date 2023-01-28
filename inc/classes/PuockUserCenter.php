<?php

namespace Puock\Theme\classes;

class PuockUserCenter implements IPuockClassLoad
{

    private static array $menus = [];

    public static function load()
    {
        pk_ajax_register('pk_user_update_profile', array(__CLASS__, 'update_profile'));
        self::register_basic_menus();
    }

    private static function register_basic_menus()
    {
        self::$menus['profile'] = [
            'title' => __('个人资料', PUOCK),
            'subtitle' => __('您的基本个人资料', PUOCK),
            'call' => array(__CLASS__, 'page_profile'),
        ];
    }

    public static function get_menus()
    {
        return apply_filters('pk_user_center_menus', self::$menus);
    }

    public static function register_menu($id, $title, $call, $subtitle = '', $args = array())
    {
        self::$menus[$id] = [
            'title' => $title,
            'subtitle' => $subtitle,
            'call' => $call,
        ];
    }

    public static function update_profile()
    {
        if (is_string($data = pk_get_req_data([
                'nickname' => ['name' => __('昵称', PUOCK), 'required' => true, 'remove_html' => true],
                'user_url' => ['name' => __('网站地址', PUOCK), 'remove_html' => true, 'empty' => true],
                'description' => ['name' => __('个人说明', PUOCK), 'remove_html' => true, 'empty' => true],
            ])) === true) {
            echo pk_ajax_resp_error($data);
            wp_die();
        }
        $user = wp_get_current_user();
        if (!$user) {
            echo pk_ajax_resp_error(__('请先登录', PUOCK));
            wp_die();
        }
        $data['ID'] = $user->ID;
        if ($data['nickname'] != $user->nickname) {
            $data['display_name'] = $data['nickname'];
        }
        do_action('pk_update_user_profile_before', $data);
        if (!wp_update_user($data)) {
            echo pk_ajax_resp_error(__('保存失败', PUOCK));
            wp_die();
        }
        echo pk_ajax_resp(null, __('保存成功', PUOCK));
        do_action('pk_update_user_profile_after', $data);
        wp_die();
    }

    public static function page_profile()
    {
        $userinfo = get_userdata(get_current_user_id());
        ?>
        <form action="<?php echo pk_ajax_url('pk_user_update_profile') ?>" class="ajax-form" data-no-reset>
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">ID</label>
                <div class="col-sm-10">
                    <input type="text" readonly class="form-control" value="<?php echo $userinfo->ID ?>">
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">用户名</label>
                <div class="col-sm-10">
                    <input type="text" readonly class="form-control" value="<?php echo $userinfo->user_nicename ?>">
                    <small class="c-sub">用户名不可更改</small>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">昵称</label>
                <div class="col-sm-10">
                    <input name="nickname" type="text" class="form-control" value="<?php echo $userinfo->nickname ?>">
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">网站地址</label>
                <div class="col-sm-10">
                    <input name="user_url" type="url" class="form-control" value="<?php echo $userinfo->user_url ?>">
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">个人说明</label>
                <div class="col-sm-10">
                    <textarea name="description" class="form-control"
                              rows="4"><?php echo $userinfo->description ?></textarea>
                </div>
            </div>
            <div class="mb-3 text-center">
                <button class="btn btn-primary btn-sm" type="submit">提交保存</button>
            </div>
        </form>
        <?php
    }
}
