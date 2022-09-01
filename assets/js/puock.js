const puockGlobalData = {
    loads: {}
}

class Puock {
    data = {
        tag: 'puock',
        params: {
            home: null,
            use_post_menu: false,
            is_single: false,
            is_pjax: false,
            vd_comment: false,
            main_lazy_img: false,
            link_blank_open: false,
            async_view_id: null,
        },
        comment: {
            loading: false,
            time: 5,
            val: null,
            replyId: null
        }
    }

    // 全局一次加载或注册的事件
    onceInit() {
        this.pageInit()
        $(document).on("click", ".fancybox", () => {
            return false;
        });
        $(document).on("click", "#return-top-bottom>div", (e) => {
            const to = $(this.ct(e)).attr("data-to");
            const scroll_val = to === 'top' ? 0 : window.document.body.clientHeight;
            $('html,body').animate({scrollTop: scroll_val}, 800)
        });
        $(document).on("click", ".colorMode", () => {
            this.modeChange(null, true);
        });
        $(document).on("click", ".comment-captcha", (e) => {
            this.loadCommentCaptchaImage($(this.ct(e)))
        });
        if (this.data.params.is_pjax) {
            this.instanceClickLoad()
        }
        this.sidebarMenuEventInit()
        this.searchInit()
        this.eventShareStart()
        this.modeInit();
        this.registerMobileMenuEvent()
        this.registerModeChangeEvent()
        this.eventCommentPageChangeEvent()
        this.eventCommentPreSubmit()
        this.eventSmiley()
        this.eventOpenCommentBox()
        this.eventCloseCommentBox()
        this.eventSendPostLike()
        this.eventPostMainBoxResize()
    }

    pageInit() {
        this.loadParams()
        this.loadCommentCaptchaImage(null)
        this.pageChangeInit()
        if (this.data.params.is_single) {
            if (this.data.params.use_post_menu) {
                this.generatePostMenuHTML()
            }
            this.generatePostQrcode()
        }
    }

    instanceClickLoad() {
        InstantClick.init('mousedown');
        InstantClick.go = (url) => {
            const link = document.createElement('a');
            link.href = url;
            document.body.appendChild(link);
            link.click();
        }
        InstantClick.on('change', () => {
            this.loadParams();
            this.pageChangeInit()
        })
        // InstantClick.on('receive',(url, body, title)=>{
        //     console.log(body)
        //     this.loadParams($(body))
        // })
        this.loadCommentInfo();
    }

    ct(e) {
        return e.currentTarget
    }

    pageLinkBlankOpenInit() {
        if (this.data.params.link_blank_open) {
            $("#post-main-content").find("a").each((_, item) => {
                $(item).attr('target', 'blank')
            })
        }
    }

    loadCommentCaptchaImage(el) {
        if (el == null) {
            el = $(".comment-captcha");
        }
        const url = el.attr("data-path") + '&t=' + (new Date()).getTime()
        if (el.attr("src") == "") {
            el.attr("data-src", url)
        } else {
            el.attr("src", url)
        }

    }

    searchInit() {
        const toggle = () => {
            const search = $("#search");
            const open = search.attr("data-open") === "true";
            let tag = open ? 'Out' : 'In';
            search.attr("class", "animated fade" + tag + "Left");
            $("#search-backdrop").attr("class", "modal-backdrop animated fade" + tag + "Right");
            search.attr("data-open", !open);
        }
        $(document).on("click", ".search-modal-btn", () => {
            toggle();
        });
        $(document).on("click", "#search-backdrop", () => {
            toggle();
        })
        $(document).on("submit", ".global-search-form", (e) => {
            if (this.data.params.is_pjax) {
                const el = $(e.currentTarget)
                InstantClick.go(el.attr("action") + "/?" + el.serialize())
                return false;
            }
            return true;
        })
    }

    eventShareStart() {
        $(document).on("click", ".share-to>div", (e) => {
            const id = $(this.ct(e)).attr("data-id");
            if (id === 'wx') return;
            const url = window.location.href;
            const title = $("#post-title").text();
            const wb_key = '';
            let to = null;
            switch (id) {
                case 'wb':
                    to = 'https://service.weibo.com/share/share.php?pic=&title=' + title + '&url=' + url + '&appkey=' + wb_key;
                    break;
                case 'qzone':
                    to = 'https://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?title=' + title + '&url=' + url;
                    break;
                case 'tw':
                    to = 'https://twitter.com/intent/tweet?url=' + url;
                    break;
                case 'fb':
                    to = 'https://www.facebook.com/sharer.php?u' + url;
                    break;
            }
            window.open(to, '_blank');
        });
    }

