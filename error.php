<?php

include '../../../wp-blog-header.php';

$error_info = "";

pk_session_call(function () use (&$error_info) {
    $error_info = @$_SESSION['error_info'];
    unset($_SESSION['error_info']);
});

if (empty($error_info)) {
    $error_info = __('无错误信息', PUOCK);
}

get_header();
?>

<div id="content" class="mt20 container min-height-container">

    <?php echo pk_breadcrumbs() ?>

    <div class="text-center p-block  puock-text">
        <h3 class="mt20"><?php echo $error_info ?></h3>
        <div class="text-center mt20">
            <a class="a-link" href="<?php echo home_url() ?>"><i class="fa fa-home"></i>&nbsp;<?php _e('返回首页', PUOCK) ?>
            </a>
        </div>
    </div>
</div>


<?php get_footer() ?>
