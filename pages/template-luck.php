<?php
/*
Template Name: 试试手气
*/
$posts = get_posts('numberposts=1&orderby=rand');
foreach($posts as $post){
    header("Location:".get_the_permalink($post));
    break;
}