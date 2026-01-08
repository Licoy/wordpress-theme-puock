<?php

include '../../../../wp-blog-header.php';
pk_set_custom_seo(__("链接跳转", PUOCK));
$url = $_GET['to'] ?? '';
$name = $_GET['name'] ?? '';
if (!empty($name)) {
    $name = base64_decode(str_replace(' ','+',$name));
}
$error = null;
if (empty($url)) {
    $error = __("目标网址为空，无法进行跳转", PUOCK);
} else {
    $url = htmlentities(base64_decode($url));
    if (strpos($url, "https://") !== 0 && strpos($url, "http://") !== 0) {
        $error = __("跳转链接协议有误", PUOCK);
    } else {
        if (pk_is_cur_site($url)) {
            header("Location:" . $url);
            exit();
        }
    }
}

get_header();


?>

<div id="content" class="mt20 container min-height-container">

    <?php echo pk_breadcrumbs() ?>

    <div class="text-center p-block puock-text">
        <h3 class="mt20"><?php _e('跳转提示', PUOCK) ?></h3>
        <?php if (!empty($error)): ?>
            <p class="mt20"><?php echo $error ?></p>
        <?php else: ?>
            <p class="mt20">
                <span><?php echo sprintf(__('您即将离开%s跳转至', PUOCK), get_bloginfo('name')); ?></span><?php echo empty($name) ? $url : $name; ?><span><?php _e('，确定进入吗？', PUOCK); ?></span>
            </p>
        <?php endif; ?>
        <div class="text-center mt20">
            <a rel="nofollow" href="<?php echo $url; ?>" class="btn btn-ssm btn-primary"><i
                        class="fa-regular fa-paper-plane"></i>&nbsp;<?php _e('立即进入', PUOCK) ?></a>
            <a href="<?php echo home_url() ?>" class="btn btn-ssm btn-secondary"><i
                        class="fa fa-home"></i>&nbsp;<?php _e('返回首页', PUOCK) ?></a>
        </div>
    </div>
</div>


<?php get_footer() ?>