    sidebarMenuEventInit() {
        $(document).on("touchend", "#post-menu-state", (e) => {
            e.preventDefault();
            this.toggleMenu();
        });
        $(document).on("click", "#post-menu-state", () => {
            this.toggleMenu();
        });
        $(document).on("click", ".pk-menu-to", (e) => {
            const to = $(this.ct(e)).attr("href");
            const headerHeight = $("#header").innerHeight();
            $("html, body").animate({
                scrollTop: ($(to).offset().top - headerHeight - 10) + "px"
            }, {
                duration: 500,
                easing: "swing"
            });
            return false;
        });
    }

    toggleMenu() {
        const el = $("#post-menu-state")
        const className = "data-open";
        const open = el.hasClass(className);
        const content = $("#post-menu-content");
        if (open) {
            content.hide();
            el.removeClass(className);
        } else {
            content.show();
            el.addClass(className);
        }
    }

    lazyLoadInit(el = '.lazyload') {
        if (window.LazyLoad !== undefined) {
            new window.LazyLoad(document.querySelectorAll([el, "[data-lazy=true]"]), {
                root: null,
                rootMargin: "0px",
                threshold: 0
            });
        }
    }

    loadParams() {
        this.data.params = puock_metas;
        this.data.commentVd = this.data.params.vd_comment === 'on';
    }

    initReadProgress() {
        const readProgress = $("#page-read-progress .progress-bar");
        document.addEventListener('scroll', () => {
            const a = window.scrollY / (document.documentElement.scrollHeight - window.innerHeight) * 100;
            readProgress.attr("style", "width:" + a.toFixed(0) + "%");
        });
    }

    pageChangeInit() {
        this.initReadProgress()
        this.modeInit();
        this.loadCommentInfo();
        this.katexParse();
        this.initCodeHighlight();
        this.pageLinkBlankOpenInit()
        this.loadCommentCaptchaImage(null);
        this.generatePostQrcode();
        this.initGithubCard();
        this.keyUpHandle();
        this.loadHitokoto();
        this.asyncCacheViews();
        if (this.data.params.use_post_menu) {
            this.generatePostMenuHTML()
        }
        $('[data-toggle="tooltip"]').tooltip({placement: 'auto', trigger: 'hover'});
        $("#post-main .entry-content").viewer({
            navbar: false,
            url: this.data.params.main_lazy_img ? 'data-src' : 'src'
        });
        new ClipboardJS('.copy-post-link', {
            text: () => {
                const $copyEl = $(".copy-post-link");
                $copyEl.find('span').html("已复制");
                $copyEl.attr("disabled", true);
                setTimeout(() => {
                    $copyEl.find('span').html("复制链接");
                    $copyEl.attr("disabled", false);
                }, 3000);
                return location.href;
            }
        });
        this.lazyLoadInit()
    }


    getPostMenuStructure() {
        let res = []
        for (let item of $(".entry-content").find('h1,h2,h3,h4,h5,h6')) {
            res.push({name: $(item).text().trim(), level: item.tagName.toLowerCase(), id: $(item).attr("id")})
        }
        return res
    }

    generatePostMenuHTML() {
        const menus = this.getPostMenuStructure();
        if (menus.length > 0) {
            let result = "<ul>";
            if (menus.length > 0) {
                let heightLevel = 6;
                for (let i = 0; i < menus.length; i++) {
                    const level = parseInt(menus[i].level[1]);
                    if (level < heightLevel) {
                        heightLevel = level;
                    }
                }
                for (let i = 0; i < menus.length; i++) {
                    const m = menus[i];
                    let pl = 0;
                    const level = parseInt(m.level[1]);
                    if (level > heightLevel) {
                        pl = (level - heightLevel) * 10;
                    }
                    result += `<li style='padding-left:${pl}px' class='t-line-1'><i class='czs-angle-right-l t-sm c-sub mr-1'></i><a class='pk-menu-to a-link t-w-400 t-md' href='#${m.id}'>${m.name}</a></li>`;
                }
            }
            result += "</ul>"
            $("#post-menu-content").html(result)
        } else {
            $("#post-menus").remove()
        }
    }

