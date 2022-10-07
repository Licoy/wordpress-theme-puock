<?php if (pk_is_checked('post_reward')): ?>
    <!-- 赏-模态框 -->
    <div class="modal fade" id="rewardModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title puock-text">打赏</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-close t-md"></i></span>
                    </button>
                </div>
                <div class="modal-body puock-text t-md">
                    <?php
                    $reward_alipay = pk_get_option('post_reward_alipay');
                    $reward_wx = pk_get_option('post_reward_wx');
                    if (empty($reward_alipay) && empty($reward_wx)) {
                        echo '<div class="mt20 text-center">暂无打赏二维码</div>';
                    } else {
                        ?>
                        <div class="p-flex-sbc">
                            <?php if (!empty($reward_alipay)): ?>
                                <div class="mr10" id="reward-alipay">
                                    <img <?php echo pk_get_lazy_img_info($reward_alipay, 'w-100', null, null, false) ?>
                                         alt="支付宝赞赏"
                                         title="支付宝赞赏" data-toggle="tooltip"/>
                                    <p class="mt10 text-center fs12"><i class="fa-brands fa-alipay"></i>&nbsp;请使用支付宝扫一扫</p>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($reward_wx)): ?>
                                <div id="reward-wx">
                                    <img <?php echo pk_get_lazy_img_info($reward_wx, 'w-100', null, null, false) ?>
                                         alt="微信赞赏"
                                         title="微信赞赏" data-toggle="tooltip"/>
                                    <p class="mt10 text-center fs12"><i class="fa-brands fa-weixin"></i>&nbsp;请使用微信扫一扫</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
