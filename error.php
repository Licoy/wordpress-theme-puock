<?php

include '../../../wp-blog-header.php';

get_header();

$error_info = $_SESSION['error_info'];
unset($_SESSION['error_info']);

if (empty($error_info)) {
    $error_info = __('无错误信息', PUOCK);
}

?>

<div id="content" class="mt20 container min-height-container">

    <?php echo pk_breadcrumbs() ?>

    <div class="text-center p-block  puock-text">
        <h3 class="mt20"><?php echo $error_info ?></h3>
        <h5 class="mt20"><span id="time-count-down">3</span><?php _e('秒后即将跳转至首页', PUOCK) ?></h5>
        <div class="text-center mt20">
            <a class="a-link" href="<?php echo home_url() ?>"><i class="czs-home-l"></i>&nbsp;<?php _e('返回首页', PUOCK) ?>
            </a>
        </div>
    </div>
    <script>
        var timeCountDownS = 3;
        var timeCountDownVal = 3;
        timeCountDownVal = setInterval(function () {
            $("#time-count-down").text(--timeCountDownS);
        }, 1000);
        setTimeout(function () {
            window.clearInterval(timeCountDownVal);
            window.location = '/';
        }, 3000);
    </script>
</div>


<?php get_footer() ?>
