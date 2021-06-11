$(function () {

    $(document).on("click",".fancybox",function () {
        return false;
    });

    $(document).on("click",".share-to>div",function () {
        const id = $(this).attr("data-id");
        const url = window.location.href;
        let to = null;
        const title = $("#post-title").text();
        const wb_key = '';//TODO 填写自己的应用KEY
        if(id==='wx') return;
        switch (id) {
            case 'wb':to='http://service.weibo.com/share/share.php?pic=&title='+title+'&url='+url+'&appkey='+wb_key;break;
            case 'qzone':to='http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?title='+title+'&url='+url;break;
            case 'tw':to='https://twitter.com/intent/tweet?url='+url;break;
            case 'fb':to='http://www.facebook.com/sharer.php?u'+url;break;
        }
        window.open(to,'_blank');
    });

    $(document).on("click","#return-top-bottom>div",function () {
        const to = $(this).attr("data-to");
        const scroll_val = to==='top' ? 0 : window.document.body.clientHeight;
        $('html,body').animate({scrollTop:scroll_val},800)
    });

    function toggleMenu(vm){
        const className = "data-open";
        const open = vm.hasClass(className);
        const content = $("#post-menu-content");
        if(open){
            content.hide();
            vm.removeClass(className);
        }else{
            content.show();
            vm.addClass(className);
        }
    }

    $(document).on("touchend", "#post-menu-state", function (e){
        e.preventDefault();
        toggleMenu($(this));
    });

    $(document).on("click", "#post-menu-state", function (){
        toggleMenu($(this));
    });



    $(document).on("click", ".pk-menu-to", function (){
        const to = $(this).attr("href");
        const headerHeight = $("#header").innerHeight();
        $("html, body").animate({
            scrollTop: ($(to).offset().top - headerHeight - 10) + "px"
        }, {
            duration: 500,
            easing: "swing"
        });
        return false;
    });

});





