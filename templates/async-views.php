<?php
if(pk_is_checked('async_view')){
    $async_view_id = get_the_ID();
    if($async_view_id){
    echo '<script data-instant>$(function() {
  asyncCacheViews('.$async_view_id.');
})</script>';
}};