<?php

abstract class puockWidgetBase extends WP_Widget{

    public static $puock = 'Puock主题';

    protected $title = "标题";

    protected $pre_title = '显示近期的';

    function __construct() {
        $this->WP_Widget($this->get_class_name(), self::$puock." ".$this->title,
            array('description' => $this->pre_title.$this->title));
    }

    public function html_gen($instance, $title, $key, $type='input', $showLabel=true){
        $fid = $this->get_field_id($key);
        $fname = $this->get_field_name($key);
        $out = "<p>";
        if($showLabel){
            $out .= "<label for='{$fid}'>{$title}：</label>";
        }
        if($type=='input'){
            $out .= "<input class='widefat' id='{$fid}' type='text' name='{$fname}'
                   value='".(@$instance[$key])."' />";
        }
        if($type=='cats'){
            $out .= wp_dropdown_categories(array('name' => $fname,'echo'=>0,
                'show_option_all' => '全部分类', 'hide_empty'=>0, 'hierarchical'=>1, 'selected'=>@$instance[$key]));
        }
        if($type=='text'){
            $out .= '<textarea class="monospace widefat" rows="10" cols="40" id="'.($fid).'" 
                name="'.$fname.'">'.@$instance[$key].'</textarea>';
        }
        if($type=='checkbox'){
            $use = (isset($instance[$key]) && $instance[$key]=='on') ? 'checked' : '';
            $out .= "<input id='{$fid}'
                name='{$fname}' type='checkbox' ".$use."/>
                <label for='{$fid}'>&nbsp;{$title}</label>";
        }
        $out .= "</p>";
        echo $out;
    }

    public function default_value($instance){
        $args = array();
        foreach ($this->get_fields() as $val){
            if(isset($val['val']) && !empty($val['val'])){
                $args[$val['id']] = $val['val'];
            }
        }
        return wp_parse_args( (array) $instance, $args);
    }

    public function get_fields(){
        return array();
    }

    /**
     * 合并公用字段
     * @param $array
     * @return array
     */
    public function merge_common_fields($array){
        return array_merge($array,array(
            array('id'=>'hide_title', 'val'=>0),
            array('id'=>'icon', 'val'=>'czs-plugin'),
            array('id'=>'classes', 'val'=>''),
        ));
    }

    /**
     * 合并公用form表单
     * @param $instance
     */
    public function merge_common_form($instance){
        $this->html_gen($instance, '隐藏标题', 'hide_title','checkbox',false);
        $this->html_gen($instance, '图标类', 'icon');
        $this->html_gen($instance, '区块class类', 'classes');
    }

    function update( $cur, $old ) {
        foreach ($this->get_fields() as $val){
            if(isset($val['strip']) && $val['strip']){
                $old[$val['id']] = strip_tags($cur[$val['id']]);
            }else{
                $old[$val['id']] = $cur[$val['id']];
            }
        }
        return $old;
    }

    //获取类名
    abstract function get_class_name();

    //是否勾选
    public function is_checked($val){
        return 'on' === $val;
    }

    //获取icon
    public function get_icon($val,$default='czs-plugin'){
        if(!empty($val)){
            return $val;
        }
        return $default;
    }

    /**
     * 获取公用区块顶部代码
     * @param $instance
     */
    public function get_common_widget_header($instance){
        ?>

        <div class="p-block <?php echo $instance['classes'] ?>">
            <?php if(!$this->is_checked($instance['hide_title'])): ?>
                <div>
                <span class="t-lg border-bottom border-primary
                puock-text pb-2"><i class="<?php echo $this->get_icon($instance['icon']) ?> mr-1"></i><?php echo $instance['title'] ?></span>
                </div>
            <?php endif; ?>
            <div class="<?php if(!$this->is_checked($instance['hide_title'])): ?>mt20<?php endif; ?>">

        <?php
    }

    /**
     * 获取公用区块底部代码
     * @param $instance
     */
    public function get_common_widget_footer($instance){
        echo '</div></div>';
    }

