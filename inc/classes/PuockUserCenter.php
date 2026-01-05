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

        self::$menus['oauth'] = [
            'title' => __('账号绑定', PUOCK),
            'subtitle' => __('绑定或解绑第三方账号', PUOCK),
            'call' => array(__CLASS__, 'page_oauth'),
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
                    <div class="row align-items-center">
                        <div class="col-4">
                            <input type="text" readonly class="form-control" value="<?php echo $userinfo->ID ?>">
                        </div>
                        <div class="col-8 d-flex">
                            <?php if (current_user_can('administrator')): ?>
                                <a data-no-instant href="<?php echo get_admin_url() ?>" type="button" class="btn btn-ssm btn-primary me-1">
                                    <i class="fa fa-magic"></i>
                                    <span><?php _e('WP后台', PUOCK) ?></span>
                                </a>
                            <?php endif; ?>
                            <button type="button"
                                    onclick="layer.confirm('<?php _e('确认注销登陆吗？', PUOCK) ?>',function (){window.Puock.goUrl('<?php echo wp_logout_url('/'); ?>')})"
                                    class="btn btn-ssm btn-danger">
                                <i class="fa fa-sign-out"></i>
                                <span><?php _e('注销登录', PUOCK) ?></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label"><?php _e('用户名', PUOCK) ?></label>
                <div class="col-sm-10">
                    <input type="text" readonly class="form-control" value="<?php echo $userinfo->user_nicename ?>">
                    <small class="c-sub"><?php _e('用户名不可更改', PUOCK) ?></small>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label"><?php _e('昵称', PUOCK) ?></label>
                <div class="col-sm-10">
                    <input name="nickname" type="text" class="form-control" value="<?php echo $userinfo->nickname ?>">
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label"><?php _e('网站地址', PUOCK) ?></label>
                <div class="col-sm-10">
                    <input name="user_url" type="url" class="form-control" value="<?php echo $userinfo->user_url ?>">
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label"><?php _e('个人说明', PUOCK) ?></label>
                <div class="col-sm-10">
                    <textarea name="description" class="form-control"
                              rows="4"><?php echo $userinfo->description ?></textarea>
                </div>
            </div>
            <div class="mb-3 text-center">
                <button class="btn btn-primary btn-sm" type="submit"><?php _e('提交保存', PUOCK) ?></button>
            </div>
        </form>
        <?php
    }

    public static function page_oauth()
    {
        $user = wp_get_current_user();
        $oauth_list = function_exists('pk_oauth_list') ? pk_oauth_list($user) : [];
        $redirect = home_url('/uc/oauth');

        $enabled_oauth_list = [];
        foreach ($oauth_list as $item_key => $item_val) {
            if (!pk_oauth_is_enabled($item_key, $item_val)) {
                continue;
            }
            $enabled_oauth_list[$item_key] = $item_val;
        }
        ?>
        <div class="mb-3">
            <h5 class="mb-3"><?php _e('第三方账号绑定', PUOCK); ?></h5>

            <?php if (empty($enabled_oauth_list)): ?>
                <div class="alert alert-warning mb-0">
                    <?php _e('站点暂未开启任何第三方登录/绑定方式', PUOCK); ?>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column gap-2 w-100">
                    <?php foreach ($enabled_oauth_list as $item_key => $item_val):
                        $bind_url = pk_oauth_url_page_ajax($item_key, $redirect);
                        $unbind_url = pk_oauth_clear_bind_url2($item_key, $redirect);

                        $is_bound = !empty($item_val['openid']);
                        $label = (string)($item_val['label'] ?? $item_key);
                        $color_type = (string)($item_val['color_type'] ?? 'primary');
                        $icon = isset($item_val['icon']) ? (string)$item_val['icon'] : '';
                        ?>
                        <div class="pk-border-1 rounded px-3 py-2 d-flex align-items-center justify-content-between flex-wrap gap-3 w-100">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <?php if ($icon && (strpos($icon, 'http://') === 0 || strpos($icon, 'https://') === 0 || strpos($icon, '//') === 0)) : ?>
                                    <img
                                        src="<?php echo esc_url($icon); ?>"
                                        alt="<?php echo esc_attr($label); ?>"
                                        width="22"
                                        height="22"
                                        class="rounded-circle flex-shrink-0"
                                    />
                                <?php elseif ($icon) : ?>
                                    <i class="<?php echo esc_attr($icon); ?> text-<?php echo esc_attr($color_type); ?> fs-5"></i>
                                <?php endif; ?>

                                <div class="fw-semibold lh-sm">
                                    <?php echo esc_html($label); ?>
                                </div>

                                <?php if ($is_bound) : ?>
                                    <span class="badge bg-success"><?php _e('已绑定', PUOCK); ?></span>
                                <?php else : ?>
                                    <span class="badge bg-secondary"><?php _e('未绑定', PUOCK); ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex align-items-center justify-content-end">
                                <?php if ($is_bound) : ?>
                                    <a
                                        href="<?php echo esc_url($unbind_url); ?>"
                                        class="btn btn-sm btn-outline-danger px-2 py-0"
                                        onclick="return confirm('<?php echo esc_js(__('确认解除绑定吗？', PUOCK)); ?>');"
                                    >
                                        <?php _e('解除绑定', PUOCK); ?>
                                    </a>
                                <?php else : ?>
                                    <a
                                        href="<?php echo esc_url($bind_url); ?>"
                                        class="btn btn-sm btn-outline-<?php echo esc_attr($color_type); ?> px-2 py-0"
                                        target="_blank"
                                    >
                                        <?php _e('立即绑定', PUOCK); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="c-sub fs12">
                    <?php _e('绑定成功后，可使用第三方账号直接登录。', PUOCK); ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
}