    initCodeHighlight(fullChange = true) {
        if (window.hljs !== undefined) {
            window.hljs.configure({ignoreUnescapedHTML: true})
            document.querySelectorAll('pre').forEach((block, index) => {
                const el = $(block);
                if (!el.attr("id")) {
                    el.attr("id", "hljs-item-" + index)
                    el.before("<div class='pk-code-tools' data-pre-id='hljs-item-" + index + "'><div class='dot'>" +
                        "<i></i><i></i><i></i></div><div class='actions'><div><i class='i czs-list-clipboard-l cp-code' data-clipboard-target='#hljs-item-" + index + "'></i></div></div></div>")
                    window.hljs.highlightBlock(block);
                    window.hljs.lineNumbersBlock(block);
                }
            });
            if (fullChange) {
                const cp = new ClipboardJS('.cp-code');
                cp.on("success", (e) => {
                    const el = $(e.trigger);
                    el.removeClass("czs-list-clipboard-l").addClass("czs-right-clipboard-l")
                    setTimeout(() => {
                        el.removeClass("czs-right-clipboard-l").addClass("czs-list-clipboard-l")
                    }, 1500)
                })
            }
        }
    }

    generatePostQrcode() {
        //生成微信分享二维码
        const wsEl = $("#wx-share");
        const qrUrl = wsEl.attr("data-url");
        wsEl.attr("data-original-title", `<p class='text-center t-sm mb-1 mt-1'>使用微信扫一扫</p><img width="180" class='mb-1' alt='微信二维码' src='${qrUrl}'/>`)
    }


    localstorageToggle(name, val = null) {
        return val != null ? localStorage.setItem(name, val) : localStorage.getItem(name);
    }

    loadCommentInfo() {
        const authorText = this.localstorageToggle("comment_author"),
            emailText = this.localstorageToggle("comment_email"),
            urlText = this.localstorageToggle("comment_url");
        if (authorText != null && emailText != null) {
            $("#author").val(authorText);
            $("#email").val(emailText);
            $("#url").val(urlText);
        }
    }

    setCommentInfo() {
        this.localstorageToggle("comment_author", $("#author").val());
        this.localstorageToggle("comment_email", $("#email").val());
        this.localstorageToggle("comment_url", $("#url").val());
    }

    asyncCacheViews() {
        if (this.data.params.async_view_id) {
            $.post(this.data.params.home + "/wp-admin/admin-ajax.php?action=async_pk_views",
                {id: this.data.params.async_view_id}, (res) => {
                    if (res.code !== 0) {
                        console.error(res.msg)
                    } else {
                        $("#post-views").text(res.data)
                    }
                }, 'json').error((e) => {
                console.error(e)
            })
        }
    }

    modeInit() {
        let light = this.localstorageToggle('light');
        if (light !== undefined) {
            this.modeChange(light);
        }
    }

    modeChange(isLight = null, isSwitch = false) {
        const body = $("body");
        if (typeof (isLight) === "string") {
            isLight = isLight === 'true';
        }
        if (isLight === null) {
            isLight = body.hasClass(this.data.tag + "-light");
        }
        if (isSwitch) {
            isLight = !isLight;
        }
        let dn = 'd-none';
        if (isLight) {
            $("#logo-light").removeClass(dn);
            $("#logo-dark").addClass(dn);
        } else {
            $("#logo-dark").removeClass(dn);
            $("#logo-light").addClass(dn);
        }
        body.removeClass(isLight ? this.data.tag + "-dark" : this.data.tag + "-light");
        body.addClass(isLight ? this.data.tag + "-light" : this.data.tag + "-dark");
        this.localstorageToggle('light', isLight)
        Cookies.set('mode', isLight ? 'light' : 'dark')
    }

    modeChangeListener() {
        this.modeChange(!window.matchMedia('(prefers-color-scheme:dark)').matches);
    }

    registerModeChangeEvent() {
        try {
            window.matchMedia('(prefers-color-scheme:dark)').addEventListener('change', this.modeChangeListener);
        } catch (ex) {
            window.matchMedia('(prefers-color-scheme:dark)').addListener(this.modeChangeListener);
        }
    }

    infoToastShow(text, title = '提示') {
        const infoToast = $('#infoToast');
        $("#infoToastTitle").html(title);
        $("#infoToastText").html(text);
        infoToast.modal('show');
    }

    registerMobileMenuEvent() {
        const fn = (s) => {
            if (typeof (s) !== 'string') {
                s = 'Out'
            }
            $("#mobile-menu").attr("class", "animated fade" + s + "Left");
            $("#mobile-menu-backdrop").attr("class", "modal-backdrop animated fade" + s + "Right");
        }
        $(document).on("click", "#mobile-menu-backdrop", fn);
        $(document).on("click", ".mobile-menu-close", fn);
        $(document).on("click", ".mobile-menu-s", () => {
            fn('In');
        });
    }

    gotoCommentArea(speed = 800) {
        const top = $("#comments").offset().top - $("#header").height() - 10;
        $('html,body').animate({scrollTop: top}, speed);
        this.lazyLoadInit()
    }

