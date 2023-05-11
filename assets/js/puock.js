const puockGlobalData = {
    loads: {}
}

const TYPE_PRIMARY = "primary"
const TYPE_WARNING = "warning"
const TYPE_DANGER = "danger"
const TYPE_SUCCESS = "success"
const TYPE_INFO = "info"

class Puock {

    data = {
        tag: 'puock',
        params: {
            home: null,
            use_post_menu: false,
            is_single: false,
            is_pjax: false,
            vd_comment: false,
            vd_gt_id: null,
            vd_type: null,
            main_lazy_img: false,
            link_blank_open: false,
            async_view_id: null,
            mode_switch: false,
            async_view_generate_time: null,
            off_img_viewer:false,
            off_code_highlighting:false
        },
        comment: {
            loading: false,
            time: 5,
            val: null,
            replyId: null
        },
        instance: {},
        modalStorage: {}
    }

    // 全局一次加载或注册的事件
    onceInit() {
        this.pageInit()
        $(document).on("click", ".fancybox", () => {
            return false;
        });
        $(document).on("click", "#rb-float-actions>div", (e) => {
            const el = $(this.ct(e));
            const to = el.data("to");
            if (to) {
                const scroll_val = to === 'top' ? 0 : window.document.body.clientHeight;
                $('html,body').stop().animate({scrollTop: scroll_val}, 50)
                return;
            }
            const toArea = el.data("to-area");
            if (toArea) {
                this.gotoArea(toArea)
            }
        });
        $(document).on("click", ".colorMode", () => {
            this.modeChange(null, true);
        });
        $(document).on("click", ".captcha", (e) => {
            this.loadCommentCaptchaImage($(this.ct(e)))
        });
        if (this.data.params.is_pjax) {
            this.instanceClickLoad()
        }
        this.initBasicDOMEvent()
        this.sidebarMenuEventInit()
        this.searchInit()
        this.eventShareStart()
        this.modeInit();
        this.registerMobileMenu()
        this.registerModeChangeEvent()
        this.eventCommentPageChangeEvent()
        this.eventCommentPreSubmit()
        this.eventSmiley()
        this.eventOpenCommentBox()
        this.eventCloseCommentBox()
        this.eventSendPostLike()
        this.eventPostMainBoxResize()
        this.swiperOnceEvent()
        this.initModalToggle()
        layer.config({shade: 0.5})
        console.log("\n %c Puock Theme %c https://github.com/Licoy/wordpress-theme-puock \n\n",
            "color:#f1ab0e;background:#030307;padding:5px 0;border-top-left-radius:8px;border-bottom-left-radius:8px",
            "background:#aa80ff;padding:5px 0;border-top-right-radius:8px;border-bottom-right-radius:8px");
    }

