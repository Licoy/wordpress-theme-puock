
window.puock = {
    lazyLoadInit:function (el='.lazyload') {
        new LazyLoad(document.querySelectorAll([el,"[data-lazy=true]"]), {
            root: null,
            rootMargin: "0px",
            threshold: 0
        });
    },
    loadParams:function (){
        window.puockParams = eval('('+$("meta[name='puock-params']").attr("content")+')')
        window.vdCommentOpen = window.puockParams.vd_comment === 'on';
    }
}



$(function () {

    window.puock.loadParams();

    if(window.vdCommentOpen){
        vaptcha({
            vid: window.puockParams.vd_vid,
            type: 'invisible',
            scene: 3, // 场景值 默认0
            offline_server: 'http://ww.ss',
            area:'cn'
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
        window.puock.lazyLoadInit();
    }

    //获取文章的目录结构
    function getPostMenuStructure(){
        let res = []
        for (let item of $(".entry-content").find('h1,h2,h3,h4,h5,h6')) {
            res.push({name: $(item).text().trim(), level: item.tagName.toLowerCase(), id: $(item).attr("id")})
        }
        return res
    }

    function generatePostMenuHTML(){
        const menus = getPostMenuStructure();
        let genHtml = "<ul>";
        if (menus.length > 0){
            let heightLevel = 6;
            for (let i = 0; i < menus.length; i++) {
                const level = parseInt(menus[i].level[1]);
                if (level < heightLevel){
                    heightLevel = level;
                }
            }
            for (let i = 0; i < menus.length; i++) {
                const m = menus[i];
                let pl = 0;
                const level = parseInt(m.level[1]);
                if (level > heightLevel){
                    pl = (level - heightLevel) * 10;
                }
                genHtml += "<li style='padding-left: "+pl+"px' class='t-line-1'><i class='czs-angle-right-l t-sm c-sub mr-1'></i><a class='pk-menu-to a-link t-w-400 t-md'" +
                    " href='#"+m.id+"'>"+m.name+"</a></li>";
            }
        }
        genHtml += "</ul>"
        $("#post-menu-content").html(genHtml)
    }

    document.querySelectorAll('pre').forEach((block) => {
        hljs.highlightBlock(block);
    });

    if(global_params.is_single){

        //生成微信分享二维码
        setTimeout(function () {
            var wx = $("#wx-share");
            QRCode.toDataURL(window.location.href,{ errorCorrectionLevel: 'H'}, function (err, url) {
                if(!err){
                    wx.attr("data-original-title","<p class='text-center t-sm mb-1 mt-1'>使用微信扫一扫</p><img class='mb-1' alt='微信二维码' src='"+url+"'/>")
                }
            })
        },1000)

        if(window.puockParams.use_post_menu){
            generatePostMenuHTML()
        }
    }

    // (function sidebarPosition() {
    //     var sidebar = $("#sidebar");
    //     var main = sidebar.find('.sidebar-main');
    //     var overHeight = sidebar.offset().top + main.height() + 50;
    //     var footerHeight = $("#footer").height();
    //     var showHeight = $(document).height() - footerHeight - main.height() + 70;
    // })();
});