<?php
if(isset($_GET['exec'])){

    function pk_admin_msg_bar($msg,$type="success"){
        return '<div id="message" class="notice notice-'.$type.'  is-dismissible">
				<p><strong>'.$msg.'</strong></p></div>';
    }

    function pk_check_github_tag_version(){
        $res = wp_remote_request("https://api.github.com/repos/Licoy/wordpress-theme-puock/tags", array(
            'method'=> "GET",
            'headers'=> array('Content-Type'=>'application/json;charset=UTF-8'),
            'timeout'=> 10
        ));
        if ( is_array( $res ) && ! is_wp_error( $res ) && $res['response']['code'] == 200) {
            $tags = json_decode($res['body'], true);
            if (count($tags) > 0){
                $latest_version = (float)str_replace('v', '', $tags[0]['name']);
                if($latest_version > PUOCK_CUR_VER){
                    return pk_admin_msg_bar("检测到新版本V".$latest_version.
                        "&nbsp;<a target='_blank' 
                        href='https://github.com/Licoy/wordpress-theme-puock/releases/tag/v${latest_version}'>点此进入手动下载更新</a>");
                }
            }
            return pk_admin_msg_bar("当前版本已是最新版本");
        }
        return pk_admin_msg_bar("检测更新失败", "warning");
    }
    $get_exec = $_GET['exec'];
    if ($get_exec == "update_check"){
        echo pk_check_github_tag_version();
    }
}

?>
<h2>
    <?php echo esc_html( $menu['page_title'] ); ?>
    <code>版本：<?php echo sprintf("%.1f",PUOCK_CUR_VER) ?></code>
    <code><a href="?page=options-framework&exec=update_check">检查更新<small>(仅检测版本)</small></a></code>
    <code><a href="https://github.com/Licoy/wordpress-theme-puock#%E6%94%AF%E6%8C%81" target="_blank" rel="nofollow">赞赏支持</a></code>
    <code><a href="https://www.gnu.org/licenses/gpl-3.0.html" target="_blank" rel="nofollow">开源协议</a></code>
</h2>