    pageInit() {
        this.loadParams()
        this.pageChangeInit()
        if (this.data.params.is_single) {
            if (this.data.params.use_post_menu) {
                this.generatePostMenuHTML()
            }
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
        InstantClick.on('change', (e) => {
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

    initBasicDOMEvent() {
        // el show or hide event
        $(document).on("click", ".toggle-el-show-hide", (e) => {
            const el = $(this.ct(e));
            const target = $(el.attr("data-target"));
            const self = $(el.attr("data-self"));
            const modalTitle = el.attr("data-modal-title");
            if (target.hasClass("d-none")) {
                self.addClass("d-none");
                target.removeClass("d-none");
            } else {
                self.removeClass("d-none");
                target.addClass("d-none");
            }
            if (modalTitle) {
                el.closest(".modal").find(".modal-title").text(modalTitle);
            }
        });
        // form ajax submit
        $(document).on("submit", ".ajax-form", (e) => {
            e.preventDefault();
            const form = $(this.ct(e));
            const formEls = form.find(":input")
            if (formEls.length === 0) {
                this.toast('表单元素为空', TYPE_DANGER)
                return false;
            }
            for (let i = 0; i < formEls.length; i++) {
                const el = $(formEls[i]);
                if (el.attr("data-required") !== undefined && el.val() === "") {
                    this.toast(el.attr("data-tip") || el.attr("placeholder"), TYPE_WARNING)
                    return false;
                }
            }
            const validateType = form.data("validate");
            const startSubmit = (args = {}) => {
                const url = form.attr("action");
                const method = form.attr("method");
                const data = this.parseFormData(form, args);
                const dataType = "json";
                const successTip = form.attr("data-success");
                const errorTip = form.attr("data-error");
                const loading = this.startLoading()
                $.ajax({
                    url, method, data, dataType,
                    success: (res) => {
                        this.stopLoading(loading)
                        if (res.code === 0 || res.success) {
                            this.toast(res.msg || successTip, TYPE_SUCCESS)
                            if (form.data("no-reset") === undefined) {
                                form.trigger("reset")
                            }
                            if (res.data) {
                                const resData = res.data
                                if (resData.action) {
                                    setTimeout(() => {
                                        switch (resData.action) {
                                            case 'reload':
                                                this.goUrl(window.location.href)
                                                break
                                        }
                                    }, 500)
                                }
                            }
                        } else {
                            this.toast(res.msg || res.data || errorTip, TYPE_DANGER)
                            this.loadCommentCaptchaImage(form, true)
                        }
                    },
                    error: (e) => {
                        this.stopLoading(loading)
                        this.toast(`请求错误：${e.statusText}`, TYPE_DANGER)
                        this.loadCommentCaptchaImage(form, true)
                    }
                })
            }
            if (validateType === 'gt') {
                this.gt.validate((code) => {
                    startSubmit(code)
                });
            } else {
                startSubmit()
            }
            return false;
        })
    }

    pageLinkBlankOpenInit() {
        if (this.data.params.link_blank_open) {
            $(".entry-content").find("a").each((_, item) => {
                $(item).attr('target', 'blank')
            })
        }
    }

    loadCommentCaptchaImage(el, parent = false) {
        if (parent) {
            el.find(".captcha").each((_, item) => {
                this.loadCommentCaptchaImage($(item))
            })
        } else {
            const url = el.attr("src") + '&t=' + (new Date()).getTime()
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
            if (!open) {
                search.find("input").focus();
            }
        }
        $(document).on("click", ".search-modal-btn", () => {
            toggle();
        });
        $(document).on("click", "#search-backdrop", () => {
            toggle();
        })
        $(document).on("submit", ".global-search-form", (e) => {
            e.preventDefault();
            const el = $(this.ct(e));
            this.goUrl(el.attr("action") + "/?" + el.serialize())
        })
    }

    goUrl(url) {
        if (this.data.params.is_pjax) {
            InstantClick.go(url)
        } else {
            window.location.href = url
        }
    }

    gt = {
        validate: (success = undefined) => {
            this.data.instance.gt_callback = success
            this.data.instance.gt.showCaptcha();
        }
    }

    validateInit() {
        if (this.data.params.vd_type === 'gt') {
            initGeetest4({
                captchaId: this.data.params.vd_gt_id,
                product: 'bind',
            }, (captchaObj) => {
                this.data.instance.gt = captchaObj;
                captchaObj.onSuccess(() => {
                    const code = this.data.instance.gt.getValidate();
                    this.data.instance.gt_callback && this.data.instance.gt_callback(code)
                })
            });
        }
    }

    rippleInit() {
        const args = {
            debug: false,
            on: 'mousedown',
            opacity: 0.4,
            color: "var(--pk-c-light)",
            multi: false,
            duration: 0.6,
            rate: function (pxPerSecond) {
                return pxPerSecond;
            },
            easing: 'linear'
        }
        jQuery.ripple(".btn", args);
        jQuery.ripple(".ww", args);
    }

    eventShareStart() {
        $(document).on("click", ".share-to", (e) => {
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
            if (to) window.open(to, '_blank');
        });
    }

    sidebarMenuEventInit() {
        let currentOpenSubMenu = null;
        $(document).on("touchend", ".post-menu-toggle", (e) => {
            e.preventDefault();
            this.toggleMenu();
        });
        $(document).on("click", ".post-menu-toggle", () => {
            this.toggleMenu();
        });
        $(document).on("click", ".post-menu-item", (e) => {
            const el = $(this.ct(e))
            const id = el.attr("data-id")
            if (currentOpenSubMenu) {
                const parentUl = el.parents("ul")
                let curClass = "post-menu-sub-" + currentOpenSubMenu
                while (true) {
                    if (typeof (curClass) === "undefined") {
                        break
                    }
                    const currentMenu = $("." + curClass)
                    const classStr = currentMenu.attr("class")
                    const und = typeof (classStr) == "undefined"
                    if (und || parentUl.attr("class") === currentMenu.attr("class")) {
                        break;
                    } else {
                        currentMenu.hide();
                        curClass = currentMenu.parents("ul").attr("class");
                    }
                }
            }
            const subMenu = $(".post-menu-sub-" + id)
            if (subMenu.length > 0) {
                subMenu.show()
                currentOpenSubMenu = id
            }
        });
        $(document).on("click", ".pk-menu-to", (e) => {
            const to = $(this.ct(e)).attr("href");
            const headerHeight = $("#header").innerHeight();
            $("html, body").stop().animate({
                scrollTop: ($(to).offset().top - headerHeight - 10) + "px"
            }, {
                duration: 50,
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

    lazyLoadInit(parent = null, el = '.lazy') {
        if (window.lozad) {
            const observer = lozad([el, 'img[data-lazy="true"]'], {
                rootMargin: '10px 0px',
                threshold: 0.1,
                enableAutoReload: true,
                load: (el) => {
                    el.classList.add('loaded');
                    el.src = el.getAttribute('data-src');
                }
            });
            observer.observe();
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

    tooltipInit(el = $("[data-bs-toggle=\"tooltip\"]")) {
        [...el].map(tooltipTriggerEl => {
            new bootstrap.Tooltip(tooltipTriggerEl, {
                placement: 'bottom', trigger: 'hover'
            })
        })
    }

    pageChangeInit() {
        this.initReadProgress()
        this.modeInit();
        this.loadCommentInfo();
        this.katexParse();
        this.initCodeHighlight();
        this.pageLinkBlankOpenInit()
        this.initGithubCard();
        this.keyUpHandle();
        this.loadHitokoto();
        this.asyncCacheViews();
        this.swiperInit();
        this.validateInit();
        this.rippleInit();
        if (this.data.params.use_post_menu) {
            this.generatePostMenuHTML()
        }
        this.tooltipInit()
        if(!this.data.params.off_img_viewer){
            jQuery(".entry-content").viewer({
                navbar: false,
                url: this.data.params.main_lazy_img ? 'data-src' : 'src'
            });
        }
        const cp = new ClipboardJS('.pk-copy', {
            text: (trigger) => {
                const t = $(trigger)
                let input = t.attr("data-cp-input")
                let el = t.attr("data-cp-el")
                let val = t.attr("data-cp-val")
                let text;
                if (typeof val !== "undefined") {
                    text = val
                } else if (typeof input !== "undefined") {
                    text = $(input).val()
                } else if (typeof el !== "undefined") {
                    text = $(el).text()
                } else {
                    text = t.text()
                }
                return text;
            },
        });
        cp.on("success", (e) => {
            let name = $(e.trigger).attr('data-cp-title') || "";
            this.toast(`复制${name}成功`)
        })
        cp.on("error", (e) => {
            let name = $(e.trigger).attr('data-cp-title') || "";
            this.toast(`复制${name}失败`, TYPE_DANGER)
        })
        this.lazyLoadInit()
        $('#post-main, #sidebar').theiaStickySidebar({
            additionalMarginTop: 20
        });
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
                const finalMenus = []
                let maxLevel = 6;
                const initChildren = (item) => {
                    item.children = []
                    return item
                }
                const getLevel = (item) => {
                    item.levelInt = parseInt(item.level.replace("h", ""))
                    if (item.levelInt < maxLevel) {
                        maxLevel = item.levelInt
                    }
                    return item.levelInt
                }
                const firstMenu = initChildren(menus[0])
                const firstLevel = getLevel(firstMenu)
                let loadIndex = 0;
                const eqLevelFn = (unMenu, parentMen) => {
                    const nextUnMenu = loadMenu(unMenu, parentMen)
                    if (nextUnMenu != null) {
                        if (getLevel(nextUnMenu) === getLevel(unMenu)) {
                            return eqLevelFn(nextUnMenu, parentMen)
                        }
                    }
                    return nextUnMenu;
                }
                const loadMenu = (menu, parentMenu) => {
                    if (loadIndex >= menus.length - 1) {
                        return null;
                    }
                    const nextIndex = ++loadIndex;
                    const nextMenu = initChildren(menus[nextIndex])
                    const nowLevel = getLevel(menu)
                    const nextLevel = getLevel(nextMenu)
                    let unknownMenu = null;
                    if (nextLevel === firstLevel) {
                        finalMenus.push(nextMenu)
                        unknownMenu = loadMenu(nextMenu, null)
                    } else if (nextLevel > nowLevel) {
                        menu.children.push(nextMenu)
                        unknownMenu = loadMenu(nextMenu, menu)
                    } else if (nextLevel === nowLevel && parentMenu != null) {
                        parentMenu.children.push(nextMenu)
                        unknownMenu = loadMenu(nextMenu, parentMenu)
                    } else {
                        return nextMenu
                    }
                    if (unknownMenu != null) {
                        const unknownLevel = getLevel(unknownMenu)
                        if (unknownLevel === nowLevel) {
                            parentMenu.children.push(unknownMenu)
                            unknownMenu = eqLevelFn(unknownMenu, parentMenu)
                        }
                    }
                    return unknownMenu
                }
                finalMenus.push(firstMenu)
                while (true) {
                    const unknownMenu = loadMenu(firstMenu, null)
                    if (unknownMenu == null) {
                        break
                    }
                    loadMenu(unknownMenu, null)
                }
                let menuIndex = 0;
                const outHtml = (item, parent) => {
                    ++menuIndex;
                    const id = menuIndex;
                    const pl = (item.levelInt - maxLevel) * 10
                    let out = `<li data-level="${item.levelInt}" style='padding-left:${pl}px'>`
                    out += `<a class='pk-menu-to a-link t-w-400 t-md post-menu-item' data-parent="${parent}" data-id="${id}" href='#${item.id}'><i class='fa ${item.children.length > 0 ? 'fa-angle-right' : 'fa-file-invoice'} t-sm c-sub mr-1'></i> ${item.name}</a>`
                    if (item.children.length > 0) {
                        out += `<ul class="post-menu-sub-${id}" data-parent="${parent + 1}">`
                        for (let child of item.children) {
                            out += outHtml(child, id)
                        }
                        out += `</ul>`
                    }
                    out += "</li>"
                    return out;
                }
                finalMenus.forEach(item => {
                    result += outHtml(item, menuIndex)
                })
            }
            result += "</ul>"
            $("#post-menu-content-items").html(result);
            $(".post-menus-box").show();
        }
    }

    initCodeHighlight(fullChange = true) {
        if(this.data.params.off_code_highlighting){
            return
        }
        if (window.hljs !== undefined) {
            window.hljs.configure({ignoreUnescapedHTML: true})
            document.querySelectorAll('pre').forEach((block, index) => {
                const el = $(block);
                const codeChildClass = el.children("code") ? el.children("code").attr("class") : undefined;
                if (codeChildClass) {
                    if (codeChildClass.indexOf("katex") !== -1 || codeChildClass.indexOf("latex") !== -1 || codeChildClass.indexOf("flowchart") !== -1
                        || codeChildClass.indexOf("flow") !== -1 || codeChildClass.indexOf("seq") !== -1 || codeChildClass.indexOf("math") !== -1) {
                        return;
                    }
                }
                if (!el.attr("id")) {
                    el.attr("id", "hljs-item-" + index)
                    el.before("<div class='pk-code-tools' data-pre-id='hljs-item-" + index + "'><div class='dot'>" +
                        "<i></i><i></i><i></i></div><div class='actions'><div><i class='i fa fa-copy cp-code' data-clipboard-target='#hljs-item-" + index + "'></i></div></div></div>")
                    window.hljs.highlightBlock(block);
                    window.hljs.lineNumbersBlock(block);
                }
            });
            if (fullChange) {
                const cp = new ClipboardJS('.cp-code');
                cp.on("success", (e) => {
                    e.clearSelection();
                    this.toast('已复制到剪切板')
                })
            }
        }
    }

    localstorageToggle(name, val = null) {
        return val != null ? localStorage.setItem(name, val) : localStorage.getItem(name);
    }

    loadCommentInfo() {
        const authorText = this.localstorageToggle("comment_author"),
            emailText = this.localstorageToggle("comment_email"),
            urlText = this.localstorageToggle("comment_url");
        if (authorText != null && emailText != null) {
            $("#comment_author").val(authorText);
            $("#comment_email").val(emailText);
            $("#comment_url").val(urlText);
        }
    }

    setCommentInfo() {
        this.localstorageToggle("comment_author", $("#comment_author").val());
        this.localstorageToggle("comment_email", $("#comment_email").val());
        this.localstorageToggle("comment_url", $("#comment_url").val());
    }

    asyncCacheViews() {
        if (this.data.params.async_view_id && this.data.params.async_view_generate_time) {
            if (((new Date()).getTime() / 1000) - this.data.params.async_view_generate_time > 10) {
                $.post(this.data.params.home + "/wp-admin/admin-ajax.php?action=async_pk_views",
                    {id: this.data.params.async_view_id}, (res) => {
                        if (res.code !== 0) {
                            console.error(res.msg)
                        } else {
                            $("#post-views").text(res.data)
                        }
                    }, 'json').fail((e) => {
                    console.error(e)
                })
            }
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
        $(".colorMode").each((_, e) => {
            const el = $(e);
            let target;
            if (el.prop("localName") === 'i') {
                target = el;
            } else {
                target = $(el).find("i");
            }
            if (target) {
                target.removeClass("fa-sun").removeClass("fa-moon").addClass(isLight ? "fa-sun" : "fa-moon");
            }
        })
        body.removeClass(isLight ? this.data.tag + "-dark" : this.data.tag + "-light");
        body.addClass(isLight ? this.data.tag + "-light" : this.data.tag + "-dark");
        this.localstorageToggle('light', isLight)
        Cookies.set('mode', isLight ? 'light' : 'dark')
    }

    modeChangeListener() {
        this.modeChange(!window.matchMedia('(prefers-color-scheme:dark)').matches);
    }

    registerModeChangeEvent() {
        if (this.data.params.mode_switch) {
            try {
                window.matchMedia('(prefers-color-scheme:dark)').addEventListener('change', () => {
                    this.modeChangeListener()
                });
            } catch (ex) {
                window.matchMedia('(prefers-color-scheme:dark)').addListener(() => {
                    this.modeChangeListener()
                });
            }
        }
    }

    infoToastShow(text, title = '提示') {
        const infoToast = $('#infoToast');
        $("#infoToastTitle").html(title);
        $("#infoToastText").html(text);
        infoToast.modal('show');
    }

    registerMobileMenu() {
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

    gotoArea(el, speed = 50) {
        const top = $(el).offset().top - $("#header").height() - 10;
        $('html,body').stop().animate({scrollTop: top}, speed);
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
            this.gotoArea("#comments");
            loadBox.removeClass('d-none');
            $.post(href, {}, (data) => {
                postCommentsEl.html($(data).find("#post-comments"));
                loadBox.addClass('d-none');
                this.initCodeHighlight(false);
                this.lazyLoadInit(postCommentsEl);
            }).fail(() => {
                location = href;
            });
            return false;
        })

    }

    parseFormData(formEl, args = {}) {
        const dataArr = formEl.serializeArray();
        const data = {...args};
        for (let i = 0; i < dataArr.length; i++) {
            data[dataArr[i].name] = dataArr[i].value;
        }
        return jQuery.param(data);
    }

    eventCommentPreSubmit() {
        $(document).on('submit', '#comment-form', (e) => {
            e.preventDefault();
            if ($("#comment-logged").val() === '0' && ($.trim($("#comment_author").val()) === '' || $.trim($("#comment_email").val()) === '')) {
                this.toast('评论信息不能为空', TYPE_WARNING);
                return;
            }
            if ($.trim($("#comment").val()) === '') {
                this.toast('评论内容不能为空', TYPE_WARNING);
                return;
            }
            if (this.data.params.vd_comment) {
                if (this.data.params.vd_type === 'img') {
                    if ($.trim($("#comment-vd").val()) === '') {
                        this.toast('验证码不能为空', TYPE_WARNING);
                        return;
                    }
                } else {
                    this.gt.validate((code) => {
                        this.commentSubmit(this.ct(e), code)
                    })
                    return;
                }
            }
            this.commentSubmit(this.ct(e))
        })
    }

    commentSubmit(target, args = {}) {
        let submitUrl = $("#comment-form").attr("action");
        this.commentFormLoadStateChange();
        const el = $(target);
        $.ajax({
            url: submitUrl,
            data: this.parseFormData(el, args),
            type: el.attr('method'),
            success: (data) => {
                this.toast('评论已提交成功', TYPE_SUCCESS);
                this.loadCommentCaptchaImage($(".comment-captcha"));
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
                    this.toast(jsonVal.msg, TYPE_DANGER);
                    if (jsonVal.refresh_code) {
                        this.loadCommentCaptchaImage($(".comment-captcha"));
                    }
                } else {
                    this.toast(res.responseText, TYPE_DANGER);
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
                this.toast('结构有误', TYPE_DANGER);
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
                    this.toast(res.t);
                }
            }, 'json').fail(() => {
                this.toast('点赞异常', TYPE_DANGER);
            })
        })
    }

    eventSmiley() {
        $(document).on('click', '.smiley-img', (e) => {
            const comment = $("#comment");
            comment.val(comment.val() + ' ' + $(this.ct(e)).attr("data-id") + ' ');
            layer.closeAll();
        })
    }

    startLoading() {
        return layer.load(0, {
            shade: [0.5, '#000']
        })
    }

    stopLoading(id = null) {
        layer.close(id)
    }

    getRemoteHtmlNode(url, callback) {
        const loading = this.startLoading()
        $.ajax({
            url: url,
            type: 'GET',
            success: (res)=>{
                this.stopLoading(loading)
                callback(res)
            },
            error: (err)=> {
                console.error(err)
                this.stopLoading(loading)
                this.toast("获取内容节点数据失败", TYPE_DANGER)
            }
        })
    }

    initModalToggle() {
        $(document).on("click", ".pk-modal-toggle", (e) => {
            const el = $(this.ct(e));
            const noTitle = el.data("no-title") !== undefined;
            const noPadding = el.data("no-padding") !== undefined;
            const title = el.attr("title") || el.data("title") || '提示';
            const url = el.data("url");
            const onceLoad = el.data("once-load")
            const id = SparkMD5.hash(url)
            if (onceLoad && this.data.modalStorage[id]) {
                this.modalLoadRender(id, this.data.modalStorage[id], title, noTitle, noPadding)
            } else {
                this.getRemoteHtmlNode(url, (res) => {
                    if (onceLoad) {
                        if (!this.data.modalStorage[id]) {
                            this.data.modalStorage[id] = res;
                        }
                    }
                    this.modalLoadRender(id, res, title, noTitle, noPadding)
                })
            }
        })
    }

    modalLoadRender(dataId, html, title, noTitle, noPadding) {
        const id = "pk-modal-" + dataId;
        layer.open({
            type: 1,
            title: noTitle ? false : title,
            content: `<div id="${id}" style='${noPadding ? '' : 'padding: 20px'}' class='fs14'>${html}</div>`,
            shadeClose: true,
        })
        const idEl = $("#" + id);
        this.lazyLoadInit(idEl);
        this.tooltipInit(idEl.find("[data-bs-toggle=\"tooltip\"]"));
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
                    const link_html = `class="hide-hover" href="${res.html_url}" target="_blank" rel="noreferrer"`;
                    el.html(`<div class="card-header"><i class="fa-brands fa-github"></i><a ${link_html}>${res.full_name}</a></div>
                    <div class="card-body">${res.description}</div>
                    <div class="card-footer">
                    <div class="row">
                    <div class="col-4"><i class="fa-regular fa-star"></i><a ${link_html}>${res.stargazers_count}</a></div>
                    <div class="col-4"><i class="fa-solid fa-code-fork"></i><a ${link_html}>${res.forks}</a></div>
                    <div class="col-4"><i class="fa-regular fa-eye"></i><a ${link_html}>${res.subscribers_count}</a></div>
                    </div>
                    </div>
                `);
                    el.addClass("loaded");
                }, 'json').fail((err) => {
                    el.html(`<div class="alert alert-danger"><i class="fa fa-warning"></i>&nbsp;请求Github项目详情异常：${repo}</div>`)
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

    swiperInit() {
        $("[data-swiper='init']").each((_, _el) => {
            const el = $(_el);
            const swiperClass = el.attr("data-swiper-class");
            const elArgs = el.attr("data-swiper-args");
            let args = {}
            if (elArgs) {
                args = JSON.parse(elArgs)
            }
            new Swiper('.' + swiperClass, args);
        });
    }

    swiperOnceEvent() {
        $(document).on("click", ".swiper-slide a", (e) => {
            if (this.data.params.is_pjax) {
                e.preventDefault();
                this.goUrl(e.currentTarget.href)
            }
        });
    }

    loadHitokoto() {
        setTimeout(() => {
            $(".widget-puock-hitokoto").each((_, v) => {
                const el = $(v);
                const api = el.attr("data-api") || "https://v1.hitokoto.cn/"
                $.get(api, (res) => {
                    el.find(".t").text(res.hitokoto ?? res.content ?? "无内容");
                    el.find('.f').text(res.from);
                    el.find('.fb').removeClass("d-none");
                }, 'json').fail((err) => {
                    console.error(err)
                    el.find(".t").text("加载失败：" + err.responseText || err);
                    el.remove(".fb");
                })
            })
        }, 300)
    }


    toast(msg, type = TYPE_PRIMARY, options = {}) {
        options = Object.assign({
            duration: 2600,
            close: false,
            position: 'right',
            gravity: 'bottom',
            offset: {},
            className: 't-' + type,
        }, options)
        const t = Toastify({
            text: msg,
            ...options
        });
        t.showToast();
        return t;
    }

}

jQuery(() => {
        if (window.$ === undefined) {
            window.$ = jQuery;
        }
        window.Puock = new Puock()
        window.Puock.onceInit()
    }
)

