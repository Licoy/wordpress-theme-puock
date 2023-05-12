jQuery(function () {
    (function ($, $p) {

        class PuockPageAi {

            data = null

            constructor() {
                this.data = {
                    chatList:[]
                }
                this.init()
                this.onAiSubmit()
                this.onClearHistory()
                this.onCopyChat()
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
                $(".chat-submit").on("click", (e) => {
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
                    const chat = this.putMsg({
                        ai: true,
                        avatar: aiMetaInfo.aiAvatar,
                        content: '',
                        load: true
                    })
                    el.attr("disabled", true)
                    inputEl.val("")
                    const chatEl = $("#" + chat.id)
                    const contentEl = chatEl.find(".content-box")
                    const closeLoading = () => {
                        chatEl.find(".cursor-blink-after").removeClass("cursor-blink-after")
                        el.attr("disabled", false)
                    }
                    $.post(aiMetaInfo.url, {text: text}, (res)=> {
                        chat.content = res
                        contentEl.html(this.parseContent(res))
                        $p.initCodeHighlight("#"+chat.id)
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
                const chat = {id:id, ...data}
                this.data.chatList.push(chat)
                const html = `<div id="${id}" class="chat-item is-${data.ai ? 'ai' : 'user'}">
                                <div class="row">
                                    <div class="col-auto">
                                        <img src="${data.avatar}" class="avatar md-avatar" alt="avatar">
                                    </div>
                                    <div class="col">
                                        <div class="fs14 content-box ${data.ai ? 'cursor-blink-after' : ''}">${this.parseContent(data.content)}</div>
                                        <div class="d-flex fs14 align-items-center mt-2">
                                            <div class="text-muted fs12 primary-text-hover pk-copy" data-cp-title="对话信息" data-cp-func="puockAiChatCopy" data-id="${id}"><i class="fa-regular fa-copy mr-1"></i>复制</div>
                                        </div>
                                    </div>
                                </div>
                            </div>`
                $(".chats").append(html)
                $p.initCodeHighlight("#"+id)
                return chat
            }

            onClearHistory() {
                $(".chat-clear-history").on("click", () => {
                    const template = $(".chats").find(".chat-template")
                    const template_html = template.prop("outerHTML") ?? ""
                    layer.confirm("确定要清空历史记录吗？", {
                        btn: ['确定', '取消']
                    }, (index) => {
                        layer.close(index)
                        $(".chats").html(template_html)
                    })
                })
            }

            onCopyChat(){
                window.puockAiChatCopy = (el)=>{
                    const id = el.data("id")
                    const chat = this.data.chatList.find((val)=>{
                        if(val.id===id){
                            return val;
                        }
                        return null;
                    })
                    return chat ? chat.content : "";
                }
            }

            parseContent(str){
                return marked.parse(str, {
                    breaks: true
                })
            }

        }

        new PuockPageAi();
    })(window.jQuery, window.Puock)
})
