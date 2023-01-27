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
                <div class="list-group">
                    <a href="#" class="ta3 list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div>基本信息</div>
                            <span class="c-sub fs12">基本信息</span>
                        </div>
                    </a>
<!--                    <a href="#" class="ta3 list-group-item d-flex justify-content-between align-items-start">-->
<!--                        <div class="ms-2 me-auto">-->
<!--                            <div>社交账号</div>-->
<!--                            <span class="c-sub fs12">第三方社交账号绑定</span>-->
<!--                        </div>-->
<!--                    </a>-->
                </div>
            </div>
            <div class="col-lg-9 col-md-12 fs14">
                <form action="">
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
                            <input type="text" class="form-control" value="<?php echo $userinfo->nickname ?>">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-2 col-form-label">网站地址</label>
                        <div class="col-sm-10">
                            <input type="url" class="form-control" value="<?php echo $userinfo->user_url ?>">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-2 col-form-label">个人说明</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" rows="4"><?php echo $userinfo->description ?></textarea>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<?php get_footer(); ?>
