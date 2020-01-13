"use strict";
var _createClass = (function () {
    function e(e, t) {
        for (var i = 0; i < t.length; i++) {
            var a = t[i];
            a.enumerable = a.enumerable || !1, a.configurable = !0, "value" in a && (a.writable = !0), Object.defineProperty(e, a.key, a)
        }
    }

    return function (t, i, a) {
        return i && e(t.prototype, i), a && e(t, a), t
    }
})();

function _classCallCheck(e, t) {
    if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
}

!(function () {
    var e = (function () {
        function e(t) {
            _classCallCheck(this, e), this.iframe = null, this.widgetId = t
        }

        return _createClass(e, [{
            key: "destructor", value: function () {
                var e;
                this.iframe && (e = this.iframe).parentNode && e.parentNode.removeChild(e)
            }
        }, {
            key: "initDetailViewIframe", value: function (e, t, i) {
                i = i || {};
                this.iframe = document.createElement("iframe"), this.iframe.setAttribute("id", "wallsio-widget-detail-view"), this.iframe.setAttribute("style", ["border: 0", "width: 100%", "height: 100%", "position: fixed", "top: 0", "right: 0", "bottom: 0", "left: 0", "z-index: 2147483647", "display: none", "opacity: 0", "transition: opacity 0.3s ease"].join(";"));
                var a = t + "/p/" + e, n = ["theme=single", "widget_id=" + this.widgetId];
                for (var r in i) if (i.hasOwnProperty(r)) {
                    var s = i[r];
                    n.push(r + "=" + s)
                }
                var o = a + "?" + n.join("&");
                this.iframe.setAttribute("src", o), document.body.appendChild(this.iframe)
            }
        }, {
            key: "triggerDocumentEventInDetailViewIframe", value: function (e, t) {
                return !!this.iframe.contentWindow && (this.iframe.contentWindow.postMessage({
                    method: "Wallsio.triggerDocumentEvent",
                    args: [e, t]
                }, "*"), !0)
            }
        }, {
            key: "changeCheckin", value: function (e) {
                this.triggerDocumentEventInDetailViewIframe("wall.changeDetailCheckin", {checkinHash: e})
            }
        }, {
            key: "showWidgetDetail", value: function (e, t, i) {
                !!this.iframe && this.iframe.parentElement ? this.changeCheckin(e) : this.initDetailViewIframe(e, t, i), this.iframe.style.setProperty("display", "block"), this.iframe.style.setProperty("opacity", "1")
            }
        }, {
            key: "hideWidgetDetail", value: function () {
                var e = this;
                this.iframe ? (this.iframe.style.setProperty("opacity", "0"), setTimeout((function () {
                    e.iframe.style.setProperty("display", "none")
                }), 300)) : console.error("WallsioWidgetDetailView.hideWidgetDetail() was called, but the iframe doesn't exist yet.")
            }
        }]), e
    })();
    window.WallsioWidgetDetailView || (window.WallsioWidgetDetailView = e)
})();
"use strict";
var _createClass = (function () {
    function t(t, e) {
        for (var i = 0; i < e.length; i++) {
            var a = e[i];
            a.enumerable = a.enumerable || !1, a.configurable = !0, "value" in a && (a.writable = !0), Object.defineProperty(t, a.key, a)
        }
    }

    return function (e, i, a) {
        return i && t(e.prototype, i), a && t(e, a), e
    }
})();

function _toConsumableArray(t) {
    if (Array.isArray(t)) {
        for (var e = 0, i = Array(t.length); e < t.length; e++) i[e] = t[e];
        return i
    }
    return Array.from(t)
}

function _classCallCheck(t, e) {
    if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function")
}

