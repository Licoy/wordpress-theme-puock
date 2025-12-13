<?php

if (pk_is_checked('post_poster_open')) {
    pk_ajax_register('pk_poster', 'pk_poster_page_callback', true);
}
function pk_poster_page_callback()
{
    $id = $_REQUEST['id'];
    if (empty($id)) {
        wp_die('无效的文章ID: ' . $id);
    }
    $post = get_post($id);
    if (empty($post)) {
        wp_die('无效的文章ID: ' . $id);
    }
    setup_postdata($post);
    $title = get_the_title($post);
    $qrcode_url = PUOCK_ABS_URI . pk_post_qrcode(get_permalink($post));
    $el_id = 'post-poster-main-' . $post->ID;
    ?>

    <div class="post-poster">
        <div class="post-poster-main" id="<?php echo $el_id; ?>">
            <div class="cover">
                <img crossOrigin="anonymous" src="<?php echo pk_get_img_thumbnail_src(get_post_images($post),640,320) ?>" alt="poster">
            </div>
            <div class="content">
                <p class="title mt20 fs16"><?php echo $title ?></p>
                <p class="excerpt text-3line fs14 mt20 c-sub"><?php echo get_the_excerpt() ?></p>
                <div class="info mt20">
                    <img class="qrcode" src="<?php echo $qrcode_url ?>" alt="<?php echo $title ?>">
                    <?php if (!pk_is_checked('on_txt_logo') || empty(pk_get_option('light_logo'))): ?>
                        <img class="logo" src="<?php echo pk_get_option('light_logo') ?>" alt="logo">
                    <?php else: ?>
                        <p class="tip c-sub fs14">@<?php echo pk_get_web_title() ?></p>
                    <?php endif; ?>
                </div>
                <p class="tip c-sub fs12 mt20 p-flex-center"><i class="fa-solid fa-qrcode"></i>&nbsp;长按识别二维码查看文章内容</p>
            </div>
        </div>
    </div>
    <!--    <div class="mt20 d-flex justify-content-center">-->
    <!--        <div class="btn btn-primary btn-sm"><i class="fa fa-download"></i> 下载海报</div>-->
    <!--    </div>-->
    <script>
        $(function () {
            const loadingId = window.Puock.startLoading();
            const rootSelector = "#<?php echo $el_id; ?>";
            const rootEl = document.querySelector(rootSelector);

            const waitForImages = (node) => {
                if (!node) return Promise.resolve();
                const imgs = Array.from(node.querySelectorAll('img'));
                // 预先设置 crossOrigin，避免已加载的图片污染 canvas
                imgs.forEach(img => {
                    if (!img.crossOrigin) img.crossOrigin = 'anonymous';
                });
                const tasks = imgs.map(img => img.complete ? Promise.resolve() : new Promise(resolve => {
                    img.addEventListener('load', resolve, {once: true});
                    img.addEventListener('error', resolve, {once: true});
                }));
                return Promise.all(tasks);
            };

            const waitForFonts = async () => {
                if (document.fonts && document.fonts.ready) {
                    await document.fonts.ready;
                }
                // 字体 ready 后再给一点缓冲，避免字体 fallback -> 目标字体切换时的布局抖动
                await new Promise(resolve => setTimeout(resolve, 150));
            };

            const settleLayout = async () => {
                // 两帧保证最新布局
                await new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve)));
                // 再小延时，等字体重排完成
                await new Promise(resolve => setTimeout(resolve, 80));
            };

            (async () => {
                try {
                    if (!rootEl) {
                        throw new Error('未找到海报容器');
                    }

                    await Promise.all([waitForImages(rootEl), waitForFonts()]);

                    // 确保布局稳定后再截图（图片、字体、布局都稳定）
                    await settleLayout();

                    const canvas = await html2canvas(rootEl, {
                        allowTaint: true,
                        useCORS: true,
                        backgroundColor: '#ffffff',
                        scale: window.devicePixelRatio || 2,
                        letterRendering: true,
                        logging: false,
                        scrollX: 0,
                        scrollY: 0
                    });

                    const $root = $(rootSelector);
                    $root.show();
                    $root.html("<img class='result' src='" + canvas.toDataURL("image/png") + "' alt='<?php echo $title ?>'>");
                } catch (err) {
                    console.error(err);
                    window.Puock.toast("生成海报失败，请到Console查看错误信息", TYPE_DANGER);
                } finally {
                    window.Puock.stopLoading(loadingId);
                }
            })();
        })
    </script>
    <?php

    wp_die();
}
