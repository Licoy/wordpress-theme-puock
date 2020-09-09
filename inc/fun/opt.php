<?php
// 点赞
function puock_post_like(){
    $id = $_POST["um_id"];
    $action = $_POST["um_action"];
    if ( $action == 'like'){
        $cookie_key = 'puock_like_'.$id;
        if(!empty($_COOKIE[$cookie_key])){
            echo json_encode(array('e'=>1,'t'=>'你已经点过赞了'));
            die;
        }
        $like_num = get_post_meta($id,'puock_like',true);
        $expire = time() + (86400);
        $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
        setcookie($cookie_key,$id,$expire,'/',$domain,false);
        if (!$like_num || !is_numeric($like_num)) {
            update_post_meta($id, 'puock_like', 1);
        }
        else {
            update_post_meta($id, 'puock_like', ($like_num + 1));
        }
        echo json_encode(array('e'=>0,'t'=>'点赞成功','d'=>get_post_meta($id,'puock_like',true)));
    }
    die;
}
add_action('wp_ajax_nopriv_puock_like', 'puock_post_like');
add_action('wp_ajax_puock_like', 'puock_post_like');

// 获取当前访问cookie是否点赞
function puock_post_is_like(){
    global $post;
    return !empty($_COOKIE['puock_like_'.$post->ID]);
}

//移除wp自带的widget
function init_unregister_widgets() {
    unregister_widget( 'WP_Widget_Recent_Comments' );
    unregister_widget( 'WP_Widget_Recent_Posts' );
    unregister_widget( 'WP_Widget_RSS' );
    unregister_widget( 'WP_Widget_Search' );
    unregister_widget( 'WP_Widget_Tag_Cloud' );
    unregister_widget( 'WP_Widget_Text' );
    unregister_widget( 'WP_Nav_Menu_Widget' );
//    unregister_widget( 'WP_Widget_Archives' );
    unregister_widget( 'WP_Widget_Calendar' );
    unregister_widget( 'WP_Widget_Categories' );
    unregister_widget( 'WP_Widget_Links' );
    unregister_widget( 'WP_Widget_Meta' );
    unregister_widget( 'WP_Widget_Pages' );
    unregister_widget( 'WP_Widget_Media_Gallery' );
    unregister_widget( 'WP_Widget_Media_Video' );
    unregister_widget( 'WP_Widget_Media_Audio' );
}
add_action( 'widgets_init', 'init_unregister_widgets' );

// 页面添加html后缀
function html_page_permalink() {
    global $wp_rewrite;
    if ( !strpos($wp_rewrite->get_page_permastruct(), '.html')){
        $wp_rewrite->page_structure = $wp_rewrite->page_structure . '.html';
    }
}

// 添加斜杠
function add_init_trailingslashit($string, $type_of_url) {
    if ( $type_of_url != 'single' && $type_of_url != 'page' )
        $string = trailingslashit($string);
    return $string;
}
//解析get参数
function get_path_query($query)
{
    $querys = explode('&', $query);
    $params = array();
    foreach ($querys as $param)
    {
        $item = explode('=', $param);
        $params[$item[0]] = $item[1];
    }
    return $params;
}