    pushAjaxCommentHistoryState(href) {
        history.pushState({foo: "bar"}, "page 2", href);
    }

    eventCommentPageChangeEvent() {
        $(document).on('click', '.comment-ajax-load a.page-numbers', (e) => {
            const postCommentsEl = $("#post-comments");
            const loadBox = $("#comment-ajax-load");
            $("#comment-cancel").click();
            let href = $(this.ct(e)).attr("href");
            this.pushAjaxCommentHistoryState(href);
            postCommentsEl.html(" ");
            this.gotoCommentArea(200);
            loadBox.removeClass('d-none');
            $.post(href, {}, (data) => {
                postCommentsEl.html($(data).find("#post-comments"));
                loadBox.addClass('d-none');
                this.initCodeHighlight(false);
                this.gotoCommentArea()
            }).error(() => {
                location = href;
            });
            return false;
        })

    }

    eventCommentPreSubmit() {
        $(document).on('submit', '#comment-form', (e) => {
            if ($("#comment-logged").val() === '0' && ($.trim($("#author").val()) === '' || $.trim($("#email").val()) === '')) {
                this.infoToastShow('评论信息不能为空');
                return false;
            }
            if (this.data.params.vd_comment) {
                if ($.trim($("#comment-vd").val()) === '') {
                    this.infoToastShow('验证码不能为空');
                    return false;
                }
            }
            if ($.trim($("#comment").val()) === '') {
                this.infoToastShow('评论内容不能为空');
                return false;
            }
            this.commentSubmit(this.ct(e))
            return false;
        })
    }

    commentSubmit(target) {
        let submitUrl = $("#comment-form").attr("action");
        this.commentFormLoadStateChange();
        $.ajax({
            url: submitUrl,
            data: $(target).serialize(),
            type: $(target).attr('method'),
            success: (data) => {
                this.infoToastShow('评论已提交成功');
                this.loadCommentCaptchaImage(null);
                $("#comment-vd").val("");
                $("#comment").val("");
                if (this.data.comment.replyId != null) {
                    let comment = $('#comment-' + this.data.comment.replyId);
                    let ch = comment.find(".children");
                    if (ch.length === 0) {
                        comment.append("<ul class='children'></ul>");
                        ch = comment.find(".children");
                    }
                    ch.prepend(data);
                } else {
                    $('#post-comments').prepend(data);
                }
                $("#comment-cancel").click();
                this.commentFormLoadStateChange();
                this.setCommentInfo()
            },
            error: (res) => {
                let jsonVal = null;
                try {
                    jsonVal = JSON.parse(res.responseText)
                } catch (e) {
                }
                this.commentFormLoadStateChange();
                if (jsonVal) {
                    this.infoToastShow(jsonVal.msg);
                    if (jsonVal.refresh_code) {
                        this.loadCommentCaptchaImage(null);
                    }
                } else {
                    this.infoToastShow(res.responseText);
                }
            }
        });
    }

    commentFormLoadStateChange() {
        const commentSubmit = $("#comment-submit");
        if (this.data.comment.loading) {
            commentSubmit.html("请等待" + this.data.comment.time + "s");
            this.data.comment.val = setInterval(() => {
                if (this.data.comment.time <= 1) {
                    clearInterval(this.data.comment.val);
                    commentSubmit.html("提交评论");
                    commentSubmit.removeAttr("disabled");
                    this.data.comment.time = 5;
                } else {
                    --this.data.comment.time;
                    commentSubmit.html("请等待" + this.data.comment.time + "s");
                }
            }, 1000);
        } else {
            commentSubmit.html('<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>提交中...');
            commentSubmit.attr("disabled", true)
        }
        this.data.comment.loading = !this.data.comment.loading;
    }

    eventOpenCommentBox() {
        $(document).on("click", "[id^=comment-reply-]", (e) => {
            this.data.comment.replyId = $(this.ct(e)).attr("data-id");
            if ($.trim(this.data.comment.replyId) === '') {
                this.infoToastShow('结构有误');
                return;
            }
            const cf = $("#comment-form"),
                cb = $("#comment-box-" + this.data.comment.replyId);
            cf.addClass("box-sw");
            cb.removeClass("d-none").append(cf);
            $("#comment-cancel").removeClass("d-none");
            $("#comment").val("");
            $("#comment_parent").val(this.data.comment.replyId);
        })

    }

    eventCloseCommentBox() {
        $(document).on("click", "#comment-cancel", () => {
            const cf = $("#comment-form"),
                cb = $("#comment-box-" + this.data.comment.replyId);
            cf.removeClass("box-sw");
            cb.addClass("d-none");
            $("#comment-form-box").append(cf);
            $("#comment-cancel").addClass("d-none");
            this.data.comment.replyId = null;
        })
    }

