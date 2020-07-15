<?php if(pk_is_checked('post_reward')): ?>
    <!-- 赏-模态框 -->
    <div class="modal fade" id="rewardModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title puock-text">打赏</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="czs-close-l t-md"></i></span>
                    </button>
                </div>
                <div class="modal-body puock-text t-md">
                    <?php
                    $reward_alipay = pk_get_option('post_reward_alipay');
                    $reward_wx = pk_get_option('post_reward_wx');
                    if(empty($reward_alipay) && empty($reward_wx)){
                        echo '<div class="mt20 text-center">暂无打赏二维码</div>';
                    }else{
                        ?>
                        <div class="text-center">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="reward-alipay-tab" data-toggle="tab" href="#reward-alipay" role="tab" aria-controls="reward-alipay" aria-selected="true"><i class="czs-alipay"></i>&nbsp;支付宝</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="reward-wx-tab" data-toggle="tab" href="#reward-wx" role="tab" aria-controls="reward-wx" aria-selected="false"><i class="czs-weixinzhifu"></i>&nbsp;微信</a>
                                </li>
                            </ul>
                            <div class="tab-content mt15">
                                <div class="tab-pane fade show active" id="reward-alipay" role="tabpanel" aria-labelledby="reward-alipay-tab">
                                    <?php if(!empty($reward_alipay)): ?>
                                        <img width="240px" <?php echo pk_get_lazy_img_info($reward_alipay,'',null,null,false) ?> alt="支付宝赞赏"
                                             title="支付宝赞赏" data-toggle="tooltip"/>
                                        <p class="mt10">请使用支付宝扫一扫</p>
                                    <?php else:echo '未配置支付宝赞赏';endif; ?>
                                </div>
                                <div class="tab-pane fade" id="reward-wx" role="tabpanel" aria-labelledby="reward-wx-tab">
                                    <?php if(!empty($reward_wx)): ?>
                                        <img width="240px" <?php echo pk_get_lazy_img_info($reward_wx,'',null,null,false) ?> alt="微信赞赏"
                                             title="微信赞赏" data-toggle="tooltip"/>
                                        <p class="mt10">请使用微信扫一扫</p>
                                    <?php else:echo '未配置微信赞赏';endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>