//GrAvatar头像源
$gravatar_urls = array('www.gravatar.com','0.gravatar.com','1.gravatar.com','2.gravatar.com');
function cn_avatar($avatar) {
    global $gravatar_urls;
    return str_replace( $gravatar_urls, 'cn.gravatar.com', $avatar );
}
function cn_ssl_avatar($avatar) {
    global $gravatar_urls;
    return str_replace("http://","https://",str_replace( $gravatar_urls, 'cn.gravatar.com', $avatar ));
}
function loli_ssl_avatar($avatar) {
    global $gravatar_urls;
    return str_replace("http://","https://",str_replace( $gravatar_urls, 'gravatar.loli.net', $avatar ));
}
//评论回复邮件通知
function comment_mail_notify($comment_id) {
    $comment = get_comment($comment_id);
    $parent_id = $comment->comment_parent ? $comment->comment_parent : '';
    $spam_confirmed = $comment->comment_approved;
    if ($spam_confirmed != 1) { return; }
    if ($parent_id!='' && $spam_confirmed!='spam') {
        $wp_email = 'no-reply@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));
        $to = trim(get_comment($parent_id)->comment_author_email);
        if(pk_check_email_is_sysgen($to)){ // 默认生成的第三方注册邮箱，未进行修改
            return;
        }
        if($to==$comment->comment_author_email){ return; }
        $subject = '您在 [' . get_option("blogname") . '] 的文章评论有了新的回复';
        $message = get_comment_notify_template($comment,$parent_id);
        $from = "From: \"" . get_option('blogname') . "\" <$wp_email>";
        $headers = "$from\nContent-Type: text/html; charset=" . get_option('blog_charset') . "\n";
        wp_mail( $to, $subject, $message, $headers );
    }
}
//评论添加@
function pk_comment_add_at( $text, $comment = '') {
    if( $comment->comment_parent > 0) {
        $text = "<a class='c-sub' href='#comment-{$comment->comment_parent}'>@".get_comment_author($comment->comment_parent)."&nbsp;</a>".$text;
    }
    return $text;
}
//添加短代码支持
add_action('admin_print_scripts', 'sc_quicktags');
function sc_quicktags() {
    wp_enqueue_script( 'sc_quicktags', get_stylesheet_directory_uri().'/assets/js/quicktags.js',
        array('quicktags') );
};
//压缩HTML
function wp_compress_html(){

    //禁止pre标签压缩
     function pre_no_compress($content) {
        if(preg_match_all('/<\/pre>/i', $content, $matches)) {
            $content = '<!--wp-compress-html--><!--wp-compress-html no compression-->'.$content;
            $content.= '<!--wp-compress-html no compression--><!--wp-compress-html-->';
        }
        return $content;
    }
    add_filter( "the_content", "pre_no_compress");

    function wp_compress_html_main ($buffer){
        $initial=strlen($buffer);
        $buffer=explode("<!--wp-compress-html-->", $buffer);
        $count=count ($buffer);
        $out="";
        for ($i = 0; $i <= $count; $i++){
            if (stristr($buffer[$i], '<!--wp-compress-html no compression-->')) {
                $buffer[$i]=(str_replace("<!--wp-compress-html no compression-->", " ", $buffer[$i]));
            } else {
                $buffer[$i]=(str_replace("\t", " ", $buffer[$i]));
                $buffer[$i]=(str_replace("\n\n", "\n", $buffer[$i]));
                $buffer[$i]=(str_replace("\n", "", $buffer[$i]));
                $buffer[$i]=(str_replace("\r", "", $buffer[$i]));
                while (stristr($buffer[$i], '  ')) {
                    $buffer[$i]=(str_replace("  ", " ", $buffer[$i]));
                }
            }
            $out.=$buffer[$i];
        }
        $final=strlen($out);
        $savings=($initial-$final)/$initial*100;
        $savings=round($savings, 2);
        $info = "<!--压缩前为:{$initial}bytes;压缩后为:{$final}bytes;节约:{$savings}%-->";
        return $out.$info;
    }

    ob_start("wp_compress_html_main");
}
//跳转链接
function pk_go_link($url){
    if(pk_is_cur_site($url)){
        return $url;
    }
    return get_template_directory_uri().'/inc/go.php?to='.$url;
}
//检测链接是否属于本站
function pk_is_cur_site($url){
    if(strpos($url,home_url())===0){
        return true;
    }
    return false;
}
//链接新标签页打开
function pk_link_blank($content){
    return str_replace("<a href=\"", "<a target=\"_blank\" href=\"".get_template_directory_uri()."/inc/go.php?to=",$content);
}