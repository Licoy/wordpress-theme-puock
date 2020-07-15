$(function () {

    $(document).on("click",".fancybox",function () {
        return false;
    });

    $(document).on("click",".share-to>div",function () {
        var id = $(this).attr("data-id");
        var url = window.location.href;
        var to = null;
        var title = $("#post-title").text();
        var wb_key = '';//TODO 填写自己的应用KEY
        if(id==='wx') return;
        switch (id) {
            case 'wb':to='http://service.weibo.com/share/share.php?pic=&title='+title+'&url='+url+'&appkey='+wb_key;break;
            case 'qzone':to='http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?title='+title+'&url='+url;break;
            case 'tw':to='https://twitter.com/intent/tweet?url='+url;break;
            case 'fb':to='http://www.facebook.com/sharer.php?u'+url;break;
        }
        window.open(to,'_blank');
    });

});