    eventSendPostLike() {
        $(document).on("click", "#post-like", (e) => {
            const vm = $(this.ct(e))
            let id = vm.attr("data-id");
            $.post("/wp-admin/admin-ajax.php", {action: 'puock_like', um_id: id, um_action: 'like'}, (res) => {
                if (res.e === 0) {
                    vm.find("span").html(res.d);
                    vm.addClass("bg-primary text-light");
                } else {
                    this.infoToastShow(res.t);
                }
            }, 'json').error(() => {
                this.infoToastShow('点赞异常');
            })
        })
    }

    eventSmiley() {
        const el = "#twemoji"
        $(document).on('click', '#comment-smiley', () => {
            $(el).modal("show");
        });
        $(document).on('click', '.smiley-img', (e) => {
            const comment = $("#comment");
            comment.val(comment.val() + ' ' + $(this.ct(e)).attr("data-id") + ' ');
            $(el).modal("hide");
        })
    }

    eventPostMainBoxResize() {
        $(document).on("click", ".post-main-size", () => {
            const postMain = $("#post-main"),
                postSlider = $("#sidebar"),
                min = postMain.hasClass("col-lg-8");
            postMain.removeClass(min ? "col-lg-8" : "col-lg-12");
            postMain.addClass(min ? "col-lg-12" : "col-lg-8");
            min ? postSlider.removeClass("d-lg-block") : postSlider.addClass("d-lg-block");
        })
    }

    katexParse() {
        return;
        if (typeof katex !== 'undefined') {
            const ks = $(document).find(".language-katex");
            const kl = $(document).find(".language-inline");
            console.log(ks, kl)
            if (ks.length > 0) {
                ks.parent("pre").attr("style", "text-align: center; background: none;");
                ks.addClass("katex-container").removeClass("language-katex");
                $(".katex-container").each((_, v) => {
                    this.katexItemParse($(v))
                });
            }
            if (kl.length > 0) {
                kl.each((_, v) => {
                    this.katexItemParse($(v))
                });
            }
        }
    }

    katexItemParse(item) {
        const katexText = item.text();
        const el = item.get(0);
        if (item.parent("code").length === 0) {
            try {
                katex.render(katexText, el)
            } catch (err) {
                item.html("<span class='err'>" + err)
            }
        }
    }

    initGithubCard() {
        $.each($(".github-card"), (index, _el) => {
            const el = $(_el);
            const repo = el.attr("data-repo");
            if (repo) {
                $.get(`https://api.github.com/repos/${repo}`, (res) => {
                    const link_html = `class="hide-hover" href="${res.url}" target="_blank" rel="noreferrer"`;
                    el.html(`<div class="card-header"><i class="czs-github-logo"></i><a ${link_html}>${res.full_name}</a></div>
                    <div class="card-body">${res.description}</div>
                    <div class="card-footer">
                    <div class="row">
                    <div class="col-4"><i class="czs-star"></i><a ${link_html}>${res.stargazers_count}</a></div>
                    <div class="col-4"><i class="czs-code-fork"></i><a ${link_html}>${res.forks}</a></div>
                    <div class="col-4"><i class="czs-eye"></i><a ${link_html}>${res.subscribers_count}</a></div>
                    </div>
                    </div>
                `);
                    el.addClass("loaded");
                }, 'json').error((err) => {
                    el.html(`<div class="alert alert-danger"><i class="czs-warning"></i>&nbsp;请求Github项目详情异常：${repo}</div>`)
                });
            }
        })
    }

    keyUpHandle() {
        const prevOrNextEl = $(".single-next-or-pre")
        if (prevOrNextEl) {
            window.onkeyup = function (event) {
                let url = null;
                switch (event.key) {
                    case 'ArrowLeft': {
                        url = prevOrNextEl.find("a[rel='prev']").attr("href");
                        break
                    }
                    case 'ArrowRight': {
                        url = prevOrNextEl.find("a[rel='next']").attr("href");
                        break
                    }
                }
                if (url) {
                    window.location = url
                }
            }
        }
    }

    loadHitokoto() {
        $(".widget-puock-hitokoto").each((_, v) => {
            const el = $(v);
            $.get("https://v1.hitokoto.cn/", (res) => {
                el.find(".t").text(res.hitokoto);
                el.find('.f').text(res.from);
                el.find('.fb').removeClass("d-none");
            }, 'json')
        })
    }

}

$(() => {
    window.Puock = new Puock()
    window.Puock.onceInit()
})

