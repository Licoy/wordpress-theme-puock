<?php


/**
 * @throws Exception
 */
function pk_front_form_validate_code_check($type = '', $code = '')
{
    if (pk_get_option('vd_type', 'img') === 'img') {
        if (!pk_captcha_validate($type, $code)) {
            throw new Exception(__('验证码错误', PUOCK));
        }
    } else {
        pk_vd_gt_validate();
    }
}

function pk_front_login_exec()
{
    if (is_string($data = pk_get_req_data([
            'username' => ['name' => __('用户名', PUOCK), 'required' => true],
            'password' => ['name' => __('密码', PUOCK), 'required' => true],
            'vd' => ['name' => __('验证码', PUOCK), 'required' => false],
            'remember' => ['name' => __('记住我', PUOCK), 'required' => false],
        ])) === true) {
        echo pk_ajax_resp_error($data);
        wp_die();
    }
    try {
        pk_front_form_validate_code_check('login', $data['vd']);
    } catch (Exception $e) {
        echo pk_ajax_resp_error($e->getMessage());
        wp_die();
    }
    $try_open = pk_is_checked('quick_login_try_max_open');
    $try_num = $try_open ? pk_get_option('quick_login_try_max_num', 3) : 0;
    $try_ban_time = $try_open ? pk_get_option('quick_login_try_max_ban_time', 10) : 0;
    if ($try_open) {
        $ip = pk_get_client_ip();
        if (!empty(get_transient('pk_login_ban_' . $ip))) {
            echo pk_ajax_resp_error(sprintf(__('登录失败次数过多，请%d分钟后再试', PUOCK), $try_ban_time));
            wp_die();
        }
    }
    $user = wp_signon([
        'user_login' => $data['username'],
        'user_password' => $data['password'],
        'remember' => $data['remember'] === 'on',
    ], is_ssl());
    if ($user instanceof WP_User) {
        wp_set_auth_cookie($user->ID, true, is_ssl());
        echo pk_ajax_resp([
            'action' => 'reload',
        ], __('登录成功', PUOCK));
    } else {
        if ($try_open) {
            $try = get_transient('pk_login_try_' . $ip) ?? 0;
            $try++;
            if ($try >= $try_num) {
                set_transient('pk_login_ban_' . $ip, 1, $try_ban_time * 60);
            echo pk_ajax_resp_error(sprintf(__('登录失败次数过多，请%d分钟后再试', PUOCK), $try_ban_time));
                wp_die();
            } else {
                set_transient('pk_login_try_' . $ip, $try, $try_ban_time * 60);
            }
        }
        echo pk_ajax_resp_error(__('账号或密码错误', PUOCK));
    }
    wp_die();
}


function pk_front_register_exec()
{
    if (is_string($data = pk_get_req_data([
            'username' => ['name' => __('用户名', PUOCK), 'required' => true],
            'email' => ['email' => __('邮箱', PUOCK), 'required' => true],
            'password' => ['name' => __('密码', PUOCK), 'required' => true],
            'vd' => ['name' => __('验证码', PUOCK), 'required' => false],
        ])) === true) {
        echo pk_ajax_resp_error($data);
        wp_die();
    }
    if (strlen($data['username']) < 5 || strlen($data['username']) > 10) {
        echo pk_ajax_resp_error(__('用户名不合法', PUOCK));
        wp_die();
    }
    if (strlen($data['password']) < 6 || strlen($data['password']) > 18) {
        echo pk_ajax_resp_error(__('密码不合法', PUOCK));
        wp_die();
    }
    if (!is_email($data['email'])) {
        echo pk_ajax_resp_error(__('邮箱不合法', PUOCK));
        wp_die();
    }
    try {
        pk_front_form_validate_code_check('register', $data['vd']);
    } catch (Exception $e) {
        echo pk_ajax_resp_error($e->getMessage());
        wp_die();
    }
    $user_id = wp_create_user($data['username'], $data['password'], $data['email']);
    if ($user_id instanceof WP_Error) {
        echo pk_ajax_resp_error($user_id->get_error_message());
    } else {
        wp_set_auth_cookie($user_id, true, is_ssl());
        echo pk_ajax_resp([
            'action' => 'reload',
        ], __('注册成功，已自动登录', PUOCK));
    }
    wp_die();
}

