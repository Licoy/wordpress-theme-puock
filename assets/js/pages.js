$(function () {

    window.vdCommentOpen = $("meta[name='vd-comment']").attr("content") === 'on';

    if(window.vdCommentOpen){
        vaptcha({
            vid: '5e955262370a0ce37126055d', // 验证单元id
            type: 'invisible', // 显示类型 隐藏式
            scene: 3, // 场景值 默认0
            offline_server: 'http://ww.ss',
        }).then(function (vaptchaObj) {
            window.vaptchaInstance = vaptchaObj;
            window.vaptchaInstance.listen('pass', function() {
                $("#comment-vd").val(window.vaptchaInstance.getToken());
                window.vaptchaInstance.reset();
                $.comment_form_submit_exec($("#comment-form"));
            });
        })
    }

    (function initReadProgress() {
        var readProgress = $("#page-read-progress .progress-bar");
        document.addEventListener('scroll', function () {
            var a = window.scrollY / (document.documentElement.scrollHeight - window.innerHeight) * 100;
            readProgress.attr("style","width:"+a.toFixed(0)+"%");
        });
    })();

    InstantClick.on('change',function () {
        pageChangeInit();
    });

    if(!global_params.is_pjax){
        pageChangeInit();
    }

    function pageChangeInit(){
        if(document.getElementById("post-main")){
            new Viewer(document.getElementById("post-main"),{
                navbar:false,
            });
        }
        $('[data-toggle="tooltip"]').tooltip({placement:'auto',trigger:'hover'});
        if(global_params.is_single==1){
            new ClipboardJS('.copy-post-link', {
                text: function () {
                    var $copy = $(".copy-post-link");
                    $copy.find('span').html("已复制");
                    $copy.attr("disabled",true);
                    setTimeout(function () {
                        $copy.find('span').html("复制链接");
                        $copy.attr("disabled",false);
                    },3000);
                    return location.href;
                }
            });
        }
        new LazyLoad(document.querySelectorAll([".lazyload","[data-lazy=true]"]), {
            root: null,
            rootMargin: "0px",
            threshold: 0
        });
    }

    document.querySelectorAll('pre').forEach((block) => {
        hljs.highlightBlock(block);
    });

    if(global_params.is_single){
        setTimeout(function () {
            var wx = $("#wx-share");
            QRCode.toDataURL(window.location.href,{ errorCorrectionLevel: 'H'}, function (err, url) {
                if(!err){
                    wx.attr("data-original-title","<p class='text-center t-sm mb-1 mt-1'>使用微信扫一扫</p><img class='mb-1' alt='微信二维码' src='"+url+"'/>")
                }
            })
        },1000)
    }

    // (function sidebarPosition() {
    //     var sidebar = $("#sidebar");
    //     var main = sidebar.find('.sidebar-main');
    //     var overHeight = sidebar.offset().top + main.height() + 50;
    //     var footerHeight = $("#footer").height();
    //     var showHeight = $(document).height() - footerHeight - main.height() + 70;
    // })();
});