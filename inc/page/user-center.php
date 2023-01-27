<?php
if (!is_user_logged_in()) {
    wp_redirect(home_url());
    exit;
}
pk_set_custom_seo('用户中心');
$userinfo = get_userdata(get_current_user_id());
get_header();
?>

<div id="content" class="mt20 container min-height-container">
    <?php echo pk_breadcrumbs() ?>
    <div class="p-block" id="user-center">
        <div class="row row-cols-1">
            <div class="col-lg-3 col-md-12">
                <div class="list-group user-center-menus">
                    <a href="#" class="ta3 list-group-item d-flex justify-content-between align-items-start current">
                        <div class="ms-2 me-auto">
                            <div>基本信息</div>
                            <span class="sub-title d-none d-md-inline-block">基本信息</span>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-9 col-md-12 fs14">
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
                            <textarea name="description" class="form-control" rows="4"><?php echo $userinfo->description ?></textarea>
                        </div>
                    </div>
                    <div class="mb-3 text-center">
                        <button class="btn btn-primary btn-sm" type="submit">提交保存</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<?php get_footer(); ?>