function pk_front_forget_password_exec()
{
    if (is_string($data = pk_get_req_data([
            'email' => ['email' => __('邮箱', PUOCK), 'required' => true],
            're-password' => ['email' => __('重复密码', PUOCK), 'required' => true],
            'password' => ['name' => __('密码', PUOCK), 'required' => true],
            'vd' => ['name' => __('验证码', PUOCK), 'required' => false],
        ])) === true) {
        echo pk_ajax_resp_error($data);
        wp_die();
    }
    if (strlen($data['password']) < 6 || strlen($data['password']) > 18) {
        echo pk_ajax_resp_error(__('密码不合法', PUOCK));
        wp_die();
    }
    if ($data['password'] !== $data['re-password']) {
        echo pk_ajax_resp_error(__('两次密码不一致', PUOCK));
        wp_die();
    }
    if (!is_email($data['email'])) {
        echo pk_ajax_resp_error(__('邮箱不合法', PUOCK));
        wp_die();
    }
    try {
        pk_front_form_validate_code_check('forget-password', $data['vd']);
    } catch (Exception $e) {
        echo pk_ajax_resp_error($e->getMessage());
        wp_die();
    }
    $user = get_user_by('email', $data['email']);
    if (empty($user)) {
        echo pk_ajax_resp_error(__('不存在该邮箱的用户', PUOCK));
        wp_die();
    }
    $code = md5($data['email'] . wp_generate_password(20, false));
    set_transient('pk_forget_password_' . $code, ['password' => $data['password'], 'email' => $data['email']], 60 * 5);
    $url = pk_ajax_url('pk_front_forget_password_reset_exec', [
        'code' => $code,
    ]);
    if (wp_mail($data['email'], __('密码重置', PUOCK) . ' - ' . pk_get_web_title(), sprintf(__('您的密码重置链接为：%s，请在5分钟内点击链接重置密码', PUOCK), $url))) {
        echo pk_ajax_resp(null, __('重置密码链接已发送至邮箱', PUOCK));
    } else {
        echo pk_ajax_resp_error(__('重置密码链接邮件发送失败', PUOCK));
    }
    wp_die();
}

function pk_front_forget_password_reset_exec()
{
    $code = $_REQUEST['code'] ?? '';
    if (empty($code)) {
        pk_ajax_result_page(false, __('密码重置失败：密码重置链接无效', PUOCK));
    }
    $info = get_transient('pk_forget_password_' . $code);
    if (empty($info)) {
        pk_ajax_result_page(false, __('密码重置失败：密码重置链接无效', PUOCK));
    }
    $user = get_user_by('email', $info['email']);
    if (empty($user)) {
        pk_ajax_result_page(false, __('密码重置失败：用户不存在', PUOCK));
    }
    delete_transient('pk_forget_password_' . $code);
    wp_set_password($info['password'], $user->ID);
    pk_ajax_result_page(true, __('密码重置成功，请返回登录', PUOCK));
}


if (pk_is_checked('open_quick_login')) {
    if (!pk_is_checked('only_quick_oauth')) {
        pk_ajax_register('pk_front_login_exec', 'pk_front_login_exec', true);
        if (get_option('users_can_register') == 1) {
            pk_ajax_register('pk_front_register_exec', 'pk_front_register_exec', true);
        }
        if (pk_is_checked('quick_login_forget_password')) {
            pk_ajax_register('pk_front_forget_password_exec', 'pk_front_forget_password_exec', true);
            pk_ajax_register('pk_front_forget_password_reset_exec', 'pk_front_forget_password_reset_exec', true);
        }
    }
    pk_ajax_register('pk_font_login_page', 'pk_front_login_page_callback', true);
}


