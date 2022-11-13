<?php

function pk_front_login_exec()
{
    if (is_string($data = pk_get_req_data([
            'username' => ['name' => '用户名', 'required' => true],
            'password' => ['name' => '密码', 'required' => true],
            'vd' => ['name' => '验证码', 'required' => true],
        ])) === true) {
        echo pk_ajax_resp_error($data);
        wp_die();
    }
    $user = wp_signon([
        'user_login' => $data['username'],
        'user_password' => $data['password'],
        'remember' => true
    ],is_ssl());
    if ($user instanceof WP_User) {
        wp_set_auth_cookie($user->ID, true, is_ssl());
        echo pk_ajax_resp([
            'action' => 'reload'
        ], '登录成功');
    } else {
        echo pk_ajax_resp_error('账号或密码错误');
    }
    wp_die();
}

pk_ajax_register('pk_front_login_exec', 'pk_front_login_exec', true);


pk_ajax_register('pk_font_login_page', 'pk_front_login_page_callback', true);


function pk_front_login_page_callback()
{
    $redirect = $_GET['redirect'] ?? get_edit_profile_url();
    ?>

    <form id="front-login-form" action="<?php echo pk_ajax_url('pk_front_login_exec'); ?>" method="post"
          class="ajax-form">
        <div class="mb15">
            <label for="_front_login_username" class="form-label">用户名/邮箱</label>
            <input type="text" name="username" class="form-control form-control-sm" id="_front_login_username"
                   data-required
                   placeholder="请输入用户名或邮箱">
        </div>
        <div class="mb15">
            <label for="_front_login_password" class="form-label">密码</label>
            <input type="password" name="password" class="form-control form-control-sm" data-required
                   id="_front_login_password"
                   placeholder="请输入密码">
        </div>
        <div class="mb15">
            <label for="_front_login_vd" class="form-label">验证码</label>
            <div class="row flex-row justify-content-end">
                <div class="col-8 col-sm-7 text-right pl15">
                    <input type="text" data-required placeholder="请输入验证码" maxlength="4"
                           class="form-control form-control-sm t-sm" name="vd"
                           autocomplete="off"
                           id="_front_login_vd">
                </div>
                <div class="col-4 col-sm-5 pr15">
                    <img class="captcha" src="<?php echo pk_captcha_url('login', 100, 28) ?>" alt="验证码">
                </div>
            </div>
        </div>
        <div class="mb15 d-flex justify-content-center wh100">
            <button class="btn btn-ssm btn-primary mr5" type="submit"><i class="fa fa-right-to-bracket"></i> 立即登录
            </button>
        </div>
        <div class="mb15 d-flex justify-content-between align-content-center fs12">
            <a class="c-sub t-hover-primary toggle-el-show-hide" data-target="#front-register-form"
               data-self="#front-login-form" href="javascript:void(0)">还没有账号？立即注册</a>
            <a class="c-sub t-hover-primary" href="javascript:void(0)">忘记密码？立即找回密码</a>
        </div>
    </form>

    <form id="front-register-form" action="<?php echo pk_ajax_url('pk_front_register_exec'); ?>" method="post"
          class="d-none ajax-form">
        <div class="mb15">
            <label for="_front_register_email" class="form-label">邮箱</label>
            <input type="email" name="email" class="form-control form-control-sm" data-required
                   id="_front_register_email" placeholder="请输入邮箱">
        </div>
        <div class="mb15">
            <label for="_front_register_password" class="form-label">密码</label>
            <input type="password" name="password" class="form-control form-control-sm" data-required
                   id="_front_register_password" placeholder="请输入密码">
        </div>
        <div class="mb15 d-flex justify-content-center wh100">
            <button class="btn btn-ssm btn-info mr5" type="submit"><i class="fa fa-right-to-bracket"></i> 立即注册
            </button>
        </div>
        <div class="mb15 d-flex justify-content-end fs12">
            <a class="c-sub t-hover-primary toggle-el-show-hide" href="javascript:void(0)"
               data-self="#front-register-form" data-target="#front-login-form">已有账号？立即登录</a>
        </div>
    </form>

    <div class="mb15">
        <p class="c-sub text-center fs12 t-separator">第三方登录</p>
        <?php pk_oauth_quick_buttons(true, $redirect) ?>
    </div>

    <?php

    wp_die();
}
