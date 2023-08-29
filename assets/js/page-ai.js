(function (global, factory) {
    global.FetchEventSource = factory();
}(window, (function () {
    function _defineProperty(obj, key, value) {
        if (key in obj) {
            Object.defineProperty(obj, key, {
                value: value,
                enumerable: true,
                configurable: true,
                writable: true
            });
        } else {
            obj[key] = value;
        }

        return obj;
    }

    function _classPrivateFieldGet(receiver, privateMap) {
        var descriptor = privateMap.get(receiver);

        if (!descriptor) {
            throw new TypeError("attempted to get private field on non-instance");
        }

        if (descriptor.get) {
            return descriptor.get.call(receiver);
        }

        return descriptor.value;
    }

    function _classPrivateFieldSet(receiver, privateMap, value) {
        var descriptor = privateMap.get(receiver);

        if (!descriptor) {
            throw new TypeError("attempted to set private field on non-instance");
        }

        if (descriptor.set) {
            descriptor.set.call(receiver, value);
        } else {
            if (!descriptor.writable) {
                throw new TypeError("attempted to set read only private field");
            }

            descriptor.value = value;
        }

        return value;
    }

    var isAbsoluteUrl = url => {
        if (typeof url !== 'string') {
            throw new TypeError(`Expected a \`string\`, got \`${typeof url}\``);
        } // Don't match Windows paths `c:\`


        if (/^[a-zA-Z]:\\/.test(url)) {
            return false;
        } // Scheme: https://tools.ietf.org/html/rfc3986#section-3.1
        // Absolute URL: https://tools.ietf.org/html/rfc3986#section-4.3


        return /^[a-zA-Z][a-zA-Z\d+\-.]*:/.test(url);
    };

    /* eslint-disable no-underscore-dangle */

    /* eslint-disable no-await-in-loop */
    // ready state
    const CONNECTING = 0;
    const OPEN = 1;
    const CLOSED = 2;

    var _controller = new WeakMap();

    var _config = new WeakMap();

    var _lastId = new WeakMap();

    class BaseFetchEventSource extends EventTarget {
        constructor(url, config) {
            super(); // check url is set

            _defineProperty(this, "url", void 0);

            _defineProperty(this, "withCredentials", void 0);

            _defineProperty(this, "readyState", CONNECTING);

            _defineProperty(this, "config", config);

            _defineProperty(this, "onopen", config.onopen ?? void 0);

            _defineProperty(this, "onmessage", config.onmessage ?? void 0);

            _defineProperty(this, "onerror", config.onerror ?? void 0);

            _defineProperty(this, "onclose", config.onclose ?? void 0);

            _defineProperty(this, "_fetch", void 0);

            _defineProperty(this, "_decode", void 0);

            _defineProperty(this, "_getOrigin", void 0);

            _defineProperty(this, "_transformStream", stream => stream);

            _controller.set(this, {
                writable: true,
                value: new AbortController()
            });

            _config.set(this, {
                writable: true,
                value: {
                    headers: new Headers(),
                    reconnectionDelay: 2000,
                    withCredentials: false
                }
            });

            _lastId.set(this, {
                writable: true,
                value: ''
            });

            if (!url) {
                throw new Error('Cannot open an FetchEventSource to an empty URL.');
            }

            this.url = url;

            _classPrivateFieldSet(this, _config, { ..._classPrivateFieldGet(this, _config),
                ...config
            });

            this.withCredentials = _classPrivateFieldGet(this, _config).withCredentials; // if autoStart set then start connection

            if (_classPrivateFieldGet(this, _config).autoStart) {
                // start on nextTick so everything is initialized
                setTimeout(this.start.bind(this), 0);
            }
        }

        async start() {
            try {
                await this._connect();
            } catch (e) {
                // start new connection if user did not abort
                if (!_classPrivateFieldGet(this, _controller).signal.aborted) {
                    this._error(e);

                    await new Promise(resolve => setTimeout(resolve, _classPrivateFieldGet(this, _config).reconnectionDelay));
                }
            }
        }

        async _connect() {
            _classPrivateFieldSet(this, _controller, new AbortController()); // setup fetch and headers


            const {
                fetch = this._fetch,
                headers
            } = _classPrivateFieldGet(this, _config);


            headers['Accept'] = 'text/event-stream';

            if (_classPrivateFieldGet(this, _lastId)) {
                headers['Last-Event-ID'] = _classPrivateFieldGet(this, _lastId);
            } // connecting



            this.readyState = CONNECTING; // make fetch request to SSE

            const response = await fetch(this.url, {
                signal: _classPrivateFieldGet(this, _controller).signal,
                headers,
                credentials: _classPrivateFieldGet(this, _config).withCredentials ? 'include' : 'same-origin',
                cache: 'no-store',
                body: this.config.body,
                method: this.config.method,
            }); // fail if not 200 response

            if (!response || response.status !== 200) {
                this.close();

                this._error(new Error('Bad status'));

                return;
            } // check is correct content-type from response


            const type = response.headers.get('content-type');
            if (this.onopen) await this.onopen(response);

            if (!type || type.split(';')[0] !== 'text/event-stream') {
                this.close();
                return;
            } // connected

            this.readyState = OPEN;
            this.dispatchEvent(new Event('open')); // transform stream and process reader

            const reader = this._transformStream(response.body).getReader();

            await this._process(reader);
        }

        async _process(reader) {
            let type = '';
            let data = [];
            let buffer = '';

            for (;;) {
                // stop if has been aborted
                if (_classPrivateFieldGet(this, _controller).signal.aborted) {
                    console.log(22222)
                    reader.cancel();
                    return;
                } // check if buffer contains CRLF, LF or CR


                const regex = /\r\n|\r|\n/.exec(buffer);

                if (regex) {
                    // get next line which can be a new line
                    const line = buffer.slice(0, regex.index) || regex[0];
                    buffer = buffer.slice(regex.index + regex[0].length);
                    const [key, value = ''] = line.split(/:(.+)?/, 2).map(v2 => v2 === null || v2 === void 0 ? void 0 : v2.trimLeft()); // send event if line is a new line

                    if (line.match(/^(\r\n|\r|\n)$/) && (type || data)) {
                        // create event
                        const event = new MessageEvent(type, {
                            origin: this._getOrigin(),
                            data: data.join('\n'),
                            lastEventId: _classPrivateFieldGet(this, _lastId)
                        }); // dispatch event

                        this.dispatchEvent(event);
                        if (type === 'message' && this.onmessage) this.onmessage(event); // reset event data

                        type = '';
                        data = [];
                    } // process line


                    switch (key) {
                        case 'event':
                        {
                            type = value;
                            break;
                        }

                        case 'data':
                        {
                            type = type || 'message';
                            data.push(value);
                            break;
                        }

                        case 'id':
                        {
                            _classPrivateFieldSet(this, _lastId, value);

                            break;
                        }

                        case 'retry':
                        {
                            const num = Number(value);
                            if (num) _classPrivateFieldGet(this, _config).reconnectionDelay = num;
                            break;
                        }
                    }
                } else {
                    // read next data
                    const {
                        value: byteValue,
                        done
                    } = await reader.read();
                    if (done) {
                        this.close()
                        return
                    }

                    const value = typeof byteValue === 'string' ? byteValue : this._decode(byteValue);
                    buffer += value;
                }
            }
        }

        close() {
            this.readyState = CLOSED;

            _classPrivateFieldGet(this, _controller).abort();
            if (this.onclose) this.onclose();
        }

        _error(error) {
            const event = new ErrorEvent('error', {
                error,
                message: error.message
            });
            this.dispatchEvent(event);
            if (this.onerror) this.onerror(event);
        }

    }

    const decoder = new TextDecoder();
    class FetchEventSource extends BaseFetchEventSource {
        constructor(...args) {
            super(...args);

            _defineProperty(this, "_fetch", window.fetch.bind(window));

            _defineProperty(this, "_decode", bytes => decoder.decode(bytes));

            _defineProperty(this, "_getOrigin", () => {
                if (isAbsoluteUrl(this.url)) {
                    return new URL(this.url).origin;
                }

                return window.location.origin;
            });
        }
    }
    return FetchEventSource;
})));