    /**
    * 获取数字值
    * @param $instance
    * @param $key
    * @param int $default
     * @return int
     */
    public function get_num_val($instance, $key, $default=0){
        if(isset($instance[$key]) && !empty($instance[$key]) && is_numeric($instance[$key])){
            return $instance[$key];
        }
        foreach ($this->get_fields() as $f){
            if($f['id']==$key && !empty($f[$key]) && is_numeric($f[$key])){
                return $f[$key];
            }
        }
        return $default;
    }

}

//热门文章
class puockHotPost extends puockWidgetBase {


    protected $title = "热门文章";

    protected $pre_title = "根据阅读量显示最近的";

    function get_fields(){
        return $this->merge_common_fields(array(
            array('id'=>'title','strip'=>true, 'val'=>$this->title),
            array('id'=>'nums', 'val'=>5),
            array('id'=>'days', 'val'=>31),
        ));
    }

    function form( $instance ) {
        $instance = $this->default_value($instance);
        $this->html_gen($instance, '标题', 'title');
        $this->html_gen($instance, '显示篇数', 'nums');
        $this->html_gen($instance, '最近N天内', 'days');
        $this->merge_common_form($instance);
    }

    function get_class_name()
    {
        return __CLASS__;
    }

    function widget( $args, $instance ){
        $days = $this->get_num_val($instance, 'days');
        $nums = $this->get_num_val($instance, 'nums');
        $posts = get_views_most_post($days, $nums);
        $out = "";
        foreach ($posts as $post){
            $out .= '<div class="media-link mt20">
                    <h2 class="t-lg t-line-1" title="'.get_the_title($post).'">
                        <i class="czs-angle-right-l t-sm c-sub mr-1"></i>
                        <a class="a-link t-w-400 t-md" title="'.get_the_title($post).'"
                         href="'.get_permalink($post).'">'.get_the_title($post).'</a>
                    </h2>
                </div>';
        }
        $this->get_common_widget_header($instance);
        echo $out;
        $this->get_common_widget_footer($instance);
     }
}
add_action( 'widgets_init', function (){ register_widget('puockHotPost'); });

//最新文章
class puockNewPost extends puockWidgetBase {

    protected $title = "最新文章";

    function get_class_name()
    {
        return __CLASS__;
    }

    function get_fields(){
        return $this->merge_common_fields(array(
            array('id'=>'title','strip'=>true, 'val'=>$this->title),
            array('id'=>'days', 'val'=>31),
            array('id'=>'nums', 'val'=>5),
        ));
    }

    function form( $instance ) {
        $instance = $this->default_value($instance);
        $this->html_gen($instance, '标题', 'title');
        $this->html_gen($instance, '最近N天内', 'days');
        $this->html_gen($instance, '显示篇数', 'nums');
        $this->merge_common_form($instance);
    }

    function widget( $args, $instance ){
        global $wpdb;
        $days = $this->get_num_val($instance, 'days');
        $nums = $this->get_num_val($instance, 'nums');
        $sql = "SELECT * FROM $wpdb->posts WHERE post_type = 'post'
                AND post_status = 'publish' AND TO_DAYS(now()) - TO_DAYS(post_date) < {$days}
                ORDER BY ID DESC LIMIT 0 , {$nums} ";
        $posts = $wpdb->get_results($sql);
        $out = "";
        foreach ($posts as $post){
            $out .= '<div class="media-link mt20">
                    <h2 class="t-lg t-line-1" title="'.get_the_title($post).'">
                        <i class="czs-angle-right-l t-sm c-sub mr-1"></i>
                        <a class="a-link t-w-400 t-md" title="'.get_the_title($post).'"
                         href="'.get_permalink($post).'">'.get_the_title($post).'</a>
                    </h2>
                </div>';
        }
        $this->get_common_widget_header($instance);
        echo $out;
        $this->get_common_widget_footer($instance);
     }

}
add_action( 'widgets_init', function (){ register_widget('puockNewPost'); });

//热评文章
class puockHotCommentPost extends puockWidgetBase {

    protected $title = "热评文章";

    function get_class_name()
    {
        return __CLASS__;
    }