!(function () {
    if (document.body) {
        var t = Number.MAX_SAFE_INTEGER || 9007199254740991, e = void 0;
        "function" == typeof window.CustomEvent ? e = window.CustomEvent : (e = function (t, e) {
            e = e || {bubbles: !1, cancelable: !1, detail: void 0};
            var i = document.createEvent("CustomEvent");
            return i.initCustomEvent(t, e.bubbles, e.cancelable, e.detail), i
        }).prototype = window.Event.prototype;
        var i = (function () {
            var t = document.querySelector("script[data-wallurl]:not([data-wallsio-was-fired])");
            if (!t) throw new Error("Walls.io <script> tag not found");
            return t.dataset.wallsioWasFired = !0, t
        })(), a = i.getAttribute("id") || "wallsio-widget-" + Math.floor(Math.random() * t), n = function (t, e) {
            Object.keys(t).forEach((function (i) {
                e(t[i])
            }))
        }, o = function (t, e) {
            e.parentNode.insertBefore(t, e)
        }, s = function (t) {
            t.parentNode && t.parentNode.removeChild(t)
        }, r = function (t) {
            var e = i.getAttribute("data-" + t);
            return "" === e ? null : (function (t) {
                return "string" == typeof t && "" !== t && !isNaN(t)
            })(e) ? Number(e) : e
        }, l = function (t, e) {
            var i, a, n = void 0, o = (i = t.wallUrl, (a = document.createElement("a")).href = i, a),
                s = o.search.replace(/^\?/, "");
            return (n = s.length > 0 ? s.split("&") : []).push("widget_id=" + e), (t.notscrollable || t.autoheight) && n.push("notscrollable=1"), t.autoheight && n.push("autoheight=1"), (n = n.filter((function (t, e, i) {
                return i.lastIndexOf(t) === e
            }))).length > 0 && (o.search = "?" + n.join("&")), o.href
        }, h = function (t) {
            return "wallsio-load-more-button-" + t
        }, d = (function () {
            function t(e) {
                _classCallCheck(this, t), this.widgetId = e, this.detailView = new WallsioWidgetDetailView(e), this.autoHeightInterval = null, this.currentHeight = 0, this.isInitialized = !1, this.isLoaded = !1, this.params = (function () {
                    var t = {};
                    if (["autoheight", "height", "injectLoadMoreButton", "lazyload", "lazyloadDistance", "loadMoreCount", "loadMoreText", "notscrollable", "iframe", "title", "wallUrl", "width"].forEach((function (e) {
                        t[e] = r(e)
                    })), !t.wallUrl) throw new Error("missing data-wallurl attribute");
                    return t.height || (t.height = 400), t
                })(), this.initialHeight = this.params.height, this.iframe = this.getInitialIframe();
                var i = this.params.lazyload;
                this.init(), i ? this._initLazyLoad() : this.loadContent()
            }

            return _createClass(t, [{
                key: "getInitialIframe", value: function () {
                    var t = this.params.iframe;
                    if (!t) return null;
                    var e = document.getElementById(t);
                    return e ? e.tagName.toLowerCase() ? e : (console.error("Element with id '" + t + "' is not an iframe"), null) : (console.error("There is no element with id '" + t + "'"), null)
                }
            }, {
                key: "init", value: function () {
                    this.isInitialized || (this.initDom(), this._initPostMessageChannel(), this.params.autoheight && this.initAutoHeight(), this.isInitialized = !0)
                }
            }, {
                key: "initAutoHeight", value: function () {
                    var t = this;
                    this.iframe.addEventListener("load", (function () {
                        t.setAutoHeight(!0)
                    }))
                }
            }, {
                key: "initDom", value: function () {
                    var t, e;
                    this.iframe || (this.iframe = document.createElement("iframe")), this.initIframe(), e = this.iframe, document.body.contains(e) || (t = this.iframe, o(t, i)), this.params.injectLoadMoreButton && this.injectLoadMoreButton()
                }
            }, {
                key: "injectLoadMoreButton", value: function () {
                    var t = this.createLoadMoreButton();
                    o(t, i)
                }
            }, {
                key: "restoreLoadMoreButtonIfNeeded", value: function () {
                    if (this.params.injectLoadMoreButton) {
                        var t = h(this.widgetId);
                        document.getElementById(t) || this.injectLoadMoreButton()
                    }
                }
            }, {
                key: "initIframe", value: function () {
                    var t = ["border: 0"];
                    this.params.width && (isNaN(this.params.width) ? t.push("width: 100%") : t.push("width: " + this.params.width + "px")), isNaN(this.params.height) ? t.push("height: 100%") : t.push("height: " + this.params.height + "px");
                    var e = this.iframe.getAttribute("id"), i = e || "wallsio-iframe-" + this.widgetId;
                    e || this.iframe.setAttribute("id", i), this.params.title && this.iframe.setAttribute("title", this.params.title), this.iframe.setAttribute("id", i), this.iframe.setAttribute("class", "wallsio-iframe " + i), this.iframe.setAttribute("allowfullscreen", ""), this.iframe.setAttribute("style", t.join(";"))
                }
            }, {
                key: "clearIframeContent", value: function () {
                    try {
                        this.iframe.contentDocument.open(), this.iframe.contentDocument.close()
                    } catch (t) {
                        console.error(t)
                    }
                }
            }, {
                key: "loadContent", value: function () {
                    if (!this.isLoaded) {
                        this.isLoaded = !0;
                        var t = l(this.params, this.widgetId);
                        this.iframe.setAttribute("src", t)
                    }
                }
            }, {
                key: "createLoadMoreButton", value: function () {
                    var t = this, e = document.createElement("button"), i = this.params.loadMoreText || "load more",
                        a = this.params.loadMoreCount || 18, n = h(this.widgetId);
                    return e.setAttribute("type", "button"), e.setAttribute("id", n), e.setAttribute("class", "wallsio-load-more-button " + n), e.innerText = i, e.addEventListener("click", (function (e) {
                        e.preventDefault(), t.loadMorePosts(a)
                    })), document.addEventListener("WallsioNoMorePosts", (function (i) {
                        i.detail.widgetId === t.widgetId && s(e)
                    })), e
                }
            }, {
                key: "destructor", value: function () {
                    clearInterval(this.autoHeightInterval), this._closePostMessageChannel(), this.iframe && s(this.iframe), this.detailView.destructor(), s(i), delete window.WallsioWidgets[this.widgetId]
                }
            }, {
                key: "_sendPostMessage", value: function (t) {
                    for (var e = arguments.length, i = Array(e > 1 ? e - 1 : 0), a = 1; a < e; a++) i[a - 1] = arguments[a];
                    return !(!this.iframe || !this.iframe.contentWindow) && (this.iframe.contentWindow.postMessage({
                        method: t,
                        args: i
                    }, "*"), !0)
                }
            }, {
                key: "_handlePostMessage", value: function (t) {
                    var e;
                    if (t && t.data && t.data.method && (!t.data.widgetId || t.data.widgetId === this.widgetId)) switch (t.data.method) {
                        case"Wallsio.height":
                            this._setIframeHeight.apply(this, t.data.args);
                            break;
                        case"Wallsio.triggerDomEvent":
                            this._triggerDomEvent.apply(this, t.data.args);
                            break;
                        case"Wallsio.showWidgetDetail":
                            (e = this.detailView).showWidgetDetail.apply(e, _toConsumableArray(t.data.args));
                            break;
                        case"Wallsio.hideWidgetDetail":
                            this._sendPostMessage("Wallsio.triggerDocumentEvent", "wall.hideIframeDetailOverlay"), this.detailView.hideWidgetDetail()
                    }
                }
            }, {
                key: "_initPostMessageChannel", value: function () {
                    this._handlePostMessage = this._handlePostMessage.bind(this), window.addEventListener("message", this._handlePostMessage)
                }
            }, {
                key: "_closePostMessageChannel", value: function () {
                    window.removeEventListener("message", this._handlePostMessage)
                }
            }, {
                key: "_getViewportHeight", value: function () {
                    return window.innerHeight
                }
            }, {
                key: "_getWidgetDistanceFromViewportBottom", value: function () {
                    return this.iframe.getBoundingClientRect().top - this._getViewportHeight()
                }
            }, {
                key: "_checkLazyLoad", value: function () {
                    if (!this.isLoaded) {
                        var t = this._getWidgetDistanceFromViewportBottom(), e = this._getViewportHeight();
                        t < (null !== this.params.lazyloadDistance ? this.params.lazyloadDistance : e) && this.loadContent()
                    }
                }
            }, {
                key: "_initLazyLoad", value: function () {
                    var t = this;
                    this.clearIframeContent();
                    var e, i, a, n,
                        o = (e = this._checkLazyLoad.bind(this), i = 100, a = void 0, n = void 0, function () {
                            var t = this, o = arguments;
                            n ? (clearTimeout(a), a = setTimeout((function () {
                                Date.now() - n >= i && (e.apply(t, o), n = Date.now())
                            }), i - (Date.now() - n))) : (e.apply(t, o), n = Date.now())
                        });
                    window.addEventListener("scroll", o, !0), setTimeout((function () {
                        t._checkLazyLoad()
                    }), 0)
                }
            }, {
                key: "_setIframeHeight", value: function (t) {
                    var e = this.initialHeight;
                    return t = Math.max(t, e), this.currentHeight !== t && (this.iframe.style.height = t + "px", this.currentHeight = t, !0)
                }
            }, {
                key: "isAutoheightActive", value: function () {
                    return !!this.autoHeightInterval
                }
            }, {
                key: "setAutoHeight", value: function (t) {
                    var e = this;
                    if (!this.iframe || !this.iframe.contentWindow) return !1;
                    var i = !t;
                    return this._sendPostMessage("Wallsio.setAutoFillEnabled", i), !1 === t ? (clearInterval(this.autoHeightInterval), !1) : !this.autoHeightInterval && (this.autoHeightInterval = setInterval((function () {
                        e._sendPostMessage("Wallsio.getHeight")
                    }), 1e3), this._sendPostMessage("Wallsio.getHeight"), !0)
                }
            }, {
                key: "loadMorePosts", value: function () {
                    var t = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : null;
                    return !(!this.iframe || !this.iframe.contentWindow) && (this._sendPostMessage("Wallsio.requestOlderCheckins", t), !0)
                }
            }, {
                key: "setNetworkFilter", value: function () {
                    var t = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : null;
                    this.isAutoheightActive() && this._setIframeHeight(this.initialHeight), this._sendPostMessage("Wallsio.setNetworkFilter", t), this.restoreLoadMoreButtonIfNeeded()
                }
            }, {
                key: "_triggerDomEvent", value: function (t) {
                    if (document.dispatchEvent) {
                        var i = new e(t, {detail: {widgetId: this.widgetId}});
                        document.dispatchEvent(i)
                    }
                }
            }]), t
        })();
        window.WallsioWidgets = window.WallsioWidgets || {}, window.WallsioWidgets[a] ? console.error('This page already contains a walls.io script widget with the id "' + a + '". To use multiple <script> widgets on the same page, please use different "id" attributes for each of them.') : (window.WallsioWidgets[a] = new d(a), window.Wallsio || (window.Wallsio = {
            setAutoHeight: function () {
                for (var t = arguments.length, e = Array(t), i = 0; i < t; i++) e[i] = arguments[i];
                n(window.WallsioWidgets, (function (t) {
                    t.setAutoHeight.apply(t, e)
                }))
            }, setNetworkFilter: function () {
                for (var t = arguments.length, e = Array(t), i = 0; i < t; i++) e[i] = arguments[i];
                n(window.WallsioWidgets, (function (t) {
                    t.setNetworkFilter.apply(t, e)
                }))
            }, loadMorePosts: function () {
                for (var t = arguments.length, e = Array(t), i = 0; i < t; i++) e[i] = arguments[i];
                n(window.WallsioWidgets, (function (t) {
                    t.loadMorePosts.apply(t, e)
                }))
            }, destructor: function () {
                n(window.WallsioWidgets, (function (t) {
                    t.destructor()
                }))
            }
        }))
    } else console.error("Please add the Walls.io <script> tag inside the <body> of the page.")
})();