function pk_front_login_page_callback()
{
    $redirect = $_GET['redirect'] ?? get_edit_profile_url();
    $forget_password_url = pk_ajax_url('pk_font_login_page', ['redirect' => $redirect]);
    $open_register = get_option('users_can_register') == 1;
    $only_quick_oauth = pk_is_checked('only_quick_oauth');
    $validate_type = pk_get_option('vd_type', 'img');
    ?>
    <div class="min-width-modal">
        <?php
        if (!$only_quick_oauth):
            ?>

            <form id="front-login-form" action="<?php echo pk_ajax_url('pk_front_login_exec'); ?>" method="post"
                  class="ajax-form" data-validate="<?php echo $validate_type; ?>">
                <div class="mb15">
                    <label for="_front_login_username" class="form-label"><?php _e('用户名/邮箱', PUOCK); ?></label>
                    <input type="text" name="username" class="form-control form-control-sm" id="_front_login_username"
                           data-required
                           placeholder="<?php esc_attr_e('请输入用户名或邮箱', PUOCK); ?>">
                </div>
                <div class="mb15">
                    <label for="_front_login_password" class="form-label"><?php _e('密码', PUOCK); ?></label>
                    <input type="password" name="password" class="form-control form-control-sm" data-required
                           id="_front_login_password"
                           placeholder="<?php esc_attr_e('请输入密码', PUOCK); ?>">
                </div>
                <?php if ($validate_type === 'img'): ?>
                    <div class="mb15">
                        <label for="_front_login_vd" class="form-label"><?php _e('验证码', PUOCK); ?></label>
                        <div class="row flex-row justify-content-end">
                            <div class="col-8 col-sm-7 text-end pl15">
                                <input type="text" data-required placeholder="<?php esc_attr_e('请输入验证码', PUOCK); ?>" maxlength="4"
                                       class="form-control form-control-sm t-sm captcha-input" name="vd"
                                       autocomplete="off"
                                       id="_front_login_vd">
                            </div>
                            <div class="col-4 col-sm-5 pr15">
                                <img class="captcha lazy" data-src="<?php echo pk_captcha_url('login', 100, 28) ?>"
                                     alt="<?php esc_attr_e('验证码', PUOCK); ?>">
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="mb15 form-check form-switch">
                    <input class="form-check-input" name="remember" type="checkbox" role="switch"
                           id="front-login-remember-me">
                    <label class="form-check-label" for="front-login-remember-me"> <?php _e('记住我', PUOCK); ?></label>
                </div>
                <div class="mb15 d-flex justify-content-center wh100">
                    <button class="btn btn-ssm btn-primary mr5" type="submit"><i class="fa fa-right-to-bracket"></i>
                        <?php _e('立即登录', PUOCK); ?>
                    </button>
                </div>
                <div class="mb15 d-flex justify-content-between align-content-center fs12">
                    <?php if ($open_register): ?>
                        <a class="c-sub t-hover-primary toggle-el-show-hide" data-target="#front-register-form"
                           data-modal-title="<?php esc_attr_e('注册', PUOCK); ?>"
                           data-self="#front-login-form" href="javascript:void(0)"><?php _e('还没有账号？立即注册', PUOCK); ?></a>
                    <?php endif; ?>
                    <?php if (pk_is_checked('quick_login_forget_password')): ?>
                        <a class="c-sub t-hover-primary toggle-el-show-hide" data-target="#front-forget-password-form"
                           data-modal-title="<?php esc_attr_e('找回密码', PUOCK); ?>"
                           data-self="#front-login-form" href="javascript:void(0)"><?php _e('忘记密码？立即找回密码', PUOCK); ?></a>
                    <?php endif; ?>
                </div>
            </form>

            <?php if ($open_register): ?>
            <form id="front-register-form" action="<?php echo pk_ajax_url('pk_front_register_exec'); ?>" method="post"
                  class="d-none ajax-form" data-validate="<?php echo $validate_type; ?>">
                <div class="mb15">
                    <label for="_front_register_username" class="form-label"><?php _e('用户名', PUOCK); ?></label>
                    <input type="text" name="username" class="form-control form-control-sm" data-required
                           id="_front_register_username" placeholder="<?php esc_attr_e('请输入最少5～10位的用户名', PUOCK); ?>">
                </div>
                <div class="mb15">
                    <label for="_front_register_email" class="form-label"><?php _e('邮箱', PUOCK); ?></label>
                    <input type="email" name="email" class="form-control form-control-sm" data-required
                           id="_front_register_email" placeholder="<?php esc_attr_e('请输入邮箱', PUOCK); ?>">
                </div>
                <div class="mb15">
                    <label for="_front_register_password" class="form-label"><?php _e('密码', PUOCK); ?></label>
                    <input type="password" name="password" class="form-control form-control-sm" data-required
                           id="_front_register_password" placeholder="<?php esc_attr_e('请输入6～18位字符的密码', PUOCK); ?>">
                </div>
                <?php if ($validate_type === 'img'): ?>
                    <div class="mb15">
                        <label for="_front_register_vd" class="form-label"><?php _e('验证码', PUOCK); ?></label>
                        <div class="row flex-row justify-content-end">
                            <div class="col-8 col-sm-7 text-end pl15">
                                <input type="text" data-required placeholder="<?php esc_attr_e('请输入验证码', PUOCK); ?>" maxlength="4"
                                       class="form-control form-control-sm t-sm" name="vd"
                                       autocomplete="off"
                                       id="_front_register_vd">
                            </div>
                            <div class="col-4 col-sm-5 pr15">
                                <img class="captcha lazy"
                                     data-src="<?php echo pk_captcha_url('register', 100, 28) ?>"
                                     alt="<?php esc_attr_e('验证码', PUOCK); ?>">
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="mb15 d-flex justify-content-center wh100">
                    <button class="btn btn-ssm btn-primary mr5" type="submit"><i class="fa fa-right-to-bracket"></i>
                        <?php _e('立即注册', PUOCK); ?>
                    </button>
                </div>
                <div class="mb15 d-flex justify-content-end fs12">
                    <a class="c-sub t-hover-primary toggle-el-show-hide" href="javascript:void(0)"
                       data-self="#front-register-form" data-target="#front-login-form"
                       data-modal-title="<?php esc_attr_e('登入', PUOCK); ?>"><?php _e('已有账号？立即登录', PUOCK); ?></a>
                </div>
            </form>
        <?php endif; ?>
            <?php if (pk_is_checked('quick_login_forget_password')): ?>
            <form id="front-forget-password-form" action="<?php echo pk_ajax_url('pk_front_forget_password_exec'); ?>"
                  method="post" class="d-none ajax-form" data-validate="<?php echo $validate_type; ?>">
                <div class="mb15">
                    <label for="_front_forget_password_email" class="form-label"><?php _e('邮箱', PUOCK); ?></label>
                    <input type="email" name="email" class="form-control form-control-sm" data-required
                           id="_front_forget_password_email" placeholder="<?php esc_attr_e('请输入邮箱', PUOCK); ?>">
                </div>
                <div class="mb15">
                    <label for="_front_forget_password_password" class="form-label"><?php _e('新密码', PUOCK); ?></label>
                    <input type="password" name="password" class="form-control form-control-sm" data-required
                           id="_front_forget_password_password" placeholder="<?php esc_attr_e('请输入6～18位字符的新密码', PUOCK); ?>">
                </div>
                <div class="mb15">
                    <label for="_front_forget_password_password_re" class="form-label"><?php _e('重复新密码', PUOCK); ?></label>
                    <input type="password" name="re-password" class="form-control form-control-sm" data-required
                           id="_front_forget_password_password_re" placeholder="<?php esc_attr_e('请重复输入6～18位字符的新密码', PUOCK); ?>">
                </div>
                <?php if ($validate_type === 'img'): ?>
                    <div class="mb15">
                        <label for="_front_forget_password_vd" class="form-label"><?php _e('验证码', PUOCK); ?></label>
                        <div class="row flex-row justify-content-end">
                            <div class="col-8 col-sm-7 text-end pl15">
                                <input type="text" data-required placeholder="<?php esc_attr_e('请输入验证码', PUOCK); ?>" maxlength="4"
                                       class="form-control form-control-sm t-sm" name="vd"
                                       autocomplete="off"
                                       id="_front_forget_password_vd">
                            </div>
                            <div class="col-4 col-sm-5 pr15">
                                <img class="captcha lazy"
                                     data-src="<?php echo pk_captcha_url('forget-password', 100, 28) ?>"
                                     alt="<?php esc_attr_e('验证码', PUOCK); ?>">
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="mb15 d-flex justify-content-center wh100">
                    <button class="btn btn-ssm btn-primary mr5" type="submit"><i class="fa fa-paper-plane"></i> <?php _e('发送邮件', PUOCK); ?>
                    </button>
                </div>
                <div class="mb15 d-flex justify-content-end fs12">
                    <a class="c-sub t-hover-primary toggle-el-show-hide" href="javascript:void(0)"
                       data-self="#front-forget-password-form" data-target="#front-login-form"
                       data-modal-title="<?php esc_attr_e('登入', PUOCK); ?>"><?php _e('想起密码？立即登录', PUOCK); ?></a>
                </div>
            </form>
        <?php endif;endif; ?>

        <div class="mb15">
            <p class="c-sub text-center fs12 t-separator"><?php _e('第三方登录', PUOCK); ?></p>
            <?php pk_oauth_quick_buttons(true, $redirect) ?>
        </div>

    </div>
    <?php

    wp_die();
}