    function get_fields(){
        return $this->merge_common_fields(array(
            array('id'=>'title','strip'=>true, 'val'=>$this->title),
            array('id'=>'days', 'val'=>31),
            array('id'=>'nums', 'val'=>5),
        ));
    }

    function form( $instance ) {
        $instance = $this->default_value($instance);
        $this->html_gen($instance, '标题', 'title');
        $this->html_gen($instance, '最近N天内', 'days');
        $this->html_gen($instance, '显示篇数', 'nums');
        $this->merge_common_form($instance);
    }

    function widget( $args, $instance ){
        global $wpdb;
        $days = $this->get_num_val($instance, 'days');
        $nums = $this->get_num_val($instance, 'nums');
        $sql = "SELECT * FROM $wpdb->posts WHERE post_type = 'post' 
                AND post_status = 'publish' AND TO_DAYS(now()) - TO_DAYS(post_date) < {$days}
                ORDER BY comment_count DESC LIMIT 0 , {$nums} ";
        $posts = $wpdb->get_results($sql);
        $out = "";
        foreach ($posts as $post){
            $out .= '<div class="media-link mt20">
                    <h2 class="t-lg t-line-1" title="'.get_the_title($post).'">
                        <i class="czs-angle-right-l t-sm c-sub mr-1"></i>
                        <a class="a-link t-w-400 t-md" title="'.get_the_title($post).'"
                         href="'.get_permalink($post).'">'.get_the_title($post).'</a>
                    </h2>
                </div>';
        }
        $this->get_common_widget_header($instance);
        echo $out;
        $this->get_common_widget_footer($instance);
    }
}
add_action( 'widgets_init', function (){ register_widget('puockHotCommentPost'); });

//读者墙
class puockReadPerson extends puockWidgetBase {

    protected $title = "读者墙";

    protected $pre_title = "展示网站的读者形成";

    function get_class_name()
    {
        return __CLASS__;
    }

    function get_fields(){
        return $this->merge_common_fields(array(
            array('id'=>'title','strip'=>true, 'val'=>$this->title),
            array('id'=>'nums', 'val'=>10),
            array('id'=>'days', 'val'=>31),
        ));
    }

    function form( $instance ) {
        $instance = $this->default_value($instance);
        $this->html_gen($instance, '标题', 'title');
        $this->html_gen($instance, '显示数量', 'nums');
        $this->html_gen($instance, '最近N天内', 'days');
        $this->merge_common_form($instance);
    }

    function widget( $args, $instance ){
        global $wpdb;
        $days = $this->get_num_val($instance, 'days',31);
        $nums = $this->get_num_val($instance, 'nums');
        $sql = "SELECT count(comment_ID) as num, comment_author_email as mail,comment_author as `name`,comment_author_url as url
                FROM $wpdb->comments WHERE user_id !=1 AND TO_DAYS(now()) - TO_DAYS(comment_date) < {$days}
                 group by comment_author_email order by num desc limit 0,{$nums}";
        $authors = $wpdb->get_results($sql);
        $this->get_common_widget_header($instance); ?>
        <div class="row puock-text">
            <?php foreach ($authors as $author): ?>
             <div class="col col-12 col-lg-6 pl-0">
                 <div class="p-2 text-truncate text-nowrap">
                    <a href="<?php echo empty($author->url) ? 'javascript:void(0)':pk_go_link($author->url) ?>" class="a-link"
                        <?php echo empty($author->url) ? '':'target="_blank"' ?> rel="nofollow">
                        <img class="md-avatar" src="<?php echo get_avatar_url($author->mail) ?>" alt="<?php echo $author->name?>">
                        <span class="t-sm"><?php echo $author->name?></span>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php  $this->get_common_widget_footer($instance);
     }
}
add_action( 'widgets_init', function (){ register_widget('puockReadPerson'); });

//最新评论
class puockNewComment extends puockWidgetBase {

    protected $title = "最新评论";

    protected $pre_title = "展示网站的";

    function get_class_name()
    {
        return __CLASS__;
    }

    function get_fields(){
        return $this->merge_common_fields(array(
            array('id'=>'title','strip'=>true, 'val'=>$this->title),
            array('id'=>'nums', 'val'=>10),
        ));
    }

