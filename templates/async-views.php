<?php
if (pk_is_checked('async_view')) {
    $async_view_id = get_the_ID();
    if ($async_view_id) {
        echo '<script ' . (pk_is_pjax() ? 'data-instant' : '') . '>$(function() {window.Puock.asyncCacheViews(' . $async_view_id . ')})</script>';
    }
}
