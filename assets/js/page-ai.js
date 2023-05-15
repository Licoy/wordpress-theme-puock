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
                $(".chat-submit").on("click", async (e) => {
                    const el = $(e.currentTarget)
                    const inputEl = $(".chat-input")
                    const text = $.trim(inputEl.val())
                    const useImgMode = $("#chat-use-img-mode").is(":checked")
                    if (text === "") {
                        $p.toast('请先输入内容')
                        return
                    }
                    this.putMsg({
                        ai: false,
                        avatar: aiMetaInfo.userAvatar,
                        content: text,
                        imgMode:useImgMode
                    })
                    const chat = this.putMsg({
                        ai: true,
                        avatar: aiMetaInfo.aiAvatar,
                        content: '',
                        load: true,
                        imgMode:useImgMode
                    })
                    el.attr("disabled", true)
                    inputEl.val("")
                    const chatEl = $("#" + chat.id)
                    const contentEl = chatEl.find(".content-box")
                    const closeLoading = () => {
                        chatEl.find(".cursor-blink-after").removeClass("cursor-blink-after")
                        el.attr("disabled", false)
                    }
                    const callback = (res, err, done) => {
                        if(err){
                            chat.content += res
                        }else{
                            chat.content = res
                        }
                        contentEl.html(this.parseContent(chat.content))
                        $p.initCodeHighlight("#"+chat.id)
                        $('html,body').stop().animate({scrollTop: $(document).height()}, 200)
                    }
                    try {
                        const f = await fetch(aiMetaInfo.url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({text: text, imgMode:useImgMode}),
                        });
                        if (!f.ok) {
                            callback("请求失败：发起请求错误", true, true)
                            return
                        }
                        const res = f.body
                        if (!res) {
                            callback("请求失败：获取内容为空", true, true)
                            return
                        }
                        let reader = res.getReader()
                        let decoder = new TextDecoder
                        let allDone = false;
                        let allText = "";
                        while (!allDone) {
                            let {value, done} = await reader.read();
                            allDone = done;
                            let s = decoder.decode(value);
                            if (s) {
                                allText += s;
                                callback(allText, false, false)
                            }
                        }
                        callback(allText, false, true)
                    } catch (e) {
                        if((e+"").indexOf('aborted') > -1){
                            callback("", true, true)
                            return
                        }
                        callback(`请求异常：${e}`, true, true)
                    }finally {
                        closeLoading()
                    }
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
                                        <div class="d-flex align-items-center mt-2 text-muted fs12">
                                            <div class="mr-1">
                                                <i class="fa fa-${data.imgMode ? 'palette' : 'robot'} mr-1"></i>${data.imgMode ? 'AI绘画' : 'AI问答'}
                                            </div>
                                            <div class="primary-text-hover pk-copy" data-cp-title="对话信息" data-cp-func="puockAiChatCopy" data-id="${id}">
                                                <span><i class="fa-regular fa-copy mr-1"></i>复制</span>
                                            </div>
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