    function form( $instance ) {
        $instance = $this->default_value($instance);
        $this->html_gen($instance, '标题', 'title');
        $this->html_gen($instance, '显示数量', 'nums');
        $this->merge_common_form($instance);
    }

    function widget( $args, $instance ){
        global $wpdb;
        $nums = $this->get_num_val($instance, 'nums');
        $sql = "SELECT comment_ID as id,comment_post_ID as pid,comment_author_email as mail,comment_author as `name`,comment_author_url as url,comment_content as text
                FROM $wpdb->comments WHERE user_id !=1 and comment_approved=1 order by comment_date desc limit 0,{$nums}";
        $comments = $wpdb->get_results($sql);
        $this->get_common_widget_header($instance); ?>
        <div class="min-comments t-md">
            <?php foreach ($comments as $comment): $text=convert_smilies( $comment->text ); ?>
             <div class="comment t-md t-line-1">
                <img class="min-avatar" src="<?php echo get_avatar_url($comment->mail) ?>" alt="<?php echo $comment->name ?>">
                <a class="puock-link" href="<?php echo get_permalink($comment->pid).'#comment-'.$comment->id ?>">
                <span class="ta3 link-hover"><?php echo $comment->name ?></span></a>
                <span class="c-sub t-w-400"><?php echo $text ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php  $this->get_common_widget_footer($instance);
     }
}
add_action( 'widgets_init', function (){ register_widget('puockNewComment'); });

//增强文本
class puockStrongText extends puockWidgetBase {

    protected $title = "HTML文本";

    protected $pre_title = "支持HTML/JS/CSS";

    function get_class_name()
    {
        return __CLASS__;
    }

    function get_fields(){
        return $this->merge_common_fields(array(
            array('id'=>'title','strip'=>true, 'val'=>$this->title),
            array('id'=>'content', 'val'=>''),
        ));
    }

    function form( $instance ) {
        $instance = $this->default_value($instance);
        $this->html_gen($instance, '标题', 'title');
        $this->html_gen($instance, '内容', 'content','text');
        $this->merge_common_form($instance);
    }

    function widget( $args, $instance ){
        $this->get_common_widget_header($instance);
        echo '<div class="puock-text t-md">'.$instance['content'].'</div>';
        $this->get_common_widget_footer($instance);
     }
}
add_action( 'widgets_init', function (){ register_widget('puockStrongText'); });

//搜索框
class puockSearch extends puockWidgetBase {

    protected $title = "搜索框";

    protected $pre_title = "提供便捷快速的";

    function get_class_name()
    {
        return __CLASS__;
    }

    function get_fields(){
        return array(
            array('id'=>'title','strip'=>true, 'val'=>'文章搜索'),
            array('id'=>'pl', 'val'=>'输入关键字回车搜索'),
            array('id'=>'hide_title', 'val'=>0),
        );
    }

    function form( $instance ) {
        $instance = $this->default_value($instance);
        $this->html_gen($instance, '标题', 'title');
        $this->html_gen($instance, '搜索框预留文字', 'pl');
        $this->html_gen($instance, '隐藏标题', 'hide_title','checkbox',false);
    }

    function widget( $args, $instance ){ ?>
        <div class="p-block">
            <?php if(!$this->is_checked($instance['hide_title'])): ?>
            <div>
                <span class="t-lg border-bottom border-primary
                puock-text pb-2"><i class="czs-search-l mr-1"></i><?php echo $instance['title'] ?></span>
            </div>
            <?php endif; ?>
            <div class="<?php if(!$this->is_checked($instance['hide_title'])): ?>mt20<?php endif; ?>">
                <form action="<?php echo home_url() ?>" method="get">
                    <div class="input-group">
                        <input type="text" name="s" class="form-control t-md" placeholder="<?php echo $instance['pl'] ?>">
                    </div>
                </form>
            </div>
        </div>
   <?php }
}
add_action( 'widgets_init', function (){ register_widget('puockSearch'); });

//随机文章
class puockRandomPost extends puockWidgetBase {


    protected $title = "随机文章";

