var HOME_URL = $("meta[name='home-url']").attr("content");
var ADMIN_AJAX_URL = HOME_URL+"/wp-admin/admin-ajax.php";
function lg(name,val=null) {
    return val!=null ? localStorage.setItem(name,val) : localStorage.getItem(name);
}
//自动载入评论信息
function puockAutoLoadCommentInfo() {
    var authorText=lg("comment_author"),
        emailText=lg("comment_email"),
        urlText=lg("comment_url");
    if (authorText!= null && emailText!=null){
        $("#author").val(authorText);
        $("#email").val(emailText);
        $("#url").val(urlText);
    }
}
//自动设置评论信息
function puockAutoSetCommentInfo() {
    lg("comment_author",$("#author").val());
    lg("comment_email",$("#email").val());
    lg("comment_url",$("#url").val());
}
//异步浏览器统计
function asyncCacheViews(postId) {
    $.post(ADMIN_AJAX_URL+"?action=async_pk_views",{id:postId},function (res) {
        if(res.code!==0){
            console.error(res.msg)
        }else{
            $("#post-views").text(res.data)
        }
    },'json').error(function (e) {
        console.error(e)
    })
}

var pk = 'puock';

$(()=>{

    if(global_params.is_pjax){
        InstantClick.init('mousedown');
        InstantClick.on('change',function () {
            modeInit();
            puockAutoLoadCommentInfo();
        });
        puockAutoLoadCommentInfo();
    }else{
        puockAutoLoadCommentInfo();
    }


//模式切换
    function modeChange(isLight=null){
        let body = $("body");
        if(typeof(isLight)=="string"){
            isLight = isLight=='true' ? true : false;
        }
        if(isLight==null){
            isLight = body.hasClass(pk+"-light");
        }
        let dn = 'd-none';
        if(!isLight){
            $("#logo-dark").addClass(dn);
            $("#logo-light").removeClass(dn);
        }else{
            $("#logo-dark").removeClass(dn);
            $("#logo-light").addClass(dn);
        }
        body.removeClass(isLight ? pk+"-light":pk+"-dark");
        body.addClass(isLight ? pk+"-dark":pk+"-light");
        localStorage.setItem('light',isLight);
    }
//模式初始化
    function modeInit() {
        let light = lg('light');
        if(light!==undefined){
            modeChange(light);
        }
    }
    modeInit();

    //显示infoToast模态框
    function infoToastShow(text,title='提示'){
        let infoToast = $('#infoToast');
        $("#infoToastTitle").html(title);
        $("#infoToastText").html(text);
        infoToast.modal('show');
    }
    window.infoToast = infoToastShow;

    $(document).on("click","#mobile-menu-backdrop",()=>{
        mobileMenuChange();
    });
    $(document).on("click",".mobile-menu-close",()=>{
        mobileMenuChange();
    });
    $(document).on("click",".mobile-menu-s",()=>{
        mobileMenuChange('In');
    });
    function mobileMenuChange(s='Out'){
        $("#mobile-menu").attr("class","animated fade"+s+"Left");
        $("#mobile-menu-backdrop").attr("class","modal-backdrop animated fade"+s+"Right");
    }

    $(document).on("click",".search-modal-btn",()=>{
        let search = $("#search");
        let open = search.attr("data-open")==="true";
        let tag = open ? 'Out' : 'In';
        search.attr("class","animated fade"+tag+"Left");
        $("#search-backdrop").attr("class","modal-backdrop animated fade"+tag+"Right");
        search.attr("data-open",!open);
    });

    $(document).on("click",".colorMode",()=>{
        modeChange();
    });

    //ajax加载评论
    function go_comment_box(){
        $('html,body').animate({scrollTop: $("#comments").offset().top},800);
    }
    function comment_ajax_push_State(href){
        var stateObj = { foo: "bar" };
        history.pushState(stateObj, "page 2", href);
    }
    $(document).on('click','.comment-ajax-load a.page-numbers',function(){
        $("#comment-cancel").click();
        let href = $(this).attr("href");
        comment_ajax_push_State(href);
        $("#post-comments").html(" ");
        $("#comment-ajax-load").removeClass('d-none');
        $.post(href,{},function(data){
            $("#post-comments").html($(data).find("#post-comments"));
            $("#comment-ajax-load").addClass('d-none');
            go_comment_box();
        }).error(function(){
            location = href;
        });
        return false;
    });

    //评论信息提交
    let curReplyCid = null; //当前回复的评论ID
    $(document).on('submit','#comment-form',function() {
        if($("#comment-logged").val()==='0' && ($.trim($("#author").val())==='' || $.trim($("#email").val())==='')){
            infoToastShow('评论信息不能为空');
            return false;
        }
        if($.trim($("#comment").val())===''){
            infoToastShow('评论内容不能为空');
            return false;
        }
        if(window.vdCommentOpen){
            window.vaptchaInstance.validate();
        }else{
            comment_form_submit_exec(this);
        }
        return false;
    });
    //评论信息提交执行
    $.comment_form_submit_exec = comment_form_submit_exec;
    function comment_form_submit_exec(_vm){
        let submitUrl = $(_vm).attr("action");
        comment_form_submit_loading();
        $.ajax({
            url: submitUrl,
            data: $(_vm).serialize(),
            type: $(_vm).attr('method'),
            success:function (data) {
                infoToastShow('评论已提交成功');
                $("#comment").val("");
                if(curReplyCid!=null){
                    let comment = $('#comment-'+curReplyCid);
                    let ch = comment.find(".children");
                    if(ch.length===0){
                        comment.append("<ul class='children'></ul>");
                        ch = comment.find(".children");
                    }
                    ch.prepend(data);
                }else{
                    $('#post-comments').prepend(data);
                }
                $("#comment-cancel").click();
                comment_form_submit_loading();
                puockAutoSetCommentInfo();
            },
            error:function (res) {
                comment_form_submit_loading();
                infoToastShow(res.responseText);
            }
        });
    }
    //评论状态
    let comment_form_submit_loading_state = false;
    let comment_form_submit_time = 5;
    let comment_form_submit_val = null;
    function comment_form_submit_loading(){
        var commentSubmit = $("#comment-submit");
        if(comment_form_submit_loading_state){
            commentSubmit.html("请等待"+comment_form_submit_time+"s");
            comment_form_submit_val = setInterval(function () {
                if(comment_form_submit_time<=1){
                    clearInterval(comment_form_submit_val);
                    commentSubmit.html("提交评论");
                    commentSubmit.removeAttr("disabled");
                    comment_form_submit_time = 5;
                }else{
                    --comment_form_submit_time;
                    commentSubmit.html("请等待"+comment_form_submit_time+"s");
                }
            },1000);
        }else{
            commentSubmit.html('<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>提交中...');
            commentSubmit.attr("disabled",true)
        }
        comment_form_submit_loading_state = !comment_form_submit_loading_state;
    }
    //回复评论框变换
    $(document).on("click","[id^=comment-reply-]",function () {
        let _this = $(this);
        curReplyCid = _this.attr("data-id");
        if($.trim(curReplyCid)===''){
            infoToastShow('结构有误');
            return;
        }
        let cf = $("#comment-form"),
            cb = $("#comment-box-"+curReplyCid);
        cf.addClass("box-sw");
        cb.removeClass("d-none").append(cf);
        $("#comment-cancel").removeClass("d-none");
        $("#comment").val("");
        $("#comment_parent").val(curReplyCid);
    });
    //取消回复评论
    $(document).on("click","#comment-cancel",function () {
        let cf = $("#comment-form"),
            cb = $("#comment-box-"+curReplyCid);
        cf.removeClass("box-sw");
        cb.addClass("d-none");
        $("#comment-form-box").append(cf);
        $("#comment-cancel").addClass("d-none");
        curReplyCid = null;
    });

    $(document).on("click","#post-like",function () {
        let vm = $(this);
        let id = vm.attr("data-id");
        $.post("/wp-admin/admin-ajax.php",{action:'puock_like',um_id:id,um_action:'like'},function (res) {
            if(res.e==0){
                vm.find("span").html(res.d);
                vm.addClass("bg-primary text-light");
            }else{
                infoToastShow(res.t);
            }
        },'json').error(function () {
            infoToastShow('点赞异常');
        })
    });

    $(document).on('click','#comment-smiley',function () {
        $("#twemoji").modal("show");
    });

    $(document).on('click','.smiley-img',function () {
        var comment = $("#comment");
        comment.val(comment.val()+' '+$(this).attr("data-id")+' ');
        $("#twemoji").modal("hide");
    });

    $(document).on("click",".post-main-size",function(){
        var postMain = $("#post-main"),
            postSlider = $("#sidebar"),
            min = postMain.hasClass("col-lg-8");
        postMain.removeClass(min ? "col-lg-8":"col-lg-12");
        postMain.addClass(min ? "col-lg-12":"col-lg-8");
        min ? postSlider.removeClass("d-lg-block") : postSlider.addClass("d-lg-block");
    });

});