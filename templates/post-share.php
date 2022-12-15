<!-- 分享至第三方 -->
<div class="modal fade" id="shareModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title puock-text"><?php _e('分享至', PUOCK) ?></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa fa-close t-md"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-center w-100">
                    <div data-id="wb" class="share-to circle-button circle-sm circle-hb text-center bg-danger text-light"><i
                            class="fa-brands fa-weibo t-md"></i></div>
                    <div data-id="wx" id="wx-share" data-toggle="tooltip" data-html="true"
                         data-url="<?php echo PUOCK_ABS_URI . pk_post_qrcode(get_permalink()) ?>"
                         class="share-to circle-button circle-sm circle-hb text-center bg-success text-light"><i
                            class="fa-brands fa-weixin t-md"></i></div>
                    <div data-id="qzone" class="share-to circle-button circle-sm circle-hb text-center bg-warning text-light">
                        <i class="fa-brands fa-qq t-md"></i></div>
                    <div data-id="tw" class="share-to circle-button circle-sm circle-hb text-center bg-info text-light"><i
                            class="fa-brands fa-twitter t-md"></i></div>
                    <div data-id="fb" class="share-to circle-button circle-sm circle-hb text-center bg-primary text-light"><i
                            class="fa-brands fa-facebook t-md"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>