    protected $pre_title = "显示指定范围内的";

    function get_fields(){
        return $this->merge_common_fields(array(
            array('id'=>'title','strip'=>true, 'val'=>$this->title),
            array('id'=>'nums', 'val'=>5),
            array('id'=>'days', 'val'=>31),
        ));
    }

    function form( $instance ) {
        $instance = $this->default_value($instance);
        $this->html_gen($instance, '标题', 'title');
        $this->html_gen($instance, '显示篇数', 'nums');
        $this->html_gen($instance, '最近N天内', 'days');
        $this->merge_common_form($instance);
    }

    function get_class_name()
    {
        return __CLASS__;
    }

    function widget( $args, $instance ){
        global $wpdb;
        $days = $this->get_num_val($instance, 'days');
        $nums = $this->get_num_val($instance, 'nums');
        $sql = "SELECT * FROM $wpdb->posts WHERE post_type = 'post'
                AND post_status = 'publish' AND TO_DAYS(now()) - TO_DAYS(post_date) < {$days}
                ORDER BY rand() DESC LIMIT 0 , {$nums} ";
        $posts = $wpdb->get_results($sql);
        $out = "";
        foreach ($posts as $post){
            $out .= '<div class="media-link mt20">
                    <h2 class="t-lg t-line-1" title="'.get_the_title($post).'">
                        <i class="czs-angle-right-l t-sm c-sub mr-1"></i>
                        <a class="a-link t-w-400 t-md" title="'.get_the_title($post).'"
                         href="'.get_permalink($post).'">'.get_the_title($post).'</a>
                    </h2>
                </div>';
        }
        $this->get_common_widget_header($instance);
        echo $out;
        $this->get_common_widget_footer($instance);
     }
}
add_action( 'widgets_init', function (){ register_widget('puockRandomPost'); });

//关于博主
class puockAboutAuthor extends puockWidgetBase {


    protected $title = "关于博主";

    protected $pre_title = "显示博客的主人-";

    function get_fields(){
        return $this->merge_common_fields(array(
            array('id'=>'title','strip'=>true, 'val'=>$this->title),
            array('id'=>'name', 'val'=>get_bloginfo('name')),
            array('id'=>'email', 'val'=>get_bloginfo('admin_email')),
            array('id'=>'des', 'val'=>get_bloginfo('description')),
            array('id'=>'cover', 'val'=>pk_get_static_url().'/assets/img/show/head-cover.jpg'),
        ));
    }

    function form( $instance ) {
        $instance = $this->default_value($instance);
        $this->html_gen($instance, '标题', 'title');
        $this->html_gen($instance, '博主名字', 'name');
        $this->html_gen($instance, '介绍(支持html/js)', 'des','text');
        $this->html_gen($instance, '邮箱(用于获取头像)', 'email');
        $this->html_gen($instance, '顶部背景图url', 'cover');
        $this->merge_common_form($instance);
    }

    function get_class_name()
    {
        return __CLASS__;
    }

    function widget( $args, $instance ){
        global $wpdb;
        $name = $instance['name'];
        $des = $instance['des'];
        $email = $instance['email'];
        $cover = $instance['cover'];
        $comment_num = $wpdb->get_var("SELECT COUNT(comment_ID) FROM $wpdb->comments");
        ?>
        <div class="widget-puock-author widget">
            <div class="header" style="background-image: url('<?php echo $cover ?>')">
                <img class="avatar" src="<?php pk_get_gravatar($email) ?>"
                 alt="<?php echo $name ?>" title="<?php echo $name ?>">
            </div>
            <div class="content t-md puock-text">
                <div class="text-center p-2">
                    <div class="t-lg"><?php echo $name ?></div>
                    <div class="mt10 t-sm"><?php echo $des ?></div>
                </div>
                <div class="row mt10">
                    <div class="col-6 text-center">
                        <div class="c-sub t-sm">阅读量</div>
                        <div><?php echo get_total_views() ?></div>
                    </div>
                    <div class="col-6 text-center">
                        <div class="c-sub t-sm">评论数</div>
                        <div><?php echo $comment_num ?></div>
                    </div>
                </div>
            </div>
        </div>
    <?php }
}
add_action( 'widgets_init', function (){ register_widget('puockAboutAuthor'); });


