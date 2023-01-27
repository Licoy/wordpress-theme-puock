<?php

include '../../../../wp-blog-header.php';
pk_set_custom_seo("链接跳转");
$url = @$_GET['to'];
$name = @$_GET['name'];
if (!empty($name)) {
    $name = base64_decode(str_replace(' ','+',$name));
}
$error = null;
if (empty($url)) {
    $error = "目标网址为空，无法进行跳转";
} else {
    $url = htmlentities(base64_decode($url));
    if (strpos($url, "https://") !== 0 && strpos($url, "http://") !== 0) {
        $error = "跳转链接协议有误";
    } else {
        if (strpos($url, home_url()) === 0) {
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
        <h3 class="mt20">跳转提示</h3>
        <?php if (!empty($error)): ?>
            <p class="mt20"><?php echo $error ?></p>
        <?php else: ?>
            <p class="mt20">
                <span>您即将离开<?php echo get_bloginfo('name') ?>跳转至</span>
                <a class="a-link text-line" rel="nofollow"
                   href="<?php echo $url ?>"><?php echo empty($name) ? $url : $name; ?></a><span> ，确定进入吗？</span>
            </p>
        <?php endif; ?>
        <div class="text-center mt20">
            <a rel="nofollow" href="<?php echo $url; ?>" class="btn btn-ssm btn-primary"><i
                        class="fa-regular fa-paper-plane"></i>&nbsp;立即进入</a>
            <a href="<?php echo home_url() ?>" class="btn btn-ssm btn-secondary"><i
                        class="fa fa-home"></i>&nbsp;返回首页</a>
        </div>
    </div>
</div>


<?php get_footer() ?>
