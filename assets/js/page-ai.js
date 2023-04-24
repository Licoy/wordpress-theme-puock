jQuery(function () {
    (function ($, $p) {

        class PuockPageAi {
            constructor() {
                this.init()
                this.onAiSubmit()
                this.onClearHistory()
            }

            init() {
                $(".chat-btn-init").remove()
                $(".chat-btn-box").removeClass("d-none")
            }

            onAiSubmit() {
                $(".chat-input").on('keydown', function (e) {
                    if (e.ctrlKey && e.which === 13) {
                        $(".chat-submit").click()
                    }
                });
                $(".chat-submit").on("click",  (e) => {
                    const el = $(e.currentTarget)
                    const inputEl = $(".chat-input")
                    const text = $.trim(inputEl.val())
                    if (text === "") {
                        $p.toast('请先输入内容')
                        return
                    }
                    this.putMsg({
                        ai: false,
                        avatar: aiMetaInfo.userAvatar,
                        content: text,
                    })
                    const chatId = this.putMsg({
                        ai: true,
                        avatar: aiMetaInfo.aiAvatar,
                        content: '',
                        load:  true
                    })
                    el.attr("disabled", true)
                    inputEl.val("")
                    const chatEl = $("#" + chatId)
                    const contentEl = chatEl.find(".content-box")
                    const closeLoading = () => {
                        chatEl.find(".cursor-blink-after").removeClass("cursor-blink-after")
                        el.attr("disabled", false)
                    }
                    $.post(aiMetaInfo.url, {text: text}, function (res) {
                        contentEl.html(res)
                        closeLoading()
                        $('html,body').stop().animate({scrollTop: $(document).height()}, 200)
                    }).fail(function (err) {
                        closeLoading()
                        console.error(err)
                        contentEl.html("<code>请求异常</code>")
                    })
                })
            }

            putMsg(data) {
                const id = "chat-" + ((new Date().getTime()) + "") + (Math.floor(Math.random() * 1000) + "")
                const html = `<div id="${id}" class="chat-item is-${data.ai ? 'ai' : 'user'}">
                                <div class="row">
                                    <div class="col-auto">
                                        <img src="${data.avatar}" class="avatar md-avatar" alt="avatar">
                                    </div>
                                    <div class="col fs14 content-box ${data.ai ? 'cursor-blink-after':''}">${data.content}</div>
                                </div>
                            </div>`
                $(".chats").append(html)
                return id
            }

            onClearHistory() {
                $(".chat-clear-history").on("click", () => {
                    layer.confirm("确定要清空历史记录吗？", {
                        btn: ['确定', '取消']
                    }, (index) => {
                        layer.close(index)
                        $(".chats").html("")
                    })
                })
            }
        }

        new PuockPageAi();
    })(window.jQuery, window.Puock)
})