//分类目录
class puockCategory extends puockWidgetBase {


    protected $title = "分类目录";

    protected $pre_title = "显示博客的所有";

    function get_fields(){
        return $this->merge_common_fields(array(
            array('id'=>'title','strip'=>true, 'val'=>$this->title),
        ));
    }

    function form( $instance ) {
        $instance = $this->default_value($instance);
        $this->html_gen($instance, '标题', 'title');
        $this->merge_common_form($instance);
    }

    function get_class_name()
    {
        return __CLASS__;
    }

    function widget( $args, $instance ){
        $cats = get_categories();
        $this->get_common_widget_header($instance);
        echo '<div class="row t-md">';
        foreach ($cats as $cat){ ?>
            <div class="col col-lg-6 text-center p-2">
            <a href="<?php echo get_category_link($cat) ?>" class="puock-bg p-2 abhl
             d-inline-block w-100" title="<?php echo $cat->name ?>"><?php echo $cat->name ?></a>
            </div>
        <?php }
        echo '</div>';
        $this->get_common_widget_footer($instance);
     }
}
add_action( 'widgets_init', function (){ register_widget('puockCategory'); });

//标签云
class puockTagCloud extends puockWidgetBase {


    protected $title = "标签云";

    protected $pre_title = "集成博客的标签为";

    function get_fields(){
        return $this->merge_common_fields(array(
            array('id'=>'title','strip'=>true, 'val'=>$this->title),
            array('id'=>'max_count','strip'=>true, 'val'=>0),
        ));
    }

    function form( $instance ) {
        $instance = $this->default_value($instance);
        $this->html_gen($instance, '标题', 'title');
        $this->html_gen($instance, '最大显示数量（0为不限制）', 'max_count');
        $this->merge_common_form($instance);
    }

    function get_class_name()
    {
        return __CLASS__;
    }

    function widget( $args, $instance ){
        $this->get_common_widget_header($instance);
        echo '<div class="widget-puock-tag-cloud">';
        $tags = get_tags();
        $max_count = $this->get_num_val($instance, 'max_count');
        if(count($tags) > 0){
            $count = 0;
            foreach ($tags as $tag){
                if ($max_count > 0 && $count >= $max_count){
                    break;
                }
                $link = get_tag_link($tag);
                echo "<a href='{$link}' class='badge d-none d-md-inline-block bg-".pk_get_color_tag()." ahfff'>{$tag->name}</a>";
                $count++;
            }
        }else{
            echo sc_tips_primary(null, "暂无标签");
        }
        echo '</div>';
        $this->get_common_widget_footer($instance);
     }
}
add_action( 'widgets_init', function (){ register_widget('puockTagCloud'); });


//一言一句话
class puockTagHitokoto extends puockWidgetBase {


    protected $title = "一言一句话";

    protected $pre_title = "随机展示";

    function get_fields(){
        return $this->merge_common_fields(array(
            array('id'=>'title','strip'=>true, 'val'=>$this->title),
        ));
    }

    function form( $instance ) {
        $instance = $this->default_value($instance);
        $this->html_gen($instance, '标题', 'title');
        $this->merge_common_form($instance);
    }

    function get_class_name()
    {
        return __CLASS__;
    }

    function widget( $args, $instance ){
        $this->get_common_widget_header($instance);
        $id = "pk-hitokoto-".wp_unique_id();
        ?>
        <div class="widget-puock-hitokoto">
            <div id="<?php echo $id ?>">
                <div class="t puock-text"></div>
                <div class="fb">-「<span class="f"></span>」</div>
            </div>
            <script type="application/javascript">
            $.get("https://v1.hitokoto.cn/", function (res){
                const el = $("#<?php echo $id ?>");
                el.find(".t").text(res.hitokoto);
                el.find('.f').text(res.from)
            },'json');
            </script>
        </div>
       <?php $this->get_common_widget_footer($instance);
     }
}
add_action( 'widgets_init', function (){ register_widget('puockTagHitokoto'); });