<?php
/*
Template Name: 友情链接
*/

$cats = get_post_meta($post->ID,'page_links_id',true);
$use_theme_link_forward = get_post_meta($post->ID,'use_theme_link_forward',true);

$links = pk_get_wp_links($cats);

$groups = array();

if(!empty($cats) && $links && count($links)>0){
    foreach ($links as $link){
        if(!array_key_exists($link->term_id,$groups)){
            $groups[$link->term_id] = array(
                    'id'=>$link->term_id,
                    'name'=>$link->name,
                    'links'=>array()
            );
        }
        $groups[$link->term_id]['links'][] = $link;
    }
}

get_header();

?>

<div id="page" class="container mt20">
    <?php get_template_part('templates/box', 'global-top') ?>
    <?php echo pk_breadcrumbs(); while (have_posts()):the_post();?>
        <?php
         ?>
        <div id="page-links">
            <div id="page-<?php the_ID() ?>" class="row row-cols-1">
                <div id="posts" class="col-12 <?php pk_open_box_animated('animated fadeInLeft') ?> ">
                    <div class="puock-text no-style">
                        <?php foreach ($groups as $group): ?>
                        <div class="p-block links-main" id="page-links-<?php echo $group['id'] ?>">
                            <h6><?php echo $group['name'] ?></h6>
                            <div class="links-main-box row t-sm">
                                <?php foreach ($group['links'] as $link): ?>
                                <a class="link-item a-link col-lg-3 col-md-4 col-sm-6 col-6" href="<?php echo $use_theme_link_forward ? pk_go_link($link->link_url,$link->link_name) : $link->link_url; ?>" target="<?php echo $link->link_target ?>"
                                    rel="<?php echo $link->link_rel ?>" title="<?php echo empty($link->link_notes) ? $link->link_name : $link->link_notes ?>"
                                   data-bs-toggle="tooltip">
                                    <div class="clearfix puock-bg">
                                        <?php if (empty($link->link_image)) : ?>
                                            <img alt="<?php echo $link->link_name ?>" <?php echo pk_get_lazy_img_info(pk_get_favicon_url($link->link_url),'md-avatar') ?> alt="<?php echo $link->link_name ?>">
                                        <?php else :?>	      
                                            <img src="<?php echo $link->link_image ;?>"  alt="<?php echo $link->link_name ;?>" class="md-avatar" />
                                        <?php endif;?>
                                        <div class="info">
                                            <p class="ml-1 text-nowrap text-truncate"><?php echo $link->link_name ?></p>
                                            <p class="c-sub ml-1 text-nowrap text-truncate"><?php echo empty($link->link_notes) ? '暂无介绍' : $link->link_notes ?></p>
                                        </div>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if(!empty(get_the_content())): ?>
                        <div class="mt20 p-block puock-text <?php get_entry_content_class() ?>">
                            <?php the_content() ?>
                            
                            <div class="p-block mt20" id="link-request-form">
                                <h6><i class="fa fa-plus-circle"></i> 申请友链</h6>
                                <form class="mt20" id="pk-link-form">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">站点名称 <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="link_name" placeholder="请输入站点名称" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">站点URL <span class="text-danger">*</span></label>
                                            <input type="url" class="form-control" name="link_url" placeholder="https://example.com" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">站点描述</label>
                                        <input type="text" class="form-control" name="link_description" placeholder="简短描述您的站点">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">站点介绍</label>
                                        <textarea class="form-control" name="link_notes" rows="3" placeholder="详细介绍您的站点（会显示在友链卡片上）"></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">联系邮箱</label>
                                            <input type="email" class="form-control" name="link_contact" placeholder="用于通知审核结果">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">站点Logo</label>
                                            <input type="url" class="form-control" name="link_image" placeholder="站点Logo图片URL">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary" id="pk-submit-link">
                                        <span class="pk-loading" style="display:none;"><i class="fa fa-spinner fa-spin"></i> 提交中...</span>
                                        <span class="pk-submit-text">提交申请</span>
                                    </button>
                                    <div class="alert alert-info mt-3" style="display:none;" id="pk-link-result"></div>
                                </form>
                            </div>
                        </div>
                    <?php else : ?>
                        <div class="mt20 p-block puock-text <?php get_entry_content_class() ?>">
                            <div class="p-block mt20" id="link-request-form">
                                <h6><i class="fa fa-plus-circle"></i> 申请友链</h6>
                                <form class="mt20" id="pk-link-form">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">站点名称 <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="link_name" placeholder="请输入站点名称" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">站点URL <span class="text-danger">*</span></label>
                                            <input type="url" class="form-control" name="link_url" placeholder="https://example.com" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">站点描述</label>
                                        <input type="text" class="form-control" name="link_description" placeholder="简短描述您的站点">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">站点介绍</label>
                                        <textarea class="form-control" name="link_notes" rows="3" placeholder="详细介绍您的站点（会显示在友链卡片上）"></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">联系邮箱</label>
                                            <input type="email" class="form-control" name="link_contact" placeholder="用于通知审核结果">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">站点Logo</label>
                                            <input type="url" class="form-control" name="link_image" placeholder="站点Logo图片URL">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary" id="pk-submit-link">
                                        <span class="pk-loading" style="display:none;"><i class="fa fa-spinner fa-spin"></i> 提交中...</span>
                                        <span class="pk-submit-text">提交申请</span>
                                    </button>
                                    <div class="alert alert-info mt-3" style="display:none;" id="pk-link-result"></div>
                                </form>
                            </div>
                        </div>
                    <?php endif;?>

                    <script>
                    jQuery(document).ready(function($) {
                        $('#pk-link-form').on('submit', function(e) {
                            e.preventDefault();
                            
                            $('#pk-submit-link .pk-loading').show();
                            $('#pk-submit-link .pk-submit-text').hide();
                            $('#pk-link-result').hide();
                            
                            var formData = {
                                link_name: $('input[name="link_name"]').val(),
                                link_url: $('input[name="link_url"]').val(),
                                link_description: $('input[name="link_description"]').val(),
                                link_notes: $('textarea[name="link_notes"]').val(),
                                link_contact: $('input[name="link_contact"]').val(),
                                link_image: $('input[name="link_image"]').val()
                            };
                            
                            $.ajax({
                                url: '<?php echo admin_url('admin-ajax.php?action=pk_link_request_submit'); ?>',
                                type: 'POST',
                                contentType: 'application/json',
                                data: JSON.stringify(formData),
                                success: function(res) {
                                    $('#pk-submit-link .pk-loading').hide();
                                    $('#pk-submit-link .pk-submit-text').show();
                                    
                                    $('#pk-link-result').removeClass('alert-danger alert-success').addClass(res.success ? 'alert-success' : 'alert-danger');
                                    $('#pk-link-result').html(res.data || '提交失败，请稍后重试').show();
                                    
                                    if (res.success) {
                                        $('#pk-link-form')[0].reset();
                                    }
                                },
                                error: function() {
                                    $('#pk-submit-link .pk-loading').hide();
                                    $('#pk-submit-link .pk-submit-text').show();
                                    
                                    $('#pk-link-result').removeClass('alert-info').addClass('alert-danger');
                                    $('#pk-link-result').html('网络错误，请稍后重试').show();
                                }
                            });
                        });
                    });
                    </script>
                    <?php comments_template() ?>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
    <?php get_template_part('templates/box', 'global-bottom') ?>
</div>

<?php get_footer() ?>