jQuery(function () {
    (function ($, $p) {

        class PuockPageAi {

            data = null

            constructor() {
                this.data = {
                    chatList: []
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
                    const useModel = $("#chat-model").val()
                    if (!useImgMode && !useModel) {
                        $p.toast('请先选择模型')
                        return
                    }
                    if (text === "") {
                        $p.toast('请先输入内容')
                        return
                    }
                    this.putMsg({
                        ai: false,
                        avatar: aiMetaInfo.userAvatar,
                        model: useModel,
                        content: text,
                        imgMode: useImgMode
                    })
                    const chat = this.putMsg({
                        ai: true,
                        avatar: aiMetaInfo.aiAvatar,
                        content: '',
                        load: true,
                        imgMode: useImgMode
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
                        if (err) {
                            chat.content += res
                        } else {
                            chat.content = res
                        }
                        contentEl.html(this.parseContent(chat.content))
                        $p.initCodeHighlight("#" + chat.id)
                        $('html,body').stop().animate({scrollTop: $(document).height()}, 200)
                    }
                    try {
                        let resultText = ''
                        await new window.FetchEventSource(aiMetaInfo.url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({text: text, imgMode: useImgMode, model: useModel}),
                            openWhenHidden: true,
                            async onopen(resp) {
                                const contentType = resp.headers.get("content-type");
                                console.log(contentType)
                                if (contentType?.startsWith("text/plain") || contentType?.startsWith("text/html")) {
                                    const content = await resp.clone().text();
                                    callback(content, false, true)
                                }
                            },
                            onmessage(msg) {
                                if(msg.data==='[DONE]'){
                                    callback(resultText, false, true)
                                    return
                                }
                                const data = JSON.parse(msg.data)
                                resultText += data.choices?.[0]?.delta?.content || ''
                                callback(resultText, false, false)
                            },
                            onerror(e) {
                                console.error('error', e);
                                callback(e, true, true)
                            }
                        }).start()
                    } catch (e) {
                        console.error(e)
                        if ((e + "").indexOf('aborted') > -1) {
                            callback("", true, true)
                            return
                        }
                        callback(`请求异常：${e}`, true, true)
                    } finally {
                        closeLoading()
                    }
                })
            }

            putMsg(data) {
                const id = "chat-" + ((new Date().getTime()) + "") + (Math.floor(Math.random() * 1000) + "")
                const chat = {id: id, ...data}
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
                $p.initCodeHighlight("#" + id)
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

            onCopyChat() {
                window.puockAiChatCopy = (el) => {
                    const id = el.data("id")
                    const chat = this.data.chatList.find((val) => {
                        if (val.id === id) {
                            return val;
                        }
                        return null;
                    })
                    return chat ? chat.content : "";
                }
            }

            parseContent(str) {
                if(!str || str.trim()==='') return str
                return marked.parse(str, {
                    breaks: true,
                    mangle:false,
                    headerIds:false,
                })
            }

        }

        new PuockPageAi();
    })(window.jQuery, window.Puock)
})
