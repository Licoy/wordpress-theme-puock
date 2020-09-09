<?php
/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 */
function optionsframework_option_name() {
    // Change this to use your theme slug
    return 'optionsframework';
}

/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the 'id' fields, make sure to use all lowercase and no spaces.
 *
 * If you are making your theme translatable, you should replace 'theme-textdomain'
 * with the actual text domain for your theme.  Read more:
 * http://codex.wordpress.org/Function_Reference/load_theme_textdomain
 */

function optionsframework_options() {

    $post_cats = get_all_category_id('category');

    $pages = array();
    $pageObjects = get_pages( 'sort_column=post_parent,menu_order' );
    $pages[''] = '请选择页面';
    foreach ($pageObjects as $page) {
        $pages[$page->ID] = $page->post_title;
    }

    $imgPath =  get_stylesheet_directory_uri() . '/assets/img';

    $themes = ['light'=>'高亮模式','dark'=>'暗黑模式'];

    $editorSettings = array(
        'textarea_rows' => 3,
        'tinymce' => array( 'plugins' => 'wordpress' )
    );

    // 全局设置
    $options[] = array(
        'name' => '全局设置',
        'type' => 'heading'
    );
    $options[] = array(
        'name' => '首页布局',
        'id' => 'index_mode',
        'std' => 'blog',
        'type' => 'radio',
        'options' => [
            'blog'=>'博客风格',
            'cms'=>'CMS风格',
            'company'=>'企业风格',
        ]
    );
    $options[] = array(
        'name' => '文章样式',
        'id' => 'post_style',
        'std' => 'list',
        'type' => 'radio',
        'options' => [
            'list'=>'列表式',
            'card'=>'卡片式'
        ]
    );
    $options[] = array(
        'name' => '默认主题模式',
        'id' => 'theme_mode',
        'std' => 'light',
        'type' => 'radio',
        'options' => $themes
    );
    $options[] = array(
        'name' => '允许切换主题模式',
        'desc' => '允许',
        'id' => 'theme_mode_s',
        'std' => '1',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => '高亮模式下LOGO',
        'desc' => '比例：500*125，请尽量选择png无底色图片',
        'id' => 'light_logo',
        "std" => "",
        'type' => 'upload'
    );

    $options[] = array(
        'name' => '暗黑模式下LOGO',
        'desc' => '比例：500*125，请尽量选择png无底色图片',
        'id' => 'dark_logo',
        "std" => "",
        'type' => 'upload'
    );

    $options[] = array(
        'name' => '网站Favicon',
        'desc' => '比例：1:1',
        'id' => 'favicon',
        "std" => "",
        'type' => 'upload'
    );

    $options[] = array(
        'name' => '禁用Gutenberg编辑器',
        'desc' => '允许',
        'id' => 'stop5x_editor',
        'std' => '0',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => '隐藏底部"感谢使用WordPress进行创作"和左上角标识',
        'desc' => '隐藏',
        'id' => 'hide_footer_wp_t',
        'std' => '0',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => '全站变灰（可供用于悼念日或其他事件变灰需求）',
        'desc' => '允许',
        'id' => 'grey',
        'std' => '0',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => '显示首页幻灯片',
        'desc' => '允许',
        'id' => 'index_carousel',
        'std' => '1',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => '',
        'desc' => '幻灯最大显示数量',
        'id' => 'index_carousel_mn',
        'std' => '3',
        'class' => 'mini',
        'type' => 'text'
    );

    $options[] = array(
        'name' => '将HTML压缩成一行',
        'desc' => '允许',
        'id' => 'compress_html',
        'std' => '0',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => '开启文章底部自定义内容',
        'id' => 'content_bottom_open',
        'desc' => '允许',
        'std' => '0',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => '文章底部自定义内容',
        'desc' => '此处为自定义HTML代码',
        'id' => 'content_bottom',
        'std' => '',
        'type' => 'textarea'
    );

    $options[] = array(
        'name' => '底部开关',
        'id' => 'footer_about_open',
        'std' => 'all_open',
        'type' => 'radio',
        'options' => [
            'all_close'=>'关闭所有',
            'all_open'=>'开启所有',
            'about_me_close'=>'仅关闭关于我们',
            'links_close'=>'仅关闭友情链接',
        ]
    );
    
    $options[] = array(
        'name' => '底部布局',
        'desc' => '仅在底部信息全部开启的情况下有效',
        'id' => 'footer_layout_mode',
        'std' => 'double',
        'type' => 'radio',
        'options' => [
            'single'=>'单栏',
            'double'=>'双栏',
        ]
    );
    
    $options[] = array(
        'name' => '底部主题版权开关',
        'id' => 'footer_theme_copyright_open',
        'std' => '1',
        'type' => 'radio',
        'options' => [
            '0'=>'关闭',
            '1'=>'开启',
        ]
    );

    $options[] = array(
        'name' => '启用后台登录保护',
        'desc' => '允许（启用后则用"wp-login.php?{后台登录保护参数}={后台登录保护值}"的方式访问）',
        'id' => 'login_protection',
        'std' => '0',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => '',
        'desc' => '后台登录保护参数',
        'id' => 'lp_user',
        'std' => 'admin',
        'type' => 'text'
    );

    $options[] = array(
        'name' => '',
        'desc' => '后台登录保护值',
        'id' => 'lp_pass',
        'std' => 'admin',
        'type' => 'text'
    );

    // 基本设置
    $options[] = array(
        'name' => '基本设置',
        'type' => 'heading'
    );

    $options[] = array(
        'name' => '图片延迟加载',
        'desc' => '启用缩略图延迟加载',
        'id' => 'basic_img_lazy_s',
        'std' => '0',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => '',
        'desc' => '启用正文图片延迟加载',
        'id' => 'basic_img_lazy_z',
        'std' => '0',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => '',
        'desc' => '启用留言头像延迟加载',
        'id' => 'basic_img_lazy_a',
        'std' => '0',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => '新标签页打开内容',
        'desc' => '正文内容链接新标签页打开',
        'id' => 'link_blank_content',
        'std' => '0',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => '评论Ajax翻页',
        'desc' => '开启',
        'id' => 'comment_ajax',
        'std' => '0',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => '页面无刷新加载',
        'desc' => '开启（新标签页打开的链接除外）',
        'id' => 'page_ajax_load',
        'std' => '1',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => '异步浏览量统计',
        'desc' => '开启（此选项为开启缓存后浏览量不自增问题解决方案）',
        'id' => 'async_view',
        'std' => '0',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => '页面模块载入动画',
        'desc' => '开启',
        'id' => 'page_animate',
        'std' => '1',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => '显示评论等级',
        'desc' => '开启',
        'id' => 'comment_level',
        'std' => '1',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => '评论回复邮件通知',
        'desc' => '允许',
        'id' => 'comment_mail_notify',
        'std' => '0',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => '评论内容显示@',
        'desc' => '允许',
        'id' => 'comment_has_at',
        'std' => '0',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => '显示正文版权说明',
        'desc' => '开启',
        'id' => 'page_copy_right',
        'std' => '1',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => '显示正文底部相关推荐',
        'desc' => '开启',
        'id' => 'page_b_recommend',
        'std' => '1',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => '友情链接页面',
        'id' => 'link_page',
        'type' => 'select',
        'class' => 'mini',
        'options' => $pages
    );

    $options[] = array(
        'name' => '首页友情链接目录ID',
        'id' => 'index_link_id',
        'std' => '',
        'class' => 'mini',
        'type' => 'text'
    );

    $options[] = array(
        'desc' => '<div><small>分类ID对照列表：</small>'.get_all_category_id('link_category').'</div>',
        'id' => 'catids',
        'type' => 'info'
    );

    $options[] = array(
        'name' => 'GrAvatar加载源',
        'id' => 'gravatar_url',
        'std' => 'wp',
        'type' => 'radio',
        'options' => [
            'wp'=>'官方默认加载节点',
            'cn'=>'官方提供的CN节点',
            'cn_ssl'=>'官方提供SSL的CN节点',
            'loli_ssl'=>'SB.SB提供的SSL节点',
        ]
    );

    $options[] = array(
        'name' => '文章赞赏',
        'desc' => '开启',
        'id' => 'post_reward',
        'std' => '0',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => '文章赞赏支付宝二维码',
        'desc' => '请选择宽高比例为1:1的图片',
        'id' => 'post_reward_alipay',
        'std' => '',
        'type' => 'upload'
    );

    $options[] = array(
        'name' => '文章赞赏微信二维码',
        'desc' => '请选择宽高比例为1:1的图片',
        'id' => 'post_reward_wx',
        'std' => '',
        'type' => 'upload'
    );

    // 第三方登录
    $options[] = array(
        'name' => '第三方登录',
        'type' => 'heading'
    );

    $options[] = array(
        'name' => '',
        'desc' => '启用QQ登录',
        'id' => 'oauth_qq',
        'std' => '0',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => 'QQ互联APP_ID',
        'id' => 'oauth_qq_id',
        'std' => '',
        'type' => 'text'
    );

    $options[] = array(
        'name' => 'QQ互联APP_KEY',
        'id' => 'oauth_qq_key',
        'std' => '',
        'type' => 'text'
    );

    // CMS布局设置
    $options[] = array(
        'name' => 'CMS布局设置',
        'type' => 'heading'
    );

    $options[] = array(
        'name' => '最新文章',
        'desc' => '显示',
        'id' => 'cms_show_new',
        'std' => '1',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => '',
        'desc' => '最新文章显示数量',
        'id' => 'cms_show_new_num',
        'std' => '5',
        'class' => 'mini',
        'type' => 'text'
    );

    $options[] = array(
        'name' => '两栏CMS分类列表',
        'desc' => '显示',
        'id' => 'cms_show_2box',
        'std' => '1',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => '两栏CMS分类ID列表',
        'id' => 'cms_show_2box_id',
        'desc'=>'每个ID之间用,进行分隔',
        'std' => '',
        'class' => 'mini',
        'type' => 'text'
    );

    $options[] = array(
        'desc' => '<div><small>分类ID对照列表：</small>'.$post_cats.'</div>',
        'id' => 'catids',
        'type' => 'info'
    );

    $options[] = array(
        'name' => '两栏CMS分类每栏显示数量',
        'id' => 'cms_show_2box_num',
        'std' => '6',
        'class' => 'mini',
        'type' => 'text'
    );

    // 企业布局设置
    $options[] = array(
        'name' => '企业布局设置',
        'type' => 'heading'
    );

    $options[] = array(
        'name' => '「关于我们」标题',
        'id' => 'company_about_t',
        'std' => '企业介绍',
        'type' => 'text'
    );

    $options[] = array(
        'name' => '「关于我们」内容（支持HTML）',
        'id' => 'company_about_c',
        'std' => '<p>关于我们内容</p>',
        'type' => 'textarea'
    );

    $options[] = array(
        'name' => '「关于我们」详情链接',
        'id' => 'company_about_a',
        'std' => home_url().'/about.html',
        'type' => 'text'
    );

    $options[] = array(
        'name' => '「产品介绍」大标题',
        'id' => 'company_product_title',
        'std' => '产品介绍',
        'type' => 'text'
    );

    //产品
    for ($i=1;$i<=4;$i++){

        $options[] = array(
            'name'=>'',
            'desc' => '「产品介绍」-第'.$i.'个-启用',
            'id' => 'company_product_open_'.$i,
            'std' => '1',
            'type' => 'checkbox',
        );

        $options[] = array(
            'name' => '「产品介绍」-第'.$i.'个-标题',
            'id' => 'company_product_t_'.$i,
            'std' => '产品'.$i,
            'type' => 'text'
        );

        $options[] = array(
            'name' => '「产品介绍」-第'.$i.'个-内容（支持HTML）',
            'id' => 'company_product_c_'.$i,
            'std' => '<h1 class="card-title pricing-card-title">¥0<small class="text-muted">/月</small></h1>
                        <ul class="list-unstyled mt-3 mb-4">
                            <li>40G存储</li>
                            <li>2GB内存</li>
                        </ul>',
            'type' => 'textarea'
        );

        $options[] = array(
            'name' => '「产品介绍」-第'.$i.'个-详情指向链接',
            'id' => 'company_product_a_'.$i,
            'std' => home_url(),
            'type' => 'text'
        );

    }

    $options[] = array(
        'name' => '「解决方案」大标题',
        'id' => 'company_soul_title',
        'std' => '解决方案',
        'type' => 'text'
    );

    //解决方案
    for ($i=1;$i<=3;$i++){

        $options[] = array(
            'name'=>'',
            'desc' => '「解决方案」-第'.$i.'个-启用',
            'id' => 'company_soul_open_'.$i,
            'std' => '1',
            'type' => 'checkbox',
        );

        $options[] = array(
            'name' => '「解决方案」-第'.$i.'个-介绍图（1:1比例）',
            'id' => 'company_soul_i_'.$i,
            'std' => '',
            'type' => 'upload'
        );

        $options[] = array(
            'name' => '「解决方案」-第'.$i.'个-标题',
            'id' => 'company_soul_t_'.$i,
            'std' => '问题'.$i,
            'type' => 'text'
        );

        $options[] = array(
            'name' => '「解决方案」-第'.$i.'个-描述（支持HTML）',
            'id' => 'company_soul_c_'.$i,
            'std' => '我们将提供问题'.$i.'的最佳解决方案',
            'type' => 'textarea'
        );

        $options[] = array(
            'name' => '「解决方案」-第'.$i.'个-详情指向链接',
            'id' => 'company_soul_a_'.$i,
            'std' => home_url(),
            'type' => 'text'
        );

    }

    //产品预览
    foreach (array('左'=>'l','右'=>'r') as $key=>$val){
        $options[] = array(
            'name'=>'',
            'desc' => '「产品预览」-'.$key.'边显示-启用',
            'id' => 'company_pr_open_'.$val,
            'std' => '1',
            'type' => 'checkbox',
        );

        $options[] = array(
            'name' => '「产品预览」-'.$key.'边显示-预览图（400*400）',
            'id' => 'company_pr_i_'.$val,
            'std' => '',
            'type' => 'upload'
        );

        $options[] = array(
            'name' => '「产品预览」-'.$key.'边显示-标题',
            'id' => 'company_pr_t_'.$val,
            'std' => '产品'.$val,
            'type' => 'text'
        );

        $options[] = array(
            'name' => '「解决方案」-'.$key.'边显示-内容（支持HTML）',
            'id' => 'company_pr_c_'.$val,
            'std' => '这里是产品'.$val.'的内容<button class="btn btn-outline-secondary">详情</button>',
            'type' => 'textarea'
        );

    }

    $options[] = array(
        'name'=>'',
        'desc' => '显示新闻',
        'id' => 'company_news_open',
        'std' => '0',
        'type' => 'checkbox',
    );

    $options[] = array(
        'name'=>'新闻模块标题',
        'id' => 'company_news_title',
        'std' => '新闻动态',
        'type' => 'text',
    );

    $options[] = array(
        'name'=>'新闻分类目录（每个分类之间用英文逗号","分隔）',
        'id' => 'company_news_cid',
        'std' => '',
        'type' => 'text',
    );

    $options[] = array(
        'desc'=>'<div>'.$post_cats.'</div>',
        'id' => 'company_news_cid_info',
        'type' => 'info',
    );

    $options[] = array(
        'name' => '新闻显示数量',
        'id' => 'company_news_max_num',
        'std' => '4',
        'class' => 'mini',
        'type' => 'text'
    );

    $options[] = array(
        'name' => '两栏CMS分类列表',
        'desc' => '显示',
        'id' => 'company_show_2box',
        'std' => '0',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => '两栏CMS分类ID列表',
        'id' => 'company_show_2box_id',
        'desc'=>'每个ID之间用,进行分隔',
        'std' => '',
        'class' => 'mini',
        'type' => 'text'
    );

    $options[] = array(
        'desc' => '<div><small>分类ID对照列表：</small>'.$post_cats.'</div>',
        'id' => 'company_catids',
        'type' => 'info'
    );

    $options[] = array(
        'name' => '两栏CMS分类每栏显示数量',
        'id' => 'company_show_2box_num',
        'std' => '6',
        'class' => 'mini',
        'type' => 'text'
    );

    // 防刷验证
    $options[] = array(
        'name' => '防刷验证',
        'type' => 'heading'
    );

    $options[] = array(
        'name' => '',
        'desc' => '启用评论防刷验证',
        'id' => 'vd_comment',
        'std' => '0',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => 'Vaptcha-API',
        'desc' => '查看服务端验证API请到<a target="_blank" href="https://www.vaptcha.com/document/install.html#api请求数据">文档中心</a>获取',
        'id' => 'vd_vaptcha_api',
        'std' => 'http://0.vaptcha.com/verify',
        'type' => 'text'
    );

    $options[] = array(
        'name' => 'Vaptcha-VID',
        'desc' => '获取VID请到<a target="_blank" href="https://user.vaptcha.com/manage">Vaptcha管理系统</a>获取',
        'id' => 'vd_vaptcha_id',
        'std' => '',
        'type' => 'text'
    );

    $options[] = array(
        'name' => 'Vaptcha-KEY',
        'id' => 'vd_vaptcha_key',
        'desc' => '获取KEY请到<a target="_blank" href="https://user.vaptcha.com/manage">Vaptcha管理系统</a>获取',
        'std' => '',
        'type' => 'text'
    );



    // SEO设置
    $options[] = array(
        'name' => '广告',
        'type' => 'heading'
    );

    $options[] = array(
        'name' => '',
        'desc' => '启用全站顶部广告',
        'id' => 'ad_g_top_c',
        'std' => '0',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => '全站顶部广告内容',
        'id' => 'ad_g_top',
        'std' => '',
        'type' => 'textarea'
    );

    $options[] = array(
        'name' => '',
        'desc' => '启用全站底部广告',
        'id' => 'ad_g_bottom_c',
        'std' => '0',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => '全站底部广告内容',
        'id' => 'ad_g_bottom',
        'std' => '',
        'type' => 'textarea'
    );

    $options[] = array(
        'name' => '',
        'desc' => '启用文章内顶部广告',
        'id' => 'ad_page_t_c',
        'std' => '0',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => '文章内顶部广告内容',
        'desc'=> '显示在面包屑导航下',
        'id' => 'ad_page_t',
        'std' => '',
        'type' => 'textarea'
    );

    $options[] = array(
        'name' => '',
        'desc' => '启用文章内容底部广告',
        'id' => 'ad_page_c_b_c',
        'std' => '0',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => '文章内容底部广告内容',
        'desc'=> '会显示在文章结尾处',
        'id' => 'ad_page_c_b',
        'std' => '',
        'type' => 'textarea'
    );

    $options[] = array(
        'name' => '',
        'desc' => '启用评论上方广告',
        'id' => 'ad_comment_t_c',
        'std' => '0',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => '评论上方广告内容',
        'id' => 'ad_comment_t',
        'std' => '',
        'type' => 'textarea'
    );

    // SEO设置
    $options[] = array(
        'name' => 'SMTP邮件',
        'type' => 'heading'
    );

    $options[] = array(
        'name' => '',
        'desc' => '启用主题SMTP覆盖WordPress默认配置',
        'id' => 'smtp_open',
        'std' => '0',
        'type' => 'checkbox'
    );

    $options[] = array(
        'desc' => '启用SSL安全模式',
        'id' => 'smtp_ssl',
        'std' => '0',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => '发件人地址',
        'id' => 'smtp_form',
        'std' => '',
        'type' => 'text'
    );

    $options[] = array(
        'name' => '发件人名称',
        'id' => 'smtp_form_n',
        'std' => '',
        'type' => 'text'
    );

    $options[] = array(
        'name' => 'SMTP服务器地址（如163邮箱的为：smtp.163.com）',
        'id' => 'smtp_host',
        'std' => '',
        'type' => 'text'
    );

    $options[] = array(
        'name' => 'SMTP发送端口',
        'id' => 'smtp_port',
        'std' => '',
        'type' => 'text'
    );

    $options[] = array(
        'name' => '邮箱账号',
        'id' => 'smtp_u',
        'std' => '',
        'type' => 'text'
    );

    $options[] = array(
        'name' => '邮箱密码（一般非邮箱账号直接密码，而是对应的平台的POP3/SMTP授权码）',
        'id' => 'smtp_p',
        'std' => '',
        'type' => 'text'
    );


    // SEO设置
    $options[] = array(
        'name' => 'SEO设置',
        'type' => 'heading'
    );

    $options[] = array(
        'name' => '',
        'desc' => '启用主题SEO功能，若您正在使用其它的SEO插件，请取消勾选',
        'id' => 'seo_open',
        'std' => '1',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => '网站Title',
        'desc' => '留空则不显示',
        'id' => 'web_title',
        'std' => '',
        'type' => 'text'
    );

    $options[] = array(
        'name' => '网站首页副标题',
        'desc' => '留空则不显示',
        'id' => 'web_title_2',
        'std' => '',
        'type' => 'text'
    );

    $options[] = array(
        'name' => 'Title连接符',
        'desc' => 'Title连接符号，例如 "-"、"|"',
        'id' => 'title_conn',
        'std' => '-',
        'class' => 'mini',
        'type' => 'text'
    );

    $options[] = array(
        'name' => '首页描述',
        'desc' => '',
        'id' => 'description',
        'std' => '一般不超过200个字符',
        'type' => 'textarea'
    );

    $options[] = array(
        'name' => '首页关键词（每个关键字之前用半角逗号分隔）',
        'desc' => '',
        'id' => 'keyword',
        'std' => '一般不超过100个字符',
        'type' => 'textarea'
    );

    $options[] = array(
        'name' => '',
        'desc' => '不显示分类链接中的"category"',
        'id' => 'no_category',
        'std' => '0',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => '发布文章主动推送至百度',
        'desc' => '启用',
        'id' => 'open_baidu_submit',
        'std' => '0',
        'type' => 'checkbox'
    );

    $options[] = array(
        'name' => '',
        'desc' => '百度推送接口地址，如：http://data.zz.baidu.com/urls?site=https://xxx.com&token=XXXXXX',
        'id' => 'baidu_submit_url',
        'std' => '',
        'type' => 'text'
    );

    $options[] = array(
        'name' => '头部流量统计代码',
        'desc' => '用于在页头添加统计代码，',
        'id' => 'tj_code_header',
        'std' => '',
        'type' => 'textarea'
    );

    $options[] = array(
        'name' => '底部流量统计代码',
        'desc' => '用于在页脚添加统计代码',
        'id' => 'tj_code_footer',
        'std' => '',
        'type' => 'textarea'
    );

    $options[] = array(
        'name' => '底部页脚信息',
        'desc' => '显示备案信息及其他相关链接',
        'id' => 'footer_info',
        'std' => 'Copyright &copy;&nbsp;&nbsp;Themes Design By Puock&nbsp;&nbsp;',
        'type' => 'editor',
        'settings' => $editorSettings
    );
    $options[] = array(
        'name' => '底部关于我们说明',
        'id' => 'footer_about_me',
        'settings' => $editorSettings,
        'type' => 'editor',
        'std' => ''
    );
    $options[] = array(
        'name' => '下载说明/申明',
        'id' => 'down_tips',
        'settings' => $editorSettings,
        'type' => 'editor',
        'std' => '本站部分资源来自于网络收集，若侵犯了你的隐私或版权，请及时联系我们删除有关信息。'
    );

    return $options;
}