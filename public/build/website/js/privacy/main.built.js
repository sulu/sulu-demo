! function () {
    function e(t, n, i) {
        function r(a, o) {
            if (!n[a]) {
                if (!t[a]) {
                    var l = "function" == typeof require && require;
                    if (!o && l) return l(a, !0);
                    if (s) return s(a, !0);
                    var c = new Error("Cannot find module '" + a + "'");
                    throw c.code = "MODULE_NOT_FOUND", c
                }
                var u = n[a] = {
                    exports: {}
                };
                t[a][0].call(u.exports, function (e) {
                    var n = t[a][1][e];
                    return r(n ? n : e)
                }, u, u.exports, e, t, n, i)
            }
            return n[a].exports
        }
        for (var s = "function" == typeof require && require, a = 0; a < i.length; a++) r(i[a]);
        return r
    }
    return e
}()({
    1: [function (e, t, n) {
        "use strict";
        var i = e("./helpers/TabManager"),
            r = e("./helpers/hideSiblingElements"),
            s = e("./helpers/showSiblingElements"),
            a = function (e, t) {
                t = t || {}, this._tabbables = null, this._excludeHidden = t.excludeHidden, this._firstTabbableElement = t.firstFocusElement, this._lastTabbableElement = null, this._relatedTarget = null, this.el = e, this._handleOnFocus = this._handleOnFocus.bind(this)
            },
            o = a.prototype;
        o.start = function () {
            this.updateTabbables(), r(this.el, null, this._excludeHidden), this._firstTabbableElement ? this.el.contains(document.activeElement) || this._firstTabbableElement.focus() : console.warn("this._firstTabbableElement is null, CircularTab needs at least one tabbable element."), this._relatedTarget = document.activeElement, document.addEventListener("focus", this._handleOnFocus, !0)
        }, o.stop = function () {
            s(this.el), document.removeEventListener("focus", this._handleOnFocus, !0)
        }, o.updateTabbables = function () {
            this._tabbables = i.getTabbableElements(this.el, this._excludeHidden), this._firstTabbableElement = this._firstTabbableElement || this._tabbables[0], this._lastTabbableElement = this._tabbables[this._tabbables.length - 1]
        }, o._handleOnFocus = function (e) {
            if (this.el.contains(e.target)) this._relatedTarget = e.target;
            else {
                if (e.preventDefault(), this.updateTabbables(), this._relatedTarget === this._lastTabbableElement || null === this._relatedTarget) return this._firstTabbableElement.focus(), void(this._relatedTarget = this._firstTabbableElement);
                if (this._relatedTarget === this._firstTabbableElement) return this._lastTabbableElement.focus(), void(this._relatedTarget = this._lastTabbableElement)
            }
        }, o.destroy = function () {
            this.stop(), this.el = null, this._tabbables = null, this._firstTabbableElement = null, this._lastTabbableElement = null, this._relatedTarget = null, this._handleOnFocus = null
        }, t.exports = a
    }, {
        "./helpers/TabManager": 3,
        "./helpers/hideSiblingElements": 5,
        "./helpers/showSiblingElements": 9
    }],
    2: [function (e, t, n) {
        "use strict";

        function i() {
            this._createElemnts(), this._bindEvents()
        }
        var r = i.prototype;
        r._bindEvents = function () {
            this._onResize = this._resize.bind(this)
        }, r._createElemnts = function () {
            this.span = document.createElement("span");
            var e = this.span.style;
            e.visibility = "hidden", e.position = "absolute", e.top = "0", e.bottom = "0", e.zIndex = "-1", this.span.innerHTML = "&nbsp;", this.iframe = document.createElement("iframe");
            var t = this.iframe.style;
            t.position = "absolute", t.top = "0", t.left = "0", t.width = "100%", t.height = "100%", this.span.appendChild(this.iframe), document.body.appendChild(this.span)
        }, r.detect = function (e) {
            this.originalSize = e || 17, this.currentSize = parseFloat(window.getComputedStyle(this.span)["font-size"]), this.currentSize > this.originalSize && this._onResize(), this.isDetecting || (this.iframe.contentWindow.addEventListener("resize", this._onResize), this.isDetecting = !0)
        }, r._resize = function (e) {
            this.currentSize = parseFloat(window.getComputedStyle(this.span)["font-size"]), this.originalSize < this.currentSize ? document.documentElement.classList.add("text-zoom") : document.documentElement.classList.remove("text-zoom"), window.dispatchEvent(new Event("resize"))
        }, r.remove = function () {
            this.isDetecting && (this.iframe.contentWindow.removeEventListener("resize", this._onResize), this.isDetecting = !1)
        }, r.destroy = function () {
            this.remove(), this.span && this.span.parentElement && this.span.parentElement.removeChild(this.span), this.span = null, this.iframe = null
        }, t.exports = new i
    }, {}],
    3: [function (e, t, n) {
        "use strict";
        var i = e("./../maps/focusableElement"),
            r = function () {
                this.focusableSelectors = i.join(",")
            },
            s = r.prototype;
        s.isFocusableElement = function (e, t, n) {
            if (t && !this._isDisplayed(e)) return !1;
            var r = e.nodeName.toLowerCase(),
                s = i.indexOf(r) > -1;
            return "a" === r || (s ? !e.disabled : !e.contentEditable || (n = n || parseFloat(e.getAttribute("tabindex")), !isNaN(n)))
        }, s.isTabbableElement = function (e, t) {
            if (t && !this._isDisplayed(e)) return !1;
            var n = e.getAttribute("tabindex");
            return n = parseFloat(n), isNaN(n) ? this.isFocusableElement(e, t, n) : n >= 0
        }, s._isDisplayed = function (e) {
            var t = e.getBoundingClientRect();
            return (0 !== t.top || 0 !== t.left || 0 !== t.width || 0 !== t.height) && "hidden" !== window.getComputedStyle(e).visibility
        }, s.getTabbableElements = function (e, t) {
            for (var n = e.querySelectorAll(this.focusableSelectors), i = n.length, r = [], s = 0; s < i; s++) this.isTabbableElement(n[s], t) && r.push(n[s]);
            return r
        }, s.getFocusableElements = function (e, t) {
            for (var n = e.querySelectorAll(this.focusableSelectors), i = n.length, r = [], s = 0; s < i; s++) this.isFocusableElement(n[s], t) && r.push(n[s]);
            return r
        }, t.exports = new r
    }, {
        "./../maps/focusableElement": 11
    }],
    4: [function (e, t, n) {
        "use strict";
        var i = e("./setAttributes"),
            r = e("./../maps/ariaMap"),
            s = e("./TabManager"),
            a = "data-original-",
            o = "tabindex",
            l = function (e, t) {
                var n = e.getAttribute(a + t);
                n || (n = e.getAttribute(t) || "", i(e, a + t, n))
            };
        t.exports = function (e, t) {
            if (s.isFocusableElement(e, t)) l(e, o), i(e, o, -1);
            else
                for (var n = s.getTabbableElements(e, t), a = n.length; a--;) l(n[a], o), i(n[a], o, -1);
            l(e, r.HIDDEN), i(e, r.HIDDEN, !0)
        }
    }, {
        "./../maps/ariaMap": 10,
        "./TabManager": 3,
        "./setAttributes": 7
    }],
    5: [function (e, t, n) {
        "use strict";
        var i = e("./hide");
        t.exports = function r(e, t, n) {
            t = t || document.body;
            for (var s = e, a = e; s = s.previousElementSibling;) i(s, n);
            for (; a = a.nextElementSibling;) i(a, n);
            e.parentElement && e.parentElement !== t && r(e.parentElement)
        }
    }, {
        "./hide": 4
    }],
    6: [function (e, t, n) {
        "use strict";
        var i = function (e, t) {
                if ("string" == typeof t)
                    for (var n = t.split(/\s+/), i = 0; i < n.length; i++) e.getAttribute(n[i]) && e.removeAttribute(n[i])
            },
            r = function (e, t) {
                if (e.length)
                    for (var n = 0; n < e.length; n++) i(e[n], t);
                else i(e, t)
            };
        t.exports = r
    }, {}],
    7: [function (e, t, n) {
        "use strict";
        var i = function (e, t, n) {
                e && 1 === e.nodeType && e.setAttribute(t, n)
            },
            r = function (e, t, n) {
                if ("string" != typeof n && (n = n.toString()), e)
                    if (e.length)
                        for (var r = 0; r < e.length; r++) i(e[r], t, n);
                    else i(e, t, n)
            };
        t.exports = r
    }, {}],
    8: [function (e, t, n) {
        "use strict";
        var i = e("./removeAttributes"),
            r = e("./setAttributes"),
            s = e("./../maps/ariaMap"),
            a = "data-original-",
            o = "tabindex",
            l = function (e, t) {
                var n = e.getAttribute(a + t);
                "string" == typeof n && (n.length ? r(e, t, n) : i(e, t), i(e, a + t))
            };
        t.exports = function (e) {
            i(e, o + " " + s.HIDDEN), l(e, o), l(e, s.HIDDEN);
            for (var t = e.querySelectorAll("[" + a + o + "]"), n = t.length; n--;) l(t[n], o)
        }
    }, {
        "./../maps/ariaMap": 10,
        "./removeAttributes": 6,
        "./setAttributes": 7
    }],
    9: [function (e, t, n) {
        "use strict";
        var i = e("./show");
        t.exports = function r(e, t) {
            t = t || document.body;
            for (var n = e, s = e; n = n.previousElementSibling;) i(n);
            for (; s = s.nextElementSibling;) i(s);
            e.parentElement && e.parentElement !== t && r(e.parentElement)
        }
    }, {
        "./show": 8
    }],
    10: [function (e, t, n) {
        "use strict";
        t.exports = {
            AUTOCOMPLETE: "aria-autocomplete",
            CHECKED: "aria-checked",
            DISABLED: "aria-disabled",
            EXPANDED: "aria-expanded",
            HASPOPUP: "aria-haspopup",
            HIDDEN: "aria-hidden",
            INVALID: "aria-invalid",
            LABEL: "aria-label",
            LEVEL: "aria-level",
            MULTILINE: "aria-multiline",
            MULTISELECTABLE: "aria-multiselectable",
            ORIENTATION: "aria-orientation",
            PRESSED: "aria-pressed",
            READONLY: "aria-readonly",
            REQUIRED: "aria-required",
            SELECTED: "aria-selected",
            SORT: "aria-sort",
            VALUEMAX: "aria-valuemax",
            VALUEMIN: "aria-valuemin",
            VALUENOW: "aria-valuenow",
            VALUETEXT: "aria-valuetext",
            ATOMIC: "aria-atomic",
            BUSY: "aria-busy",
            LIVE: "aria-live",
            RELEVANT: "aria-relevant",
            DROPEFFECT: "aria-dropeffect",
            GRABBED: "aria-grabbed",
            ACTIVEDESCENDANT: "aria-activedescendant",
            CONTROLS: "aria-controls",
            DESCRIBEDBY: "aria-describedby",
            FLOWTO: "aria-flowto",
            LABELLEDBY: "aria-labelledby",
            OWNS: "aria-owns",
            POSINSET: "aria-posinset",
            SETSIZE: "aria-setsize"
        }
    }, {}],
    11: [function (e, t, n) {
        "use strict";
        t.exports = ["input", "select", "textarea", "button", "optgroup", "option", "menuitem", "fieldset", "object", "a[href]", "*[tabindex]", "*[contenteditable]"]
    }, {}],
    12: [function (e, t, n) {
        "use strict";
        e("@marcom/ac-polyfills/Array/prototype.slice"), e("@marcom/ac-polyfills/Element/prototype.classList");
        var i = e("./className/add");
        t.exports = function () {
            var e, t = Array.prototype.slice.call(arguments),
                n = t.shift(t);
            if (n.classList && n.classList.add) return void n.classList.add.apply(n.classList, t);
            for (e = 0; e < t.length; e++) i(n, t[e])
        }
    }, {
        "./className/add": 13,
        "@marcom/ac-polyfills/Array/prototype.slice": void 0,
        "@marcom/ac-polyfills/Element/prototype.classList": void 0
    }],
    13: [function (e, t, n) {
        "use strict";
        var i = e("./contains");
        t.exports = function (e, t) {
            i(e, t) || (e.className += " " + t)
        }
    }, {
        "./contains": 14
    }],
    14: [function (e, t, n) {
        "use strict";
        var i = e("./getTokenRegExp");
        t.exports = function (e, t) {
            return i(t).test(e.className)
        }
    }, {
        "./getTokenRegExp": 15
    }],
    15: [function (e, t, n) {
        "use strict";
        t.exports = function (e) {
            return new RegExp("(\\s|^)" + e + "(\\s|$)")
        }
    }, {}],
    16: [function (e, t, n) {
        "use strict";
        var i = e("./contains"),
            r = e("./getTokenRegExp");
        t.exports = function (e, t) {
            i(e, t) && (e.className = e.className.replace(r(t), "$1").trim())
        }
    }, {
        "./contains": 14,
        "./getTokenRegExp": 15
    }],
    17: [function (e, t, n) {
        "use strict";
        e("@marcom/ac-polyfills/Array/prototype.slice"), e("@marcom/ac-polyfills/Element/prototype.classList");
        var i = e("./className/remove");
        t.exports = function () {
            var e, t = Array.prototype.slice.call(arguments),
                n = t.shift(t);
            if (n.classList && n.classList.remove) return void n.classList.remove.apply(n.classList, t);
            for (e = 0; e < t.length; e++) i(n, t[e])
        }
    }, {
        "./className/remove": 16,
        "@marcom/ac-polyfills/Array/prototype.slice": void 0,
        "@marcom/ac-polyfills/Element/prototype.classList": void 0
    }],
    18: [function (e, t, n) {
        "use strict";
        var i = e("./utils/addEventListener"),
            r = e("./shared/getEventType");
        t.exports = function (e, t, n, s) {
            return t = r(e, t), i(e, t, n, s)
        }
    }, {
        "./shared/getEventType": 20,
        "./utils/addEventListener": 22
    }],
    19: [function (e, t, n) {
        "use strict";
        var i = e("./utils/removeEventListener"),
            r = e("./shared/getEventType");
        t.exports = function (e, t, n, s) {
            return t = r(e, t), i(e, t, n, s)
        }
    }, {
        "./shared/getEventType": 20,
        "./utils/removeEventListener": 23
    }],
    20: [function (e, t, n) {
        "use strict";
        var i = e("@marcom/ac-prefixer/getEventType");
        t.exports = function (e, t) {
            var n, r;
            return n = "tagName" in e ? e.tagName : e === window ? "window" : "document", r = i(t, n), r ? r : t
        }
    }, {
        "@marcom/ac-prefixer/getEventType": 53
    }],
    21: [function (e, t, n) {
        "use strict";
        t.exports = function (e) {
            return e = e || window.event, "undefined" != typeof e.target ? e.target : e.srcElement
        }
    }, {}],
    22: [function (e, t, n) {
        "use strict";
        t.exports = function (e, t, n, i) {
            return e.addEventListener ? e.addEventListener(t, n, !!i) : e.attachEvent("on" + t, n), e
        }
    }, {}],
    23: [function (e, t, n) {
        "use strict";
        t.exports = function (e, t, n, i) {
            return e.removeEventListener ? e.removeEventListener(t, n, !!i) : e.detachEvent("on" + t, n), e
        }
    }, {}],
    24: [function (e, t, n) {
        "use strict";
        t.exports = function (e) {
            var t;
            if (e = e || window, e === window) {
                if (t = window.pageXOffset) return t;
                e = document.documentElement || document.body.parentNode || document.body
            }
            return e.scrollLeft
        }
    }, {}],
    25: [function (e, t, n) {
        "use strict";
        t.exports = function (e) {
            var t;
            if (e = e || window, e === window) {
                if (t = window.pageYOffset) return t;
                e = document.documentElement || document.body.parentNode || document.body
            }
            return e.scrollTop
        }
    }, {}],
    26: [function (e, t, n) {
        "use strict";
        t.exports = 8
    }, {}],
    27: [function (e, t, n) {
        "use strict";
        t.exports = 11
    }, {}],
    28: [function (e, t, n) {
        "use strict";
        t.exports = 1
    }, {}],
    29: [function (e, t, n) {
        "use strict";
        t.exports = 3
    }, {}],
    30: [function (e, t, n) {
        "use strict";
        var i = e("../isNode");
        t.exports = function (e, t) {
            return !!i(e) && ("number" == typeof t ? e.nodeType === t : t.indexOf(e.nodeType) !== -1)
        }
    }, {
        "../isNode": 33
    }],
    31: [function (e, t, n) {
        "use strict";
        var i = e("./isNodeType"),
            r = e("../COMMENT_NODE"),
            s = e("../DOCUMENT_FRAGMENT_NODE"),
            a = e("../ELEMENT_NODE"),
            o = e("../TEXT_NODE"),
            l = [a, o, r, s],
            c = " must be an Element, TextNode, Comment, or Document Fragment",
            u = [a, o, r],
            h = " must be an Element, TextNode, or Comment",
            m = [a, s],
            d = " must be an Element, or Document Fragment",
            f = " must have a parentNode";
        t.exports = {
            parentNode: function (e, t, n, r) {
                if (r = r || "target", (e || t) && !i(e, m)) throw new TypeError(n + ": " + r + d)
            },
            childNode: function (e, t, n, r) {
                if (r = r || "target", (e || t) && !i(e, u)) throw new TypeError(n + ": " + r + h)
            },
            insertNode: function (e, t, n, r) {
                if (r = r || "node", (e || t) && !i(e, l)) throw new TypeError(n + ": " + r + c)
            },
            hasParentNode: function (e, t, n) {
                if (n = n || "target", !e.parentNode) throw new TypeError(t + ": " + n + f)
            }
        }
    }, {
        "../COMMENT_NODE": 26,
        "../DOCUMENT_FRAGMENT_NODE": 27,
        "../ELEMENT_NODE": 28,
        "../TEXT_NODE": 29,
        "./isNodeType": 30
    }],
    32: [function (e, t, n) {
        "use strict";
        var i = e("./internal/isNodeType"),
            r = e("./ELEMENT_NODE");
        t.exports = function (e) {
            return i(e, r)
        }
    }, {
        "./ELEMENT_NODE": 28,
        "./internal/isNodeType": 30
    }],
    33: [function (e, t, n) {
        "use strict";
        t.exports = function (e) {
            return !(!e || !e.nodeType)
        }
    }, {}],
    34: [function (e, t, n) {
        "use strict";
        var i = e("./internal/validate");
        t.exports = function (e) {
            return i.childNode(e, !0, "remove"), e.parentNode ? e.parentNode.removeChild(e) : e
        }
    }, {
        "./internal/validate": 31
    }],
    35: [function (e, t, n) {
        "use strict";
        t.exports = {
            EventEmitterMicro: e("./ac-event-emitter-micro/EventEmitterMicro")
        }
    }, {
        "./ac-event-emitter-micro/EventEmitterMicro": 36
    }],
    36: [function (e, t, n) {
        "use strict";

        function i() {
            this._events = {}
        }
        var r = i.prototype;
        r.on = function (e, t) {
            this._events[e] = this._events[e] || [], this._events[e].unshift(t)
        }, r.once = function (e, t) {
            function n(r) {
                i.off(e, n), void 0 !== r ? t(r) : t()
            }
            var i = this;
            this.on(e, n)
        }, r.off = function (e, t) {
            if (this.has(e)) {
                if (1 === arguments.length) return this._events[e] = null, void delete this._events[e];
                var n = this._events[e].indexOf(t);
                n !== -1 && this._events[e].splice(n, 1)
            }
        }, r.trigger = function (e, t) {
            if (this.has(e))
                for (var n = this._events[e].length - 1; n >= 0; n--) void 0 !== t ? this._events[e][n](t) : this._events[e][n]()
        }, r.has = function (e) {
            return e in this._events != !1 && 0 !== this._events[e].length
        }, r.destroy = function () {
            for (var e in this._events) this._events[e] = null;
            this._events = null
        }, t.exports = i
    }, {}],
    37: [function (e, t, n) {
        "use strict";

        function i(e) {
            this._keysDown = {}, this._DOMKeyDown = this._DOMKeyDown.bind(this), this._DOMKeyUp = this._DOMKeyUp.bind(this), this._context = e || document, s(this._context, c, this._DOMKeyDown, !0), s(this._context, u, this._DOMKeyUp, !0), r.call(this)
        }
        var r = e("@marcom/ac-event-emitter-micro").EventEmitterMicro,
            s = e("@marcom/ac-dom-events/utils/addEventListener"),
            a = e("@marcom/ac-dom-events/utils/removeEventListener"),
            o = e("@marcom/ac-object/create"),
            l = e("./internal/KeyEvent"),
            c = "keydown",
            u = "keyup",
            h = i.prototype = o(r.prototype);
        h.onDown = function (e, t) {
            return this.on(c + ":" + e, t)
        }, h.onceDown = function (e, t) {
            return this.once(c + ":" + e, t)
        }, h.offDown = function (e, t) {
            return this.off(c + ":" + e, t)
        }, h.onUp = function (e, t) {
            return this.on(u + ":" + e, t)
        }, h.onceUp = function (e, t) {
            return this.once(u + ":" + e, t)
        }, h.offUp = function (e, t) {
            return this.off(u + ":" + e, t)
        }, h.isDown = function (e) {
            return e += "", this._keysDown[e] || !1
        }, h.isUp = function (e) {
            return !this.isDown(e)
        }, h.destroy = function () {
            return a(this._context, c, this._DOMKeyDown, !0), a(this._context, u, this._DOMKeyUp, !0), this._keysDown = null, this._context = null, r.prototype.destroy.call(this), this
        }, h._DOMKeyDown = function (e) {
            var t = this._normalizeKeyboardEvent(e),
                n = t.keyCode += "";
            this._trackKeyDown(n), this.trigger(c + ":" + n, t)
        }, h._DOMKeyUp = function (e) {
            var t = this._normalizeKeyboardEvent(e),
                n = t.keyCode += "";
            this._trackKeyUp(n), this.trigger(u + ":" + n, t)
        }, h._normalizeKeyboardEvent = function (e) {
            return new l(e)
        }, h._trackKeyUp = function (e) {
            this._keysDown[e] && (this._keysDown[e] = !1)
        }, h._trackKeyDown = function (e) {
            this._keysDown[e] || (this._keysDown[e] = !0)
        }, t.exports = i
    }, {
        "./internal/KeyEvent": 39,
        "@marcom/ac-dom-events/utils/addEventListener": 22,
        "@marcom/ac-dom-events/utils/removeEventListener": 23,
        "@marcom/ac-event-emitter-micro": 35,
        "@marcom/ac-object/create": 50
    }],
    38: [function (e, t, n) {
        "use strict";
        var i = e("./Keyboard");
        t.exports = new i
    }, {
        "./Keyboard": 37
    }],
    39: [function (e, t, n) {
        "use strict";

        function i(e) {
            this.originalEvent = e;
            var t;
            for (t in e) r.indexOf(t) === -1 && "function" != typeof e[t] && (this[t] = e[t]);
            this.location = void 0 !== this.originalEvent.location ? this.originalEvent.location : this.originalEvent.keyLocation
        }
        var r = ["keyLocation"];
        i.prototype = {
            preventDefault: function () {
                return "function" != typeof this.originalEvent.preventDefault ? void(this.originalEvent.returnValue = !1) : this.originalEvent.preventDefault()
            },
            stopPropagation: function () {
                return this.originalEvent.stopPropagation()
            }
        }, t.exports = i
    }, {}],
    40: [function (e, t, n) {
        "use strict";
        t.exports = {
            BACKSPACE: 8,
            TAB: 9,
            ENTER: 13,
            SHIFT: 16,
            CONTROL: 17,
            ALT: 18,
            COMMAND: 91,
            CAPSLOCK: 20,
            ESCAPE: 27,
            PAGE_UP: 33,
            PAGE_DOWN: 34,
            END: 35,
            HOME: 36,
            ARROW_LEFT: 37,
            ARROW_UP: 38,
            ARROW_RIGHT: 39,
            ARROW_DOWN: 40,
            DELETE: 46,
            ZERO: 48,
            ONE: 49,
            TWO: 50,
            THREE: 51,
            FOUR: 52,
            FIVE: 53,
            SIX: 54,
            SEVEN: 55,
            EIGHT: 56,
            NINE: 57,
            A: 65,
            B: 66,
            C: 67,
            D: 68,
            E: 69,
            F: 70,
            G: 71,
            H: 72,
            I: 73,
            J: 74,
            K: 75,
            L: 76,
            M: 77,
            N: 78,
            O: 79,
            P: 80,
            Q: 81,
            R: 82,
            S: 83,
            T: 84,
            U: 85,
            V: 86,
            W: 87,
            X: 88,
            Y: 89,
            Z: 90,
            NUMPAD_ZERO: 96,
            NUMPAD_ONE: 97,
            NUMPAD_TWO: 98,
            NUMPAD_THREE: 99,
            NUMPAD_FOUR: 100,
            NUMPAD_FIVE: 101,
            NUMPAD_SIX: 102,
            NUMPAD_SEVEN: 103,
            NUMPAD_EIGHT: 104,
            NUMPAD_NINE: 105,
            NUMPAD_ASTERISK: 106,
            NUMPAD_PLUS: 107,
            NUMPAD_DASH: 109,
            NUMPAD_DOT: 110,
            NUMPAD_SLASH: 111,
            NUMPAD_EQUALS: 187,
            TICK: 192,
            LEFT_BRACKET: 219,
            RIGHT_BRACKET: 221,
            BACKSLASH: 220,
            SEMICOLON: 186,
            APOSTRAPHE: 222,
            APOSTROPHE: 222,
            SPACEBAR: 32,
            CLEAR: 12,
            COMMA: 188,
            DOT: 190,
            SLASH: 191
        }
    }, {}],
    41: [function (e, t, n) {
        "use strict";
        t.exports = {
            Modal: e("./ac-modal-basic/Modal"),
            Renderer: e("./ac-modal-basic/Renderer"),
            classNames: e("./ac-modal-basic/classNames"),
            dataAttributes: e("./ac-modal-basic/dataAttributes")
        }
    }, {
        "./ac-modal-basic/Modal": 42,
        "./ac-modal-basic/Renderer": 43,
        "./ac-modal-basic/classNames": 44,
        "./ac-modal-basic/dataAttributes": 45
    }],
    42: [function (e, t, n) {
        "use strict";

        function i(e, t) {
            c.call(this), this.options = a.defaults(h, e), this.renderer = new u(t), this.opened = !1, this._keysToClose = [l.ESCAPE], this._attachedKeysToClose = [], this.close = this.close.bind(this)
        }
        var r = {
                addEventListener: e("@marcom/ac-dom-events/addEventListener"),
                removeEventListener: e("@marcom/ac-dom-events/removeEventListener"),
                target: e("@marcom/ac-dom-events/target")
            },
            s = {
                getScrollX: e("@marcom/ac-dom-metrics/getScrollX"),
                getScrollY: e("@marcom/ac-dom-metrics/getScrollY")
            },
            a = {
                create: e("@marcom/ac-object/create"),
                defaults: e("@marcom/ac-object/defaults")
            },
            o = e("@marcom/ac-keyboard"),
            l = e("@marcom/ac-keyboard/keyMap"),
            c = e("@marcom/ac-event-emitter-micro").EventEmitterMicro,
            u = e("./Renderer"),
            h = {
                retainScrollPosition: !1
            },
            m = i.prototype = a.create(c.prototype);
        m.open = function () {
            this.options.retainScrollPosition && this._saveScrollPosition(), this.opened || (this._attachEvents(), this.trigger("willopen"), this.renderer.open(), this.opened = !0, this.trigger("open"))
        }, m.close = function (e) {
            var t, n;
            if (this.opened) {
                if (e && "click" === e.type && (t = r.target(e), n = this.renderer.options.dataAttributes.close, !t.hasAttribute(n))) return;
                this.trigger("willclose"), this._removeEvents(), this.renderer.close(), this.options.retainScrollPosition && this._restoreScrollPosition(), this.opened = !1, this.trigger("close")
            }
        }, m.render = function () {
            this.renderer.render()
        }, m.appendContent = function (e, t) {
            this.renderer.appendContent(e, t)
        }, m.removeContent = function (e) {
            this.renderer.removeContent(e)
        }, m.destroy = function () {
            this._removeEvents(), this.renderer.destroy();
            for (var e in this) this.hasOwnProperty(e) && (this[e] = null)
        }, m.addKeyToClose = function (e) {
            var t = this._keysToClose.indexOf(e);
            t === -1 && (this._keysToClose.push(e), this._bindKeyToClose(e))
        }, m.removeKeyToClose = function (e) {
            var t = this._keysToClose.indexOf(e);
            t !== -1 && this._keysToClose.splice(t, 1), this._releaseKeyToClose(e)
        }, m._bindKeyToClose = function (e) {
            var t = this._attachedKeysToClose.indexOf(e);
            t === -1 && (o.onUp(e, this.close), this._attachedKeysToClose.push(e))
        }, m._releaseKeyToClose = function (e) {
            var t = this._attachedKeysToClose.indexOf(e);
            t !== -1 && (o.offUp(e, this.close), this._attachedKeysToClose.splice(t, 1))
        }, m._removeEvents = function () {
            this.renderer.modalElement && r.removeEventListener(this.renderer.modalElement, "click", this.close), this._keysToClose.forEach(this._releaseKeyToClose, this)
        }, m._attachEvents = function () {
            this.renderer.modalElement && r.addEventListener(this.renderer.modalElement, "click", this.close), this._keysToClose.forEach(this._bindKeyToClose, this)
        }, m._restoreScrollPosition = function () {
            window.scrollTo(this._scrollX || 0, this._scrollY || 0)
        }, m._saveScrollPosition = function () {
            this._scrollX = s.getScrollX(), this._scrollY = s.getScrollY()
        }, t.exports = i
    }, {
        "./Renderer": 43,
        "@marcom/ac-dom-events/addEventListener": 18,
        "@marcom/ac-dom-events/removeEventListener": 19,
        "@marcom/ac-dom-events/target": 21,
        "@marcom/ac-dom-metrics/getScrollX": 24,
        "@marcom/ac-dom-metrics/getScrollY": 25,
        "@marcom/ac-event-emitter-micro": 35,
        "@marcom/ac-keyboard": 38,
        "@marcom/ac-keyboard/keyMap": 40,
        "@marcom/ac-object/create": 50,
        "@marcom/ac-object/defaults": 51
    }],
    43: [function (e, t, n) {
        "use strict";
        var i = {
                add: e("@marcom/ac-classlist/add"),
                remove: e("@marcom/ac-classlist/remove")
            },
            r = {
                defaults: e("@marcom/ac-object/defaults")
            },
            s = {
                remove: e("@marcom/ac-dom-nodes/remove"),
                isElement: e("@marcom/ac-dom-nodes/isElement")
            },
            a = e("./classNames"),
            o = e("./dataAttributes"),
            l = {
                modalElement: null,
                contentElement: null,
                closeButton: null,
                classNames: a,
                dataAttributes: o
            },
            c = function (e) {
                e = e || {}, this.options = r.defaults(l, e), this.options.classNames = r.defaults(l.classNames, e.classNames), this.options.dataAttributes = r.defaults(l.dataAttributes, e.dataAttributes), this.modalElement = this.options.modalElement, this.contentElement = this.options.contentElement, this.closeButton = this.options.closeButton
            },
            u = c.prototype;
        u.render = function () {
            return s.isElement(this.modalElement) || (this.modalElement = this.renderModalElement(this.options.classNames.modalElement)), s.isElement(this.contentElement) || (this.contentElement = this.renderContentElement(this.options.classNames.contentElement)), this.closeButton !== !1 && (s.isElement(this.closeButton) || (this.closeButton = this.renderCloseButton(this.options.classNames.closeButton)), this.modalElement.appendChild(this.closeButton)), this.modalElement.appendChild(this.contentElement), document.body.appendChild(this.modalElement), this.modalElement
        }, u.renderCloseButton = function (e) {
            var t;
            return e = e || this.options.classNames.closeButton, t = this._renderElement("button", e), t.setAttribute(this.options.dataAttributes.close, ""), t
        }, u.renderModalElement = function (e) {
            return e = e || this.options.classNames.modalElement, this._renderElement("div", e)
        }, u.renderContentElement = function (e) {
            return e = e || this.options.classNames.contentElement, this._renderElement("div", e)
        }, u.appendContent = function (e, t) {
            s.isElement(e) && (void 0 === arguments[1] ? this.contentElement.appendChild(e) : s.isElement(t) && t.appendChild(e))
        }, u.removeContent = function (e) {
            e ? this.modalElement.contains(e) && s.remove(e) : this._emptyContent()
        }, u.open = function () {
            var e = [document.documentElement].concat(this.options.classNames.documentElement),
                t = [this.modalElement].concat(this.options.classNames.modalOpen);
            i.add.apply(null, e), i.add.apply(null, t)
        }, u.close = function () {
            var e = [document.documentElement].concat(this.options.classNames.documentElement),
                t = [this.modalElement].concat(this.options.classNames.modalOpen);
            i.remove.apply(null, e), i.remove.apply(null, t)
        }, u.destroy = function () {
            var e = [document.documentElement].concat(this.options.classNames.documentElement);
            this.modalElement && document.body.contains(this.modalElement) && (this.close(), document.body.removeChild(this.modalElement)), i.remove.apply(null, e);
            for (var t in this) this.hasOwnProperty(t) && (this[t] = null)
        }, u._renderElement = function (e, t) {
            var n = document.createElement(e),
                r = [n];
            return t && (r = r.concat(t)), i.add.apply(null, r), n
        }, u._emptyContent = function () {
            this.contentElement.innerHTML = ""
        }, t.exports = c
    }, {
        "./classNames": 44,
        "./dataAttributes": 45,
        "@marcom/ac-classlist/add": 12,
        "@marcom/ac-classlist/remove": 17,
        "@marcom/ac-dom-nodes/isElement": 32,
        "@marcom/ac-dom-nodes/remove": 34,
        "@marcom/ac-object/defaults": 51
    }],
    44: [function (e, t, n) {
        "use strict";
        t.exports = {
            modalElement: "modal",
            modalOpen: "modal-open",
            documentElement: "has-modal",
            contentElement: "modal-content",
            closeButton: "modal-close"
        }
    }, {}],
    45: [function (e, t, n) {
        "use strict";
        t.exports = {
            close: "data-modal-close"
        }
    }, {}],
    46: [function (e, t, n) {
        "use strict";
        t.exports = {
            Modal: e("./ac-modal/Modal"),
            createStandardModal: e("./ac-modal/factory/createStandardModal"),
            createFullViewportModal: e("./ac-modal/factory/createFullViewportModal")
        }
    }, {
        "./ac-modal/Modal": 47,
        "./ac-modal/factory/createFullViewportModal": 48,
        "./ac-modal/factory/createStandardModal": 49
    }],
    47: [function (e, t, n) {
        "use strict";

        function i(e) {
            s.call(this), this.options = e || {}, this._modal = new r(e, this.options.renderer), this.opened = !1, this._render(), this.closeButton = this._modal.renderer.closeButton, this.modalElement = this._modal.renderer.modalElement, this.contentElement = this._modal.renderer.contentElement, this.modalElement.setAttribute("role", "dialog"), this.modalElement.setAttribute("aria-label", "Modal"), this.modalElement.setAttribute("aria-modal", "true"), this.modalElement.setAttribute("tabindex", "-1"), this.closeButton.setAttribute("aria-label", "Close"), this._circularTab = new a(this.modalElement), this._onWillOpen = this._onWillOpen.bind(this), this._onOpen = this._onOpen.bind(this), this._onWillClose = this._onWillClose.bind(this), this._onClose = this._onClose.bind(this), this._bindEvents()
        }
        var r = e("@marcom/ac-modal-basic").Modal,
            s = e("@marcom/ac-event-emitter-micro").EventEmitterMicro,
            a = e("@marcom/ac-accessibility/CircularTab"),
            o = i.prototype = Object.create(s.prototype);
        o.open = function () {
            this._modal.open(), this.opened = this._modal.opened
        }, o.close = function () {
            this._modal.close()
        }, o.appendContent = function (e) {
            this._modal.appendContent(e)
        }, o.removeContent = function (e) {
            this._modal.removeContent(e)
        }, o.destroy = function () {
            this._releaseEvents(), this._modal.destroy(), this._removeModalFocus(), this._circularTab.destroy(), this._focusObj = null;
            for (var e in this) this.hasOwnProperty(e) && (this[e] = null)
        }, o.addKeyToClose = function (e) {
            this._modal.addKeyToClose(e)
        }, o.removeKeyToClose = function (e) {
            this._modal.removeKeyToClose(e)
        }, o._render = function () {
            this._modal.render(), this._modal.renderer.modalElement.setAttribute("aria-hidden", "true")
        }, o._bindEvents = function () {
            this._modal.on("willopen", this._onWillOpen), this._modal.on("open", this._onOpen), this._modal.on("willclose", this._onWillClose), this._modal.on("close", this._onClose)
        }, o._releaseEvents = function () {
            this._modal.off("willopen", this._onWillOpen), this._modal.off("open", this._onOpen), this._modal.off("willclose", this._onWillClose), this._modal.off("close", this._onClose)
        }, o._onWillOpen = function () {
            this.trigger("willopen")
        }, o._onOpen = function () {
            this.opened = this._modal.opened, this._giveModalFocus(), this.trigger("open")
        }, o._onWillClose = function () {
            this.trigger("willclose"), this._removeModalFocus()
        }, o._onClose = function () {
            this.opened = this._modal.opened, this.trigger("close")
        }, o._giveModalFocus = function () {
            this.modalElement.removeAttribute("aria-hidden"), this._activeElement = document.activeElement, setTimeout(function () {
                this.modalElement.focus()
            }.bind(this), 300), this._circularTab.start()
        }, o._removeModalFocus = function () {
            this._circularTab.stop(), this.modalElement.setAttribute("aria-hidden", "true"), this._activeElement && (this._activeElement.focus(), this._activeElement = null)
        }, t.exports = i
    }, {
        "@marcom/ac-accessibility/CircularTab": 1,
        "@marcom/ac-event-emitter-micro": 35,
        "@marcom/ac-modal-basic": 41
    }],
    48: [function (e, t, n) {
        "use strict";

        function i(e, t) {
            var n = new r(a),
                i = t || {};
            return e && n.appendContent(e), i.removeContainerPadding && n.modalElement.classList.add("remove-container-padding"), n
        }
        var r = e("../Modal"),
            s = e("@marcom/ac-modal-basic").classNames,
            a = {
                retainScrollPosition: !0,
                renderer: {
                    classNames: {
                        documentElement: [s.documentElement].concat("has-modal-full-viewport"),
                        modalElement: [s.modalElement].concat("modal-full-viewport")
                    }
                }
            };
        t.exports = i
    }, {
        "../Modal": 47,
        "@marcom/ac-modal-basic": 41
    }],
    49: [function (e, t, n) {
        "use strict";

        function i(e) {
            var t = new r(l);
            e && t.appendContent(e);
            var n = document.createElement("div"),
                i = document.createElement("div"),
                s = document.createElement("div"),
                c = document.createElement("div");
            return o.add(n, "content-table"), o.add(i, "content-cell"), o.add(s, "content-wrapper"), o.add(c, "content-padding", "large-8", "medium-10"), t.modalElement.setAttribute(a.close, ""), s.setAttribute(a.close, ""), i.setAttribute(a.close, ""), n.appendChild(i), i.appendChild(s), s.appendChild(c), t.modalElement.appendChild(n), c.appendChild(t.contentElement), c.appendChild(t.closeButton), t
        }
        var r = e("../Modal"),
            s = e("@marcom/ac-modal-basic").classNames,
            a = e("@marcom/ac-modal-basic").dataAttributes,
            o = {
                add: e("@marcom/ac-classlist/add")
            },
            l = {
                renderer: {
                    classNames: {
                        documentElement: [s.documentElement].concat("has-modal-standard"),
                        modalElement: [s.modalElement].concat("modal-standard")
                    }
                }
            };
        t.exports = i
    }, {
        "../Modal": 47,
        "@marcom/ac-classlist/add": 12,
        "@marcom/ac-modal-basic": 41
    }],
    50: [function (e, t, n) {
        "use strict";
        var i = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (e) {
                return typeof e
            } : function (e) {
                return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e
            },
            r = function () {};
        t.exports = function (e) {
            if (arguments.length > 1) throw new Error("Second argument not supported");
            if (null === e || "object" !== ("undefined" == typeof e ? "undefined" : i(e))) throw new TypeError("Object prototype may only be an Object.");
            return "function" == typeof Object.create ? Object.create(e) : (r.prototype = e, new r)
        }
    }, {}],
    51: [function (e, t, n) {
        "use strict";
        var i = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (e) {
                return typeof e
            } : function (e) {
                return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e
            },
            r = e("./extend");
        t.exports = function (e, t) {
            if ("object" !== ("undefined" == typeof e ? "undefined" : i(e))) throw new TypeError("defaults: must provide a defaults object");
            if (t = t || {}, "object" !== ("undefined" == typeof t ? "undefined" : i(t))) throw new TypeError("defaults: options must be a typeof object");
            return r({}, e, t)
        }
    }, {
        "./extend": 52
    }],
    52: [function (e, t, n) {
        "use strict";
        e("@marcom/ac-polyfills/Array/prototype.forEach");
        var i = Object.prototype.hasOwnProperty;
        t.exports = function () {
            var e, t;
            return e = arguments.length < 2 ? [{}, arguments[0]] : [].slice.call(arguments), t = e.shift(), e.forEach(function (e) {
                if (null != e)
                    for (var n in e) i.call(e, n) && (t[n] = e[n])
            }), t
        }
    }, {
        "@marcom/ac-polyfills/Array/prototype.forEach": void 0
    }],
    53: [function (e, t, n) {
        "use strict";
        var i = e("./utils/eventTypeAvailable"),
            r = e("./shared/camelCasedEventTypes"),
            s = e("./shared/windowFallbackEventTypes"),
            a = e("./shared/prefixHelper"),
            o = {};
        t.exports = function l(e, t) {
            var n, c, u;
            if (t = t || "div", e = e.toLowerCase(), t in o || (o[t] = {}), c = o[t], e in c) return c[e];
            if (i(e, t)) return c[e] = e;
            if (e in r)
                for (u = 0; u < r[e].length; u++)
                    if (n = r[e][u], i(n.toLowerCase(), t)) return c[e] = n;
            for (u = 0; u < a.evt.length; u++)
                if (n = a.evt[u] + e, i(n, t)) return a.reduce(u), c[e] = n;
            return "window" !== t && s.indexOf(e) ? c[e] = l(e, "window") : c[e] = !1
        }
    }, {
        "./shared/camelCasedEventTypes": 54,
        "./shared/prefixHelper": 55,
        "./shared/windowFallbackEventTypes": 56,
        "./utils/eventTypeAvailable": 57
    }],
    54: [function (e, t, n) {
        "use strict";
        t.exports = {
            transitionend: ["webkitTransitionEnd", "MSTransitionEnd"],
            animationstart: ["webkitAnimationStart", "MSAnimationStart"],
            animationend: ["webkitAnimationEnd", "MSAnimationEnd"],
            animationiteration: ["webkitAnimationIteration", "MSAnimationIteration"],
            fullscreenchange: ["MSFullscreenChange"],
            fullscreenerror: ["MSFullscreenError"]
        }
    }, {}],
    55: [function (e, t, n) {
        "use strict";
        var i = ["-webkit-", "-moz-", "-ms-"],
            r = ["Webkit", "Moz", "ms"],
            s = ["webkit", "moz", "ms"],
            a = function () {
                this.initialize()
            },
            o = a.prototype;
        o.initialize = function () {
            this.reduced = !1, this.css = i, this.dom = r, this.evt = s
        }, o.reduce = function (e) {
            this.reduced || (this.reduced = !0, this.css = [this.css[e]], this.dom = [this.dom[e]], this.evt = [this.evt[e]])
        }, t.exports = new a
    }, {}],
    56: [function (e, t, n) {
        "use strict";
        t.exports = ["transitionend", "animationstart", "animationend", "animationiteration"]
    }, {}],
    57: [function (e, t, n) {
        "use strict";
        var i = {
            window: window,
            document: document
        };
        t.exports = function (e, t) {
            var n;
            return e = "on" + e, t in i || (i[t] = document.createElement(t)), n = i[t], e in n || "setAttribute" in n && (n.setAttribute(e, "return;"), "function" == typeof n[e])
        }
    }, {}],
    58: [function (e, t, n) {
        "use strict";

        function i(e) {
            e = e || {}, s.call(this), this.id = o.getNewID(), this.executor = e.executor || a, this._reset(), this._willRun = !1, this._didDestroy = !1
        }
        var r, s = e("@marcom/ac-event-emitter-micro").EventEmitterMicro,
            a = e("@marcom/ac-raf-executor/sharedRAFExecutorInstance"),
            o = e("@marcom/ac-raf-emitter-id-generator/sharedRAFEmitterIDGeneratorInstance");
        r = i.prototype = Object.create(s.prototype), r.run = function () {
            return this._willRun || (this._willRun = !0), this._subscribe();
        }, r.cancel = function () {
            this._unsubscribe(), this._willRun && (this._willRun = !1), this._reset()
        }, r.destroy = function () {
            var e = this.willRun();
            return this.cancel(), this.executor = null, s.prototype.destroy.call(this), this._didDestroy = !0, e
        }, r.willRun = function () {
            return this._willRun
        }, r.isRunning = function () {
            return this._isRunning
        }, r._subscribe = function () {
            return this.executor.subscribe(this)
        }, r._unsubscribe = function () {
            return this.executor.unsubscribe(this)
        }, r._onAnimationFrameStart = function (e) {
            this._isRunning = !0, this._willRun = !1, this._didEmitFrameData || (this._didEmitFrameData = !0, this.trigger("start", e))
        }, r._onAnimationFrameEnd = function (e) {
            this._willRun || (this.trigger("stop", e), this._reset())
        }, r._reset = function () {
            this._didEmitFrameData = !1, this._isRunning = !1
        }, t.exports = i
    }, {
        "@marcom/ac-event-emitter-micro": 35,
        "@marcom/ac-raf-emitter-id-generator/sharedRAFEmitterIDGeneratorInstance": 69,
        "@marcom/ac-raf-executor/sharedRAFExecutorInstance": 87
    }],
    59: [function (e, t, n) {
        "use strict";
        var i = e("./SingleCallRAFEmitter"),
            r = function (e) {
                this.rafEmitter = new i, this.rafEmitter.on(e, this._onRAFExecuted.bind(this)), this.requestAnimationFrame = this.requestAnimationFrame.bind(this), this.cancelAnimationFrame = this.cancelAnimationFrame.bind(this), this._frameCallbacks = [], this._nextFrameCallbacks = [], this._currentFrameID = -1, this._cancelFrameIdx = -1, this._frameCallbackLength = 0, this._nextFrameCallbacksLength = 0, this._frameCallbackIteration = 0
            },
            s = r.prototype;
        s.requestAnimationFrame = function (e) {
            return this._currentFrameID = this.rafEmitter.run(), this._nextFrameCallbacks.push(this._currentFrameID, e), this._nextFrameCallbacksLength += 2, this._currentFrameID
        }, s.cancelAnimationFrame = function (e) {
            this._cancelFrameIdx = this._nextFrameCallbacks.indexOf(e), this._cancelFrameIdx !== -1 && (this._nextFrameCallbacks.splice(this._cancelFrameIdx, 2), this._nextFrameCallbacksLength -= 2, 0 === this._nextFrameCallbacksLength && this.rafEmitter.cancel())
        }, s._onRAFExecuted = function (e) {
            for (this._frameCallbacks = this._nextFrameCallbacks, this._frameCallbackLength = this._nextFrameCallbacksLength, this._nextFrameCallbacks = [], this._nextFrameCallbacksLength = 0, this._frameCallbackIteration = 0; this._frameCallbackIteration < this._frameCallbackLength; this._frameCallbackIteration += 2) this._frameCallbacks[this._frameCallbackIteration + 1](e.time, e)
        }, t.exports = r
    }, {
        "./SingleCallRAFEmitter": 61
    }],
    60: [function (e, t, n) {
        "use strict";
        var i = e("./RAFInterface"),
            r = function () {
                this.events = {}
            },
            s = r.prototype;
        s.requestAnimationFrame = function (e) {
            return this.events[e] || (this.events[e] = new i(e)), this.events[e].requestAnimationFrame
        }, s.cancelAnimationFrame = function (e) {
            return this.events[e] || (this.events[e] = new i(e)), this.events[e].cancelAnimationFrame
        }, t.exports = new r
    }, {
        "./RAFInterface": 59
    }],
    61: [function (e, t, n) {
        "use strict";
        var i = e("./RAFEmitter"),
            r = function (e) {
                i.call(this, e)
            },
            s = r.prototype = Object.create(i.prototype);
        s._subscribe = function () {
            return this.executor.subscribe(this, !0)
        }, t.exports = r
    }, {
        "./RAFEmitter": 58
    }],
    62: [function (e, t, n) {
        "use strict";
        var i = e("./RAFInterfaceController");
        t.exports = i.requestAnimationFrame("draw")
    }, {
        "./RAFInterfaceController": 60
    }],
    63: [function (e, t, n) {
        "use strict";
        var i = e("./RAFInterfaceController");
        t.exports = i.requestAnimationFrame("update")
    }, {
        "./RAFInterfaceController": 60
    }],
    64: [function (e, t, n) {
        "use strict";

        function i(e) {
            a.call(this), this.options = r(c, e), this.loadingOptions = null, this.els = [], this.loadingQueue = null, this._queueItems = [], this._queueItemsObj = {}, this._loadOrder = [], this._timeout = null, this._didCallLoad = !1
        }
        var r = e("@marcom/ac-object/defaults"),
            s = e("@marcom/ac-queue").LiveQueue,
            a = e("@marcom/ac-event-emitter-micro").EventEmitterMicro,
            o = e("@marcom/ac-raf-emitter/update"),
            l = e("@marcom/ac-raf-emitter/draw"),
            c = {
                container: document.body,
                includeContainer: !1
            },
            u = {
                loadingPoolSize: 8,
                timeout: null,
                imageDataAttribute: "data-progressive-image",
                imageAnimate: !0,
                imageAnimateClass: "progressive-image-animated"
            };
        i.Events = {
            ImageLoad: "image-load",
            Complete: "complete"
        };
        var h = i.prototype = Object.create(a.prototype);
        h.load = function (e) {
            this._didCallLoad || (this._didCallLoad = !0, this.loadingOptions = r(u, e), this.loadingQueue = new s(this.loadingOptions.loadingPoolSize), this.els = Array.from(this._getProgressiveImageElements()), this.options.includeContainer && this.options.container.hasAttribute(this._getProgressiveImageDataAttribute()) && this.els.unshift(this.options.container), l(function () {
                var e, t, n = this.els.length;
                for (e = 0; e < n; e++) t = {
                    queueItem: this.loadingQueue.enqueue(this._loadNextItem.bind(this, e), e),
                    el: this.els[e],
                    id: e
                }, this._queueItems.push(t), this._queueItemsObj[e] = t, this.loadingOptions.imageAnimate && this.els[e].classList.add(this.loadingOptions.imageAnimateClass);
                o(function () {
                    this.loadingQueue.start(), "number" == typeof this.loadingOptions.timeout && (this._timeout = setTimeout(this.cancel.bind(this), this.loadingOptions.timeout))
                }.bind(this))
            }.bind(this)))
        }, h.setVisible = function (e) {
            return new Promise(function (t, n) {
                l(function () {
                    e.removeAttribute(this._getProgressiveImageDataAttribute()), t(), e = null
                }.bind(this))
            }.bind(this))
        }, h.cancel = function () {
            if (this.els) {
                var e, t = this.els.length;
                for (e = 0; e < t; e++) this.setVisible(this.els[e]), this.loadingOptions.imageAnimate && l(function () {
                    this.els[e].setAttribute("data-progressive-image-loaded", "")
                }.bind(this, e))
            }
            this._handleLoadingComplete()
        }, h.destroy = function () {
            this.cancel(), this.off(), a.prototype.destroy.call(this)
        }, h._loadNextItem = function (e) {
            return new Promise(function (e, t, n) {
                var i = this._queueItemsObj[e];
                this._loadAndSetVisible(i.el).then(function () {
                    var e = this._queueItems.indexOf(i);
                    this._queueItems.splice(e, 1), this._queueItemsObj[i.id] = null, t(), this._handleImageLoad(i.el), i = t = null, 1 === this.loadingQueue.count() && this._handleLoadingComplete()
                }.bind(this))
            }.bind(this, e))
        }, h._loadAndSetVisible = function (e) {
            return new Promise(function (t, n) {
                this.setVisible(e).then(function () {
                    this._getBackgroundImageSrc(e).then(function (n) {
                        this._loadImage(n).then(t), e = null
                    }.bind(this))
                }.bind(this))
            }.bind(this))
        }, h._getBackgroundImageSrc = function (e) {
            return new Promise(function (t, n) {
                o(function () {
                    var n = e.currentStyle;
                    return n || (n = window.getComputedStyle(e, !1)), e = null, 0 === n.backgroundImage.indexOf("url(") ? void t(n.backgroundImage.slice(4, -1).replace(/"/g, "")) : void t(null)
                }.bind(this))
            }.bind(this))
        }, h._getProgressiveImageDataAttribute = function () {
            return this.loadingOptions.imageDataAttribute
        }, h._getProgressiveImageCSSQuery = function () {
            return "[" + this._getProgressiveImageDataAttribute() + "]"
        }, h._getProgressiveImageElements = function () {
            return this.options.container.querySelectorAll(this._getProgressiveImageCSSQuery()) || []
        }, h._loadImage = function (e) {
            return new Promise(this._loadImagePromiseFunc.bind(this, e))
        }, h._loadImagePromiseFunc = function (e, t, n) {
            function i() {
                this.removeEventListener("load", i), t(this), t = null
            }
            if (!e) return void t(null);
            var r = new Image;
            r.addEventListener("load", i), r.src = e
        }, h._clearTimeout = function () {
            this._timeout && (window.clearTimeout(this._timeout), this._timeout = null)
        }, h._handleImageLoad = function (e) {
            l(function () {
                this.trigger(i.Events.ImageLoad, e), this.loadingOptions.imageAnimate && e.setAttribute("data-progressive-image-loaded", ""), e = null
            }.bind(this))
        }, h._handleLoadingComplete = function () {
            this.loadingQueue.stop(), this._clearTimeout(), this.trigger(i.Events.Complete)
        }, t.exports = i
    }, {
        "@marcom/ac-event-emitter-micro": 35,
        "@marcom/ac-object/defaults": 51,
        "@marcom/ac-queue": 65,
        "@marcom/ac-raf-emitter/draw": 62,
        "@marcom/ac-raf-emitter/update": 63
    }],
    65: [function (e, t, n) {
        "use strict";
        t.exports = {
            Queue: e("./ac-queue/Queue"),
            QueueItem: e("./ac-queue/QueueItem"),
            LiveQueue: e("./ac-queue/LiveQueue")
        }
    }, {
        "./ac-queue/LiveQueue": 66,
        "./ac-queue/Queue": 67,
        "./ac-queue/QueueItem": 68
    }],
    66: [function (e, t, n) {
        "use strict";

        function i(e) {
            this._queue = new r, this._maxProcesses = e || 1, this._availableSlots = this._maxProcesses, this._rafId = 0, this._isRunning = !1, this._boundFunctions = {
                _run: this._run.bind(this),
                _releaseSlot: this._releaseSlot.bind(this)
            }
        }
        e("@marcom/ac-polyfills/Promise"), e("@marcom/ac-polyfills/requestAnimationFrame"), e("@marcom/ac-polyfills/Function/prototype.bind");
        var r = e("./Queue"),
            s = e("./QueueItem"),
            a = i.prototype;
        a.start = function () {
            this._isRunning && cancelAnimationFrame(this._rafId), this._rafId = requestAnimationFrame(this._boundFunctions._run), this._isRunning = !0
        }, a.pause = function () {
            this._isRunning && (cancelAnimationFrame(this._rafId), this._rafId = 0), this._isRunning = !1
        }, a.stop = function () {
            this.pause(), this.clear()
        }, a.enqueue = function (e, t) {
            if ("function" != typeof e) throw new Error("LiveQueue can only enqueue functions");
            void 0 === t && (t = r.PRIORITY_DEFAULT);
            var n = new s(e, t);
            return this.enqueueQueueItem(n)
        }, a.enqueueQueueItem = function (e) {
            return this._queue.enqueueQueueItem(e), this._isRunning && 0 === this._rafId && this.start(), e
        }, a.dequeueQueueItem = function (e) {
            return this._queue.dequeueQueueItem(e)
        }, a.clear = function () {
            this._queue = new r
        }, a.destroy = function () {
            this.pause(), this._isRunning = !1, this._queue = null, this._boundFunctions = null
        }, a.count = function () {
            return this._queue.count() + this.pending()
        }, a.pending = function () {
            return this._maxProcesses - this._availableSlots
        }, a.isEmpty = function () {
            return 0 === this.count()
        }, a._run = function () {
            if (this._isRunning && (this._rafId = requestAnimationFrame(this._boundFunctions._run), !this._queue.isEmpty() && 0 !== this._availableSlots)) {
                var e = this._queue.dequeue(),
                    t = e.data();
                this._isPromise(t) && (this._retainSlot(), t.then(this._boundFunctions._releaseSlot, this._boundFunctions._releaseSlot)), this._stopRunningIfDone()
            }
        }, a._retainSlot = function () {
            this._availableSlots--
        }, a._releaseSlot = function () {
            this._availableSlots++, this._stopRunningIfDone()
        }, a._stopRunningIfDone = function () {
            0 != this._rafId && 0 === this._queue.count() && this._availableSlots == this._maxProcesses && (cancelAnimationFrame(this._rafId), this._rafId = 0)
        }, a._isPromise = function (e) {
            return !(!e || "function" != typeof e.then)
        }, t.exports = i
    }, {
        "./Queue": 67,
        "./QueueItem": 68,
        "@marcom/ac-polyfills/Function/prototype.bind": void 0,
        "@marcom/ac-polyfills/Promise": void 0,
        "@marcom/ac-polyfills/requestAnimationFrame": void 0
    }],
    67: [function (e, t, n) {
        "use strict";

        function i() {
            this._items = []
        }
        var r = e("./QueueItem"),
            s = i.prototype;
        s.enqueue = function (e, t) {
            void 0 === t && (t = i.PRIORITY_DEFAULT);
            var n = new r(e, t);
            return this.enqueueQueueItem(n)
        }, s.enqueueQueueItem = function (e) {
            return this._items.indexOf(e) === -1 && this._items.push(e), e
        }, s.dequeue = function () {
            this._heapSort();
            var e = this._items.length - 1,
                t = this._items[0];
            return this._items[0] = this._items[e], this._items.pop(), t
        }, s.dequeueQueueItem = function (e) {
            var t = this._items.indexOf(e);
            return t > -1 && this._items.splice(t, 1), e
        }, s.peek = function () {
            return 0 == this.count() ? null : (this._heapSort(), this._items[0])
        }, s.isEmpty = function () {
            return 0 === this._items.length
        }, s.count = function () {
            return this._items.length
        }, s.toString = function () {
            for (var e = ["Queue total items: " + this.count() + "\n"], t = 0; t < this.count(); ++t) e.push(this._items[t].toString() + "\n");
            return e.join("")
        }, s._heapSort = function () {
            for (var e = 0, t = this._items.length - 1; t >= 0; t--)
                for (var n = t; n > 0;) {
                    e++;
                    var i = Math.floor((n - 1) / 2);
                    if (this._items[n].compareTo(this._items[i]) >= 0) break;
                    var r = this._items[n];
                    this._items[n] = this._items[i], this._items[i] = r, n = i
                }
        }, i.PRIORITY_LOW = 10, i.PRIORITY_DEFAULT = 5, i.PRIORITY_HIGH = 1, t.exports = i
    }, {
        "./QueueItem": 68
    }],
    68: [function (e, t, n) {
        "use strict";

        function i(e, t) {
            this.priority = t, this.data = e, this.insertionOrder = r++
        }
        var r = 0,
            s = i.prototype;
        s.compareTo = function (e) {
            return this.priority < e.priority ? -1 : this.priority > e.priority ? 1 : this.insertionOrder < e.insertionOrder ? -1 : 1
        }, s.toString = function () {
            return "QueueItem {priority:" + this.priority + ",\tdata:" + this.data + "\tinsertionOrder:" + this.insertionOrder + "}"
        }, t.exports = i
    }, {}],
    69: [function (e, t, n) {
        "use strict";
        var i = e("@marcom/ac-shared-instance").SharedInstance,
            r = "ac-raf-emitter-id-generator:sharedRAFEmitterIDGeneratorInstance",
            s = "1.0.3",
            a = function () {
                this._currentID = 0
            };
        a.prototype.getNewID = function () {
            return this._currentID++, "raf:" + this._currentID
        }, t.exports = i.share(r, s, a)
    }, {
        "@marcom/ac-shared-instance": 88
    }],
    70: [function (e, t, n) {
        "use strict";
        t.exports = {
            majorVersionNumber: "3.x"
        }
    }, {}],
    71: [function (e, t, n) {
        "use strict";
        t.exports = {
            RAFEmitter: e("./ac-raf-emitter/RAFEmitter"),
            ThrottledRAFEmitter: e("./ac-raf-emitter/ThrottledRAFEmitter"),
            update: e("./ac-raf-emitter/update"),
            external: e("./ac-raf-emitter/external"),
            draw: e("./ac-raf-emitter/draw"),
            cancelUpdate: e("./ac-raf-emitter/cancelUpdate"),
            cancelExternal: e("./ac-raf-emitter/cancelExternal"),
            cancelDraw: e("./ac-raf-emitter/cancelDraw"),
            RAFExecutor: e("./ac-raf-emitter/RAFExecutor"),
            sharedRAFExecutorInstance: e("./ac-raf-emitter/sharedRAFExecutorInstance")
        }
    }, {
        "./ac-raf-emitter/RAFEmitter": 72,
        "./ac-raf-emitter/RAFExecutor": 73,
        "./ac-raf-emitter/ThrottledRAFEmitter": 77,
        "./ac-raf-emitter/cancelDraw": 78,
        "./ac-raf-emitter/cancelExternal": 79,
        "./ac-raf-emitter/cancelUpdate": 80,
        "./ac-raf-emitter/draw": 81,
        "./ac-raf-emitter/external": 82,
        "./ac-raf-emitter/sharedRAFExecutorInstance": 84,
        "./ac-raf-emitter/update": 85
    }],
    72: [function (e, t, n) {
        "use strict";

        function i(e) {
            e = e || {}, s.call(this), this.id = o.getNewID(), this.executor = e.executor || a, this._reset(), this._willRun = !1, this._didDestroy = !1
        }
        var r, s = e("@marcom/ac-event-emitter-micro").EventEmitterMicro,
            a = e("./sharedRAFExecutorInstance"),
            o = e("./sharedRAFEmitterIDGeneratorInstance");
        r = i.prototype = Object.create(s.prototype), r.run = function () {
            return this._willRun || (this._willRun = !0), this._subscribe()
        }, r.cancel = function () {
            this._unsubscribe(), this._willRun && (this._willRun = !1), this._reset()
        }, r.destroy = function () {
            var e = this.willRun();
            return this.cancel(), this.executor = null, s.prototype.destroy.call(this), this._didDestroy = !0, e
        }, r.willRun = function () {
            return this._willRun
        }, r.isRunning = function () {
            return this._isRunning
        }, r._subscribe = function () {
            return this.executor.subscribe(this)
        }, r._unsubscribe = function () {
            return this.executor.unsubscribe(this)
        }, r._onAnimationFrameStart = function (e) {
            this._isRunning = !0, this._willRun = !1, this._didEmitFrameData || (this._didEmitFrameData = !0, this.trigger("start", e))
        }, r._onAnimationFrameEnd = function (e) {
            this._willRun || (this.trigger("stop", e), this._reset())
        }, r._reset = function () {
            this._didEmitFrameData = !1, this._isRunning = !1
        }, t.exports = i
    }, {
        "./sharedRAFEmitterIDGeneratorInstance": 83,
        "./sharedRAFExecutorInstance": 84,
        "@marcom/ac-event-emitter-micro": 35
    }],
    73: [function (e, t, n) {
        "use strict";

        function i(e) {
            e = e || {}, this._reset(), this.updatePhases(), this.eventEmitter = new s, this._willRun = !1, this._totalSubscribeCount = -1, this._requestAnimationFrame = window.requestAnimationFrame, this._cancelAnimationFrame = window.cancelAnimationFrame, this._boundOnAnimationFrame = this._onAnimationFrame.bind(this), this._boundOnExternalAnimationFrame = this._onExternalAnimationFrame.bind(this)
        }
        var r, s = e("@marcom/ac-event-emitter-micro/EventEmitterMicro");
        r = i.prototype, r.frameRequestedPhase = "requested", r.startPhase = "start", r.runPhases = ["update", "external", "draw"], r.endPhase = "end", r.disabledPhase = "disabled", r.beforePhaseEventPrefix = "before:", r.afterPhaseEventPrefix = "after:", r.subscribe = function (e, t) {
            return this._totalSubscribeCount++, this._nextFrameSubscribers[e.id] || (t ? this._nextFrameSubscribersOrder.unshift(e.id) : this._nextFrameSubscribersOrder.push(e.id), this._nextFrameSubscribers[e.id] = e, this._nextFrameSubscriberArrayLength++, this._nextFrameSubscriberCount++, this._run()), this._totalSubscribeCount
        }, r.subscribeImmediate = function (e, t) {
            return this._totalSubscribeCount++, this._subscribers[e.id] || (t ? this._subscribersOrder.splice(this._currentSubscriberIndex + 1, 0, e.id) : this._subscribersOrder.unshift(e.id), this._subscribers[e.id] = e, this._subscriberArrayLength++, this._subscriberCount++), this._totalSubscribeCount
        }, r.unsubscribe = function (e) {
            return !!this._nextFrameSubscribers[e.id] && (this._nextFrameSubscribers[e.id] = null, this._nextFrameSubscriberCount--, 0 === this._nextFrameSubscriberCount && this._cancel(), !0)
        }, r.getSubscribeID = function () {
            return this._totalSubscribeCount += 1
        }, r.destroy = function () {
            var e = this._cancel();
            return this.eventEmitter.destroy(), this.eventEmitter = null, this.phases = null, this._subscribers = null, this._subscribersOrder = null, this._nextFrameSubscribers = null, this._nextFrameSubscribersOrder = null, this._rafData = null, this._boundOnAnimationFrame = null, this._onExternalAnimationFrame = null, e
        }, r.useExternalAnimationFrame = function (e) {
            if ("boolean" == typeof e) {
                var t = this._isUsingExternalAnimationFrame;
                return e && this._animationFrame && (this._cancelAnimationFrame.call(window, this._animationFrame), this._animationFrame = null), !this._willRun || e || this._animationFrame || (this._animationFrame = this._requestAnimationFrame.call(window, this._boundOnAnimationFrame)), this._isUsingExternalAnimationFrame = e, e ? this._boundOnExternalAnimationFrame : t || !1
            }
        }, r.updatePhases = function () {
            this.phases || (this.phases = []), this.phases.length = 0, this.phases.push(this.frameRequestedPhase), this.phases.push(this.startPhase), Array.prototype.push.apply(this.phases, this.runPhases), this.phases.push(this.endPhase), this._runPhasesLength = this.runPhases.length, this._phasesLength = this.phases.length
        }, r._run = function () {
            if (!this._willRun) return this._willRun = !0, 0 === this.lastFrameTime && (this.lastFrameTime = performance.now()), this._animationFrameActive = !0, this._isUsingExternalAnimationFrame || (this._animationFrame = this._requestAnimationFrame.call(window, this._boundOnAnimationFrame)), this.phase === this.disabledPhase && (this.phaseIndex = 0, this.phase = this.phases[this.phaseIndex]), !0
        }, r._cancel = function () {
            var e = !1;
            return this._animationFrameActive && (this._animationFrame && (this._cancelAnimationFrame.call(window, this._animationFrame), this._animationFrame = null), this._animationFrameActive = !1, this._willRun = !1, e = !0), this._isRunning || this._reset(), e
        }, r._onAnimationFrame = function (e) {
            for (this._subscribers = this._nextFrameSubscribers, this._subscribersOrder = this._nextFrameSubscribersOrder, this._subscriberArrayLength = this._nextFrameSubscriberArrayLength, this._subscriberCount = this._nextFrameSubscriberCount, this._nextFrameSubscribers = {}, this._nextFrameSubscribersOrder = [], this._nextFrameSubscriberArrayLength = 0, this._nextFrameSubscriberCount = 0, this.phaseIndex = 0, this.phase = this.phases[this.phaseIndex], this._isRunning = !0, this._willRun = !1, this._didRequestNextRAF = !1, this._rafData.delta = e - this.lastFrameTime, this.lastFrameTime = e, this._rafData.fps = 0, this._rafData.delta >= 1e3 && (this._rafData.delta = 0), 0 !== this._rafData.delta && (this._rafData.fps = 1e3 / this._rafData.delta), this._rafData.time = e, this._rafData.naturalFps = this._rafData.fps, this._rafData.timeNow = Date.now(), this.phaseIndex++, this.phase = this.phases[this.phaseIndex], this.eventEmitter.trigger(this.beforePhaseEventPrefix + this.phase), this._currentSubscriberIndex = 0; this._currentSubscriberIndex < this._subscriberArrayLength; this._currentSubscriberIndex++) null !== this._subscribers[this._subscribersOrder[this._currentSubscriberIndex]] && this._subscribers[this._subscribersOrder[this._currentSubscriberIndex]]._didDestroy === !1 && this._subscribers[this._subscribersOrder[this._currentSubscriberIndex]]._onAnimationFrameStart(this._rafData);
            for (this.eventEmitter.trigger(this.afterPhaseEventPrefix + this.phase), this._runPhaseIndex = 0; this._runPhaseIndex < this._runPhasesLength; this._runPhaseIndex++) {
                for (this.phaseIndex++, this.phase = this.phases[this.phaseIndex], this.eventEmitter.trigger(this.beforePhaseEventPrefix + this.phase), this._currentSubscriberIndex = 0; this._currentSubscriberIndex < this._subscriberArrayLength; this._currentSubscriberIndex++) null !== this._subscribers[this._subscribersOrder[this._currentSubscriberIndex]] && this._subscribers[this._subscribersOrder[this._currentSubscriberIndex]]._didDestroy === !1 && this._subscribers[this._subscribersOrder[this._currentSubscriberIndex]].trigger(this.phase, this._rafData);
                this.eventEmitter.trigger(this.afterPhaseEventPrefix + this.phase)
            }
            for (this.phaseIndex++, this.phase = this.phases[this.phaseIndex], this.eventEmitter.trigger(this.beforePhaseEventPrefix + this.phase), this._currentSubscriberIndex = 0; this._currentSubscriberIndex < this._subscriberArrayLength; this._currentSubscriberIndex++) null !== this._subscribers[this._subscribersOrder[this._currentSubscriberIndex]] && this._subscribers[this._subscribersOrder[this._currentSubscriberIndex]]._didDestroy === !1 && this._subscribers[this._subscribersOrder[this._currentSubscriberIndex]]._onAnimationFrameEnd(this._rafData);
            this.eventEmitter.trigger(this.afterPhaseEventPrefix + this.phase), this._willRun ? (this.phaseIndex = 0, this.phaseIndex = this.phases[this.phaseIndex]) : this._reset()
        }, r._onExternalAnimationFrame = function (e) {
            this._isUsingExternalAnimationFrame && this._onAnimationFrame(e)
        }, r._reset = function () {
            this._rafData || (this._rafData = {}), this._rafData.time = 0, this._rafData.delta = 0, this._rafData.fps = 0, this._rafData.naturalFps = 0, this._rafData.timeNow = 0, this._subscribers = {}, this._subscribersOrder = [], this._currentSubscriberIndex = -1, this._subscriberArrayLength = 0, this._subscriberCount = 0, this._nextFrameSubscribers = {}, this._nextFrameSubscribersOrder = [], this._nextFrameSubscriberArrayLength = 0, this._nextFrameSubscriberCount = 0, this._didEmitFrameData = !1, this._animationFrame = null, this._animationFrameActive = !1, this._isRunning = !1, this._shouldReset = !1, this.lastFrameTime = 0, this._runPhaseIndex = -1, this.phaseIndex = -1, this.phase = this.disabledPhase
        }, t.exports = i
    }, {
        "@marcom/ac-event-emitter-micro/EventEmitterMicro": 36
    }],
    74: [function (e, t, n) {
        "use strict";
        var i = e("./SingleCallRAFEmitter"),
            r = function (e) {
                this.phase = e, this.rafEmitter = new i, this._cachePhaseIndex(), this.requestAnimationFrame = this.requestAnimationFrame.bind(this), this.cancelAnimationFrame = this.cancelAnimationFrame.bind(this), this._onBeforeRAFExecutorStart = this._onBeforeRAFExecutorStart.bind(this), this._onBeforeRAFExecutorPhase = this._onBeforeRAFExecutorPhase.bind(this), this._onAfterRAFExecutorPhase = this._onAfterRAFExecutorPhase.bind(this), this.rafEmitter.on(this.phase, this._onRAFExecuted.bind(this)), this.rafEmitter.executor.eventEmitter.on("before:start", this._onBeforeRAFExecutorStart), this.rafEmitter.executor.eventEmitter.on("before:" + this.phase, this._onBeforeRAFExecutorPhase), this.rafEmitter.executor.eventEmitter.on("after:" + this.phase, this._onAfterRAFExecutorPhase), this._frameCallbacks = [], this._currentFrameCallbacks = [], this._nextFrameCallbacks = [], this._phaseActive = !1, this._currentFrameID = -1, this._cancelFrameIdx = -1, this._frameCallbackLength = 0, this._currentFrameCallbacksLength = 0, this._nextFrameCallbacksLength = 0, this._frameCallbackIteration = 0
            },
            s = r.prototype;
        s.requestAnimationFrame = function (e, t) {
            return t === !0 && this.rafEmitter.executor.phaseIndex > 0 && this.rafEmitter.executor.phaseIndex <= this.phaseIndex ? this._phaseActive ? (this._currentFrameID = this.rafEmitter.executor.subscribeImmediate(this.rafEmitter, !0), this._frameCallbacks.push(this._currentFrameID, e), this._frameCallbackLength += 2) : (this._currentFrameID = this.rafEmitter.executor.subscribeImmediate(this.rafEmitter, !1), this._currentFrameCallbacks.push(this._currentFrameID, e), this._currentFrameCallbacksLength += 2) : (this._currentFrameID = this.rafEmitter.run(), this._nextFrameCallbacks.push(this._currentFrameID, e), this._nextFrameCallbacksLength += 2), this._currentFrameID
        }, s.cancelAnimationFrame = function (e) {
            this._cancelFrameIdx = this._nextFrameCallbacks.indexOf(e), this._cancelFrameIdx > -1 ? this._cancelNextAnimationFrame() : (this._cancelFrameIdx = this._currentFrameCallbacks.indexOf(e), this._cancelFrameIdx > -1 ? this._cancelCurrentAnimationFrame() : (this._cancelFrameIdx = this._frameCallbacks.indexOf(e), this._cancelFrameIdx > -1 && this._cancelRunningAnimationFrame()))
        }, s._onRAFExecuted = function (e) {
            for (this._frameCallbackIteration = 0; this._frameCallbackIteration < this._frameCallbackLength; this._frameCallbackIteration += 2) this._frameCallbacks[this._frameCallbackIteration + 1](e.time, e);
            this._frameCallbacks.length = 0, this._frameCallbackLength = 0
        }, s._onBeforeRAFExecutorStart = function () {
            Array.prototype.push.apply(this._currentFrameCallbacks, this._nextFrameCallbacks.splice(0, this._nextFrameCallbacksLength)), this._currentFrameCallbacksLength = this._nextFrameCallbacksLength, this._nextFrameCallbacks.length = 0, this._nextFrameCallbacksLength = 0
        }, s._onBeforeRAFExecutorPhase = function () {
            this._phaseActive = !0, Array.prototype.push.apply(this._frameCallbacks, this._currentFrameCallbacks.splice(0, this._currentFrameCallbacksLength)), this._frameCallbackLength = this._currentFrameCallbacksLength, this._currentFrameCallbacks.length = 0, this._currentFrameCallbacksLength = 0
        }, s._onAfterRAFExecutorPhase = function () {
            this._phaseActive = !1
        }, s._cachePhaseIndex = function () {
            this.phaseIndex = this.rafEmitter.executor.phases.indexOf(this.phase)
        }, s._cancelRunningAnimationFrame = function () {
            this._frameCallbacks.splice(this._cancelFrameIdx, 2), this._frameCallbackLength -= 2
        }, s._cancelCurrentAnimationFrame = function () {
            this._currentFrameCallbacks.splice(this._cancelFrameIdx, 2), this._currentFrameCallbacksLength -= 2
        }, s._cancelNextAnimationFrame = function () {
            this._nextFrameCallbacks.splice(this._cancelFrameIdx, 2), this._nextFrameCallbacksLength -= 2, 0 === this._nextFrameCallbacksLength && this.rafEmitter.cancel()
        }, t.exports = r
    }, {
        "./SingleCallRAFEmitter": 76
    }],
    75: [function (e, t, n) {
        "use strict";
        var i = e("./RAFInterface"),
            r = function () {
                this.events = {}
            },
            s = r.prototype;
        s.requestAnimationFrame = function (e) {
            return this.events[e] || (this.events[e] = new i(e)), this.events[e].requestAnimationFrame
        }, s.cancelAnimationFrame = function (e) {
            return this.events[e] || (this.events[e] = new i(e)), this.events[e].cancelAnimationFrame
        }, t.exports = new r
    }, {
        "./RAFInterface": 74
    }],
    76: [function (e, t, n) {
        "use strict";
        var i = e("./RAFEmitter"),
            r = function (e) {
                i.call(this, e)
            },
            s = r.prototype = Object.create(i.prototype);
        s._subscribe = function () {
            return this.executor.subscribe(this, !0)
        }, t.exports = r
    }, {
        "./RAFEmitter": 72
    }],
    77: [function (e, t, n) {
        "use strict";

        function i(e, t) {
            a.call(this), t = t || {}, this._fps = e || 0, this._delta = 0, this._currentFps = 0, this._rafEmitter = t.rafEmitter || new s, this._lastThrottledTime = 0, this._didEmitFrameData = !1, this._rafEmitterEvent = null, this._shouldDraw = !1, this._boundOnRAFEmitterUpdate = this._onRAFEmitterUpdate.bind(this), this._boundOnRAFEmitterDraw = this._onRAFEmitterDraw.bind(this), this._boundOnRAFEmitterStop = this._onRAFEmitterStop.bind(this), this._rafEmitter.on("update", this._boundOnRAFEmitterUpdate), this._rafEmitter.on("draw", this._boundOnRAFEmitterDraw), this._rafEmitter.on("stop", this._boundOnRAFEmitterStop)
        }
        var r, s = e("./RAFEmitter"),
            a = e("@marcom/ac-event-emitter-micro").EventEmitterMicro;
        r = i.prototype = Object.create(a.prototype), r.setFps = function (e) {
            return e !== this._fps && (this._fps = e, !0)
        }, r.getFps = function () {
            return this._fps
        }, r.run = function () {
            return this._rafEmitter.run()
        }, r.cancel = function () {
            return this._rafEmitter.cancel()
        }, r.willRun = function () {
            return this._rafEmitter.willRun()
        }, r.isRunning = function () {
            return this._rafEmitter.isRunning()
        }, r.destroy = function () {
            var e = this._rafEmitter.destroy();
            return a.prototype.destroy.call(this), this._rafEmitter = null, this._boundOnRAFEmitterUpdate = null, this._boundOnRAFEmitterDraw = null, this._boundOnRAFEmitterStop = null, this._rafEmitterEvent = null, e
        }, r._onRAFEmitterUpdate = function (e) {
            if (0 === this._lastThrottledTime && (this._lastThrottledTime = this._rafEmitter.executor.lastFrameTime), this._delta = e.time - this._lastThrottledTime, !this._fps) throw new TypeError("FPS is not defined.");
            return this._currentFps = 1e3 / this._delta, this._currentFps > this._fps ? void this._rafEmitter.run() : (this._rafEmitterEvent = Object.assign({}, e), this._rafEmitterEvent.delta = this._delta, this._rafEmitterEvent.fps = this._currentFps, this._lastThrottledTime = this._rafEmitterEvent.time, this._shouldDraw = !0, this._didEmitFrameData || (this.trigger("start", this._rafEmitterEvent), this._didEmitFrameData = !0), void this.trigger("update", this._rafEmitterEvent))
        }, r._onRAFEmitterDraw = function () {
            this._shouldDraw && (this._shouldDraw = !1, this.trigger("draw", this._rafEmitterEvent))
        }, r._onRAFEmitterStop = function () {
            this._lastThrottledTime = 0, this._didEmitFrameData = !1, this.trigger("stop", this._rafEmitterEvent)
        }, t.exports = i
    }, {
        "./RAFEmitter": 72,
        "@marcom/ac-event-emitter-micro": 35
    }],
    78: [function (e, t, n) {
        "use strict";
        var i = e("./RAFInterfaceController");
        t.exports = i.cancelAnimationFrame("draw")
    }, {
        "./RAFInterfaceController": 75
    }],
    79: [function (e, t, n) {
        "use strict";
        var i = e("./RAFInterfaceController");
        t.exports = i.cancelAnimationFrame("external")
    }, {
        "./RAFInterfaceController": 75
    }],
    80: [function (e, t, n) {
        "use strict";
        var i = e("./RAFInterfaceController");
        t.exports = i.cancelAnimationFrame("update")
    }, {
        "./RAFInterfaceController": 75
    }],
    81: [function (e, t, n) {
        "use strict";
        var i = e("./RAFInterfaceController");
        t.exports = i.requestAnimationFrame("draw")
    }, {
        "./RAFInterfaceController": 75
    }],
    82: [function (e, t, n) {
        "use strict";
        var i = e("./RAFInterfaceController");
        t.exports = i.requestAnimationFrame("external")
    }, {
        "./RAFInterfaceController": 75
    }],
    83: [function (e, t, n) {
        "use strict";
        var i = e("@marcom/ac-shared-instance").SharedInstance,
            r = e("../.release-info.js").majorVersionNumber,
            s = function () {
                this._currentID = 0
            };
        s.prototype.getNewID = function () {
            return this._currentID++, "raf:" + this._currentID
        }, t.exports = i.share("@marcom/ac-raf-emitter/sharedRAFEmitterIDGeneratorInstance", r, s)
    }, {
        "../.release-info.js": 70,
        "@marcom/ac-shared-instance": 88
    }],
    84: [function (e, t, n) {
        "use strict";
        var i = e("@marcom/ac-shared-instance").SharedInstance,
            r = e("../.release-info.js").majorVersionNumber,
            s = e("./RAFExecutor");
        t.exports = i.share("@marcom/ac-raf-emitter/sharedRAFExecutorInstance", r, s)
    }, {
        "../.release-info.js": 70,
        "./RAFExecutor": 73,
        "@marcom/ac-shared-instance": 88
    }],
    85: [function (e, t, n) {
        "use strict";
        var i = e("./RAFInterfaceController");
        t.exports = i.requestAnimationFrame("update")
    }, {
        "./RAFInterfaceController": 75
    }],
    86: [function (e, t, n) {
        "use strict";

        function i(e) {
            e = e || {}, this._reset(), this._willRun = !1, this._totalSubscribeCount = -1, this._requestAnimationFrame = window.requestAnimationFrame, this._cancelAnimationFrame = window.cancelAnimationFrame, this._boundOnAnimationFrame = this._onAnimationFrame.bind(this), this._boundOnExternalAnimationFrame = this._onExternalAnimationFrame.bind(this)
        }
        e("@marcom/ac-polyfills/performance/now");
        var r;
        r = i.prototype, r.subscribe = function (e, t) {
            return this._totalSubscribeCount++, this._nextFrameSubscribers[e.id] || (t ? this._nextFrameSubscribersOrder.unshift(e.id) : this._nextFrameSubscribersOrder.push(e.id), this._nextFrameSubscribers[e.id] = e, this._nextFrameSubscriberArrayLength++, this._nextFrameSubscriberCount++, this._run()), this._totalSubscribeCount
        }, r.unsubscribe = function (e) {
            return !!this._nextFrameSubscribers[e.id] && (this._nextFrameSubscribers[e.id] = null, this._nextFrameSubscriberCount--, 0 === this._nextFrameSubscriberCount && this._cancel(), !0)
        }, r.trigger = function (e, t) {
            var n;
            for (n = 0; n < this._subscriberArrayLength; n++) null !== this._subscribers[this._subscribersOrder[n]] && this._subscribers[this._subscribersOrder[n]]._didDestroy === !1 && this._subscribers[this._subscribersOrder[n]].trigger(e, t)
        }, r.destroy = function () {
            var e = this._cancel();
            return this._subscribers = null, this._subscribersOrder = null, this._nextFrameSubscribers = null, this._nextFrameSubscribersOrder = null, this._rafData = null, this._boundOnAnimationFrame = null, this._onExternalAnimationFrame = null, e
        }, r.useExternalAnimationFrame = function (e) {
            if ("boolean" == typeof e) {
                var t = this._isUsingExternalAnimationFrame;
                return e && this._animationFrame && (this._cancelAnimationFrame.call(window, this._animationFrame), this._animationFrame = null), !this._willRun || e || this._animationFrame || (this._animationFrame = this._requestAnimationFrame.call(window, this._boundOnAnimationFrame)), this._isUsingExternalAnimationFrame = e, e ? this._boundOnExternalAnimationFrame : t || !1
            }
        }, r._run = function () {
            if (!this._willRun) return this._willRun = !0, 0 === this.lastFrameTime && (this.lastFrameTime = performance.now()), this._animationFrameActive = !0, this._isUsingExternalAnimationFrame || (this._animationFrame = this._requestAnimationFrame.call(window, this._boundOnAnimationFrame)), !0
        }, r._cancel = function () {
            var e = !1;
            return this._animationFrameActive && (this._animationFrame && (this._cancelAnimationFrame.call(window, this._animationFrame), this._animationFrame = null), this._animationFrameActive = !1, this._willRun = !1, e = !0), this._isRunning || this._reset(), e
        }, r._onSubscribersAnimationFrameStart = function (e) {
            var t;
            for (t = 0; t < this._subscriberArrayLength; t++) null !== this._subscribers[this._subscribersOrder[t]] && this._subscribers[this._subscribersOrder[t]]._didDestroy === !1 && this._subscribers[this._subscribersOrder[t]]._onAnimationFrameStart(e)
        }, r._onSubscribersAnimationFrameEnd = function (e) {
            var t;
            for (t = 0; t < this._subscriberArrayLength; t++) null !== this._subscribers[this._subscribersOrder[t]] && this._subscribers[this._subscribersOrder[t]]._didDestroy === !1 && this._subscribers[this._subscribersOrder[t]]._onAnimationFrameEnd(e);
        }, r._onAnimationFrame = function (e) {
            this._subscribers = this._nextFrameSubscribers, this._subscribersOrder = this._nextFrameSubscribersOrder, this._subscriberArrayLength = this._nextFrameSubscriberArrayLength, this._subscriberCount = this._nextFrameSubscriberCount, this._nextFrameSubscribers = {}, this._nextFrameSubscribersOrder = [], this._nextFrameSubscriberArrayLength = 0, this._nextFrameSubscriberCount = 0, this._isRunning = !0, this._willRun = !1, this._didRequestNextRAF = !1, this._rafData.delta = e - this.lastFrameTime, this.lastFrameTime = e, this._rafData.fps = 0, this._rafData.delta >= 1e3 && (this._rafData.delta = 0), 0 !== this._rafData.delta && (this._rafData.fps = 1e3 / this._rafData.delta), this._rafData.time = e, this._rafData.naturalFps = this._rafData.fps, this._rafData.timeNow = Date.now(), this._onSubscribersAnimationFrameStart(this._rafData), this.trigger("update", this._rafData), this.trigger("external", this._rafData), this.trigger("draw", this._rafData), this._onSubscribersAnimationFrameEnd(this._rafData), this._willRun || this._reset()
        }, r._onExternalAnimationFrame = function (e) {
            this._isUsingExternalAnimationFrame && this._onAnimationFrame(e)
        }, r._reset = function () {
            this._rafData = {
                time: 0,
                delta: 0,
                fps: 0,
                naturalFps: 0,
                timeNow: 0
            }, this._subscribers = {}, this._subscribersOrder = [], this._subscriberArrayLength = 0, this._subscriberCount = 0, this._nextFrameSubscribers = {}, this._nextFrameSubscribersOrder = [], this._nextFrameSubscriberArrayLength = 0, this._nextFrameSubscriberCount = 0, this._didEmitFrameData = !1, this._animationFrame = null, this._animationFrameActive = !1, this._isRunning = !1, this._shouldReset = !1, this.lastFrameTime = 0
        }, t.exports = i
    }, {
        "@marcom/ac-polyfills/performance/now": void 0
    }],
    87: [function (e, t, n) {
        "use strict";
        var i = e("@marcom/ac-shared-instance").SharedInstance,
            r = "ac-raf-executor:sharedRAFExecutorInstance",
            s = "2.0.1",
            a = e("./RAFExecutor");
        t.exports = i.share(r, s, a)
    }, {
        "./RAFExecutor": 86,
        "@marcom/ac-shared-instance": 88
    }],
    88: [function (e, t, n) {
        "use strict";
        t.exports = {
            SharedInstance: e("./ac-shared-instance/SharedInstance")
        }
    }, {
        "./ac-shared-instance/SharedInstance": 89
    }],
    89: [function (e, t, n) {
        "use strict";
        var i = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (e) {
                return typeof e
            } : function (e) {
                return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e
            },
            r = window,
            s = "AC",
            a = "SharedInstance",
            o = r[s],
            l = function () {
                var e = {};
                return {
                    get: function (t, n) {
                        var i = null;
                        return e[t] && e[t][n] && (i = e[t][n]), i
                    },
                    set: function (t, n, i) {
                        return e[t] || (e[t] = {}), "function" == typeof i ? e[t][n] = new i : e[t][n] = i, e[t][n]
                    },
                    share: function (e, t, n) {
                        var i = this.get(e, t);
                        return i || (i = this.set(e, t, n)), i
                    },
                    remove: function (t, n) {
                        var r = "undefined" == typeof n ? "undefined" : i(n);
                        if ("string" === r || "number" === r) {
                            if (!e[t] || !e[t][n]) return;
                            return void(e[t][n] = null)
                        }
                        e[t] && (e[t] = null)
                    }
                }
            }();
        o || (o = r[s] = {}), o[a] || (o[a] = l), t.exports = o[a]
    }, {}],
    90: [function (e, t, n) {
        "use strict";
        t.exports = {
            version: "3.0.2",
            major: "3.x",
            majorMinor: "3.0"
        }
    }, {}],
    91: [function (e, t, n) {
        "use strict";

        function i(e, t) {
            if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }

        function r(e, t) {
            if (!e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
            return !t || "object" != typeof t && "function" != typeof t ? e : t
        }

        function s(e, t) {
            if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function, not " + typeof t);
            e.prototype = Object.create(t && t.prototype, {
                constructor: {
                    value: e,
                    enumerable: !1,
                    writable: !0,
                    configurable: !0
                }
            }), t && (Object.setPrototypeOf ? Object.setPrototypeOf(e, t) : e.__proto__ = t)
        }
        var a = function () {
                function e(e, t) {
                    for (var n = 0; n < t.length; n++) {
                        var i = t[n];
                        i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i)
                    }
                }
                return function (t, n, i) {
                    return n && e(t.prototype, n), i && e(t, i), t
                }
            }(),
            o = e("@marcom/ac-event-emitter-micro").EventEmitterMicro,
            l = e("./Model/AnimSystemModel"),
            c = e("./Keyframes/Keyframe"),
            u = e("./Keyframes/KeyframeCSSClass"),
            h = e("./Keyframes/KeyframeDiscreteEvent"),
            m = e("./ScrollGroup"),
            d = e("./TimeGroup"),
            f = e("./.release-info"),
            p = {
                update: e("@marcom/ac-raf-emitter/update"),
                cancelUpdate: e("@marcom/ac-raf-emitter/cancelUpdate"),
                external: e("@marcom/ac-raf-emitter/external"),
                draw: e("@marcom/ac-raf-emitter/draw")
            },
            v = null,
            y = function (e) {
                function t() {
                    i(this, t);
                    var e = r(this, (t.__proto__ || Object.getPrototypeOf(t)).call(this));
                    if (v) throw "You cannot create multiple AnimSystems. You probably want to create multiple groups instead. You can have unlimited groups on a page";
                    return v = e, e.groups = [], e.scrollSystems = [], e.timeSystems = [], e._forceUpdateRAFId = -1, e._initialized = !1, e.model = l, e.version = f.version, e.onScroll = e.onScroll.bind(e), e.onResizedDebounced = e.onResizedDebounced.bind(e), e.onResizeImmediate = e.onResizeImmediate.bind(e), e
                }
                return s(t, e), a(t, [{
                    key: "initialize",
                    value: function () {
                        this._initialized || (this._initialized = !0, this.timeSystems = [], this.scrollSystems = [], this.groups = [], this.setupEvents(), this.initializeResizeFilter(), this.initializeModel(), this.createDOMGroups(), this.createDOMKeyframes())
                    }
                }, {
                    key: "remove",
                    value: function () {
                        var e = this;
                        return Promise.all(this.groups.map(function (e) {
                            return e.remove()
                        })).then(function () {
                            e.groups = null, e.scrollSystems = null, e.timeSystems = null, window.clearTimeout(l.RESIZE_TIMEOUT), window.removeEventListener("scroll", e.onScroll), window.removeEventListener("resize", e.onResizeImmediate), e._events = {}, e._initialized = !1
                        })
                    }
                }, {
                    key: "destroy",
                    value: function () {
                        this.remove()
                    }
                }, {
                    key: "createTimeGroup",
                    value: function (e) {
                        var t = new d(e, this);
                        return this.groups.push(t), this.timeSystems.push(t), this.trigger(l.EVENTS.ON_GROUP_CREATED, t), t
                    }
                }, {
                    key: "createScrollGroup",
                    value: function (e) {
                        if (!e) throw "AnimSystem scroll based groups must supply an HTMLElement";
                        var t = new m(e, this);
                        return this.groups.push(t), this.scrollSystems.push(t), this.trigger(l.EVENTS.ON_GROUP_CREATED, t), t
                    }
                }, {
                    key: "removeGroup",
                    value: function (e) {
                        var t = this;
                        return Promise.all(e.keyframeControllers.map(function (t) {
                            return e.removeKeyframeController(t)
                        })).then(function () {
                            return new Promise(function (n) {
                                var i = t.groups.indexOf(e);
                                i !== -1 && t.groups.splice(i, 1), i = t.scrollSystems.indexOf(e), i !== -1 && t.scrollSystems.splice(i, 1), i = t.timeSystems.indexOf(e), i !== -1 && t.timeSystems.splice(i, 1), e.destroy(), n()
                            })
                        })
                    }
                }, {
                    key: "createDOMGroups",
                    value: function () {
                        var e = this;
                        document.body.setAttribute("data-anim-scroll-group", "body"), document.querySelectorAll("[data-anim-scroll-group]").forEach(function (t) {
                            return e.createScrollGroup(t)
                        }), document.querySelectorAll("[data-anim-time-group]").forEach(function (t) {
                            return e.createTimeGroup(t)
                        }), this.trigger(l.EVENTS.ON_DOM_GROUPS_CREATED, this.groups)
                    }
                }, {
                    key: "createDOMKeyframes",
                    value: function () {
                        var e = this,
                            t = [];
                        [c.DATA_ATTRIBUTE, u.DATA_ATTRIBUTE, h.DATA_ATTRIBUTE].forEach(function (e) {
                            for (var n = 0; n < 12; n++) t.push(e + (0 === n ? "" : "-" + (n - 1)))
                        });
                        for (var n = 0; n < t.length; n++)
                            for (var i = t[n], r = document.querySelectorAll("[" + i + "]"), s = 0; s < r.length; s++) {
                                var a = r[s],
                                    o = JSON.parse(a.getAttribute(i));
                                this.addKeyframe(a, o)
                            }
                        p.update(function () {
                            e.groups.forEach(function (e) {
                                return e.onKeyframesDirty({
                                    silent: !0
                                })
                            }), e.groups.forEach(function (e) {
                                return e.trigger(l.EVENTS.ON_DOM_KEYFRAMES_CREATED, e)
                            }), e.trigger(l.EVENTS.ON_DOM_KEYFRAMES_CREATED, e), e.groups.forEach(function (e) {
                                e.forceUpdate({
                                    waitForNextUpdate: !1,
                                    silent: !0
                                }), e.reconcile()
                            }), e.onScroll()
                        }, !0)
                    }
                }, {
                    key: "initializeResizeFilter",
                    value: function () {
                        if (!l.cssDimensionsTracker) {
                            var e = document.querySelector(".cssDimensionsTracker") || document.createElement("div");
                            e.setAttribute("cssDimensionsTracker", "true"), e.style.position = "fixed", e.style.top = "0", e.style.width = "100%", e.style.height = "100vh", e.style.pointerEvents = "none", e.style.visibility = "hidden", e.style.zIndex = "-1", document.documentElement.appendChild(e), l.cssDimensionsTracker = e
                        }
                    }
                }, {
                    key: "initializeModel",
                    value: function () {
                        l.pageMetrics.windowHeight = l.cssDimensionsTracker.clientHeight, l.pageMetrics.windowWidth = l.cssDimensionsTracker.clientWidth, l.pageMetrics.scrollY = window.scrollY || window.pageYOffset, l.pageMetrics.scrollX = window.scrollX || window.pageXOffset, l.pageMetrics.breakpoint = l.getBreakpoint();
                        var e = document.documentElement.getBoundingClientRect();
                        l.pageMetrics.documentOffsetX = e.left + l.pageMetrics.scrollX, l.pageMetrics.documentOffsetY = e.top + l.pageMetrics.scrollY
                    }
                }, {
                    key: "setupEvents",
                    value: function () {
                        window.removeEventListener("scroll", this.onScroll), window.addEventListener("scroll", this.onScroll), window.removeEventListener("resize", this.onResizeImmediate), window.addEventListener("resize", this.onResizeImmediate)
                    }
                }, {
                    key: "onScroll",
                    value: function () {
                        l.pageMetrics.scrollY = window.scrollY || window.pageYOffset, l.pageMetrics.scrollX = window.scrollX || window.pageXOffset;
                        for (var e = 0, t = this.scrollSystems.length; e < t; e++) this.scrollSystems[e]._onScroll();
                        this.trigger(l.PageEvents.ON_SCROLL, l.pageMetrics)
                    }
                }, {
                    key: "onResizeImmediate",
                    value: function () {
                        var e = l.cssDimensionsTracker.clientWidth,
                            t = l.cssDimensionsTracker.clientHeight;
                        if (e !== l.pageMetrics.windowWidth || t !== l.pageMetrics.windowHeight) {
                            l.pageMetrics.windowWidth = e, l.pageMetrics.windowHeight = t, l.pageMetrics.scrollY = window.scrollY || window.pageYOffset, l.pageMetrics.scrollX = window.scrollX || window.pageXOffset;
                            var n = document.documentElement.getBoundingClientRect();
                            l.pageMetrics.documentOffsetX = n.left + l.pageMetrics.scrollX, l.pageMetrics.documentOffsetY = n.top + l.pageMetrics.scrollY, window.clearTimeout(l.RESIZE_TIMEOUT), l.RESIZE_TIMEOUT = window.setTimeout(this.onResizedDebounced, 250), this.trigger(l.PageEvents.ON_RESIZE_IMMEDIATE, l.pageMetrics)
                        }
                    }
                }, {
                    key: "onResizedDebounced",
                    value: function () {
                        var e = this;
                        p.update(function () {
                            var t = l.pageMetrics.breakpoint,
                                n = l.getBreakpoint(),
                                i = n !== t;
                            if (i) {
                                l.pageMetrics.previousBreakpoint = t, l.pageMetrics.breakpoint = n;
                                for (var r = 0, s = e.groups.length; r < s; r++) e.groups[r]._onBreakpointChange();
                                e.trigger(l.PageEvents.ON_BREAKPOINT_CHANGE, l.pageMetrics)
                            }
                            for (var a = 0, o = e.groups.length; a < o; a++) e.groups[a].forceUpdate({
                                waitForNextUpdate: !1
                            });
                            e.trigger(l.PageEvents.ON_RESIZE_DEBOUNCED, l.pageMetrics)
                        }, !0)
                    }
                }, {
                    key: "forceUpdate",
                    value: function () {
                        var e = this,
                            t = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : {},
                            n = t.waitForNextUpdate,
                            i = void 0 === n || n,
                            r = t.silent,
                            s = void 0 !== r && r;
                        this._forceUpdateRAFId !== -1 && p.cancelUpdate(this._forceUpdateRAFId);
                        var a = function () {
                            for (var t = 0, n = e.groups.length; t < n; t++) {
                                var i = e.groups[t];
                                i.forceUpdate({
                                    waitForNextUpdate: !1,
                                    silent: s
                                })
                            }
                            return -1
                        };
                        this._forceUpdateRAFId = i ? p.update(a, !0) : a()
                    }
                }, {
                    key: "addKeyframe",
                    value: function (e, t) {
                        var n = this.getGroupForTarget(e);
                        return n = n || this.getGroupForTarget(document.body), n.addKeyframe(e, t)
                    }
                }, {
                    key: "getGroupForTarget",
                    value: function (e) {
                        if (e._animInfo && e._animInfo.group) return e._animInfo.group;
                        for (var t = e; t;) {
                            if (t._animInfo && t._animInfo.isGroup) return t._animInfo.group;
                            t = t.parentElement
                        }
                    }
                }, {
                    key: "getControllerForTarget",
                    value: function (e) {
                        return e._animInfo && e._animInfo.controller ? e._animInfo.controller : null
                    }
                }]), t
            }(o);
        t.exports = window.AC.SharedInstance.share("AnimSystem", f.major, y)
    }, {
        "./.release-info": 90,
        "./Keyframes/Keyframe": 92,
        "./Keyframes/KeyframeCSSClass": 93,
        "./Keyframes/KeyframeDiscreteEvent": 95,
        "./Model/AnimSystemModel": 96,
        "./ScrollGroup": 105,
        "./TimeGroup": 106,
        "@marcom/ac-event-emitter-micro": 35,
        "@marcom/ac-raf-emitter/cancelUpdate": 80,
        "@marcom/ac-raf-emitter/draw": 81,
        "@marcom/ac-raf-emitter/external": 82,
        "@marcom/ac-raf-emitter/update": 85
    }],
    92: [function (e, t, n) {
        "use strict";

        function i(e, t) {
            if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }
        var r = function () {
                function e(e, t) {
                    for (var n = 0; n < t.length; n++) {
                        var i = t[n];
                        i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i)
                    }
                }
                return function (t, n, i) {
                    return n && e(t.prototype, n), i && e(t, i), t
                }
            }(),
            s = e("../Model/AnimSystemModel"),
            a = e("@marcom/sm-math-utils"),
            o = e("../Model/EasingFunctions"),
            l = e("../Model/UnitBezier"),
            c = e("../utils/arrayToObject"),
            u = function () {
                function e(t, n) {
                    i(this, e), this.controller = t, this.anchors = [], this.jsonProps = n, this.ease = t.group.defaultEase, this.easeFunctionString = s.KeyframeDefaults.easeFunctionString, this.easeFunction = o[this.easeFunctionString], this.start = 0, this.end = 0, this.localT = 0, this.curvedT = 0, this.id = 0, this.event = "", this.needsEventDispatch = !1, this.snapAtCreation = !1, this.isEnabled = !1, this.animValues = {}, this.breakpointMask = "SMLX", this.disabledWhen = [], this.keyframeType = s.KeyframeTypes.Interpolation, this.hold = !1
                }
                return r(e, [{
                    key: "destroy",
                    value: function () {
                        this.controller = null, this.anchors = null, this.jsonProps = null, this.easeFunction = null, this.animValues = null
                    }
                }, {
                    key: "remove",
                    value: function () {
                        return this.controller.removeKeyframe(this)
                    }
                }, {
                    key: "parseOptions",
                    value: function (e) {
                        var t = this;
                        if (this.jsonProps = e, e.relativeTo && console.error("KeyframeError: relativeTo has been removed. Use 'anchors' property instead. Found 'relativeTo':\"" + e.relativeTo + '"'), "" !== e.anchors && e.anchors) {
                            var n = Array.isArray(e.anchors) ? e.anchors : [e.anchors];
                            this.anchors = [], n.forEach(function (e) {
                                var n = e instanceof Element ? e : t.controller.group.element.querySelector(e) || document.querySelector(e);
                                return null === n ? void console.warn("Keyframe for", t.controller.friendlyName, " `anchor` failed to find '" + e + "' via querySelector in group.element or document") : (t.anchors.push(n), void t.controller.group.metrics.add(n))
                            })
                        } else this.anchors = [], e.anchors = [];
                        if (e.ease ? this.ease = parseFloat(e.ease) : e.ease = this.ease, e.hasOwnProperty("snapAtCreation") ? this.snapAtCreation = e.snapAtCreation : e.snapAtCreation = this.snapAtCreation, e.easeFunction ? this.easeFunction = e.easeFunction : e.easeFunction = this.easeFunctionString, e.breakpointMask ? this.breakpointMask = e.breakpointMask : e.breakpointMask = this.breakpointMask, e.disabledWhen ? this.disabledWhen = Array.isArray(e.disabledWhen) ? e.disabledWhen : [e.disabledWhen] : e.disabledWhen = this.disabledWhen, e.hasOwnProperty("hold") ? this.hold = e.hold : e.hold = this.hold, this.easeFunction = o[e.easeFunction], !o.hasOwnProperty(e.easeFunction)) {
                            var i = l.fromCSSString(e.easeFunction);
                            i ? this.easeFunction = i : console.error("Keyframe parseOptions cannot find EasingFunction named '" + e.easingFunction + "'")
                        }
                        for (var r in e)
                            if (s.KeyframeJSONReservedWords.indexOf(r) === -1) {
                                var a = e[r];
                                if (Array.isArray(a)) {
                                    if (this.animValues[r] = this.controller.group.expressionParser.parseArray(this, a), void 0 === this.controller.tweenProps[r] || !this.controller._ownerIsElement) {
                                        var c = 0;
                                        this.controller._ownerIsElement || (c = this.controller.element[r]);
                                        var u = new s.TargetValue(c, s.KeyframeDefaults.epsilon, this.snapAtCreation);
                                        this.controller.tweenProps[r] = u
                                    }
                                    var h = this.controller.tweenProps[r];
                                    if (e.epsilon) h.epsilon = e.epsilon;
                                    else {
                                        var m = Math.abs(this.animValues[r][0] - this.animValues[r][1]),
                                            d = Math.min(.001 * m, h.epsilon, s.KeyframeDefaults.epsilon);
                                        h.epsilon = Math.max(d, 1e-4)
                                    }
                                }
                            } this.keyframeType = this.hold ? s.KeyframeTypes.InterpolationForward : s.KeyframeTypes.Interpolation, e.event && (this.event = e.event)
                    }
                }, {
                    key: "overwriteProps",
                    value: function (e) {
                        this.animValues = {};
                        var t = Object.assign({}, this.jsonProps, e);
                        this.controller.updateKeyframe(this, t)
                    }
                }, {
                    key: "updateLocalProgress",
                    value: function (e) {
                        if (this.start === this.end || e > this.end) return this.localT = 1, void(this.curvedT = this.easeFunction(this.localT));
                        var t = (e - this.start) / (this.end - this.start),
                            n = this.hold ? this.localT : 0;
                        this.localT = a.clamp(t, n, 1), this.curvedT = this.easeFunction(this.localT)
                    }
                }, {
                    key: "reconcile",
                    value: function (e) {
                        var t = this.animValues[e],
                            n = this.controller.tweenProps[e];
                        n.initialValue = t[0], n.target = t[0] + this.curvedT * (t[1] - t[0]), n.current !== n.target && (n.current = n.target, this.needsEventDispatch || (this.needsEventDispatch = !0, this.controller.keyframesRequiringDispatch.push(this)))
                    }
                }, {
                    key: "reset",
                    value: function (e) {
                        this.localT = e || 0;
                        var t = this.ease;
                        this.ease = 1;
                        for (var n in this.animValues) this.reconcile(n);
                        this.ease = t
                    }
                }, {
                    key: "onDOMRead",
                    value: function (e) {
                        var t = this.animValues[e],
                            n = this.controller.tweenProps[e];
                        n.target = t[0] + this.curvedT * (t[1] - t[0]);
                        var i = n.current;
                        n.current += (n.target - n.current) * this.ease;
                        var r = n.current - n.target;
                        r < n.epsilon && r > -n.epsilon && (n.current = n.target, r = 0), "" === this.event || this.needsEventDispatch || (r > n.epsilon || r < -n.epsilon || 0 === r && i !== n.current) && (this.needsEventDispatch = !0, this.controller.keyframesRequiringDispatch.push(this))
                    }
                }, {
                    key: "isInRange",
                    value: function (e) {
                        return e >= this.start && e <= this.end
                    }
                }, {
                    key: "setEnabled",
                    value: function (e) {
                        e = e || c(Array.from(document.documentElement.classList));
                        var t = this.breakpointMask.indexOf(s.pageMetrics.breakpoint) !== -1,
                            n = !1;
                        return this.disabledWhen.length > 0 && (n = this.disabledWhen.some(function (t) {
                            return "undefined" != typeof e[t]
                        })), this.isEnabled = t && !n, this.isEnabled
                    }
                }, {
                    key: "updateAnimationConstraints",
                    value: function () {
                        this.start = this.controller.group.timeParser.parse(this, this.jsonProps.start), this.end = this.controller.group.timeParser.parse(this, this.jsonProps.end), this.updateAnimatedValueConstraints()
                    }
                }, {
                    key: "updateAnimatedValueConstraints",
                    value: function () {
                        for (var e in this.animValues) {
                            var t = this.jsonProps[e];
                            this.animValues[e] = this.controller.group.expressionParser.parseArray(this, t)
                        }
                    }
                }]), e
            }();
        u.DATA_ATTRIBUTE = "data-anim-tween", t.exports = u
    }, {
        "../Model/AnimSystemModel": 96,
        "../Model/EasingFunctions": 97,
        "../Model/UnitBezier": 101,
        "../utils/arrayToObject": 107,
        "@marcom/sm-math-utils": 113
    }],
    93: [function (e, t, n) {
        "use strict";

        function i(e, t) {
            if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }

        function r(e, t) {
            if (!e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
            return !t || "object" != typeof t && "function" != typeof t ? e : t
        }

        function s(e, t) {
            if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function, not " + typeof t);
            e.prototype = Object.create(t && t.prototype, {
                constructor: {
                    value: e,
                    enumerable: !1,
                    writable: !0,
                    configurable: !0
                }
            }), t && (Object.setPrototypeOf ? Object.setPrototypeOf(e, t) : e.__proto__ = t)
        }
        var a = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (e) {
                return typeof e
            } : function (e) {
                return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e
            },
            o = function () {
                function e(e, t) {
                    for (var n = 0; n < t.length; n++) {
                        var i = t[n];
                        i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i)
                    }
                }
                return function (t, n, i) {
                    return n && e(t.prototype, n), i && e(t, i), t
                }
            }(),
            l = e("./Keyframe"),
            c = e("../Model/AnimSystemModel.js"),
            u = function (e) {
                function t(e, n) {
                    i(this, t);
                    var s = r(this, (t.__proto__ || Object.getPrototypeOf(t)).call(this, e, n));
                    return s.keyframeType = c.KeyframeTypes.CSSClass, s._triggerType = t.TRIGGER_TYPE_CSS_CLASS, s.cssClass = "", s.friendlyName = "", s.style = {
                        on: null,
                        off: null
                    }, s.toggle = !1, s.isApplied = !1, s
                }
                return s(t, e), o(t, [{
                    key: "parseOptions",
                    value: function (e) {
                        if (!this.controller._ownerIsElement) throw new TypeError("CSS Keyframes cannot be applied to JS Objects");
                        if (e.x = void 0, e.y = void 0, e.scale = void 0, e.scaleX = void 0, e.scaleY = void 0, e.rotation = void 0, e.opacity = void 0, e.hold = void 0, void 0 !== e.toggle && (this.toggle = e.toggle), void 0 !== e.cssClass) this._triggerType = t.TRIGGER_TYPE_CSS_CLASS, this.cssClass = e.cssClass, this.friendlyName = "." + this.cssClass, void 0 === this.controller.tweenProps.targetClasses && (this.controller.tweenProps.targetClasses = {
                            add: [],
                            remove: []
                        });
                        else {
                            if (void 0 === e.style || !this.isValidStyleProperty(e.style)) throw new TypeError("KeyframeCSSClass no 'cssClass` property found. If using `style` property its also missing or invalid");
                            if (this._triggerType = t.TRIGGER_TYPE_STYLE_PROPERTY, this.style = e.style, this.friendlyName = "style", this.toggle = void 0 !== this.style.off || this.toggle, this.toggle && void 0 === this.style.off) {
                                this.style.off = {};
                                for (var n in this.style.on) this.style.off[n] = ""
                            }
                            void 0 === this.controller.tweenProps.targetStyles && (this.controller.tweenProps.targetStyles = {})
                        }
                        if (void 0 === e.end && (e.end = e.start), e.toggle = this.toggle, this._triggerType === t.TRIGGER_TYPE_CSS_CLASS) this.isApplied = this.controller.element.classList.contains(this.cssClass);
                        else {
                            var i = getComputedStyle(this.controller.element);
                            this.isApplied = !0;
                            for (var r in this.style.on)
                                if (i[r] !== this.style.on[r]) {
                                    this.isApplied = !1;
                                    break
                                }
                        }
                        l.prototype.parseOptions.call(this, e), this.animValues[this.friendlyName] = [0, 0], void 0 === this.controller.tweenProps[this.friendlyName] && (this.controller.tweenProps[this.friendlyName] = new c.TargetValue(0, 1, (!1))), this.keyframeType = c.KeyframeTypes.CSSClass
                    }
                }, {
                    key: "updateLocalProgress",
                    value: function (e) {
                        this.isApplied && !this.toggle || (this.start !== this.end ? !this.isApplied && e >= this.start && e <= this.end ? this._apply() : this.isApplied && this.toggle && (e < this.start || e > this.end) && this._unapply() : !this.isApplied && e >= this.start ? this._apply() : this.isApplied && this.toggle && e < this.start && this._unapply())
                    }
                }, {
                    key: "_apply",
                    value: function () {
                        if (this._triggerType === t.TRIGGER_TYPE_CSS_CLASS) this.controller.tweenProps.targetClasses.add.push(this.cssClass), this.controller.needsClassUpdate = !0;
                        else {
                            for (var e in this.style.on) this.controller.tweenProps.targetStyles[e] = this.style.on[e];
                            this.controller.needsStyleUpdate = !0
                        }
                        this.isApplied = !0
                    }
                }, {
                    key: "_unapply",
                    value: function () {
                        if (this._triggerType === t.TRIGGER_TYPE_CSS_CLASS) this.controller.tweenProps.targetClasses.remove.push(this.cssClass), this.controller.needsClassUpdate = !0;
                        else {
                            for (var e in this.style.off) this.controller.tweenProps.targetStyles[e] = this.style.off[e];
                            this.controller.needsStyleUpdate = !0
                        }
                        this.isApplied = !1
                    }
                }, {
                    key: "isValidStyleProperty",
                    value: function (e) {
                        if (!e.hasOwnProperty("on")) return !1;
                        if ("object" !== a(e.on)) throw new TypeError("KeyframeCSSClass `style` property should be in the form of: {on:{visibility:hidden, otherProperty: value}}");
                        if (this.toggle && e.hasOwnProperty("off") && "object" !== a(e.off)) throw new TypeError("KeyframeCSSClass `style` property should be in the form of: {on:{visibility:hidden, otherProperty: value}}");
                        return !0
                    }
                }, {
                    key: "reconcile",
                    value: function (e, t) {}
                }, {
                    key: "onDOMRead",
                    value: function (e, t) {}
                }, {
                    key: "updateAnimatedValueConstraints",
                    value: function () {}
                }]), t
            }(l);
        u.TRIGGER_TYPE_CSS_CLASS = 0, u.TRIGGER_TYPE_STYLE_PROPERTY = 1, u.DATA_ATTRIBUTE = "data-anim-classname", t.exports = u
    }, {
        "../Model/AnimSystemModel.js": 96,
        "./Keyframe": 92
    }],
    94: [function (e, t, n) {
        "use strict";

        function i(e, t) {
            if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }

        function r(e, t) {
            if (!e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
            return !t || "object" != typeof t && "function" != typeof t ? e : t
        }

        function s(e, t) {
            if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function, not " + typeof t);
            e.prototype = Object.create(t && t.prototype, {
                constructor: {
                    value: e,
                    enumerable: !1,
                    writable: !0,
                    configurable: !0
                }
            }), t && (Object.setPrototypeOf ? Object.setPrototypeOf(e, t) : e.__proto__ = t)
        }
        var a = function () {
                function e(e, t) {
                    for (var n = 0; n < t.length; n++) {
                        var i = t[n];
                        i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i)
                    }
                }
                return function (t, n, i) {
                    return n && e(t.prototype, n), i && e(t, i), t
                }
            }(),
            o = function b(e, t, n) {
                null === e && (e = Function.prototype);
                var i = Object.getOwnPropertyDescriptor(e, t);
                if (void 0 === i) {
                    var r = Object.getPrototypeOf(e);
                    return null === r ? void 0 : b(r, t, n)
                }
                if ("value" in i) return i.value;
                var s = i.get;
                if (void 0 !== s) return s.call(n)
            },
            l = e("../Model/AnimSystemModel"),
            c = (e("./Keyframe"), e("./KeyframeCSSClass")),
            u = e("../Model/InferKeyframeFromProps"),
            h = e("../utils/arrayToObject"),
            m = e("../Model/UUID"),
            d = e("@marcom/ac-event-emitter-micro").EventEmitterMicro,
            f = e("@marcom/decompose-css-transform"),
            p = {
                update: e("@marcom/ac-raf-emitter/update"),
                external: e("@marcom/ac-raf-emitter/external"),
                draw: e("@marcom/ac-raf-emitter/draw")
            },
            v = Math.PI / 180,
            y = {
                create: e("gl-mat4/create"),
                rotateX: e("gl-mat4/rotateX"),
                rotateY: e("gl-mat4/rotateY"),
                rotateZ: e("gl-mat4/rotateZ"),
                scale: e("gl-mat4/scale")
            },
            _ = function (e) {
                function t(e, n) {
                    i(this, t);
                    var s = r(this, (t.__proto__ || Object.getPrototypeOf(t)).call(this));
                    return s.uuid = m(), s.group = e, s.element = n, s._ownerIsElement = s.element instanceof Element, s._ownerIsElement ? s.friendlyName = s.element.tagName + "." + Array.from(s.element.classList).join(".") : s.friendlyName = s.element.friendlyName || s.uuid, s.element._animInfo = s.element._animInfo || new l.AnimInfo(e, s), s.element._animInfo.controller = s, s.element._animInfo.group = s.group, s.element._animInfo.controllers.push(s), s.tweenProps = new l.TweenProps, s.eventObject = new l.EventObject(s), s.needsStyleUpdate = !1, s.needsClassUpdate = !1, s.elementMetrics = s.group.metrics.add(s.element), s.attributes = [], s.keyframes = {}, s._allKeyframes = [], s._activeKeyframes = [], s.keyframesRequiringDispatch = [], s.updateCachedValuesFromElement(), s.boundsMin = 0, s.boundsMax = 0, s.mat2d = new Float32Array(6), s.mat4 = y.create(), s.needsWrite = !0, s.onDOMWriteImp = s._ownerIsElement ? s.onDOMWriteForElement : s.onDOMWriteForObject, s
                }
                return s(t, e), a(t, [{
                    key: "destroy",
                    value: function () {
                        if (this.element._animInfo) {
                            this.element._animInfo.controller === this && (this.element._animInfo.controller = null);
                            var e = this.element._animInfo.controllers.indexOf(this);
                            e !== -1 && this.element._animInfo.controllers.splice(e, 1), 0 === this.element._animInfo.controllers.length ? this.element._animInfo = null : (this.element._animInfo.controller = this.element._animInfo.controllers[this.element._animInfo.controllers.length - 1], this.element._animInfo.group = this.element._animInfo.controller.group)
                        }
                        this.eventObject.controller = null, this.eventObject.element = null, this.eventObject.keyframe = null, this.eventObject.tweenProps = null, this.eventObject = null, this.elementMetrics = null, this.group = null, this.keyframesRequiringDispatch = null;
                        for (var n = 0; n < this._allKeyframes.length; n++) this._allKeyframes[n].destroy();
                        this._allKeyframes = null, this._activeKeyframes = null, this.attributes = null, this.keyframes = null, this.element = null, this.tweenProps = null, o(t.prototype.__proto__ || Object.getPrototypeOf(t.prototype), "destroy", this).call(this)
                    }
                }, {
                    key: "remove",
                    value: function () {
                        return this.group.removeKeyframeController(this)
                    }
                }, {
                    key: "updateCachedValuesFromElement",
                    value: function () {
                        if (this._ownerIsElement) {
                            var e = getComputedStyle(this.element),
                                t = f(this.element, !0),
                                n = l.KeyframeDefaults.epsilon,
                                i = !1;
                            this.tweenProps.x = new l.TargetValue(t.translation[0], n, i), this.tweenProps.y = new l.TargetValue(t.translation[1], n, i), this.tweenProps.z = new l.TargetValue(t.translation[2], n, i), this.tweenProps.rotation = new l.TargetValue(t.eulerRotation[2], n, i), this.tweenProps.rotationX = new l.TargetValue(t.eulerRotation[0], n, i), this.tweenProps.rotationY = new l.TargetValue(t.eulerRotation[1], n, i), this.tweenProps.rotationZ = new l.TargetValue(t.eulerRotation[2], n, i), this.tweenProps.scale = new l.TargetValue(t.scale[0], n, i), this.tweenProps.scaleX = new l.TargetValue(t.scale[0], n, i), this.tweenProps.scaleY = new l.TargetValue(t.scale[1], n, i), this.tweenProps.opacity = new l.TargetValue(parseFloat(e.opacity), n, i)
                        }
                    }
                }, {
                    key: "addKeyframe",
                    value: function (e) {
                        var t = u(e);
                        if (!t) throw new Error("AnimSystem Cannot create keyframe for from options `" + e + "`");
                        var n = new t(this, e);
                        return n.parseOptions(e), n.id = this._allKeyframes.length, this._allKeyframes.push(n), n
                    }
                }, {
                    key: "needsUpdate",
                    value: function () {
                        for (var e = 0, t = this.attributes.length; e < t; e++) {
                            var n = this.attributes[e],
                                i = this.tweenProps[n],
                                r = Math.abs(i.current - i.target);
                            if (r > i.epsilon) return !0
                        }
                        return !1
                    }
                }, {
                    key: "updateLocalProgress",
                    value: function (e) {
                        for (var t = 0, n = this.attributes.length; t < n; t++) {
                            var i = this.attributes[t],
                                r = this.keyframes[this.attributes[t]];
                            if (1 !== r.length) {
                                var s = this.getNearestKeyframeForAttribute(i, e);
                                s && s.updateLocalProgress(e)
                            } else r[0].updateLocalProgress(e)
                        }
                    }
                }, {
                    key: "reconcile",
                    value: function () {
                        for (var e = 0, t = this.attributes.length; e < t; e++) {
                            var n = this.attributes[e],
                                i = this.getNearestKeyframeForAttribute(n, this.group.position.local);
                            i.updateLocalProgress(this.group.position.local), i.snapAtCreation && i.reconcile(n)
                        }
                    }
                }, {
                    key: "determineActiveKeyframes",
                    value: function (e) {
                        var t = this;
                        e = e || h(Array.from(document.documentElement.classList));
                        var n = this._activeKeyframes,
                            i = this.attributes;
                        this._activeKeyframes = [], this.attributes = [], this.keyframes = {};
                        for (var r = 0; r < this._allKeyframes.length; r++) {
                            var s = this._allKeyframes[r];
                            if (s.setEnabled(e)) {
                                this._activeKeyframes.push(s);
                                for (var a in s.animValues) this.keyframes[a] = this.keyframes[a] || [], this.keyframes[a].push(s), this.attributes.indexOf(a) === -1 && (this.attributes.push(a), this.tweenProps[a].isActive = !0)
                            }
                        }
                        var o = n.filter(function (e) {
                            return t._activeKeyframes.indexOf(e) === -1
                        });
                        if (0 !== o.length) {
                            var l = i.filter(function (e) {
                                return t.attributes.indexOf(e) === -1
                            });
                            if (0 !== l.length)
                                if (this.needsWrite = !0, this._ownerIsElement) p.external(function () {
                                    var e = ["x", "y", "z", "scale", "scaleX", "scaleY", "rotation", "rotationX", "rotationY", "rotationZ"],
                                        n = l.filter(function (t) {
                                            return e.indexOf(t) !== -1
                                        });
                                    n.length > 0 && t.element.style.removeProperty("transform");
                                    for (var i = 0, r = l.length; i < r; ++i) {
                                        var s = l[i],
                                            a = t.tweenProps[s];
                                        a.current = a.target = a.initialValue, a.isActive = !1, "opacity" === s && t.element.style.removeProperty("opacity")
                                    }
                                    for (var u = 0, h = o.length; u < h; ++u) {
                                        var m = o[u];
                                        m instanceof c && m._unapply()
                                    }
                                }, !0);
                                else
                                    for (var u = 0, m = l.length; u < m; ++u) {
                                        var d = this.tweenProps[l[u]];
                                        d.current = d.target = d.initialValue, d.isActive = !1
                                    }
                        }
                    }
                }, {
                    key: "onDOMRead",
                    value: function (e) {
                        for (var t = 0, n = this.attributes.length; t < n; t++) {
                            var i = this.attributes[t];
                            this.tweenProps[i].previousValue = this.tweenProps[i].current;
                            var r = this.getNearestKeyframeForAttribute(i, e.local);
                            r && r.onDOMRead(i), this.tweenProps[i].previousValue !== this.tweenProps[i].current && (this.needsWrite = !0)
                        }
                    }
                }, {
                    key: "onDOMWrite",
                    value: function () {
                        (this.needsWrite || this.needsClassUpdate || this.needsStyleUpdate) && (this.needsWrite = !1, this.onDOMWriteImp(), this.handleEventDispatch())
                    }
                }, {
                    key: "onDOMWriteForObject",
                    value: function () {
                        for (var e = 0, t = this.attributes.length; e < t; e++) {
                            var n = this.attributes[e];
                            this.element[n] = this.tweenProps[n].current
                        }
                    }
                }, {
                    key: "onDOMWriteForElement",
                    value: function () {
                        var e = this.tweenProps;
                        if (e.z.isActive || e.rotationX.isActive || e.rotationY.isActive) {
                            var t = this.mat4;
                            if (t[0] = 1, t[1] = 0, t[2] = 0, t[3] = 0, t[4] = 0, t[5] = 1, t[6] = 0, t[7] = 0, t[8] = 0, t[9] = 0, t[10] = 1, t[11] = 0, t[12] = 0, t[13] = 0, t[14] = 0, t[15] = 1, e.x.isActive || e.y.isActive || e.z.isActive) {
                                var n = e.x.current,
                                    i = e.y.current,
                                    r = e.z.current;
                                t[12] = t[0] * n + t[4] * i + t[8] * r + t[12], t[13] = t[1] * n + t[5] * i + t[9] * r + t[13], t[14] = t[2] * n + t[6] * i + t[10] * r + t[14], t[15] = t[3] * n + t[7] * i + t[11] * r + t[15]
                            }
                            if (e.rotation.isActive || e.rotationZ.isActive) {
                                var s = (e.rotation.current || e.rotationZ.current) * v;
                                y.rotateZ(t, t, s)
                            }
                            if (e.rotationX.isActive) {
                                var a = e.rotationX.current * v;
                                y.rotateX(t, t, a)
                            }
                            if (e.rotationY.isActive) {
                                var o = e.rotationY.current * v;
                                y.rotateY(t, t, o)
                            }(e.scale.isActive || e.scaleX.isActive || e.scaleY.isActive) && y.scale(t, t, [e.scale.current, e.scale.current, 1]), this.element.style.transform = "matrix3d(" + t[0] + "," + t[1] + "," + t[2] + "," + t[3] + "," + t[4] + "," + t[5] + "," + t[6] + "," + t[7] + "," + t[8] + "," + t[9] + "," + t[10] + "," + t[11] + "," + t[12] + "," + t[13] + "," + t[14] + "," + t[15] + ")"
                        } else if (e.x.isActive || e.y.isActive || e.rotation.isActive || e.rotationZ.isActive || e.scale.isActive || e.scaleX.isActive || e.scaleY.isActive) {
                            var l = this.mat2d;
                            if (l[0] = 1, l[1] = 0, l[2] = 0, l[3] = 1, l[4] = 0, l[5] = 0, e.x.isActive || e.y.isActive) {
                                var c = e.x.current,
                                    u = e.y.current,
                                    h = l[0],
                                    m = l[1],
                                    d = l[2],
                                    f = l[3],
                                    p = l[4],
                                    _ = l[5];
                                l[0] = h, l[1] = m, l[2] = d, l[3] = f, l[4] = h * c + d * u + p, l[5] = m * c + f * u + _
                            }
                            if (e.rotation.isActive || e.rotationZ.isActive) {
                                var b = (e.rotation.current || e.rotationZ.current) * v,
                                    g = l[0],
                                    E = l[1],
                                    w = l[2],
                                    A = l[3],
                                    O = l[4],
                                    S = l[5],
                                    T = Math.sin(b),
                                    k = Math.cos(b);
                                l[0] = g * k + w * T, l[1] = E * k + A * T, l[2] = g * -T + w * k, l[3] = E * -T + A * k, l[4] = O, l[5] = S
                            }
                            e.scale.isActive ? (l[0] = l[0] * e.scale.current, l[1] = l[1] * e.scale.current, l[2] = l[2] * e.scale.current, l[3] = l[3] * e.scale.current, l[4] = l[4], l[5] = l[5]) : (e.scaleX.isActive || e.scaleY.isActive) && (l[0] = l[0] * e.scaleX.current, l[1] = l[1] * e.scaleX.current, l[2] = l[2] * e.scaleY.current, l[3] = l[3] * e.scaleY.current, l[4] = l[4], l[5] = l[5]), this.element.style.transform = "matrix(" + l[0] + ", " + l[1] + ", " + l[2] + ", " + l[3] + ", " + l[4] + ", " + l[5] + ")"
                        }
                        if (e.opacity.isActive && (this.element.style.opacity = e.opacity.current), this.needsStyleUpdate) {
                            for (var P in this.tweenProps.targetStyles) null !== this.tweenProps.targetStyles[P] && (this.element.style[P] = this.tweenProps.targetStyles[P]), this.tweenProps.targetStyles[P] = null;
                            this.needsStyleUpdate = !1
                        }
                        this.needsClassUpdate && (this.tweenProps.targetClasses.add.length > 0 && this.element.classList.add.apply(this.element.classList, this.tweenProps.targetClasses.add), this.tweenProps.targetClasses.remove.length > 0 && this.element.classList.remove.apply(this.element.classList, this.tweenProps.targetClasses.remove),
                            this.tweenProps.targetClasses.add.length = 0, this.tweenProps.targetClasses.remove.length = 0, this.needsClassUpdate = !1)
                    }
                }, {
                    key: "handleEventDispatch",
                    value: function () {
                        if (0 !== this.keyframesRequiringDispatch.length) {
                            for (var e = 0, t = this.keyframesRequiringDispatch.length; e < t; e++) {
                                var n = this.keyframesRequiringDispatch[e];
                                n.needsEventDispatch = !1, this.eventObject.keyframe = n, this.eventObject.pageMetrics = l.pageMetrics, this.eventObject.event = n.event, this.trigger(n.event, this.eventObject)
                            }
                            this.keyframesRequiringDispatch.length = 0
                        }
                        this.eventObject.keyframe = null, this.eventObject.event = "draw", this.trigger("draw", this.eventObject)
                    }
                }, {
                    key: "updateAnimationConstraints",
                    value: function () {
                        for (var e = this, t = 0, n = this._activeKeyframes.length; t < n; t++) this._activeKeyframes[t].updateAnimationConstraints();
                        this.attributes.forEach(function (t) {
                            1 !== e.keyframes[t].length && e.keyframes[t].sort(l.KeyframeComparison)
                        }), this.updateDeferredPropertyValues()
                    }
                }, {
                    key: "refreshMetrics",
                    value: function () {
                        var e = new Set([this.element]);
                        this._allKeyframes.forEach(function (t) {
                            return t.anchors.forEach(function (t) {
                                return e.add(t)
                            })
                        }), this.group.metrics.refreshCollection(e), this.group.keyframesDirty = !0
                    }
                }, {
                    key: "updateDeferredPropertyValues",
                    value: function () {
                        for (var e = 0, t = this.attributes.length; e < t; e++) {
                            var n = this.attributes[e],
                                i = this.keyframes[n],
                                r = i[0];
                            if (!(r.keyframeType > l.KeyframeTypes.InterpolationForward))
                                for (var s = 0, a = i.length; s < a; s++) {
                                    var o = i[s];
                                    if (null === o.jsonProps[n][0]) {
                                        if (0 === s) {
                                            o.animValues[n][0] = this.tweenProps[n].initialValue;
                                            continue
                                        }
                                        o.animValues[n][0] = i[s - 1].animValues[n][1]
                                    }
                                    if (null === o.jsonProps[n][1]) {
                                        if (s === a - 1) throw new RangeError("AnimSystem - last keyframe cannot defer it's end value! " + n + ":[" + o.jsonProps[n][0] + ",null]");
                                        o.animValues[n][1] = i[s + 1].animValues[n][0]
                                    }
                                }
                        }
                    }
                }, {
                    key: "getBounds",
                    value: function (e) {
                        this.boundsMin = Number.MAX_VALUE, this.boundsMax = -Number.MAX_VALUE;
                        for (var t = 0, n = this.attributes.length; t < n; t++)
                            for (var i = this.keyframes[this.attributes[t]], r = 0; r < i.length; r++) {
                                var s = i[r];
                                this.boundsMin = Math.min(s.start, this.boundsMin), this.boundsMax = Math.max(s.end, this.boundsMax), e.min = Math.min(s.start, e.min), e.max = Math.max(s.end, e.max)
                            }
                    }
                }, {
                    key: "getNearestKeyframeForAttribute",
                    value: function (e, t) {
                        t = void 0 !== t ? t : this.group.position.local;
                        var n = null,
                            i = Number.POSITIVE_INFINITY,
                            r = this.keyframes[e];
                        if (void 0 === r) return null;
                        var s = r.length;
                        if (0 === s) return null;
                        if (1 === s) return r[0];
                        for (var a = 0; a < s; a++) {
                            var o = r[a];
                            if (o.isInRange(t)) {
                                n = o;
                                break
                            }
                            var l = Math.min(Math.abs(t - o.start), Math.abs(t - o.end));
                            l < i && (i = l, n = o)
                        }
                        return n
                    }
                }, {
                    key: "getAllKeyframesForAttribute",
                    value: function (e) {
                        return this.keyframes[e]
                    }
                }, {
                    key: "updateKeyframe",
                    value: function (e, t) {
                        var n = this;
                        e.parseOptions(t), e.updateAnimationConstraints(), this.group.keyframesDirty = !0, p.update(function () {
                            n.trigger(l.EVENTS.ON_KEYFRAME_UPDATED, e), n.group.trigger(l.EVENTS.ON_KEYFRAME_UPDATED, e)
                        }, !0)
                    }
                }, {
                    key: "removeKeyframe",
                    value: function (e) {
                        var t = this,
                            n = this._allKeyframes.indexOf(e);
                        return n === -1 ? Promise.resolve(null) : (this._allKeyframes.splice(n, 1), this.group.keyframesDirty = !0, new Promise(function (n) {
                            t.group.rafEmitter.executor.eventEmitter.once("before:draw", function () {
                                return n(e)
                            })
                        }))
                    }
                }, {
                    key: "updateAnimation",
                    value: function (e, t) {
                        return this.group.gui && console.warn("KeyframeController.updateAnimation(keyframe,props) has been deprecated. Please use updateKeyframe(keyframe,props)"), this.updateKeyframe(e, t)
                    }
                }]), t
            }(d);
        t.exports = _
    }, {
        "../Model/AnimSystemModel": 96,
        "../Model/InferKeyframeFromProps": 99,
        "../Model/UUID": 100,
        "../utils/arrayToObject": 107,
        "./Keyframe": 92,
        "./KeyframeCSSClass": 93,
        "@marcom/ac-event-emitter-micro": 35,
        "@marcom/ac-raf-emitter/draw": 81,
        "@marcom/ac-raf-emitter/external": 82,
        "@marcom/ac-raf-emitter/update": 85,
        "@marcom/decompose-css-transform": 111,
        "gl-mat4/create": 117,
        "gl-mat4/rotateX": 119,
        "gl-mat4/rotateY": 120,
        "gl-mat4/rotateZ": 121,
        "gl-mat4/scale": 122
    }],
    95: [function (e, t, n) {
        "use strict";

        function i(e, t) {
            if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }

        function r(e, t) {
            if (!e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
            return !t || "object" != typeof t && "function" != typeof t ? e : t
        }

        function s(e, t) {
            if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function, not " + typeof t);
            e.prototype = Object.create(t && t.prototype, {
                constructor: {
                    value: e,
                    enumerable: !1,
                    writable: !0,
                    configurable: !0
                }
            }), t && (Object.setPrototypeOf ? Object.setPrototypeOf(e, t) : e.__proto__ = t)
        }
        var a = function () {
                function e(e, t) {
                    for (var n = 0; n < t.length; n++) {
                        var i = t[n];
                        i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i)
                    }
                }
                return function (t, n, i) {
                    return n && e(t.prototype, n), i && e(t, i), t
                }
            }(),
            o = function h(e, t, n) {
                null === e && (e = Function.prototype);
                var i = Object.getOwnPropertyDescriptor(e, t);
                if (void 0 === i) {
                    var r = Object.getPrototypeOf(e);
                    return null === r ? void 0 : h(r, t, n)
                }
                if ("value" in i) return i.value;
                var s = i.get;
                if (void 0 !== s) return s.call(n)
            },
            l = e("./Keyframe"),
            c = e("../Model/AnimSystemModel.js"),
            u = function (e) {
                function t(e, n) {
                    i(this, t);
                    var s = r(this, (t.__proto__ || Object.getPrototypeOf(t)).call(this, e, n));
                    return s.keyframeType = c.KeyframeTypes.Event, s.isApplied = !1, s.hasDuration = !1, s.isCurrentlyInRange = !1, s
                }
                return s(t, e), a(t, [{
                    key: "parseOptions",
                    value: function (e) {
                        e.x = void 0, e.y = void 0, e.scale = void 0, e.scaleX = void 0, e.scaleY = void 0, e.rotation = void 0, e.style = void 0, e.cssClass = void 0, e.rotation = void 0, e.opacity = void 0, e.hold = void 0, void 0 === e.end && (e.end = e.start), this.event = e.event, this.animValues[this.event] = [0, 0], "undefined" == typeof this.controller.tweenProps[this.event] && (this.controller.tweenProps[this.event] = new c.TargetValue(0, 1, (!1))), o(t.prototype.__proto__ || Object.getPrototypeOf(t.prototype), "parseOptions", this).call(this, e), this.keyframeType = c.KeyframeTypes.Event
                    }
                }, {
                    key: "updateLocalProgress",
                    value: function (e) {
                        if (this.hasDuration) {
                            var t = this.isCurrentlyInRange,
                                n = e >= this.start && e <= this.end;
                            if (t === n) return;
                            return this.isCurrentlyInRange = n, void(n && !t ? this._trigger(this.event + ":enter") : t && !n && this._trigger(this.event + ":exit"))
                        }!this.isApplied && e >= this.start ? (this.isApplied = !0, this._trigger(this.event)) : this.isApplied && e < this.start && (this.isApplied = !1, this._trigger(this.event + ":reverse"))
                    }
                }, {
                    key: "_trigger",
                    value: function (e) {
                        this.controller.eventObject.event = e, this.controller.eventObject.keyframe = this, this.controller.trigger(e, this.controller.eventObject)
                    }
                }, {
                    key: "updateAnimationConstraints",
                    value: function () {
                        o(t.prototype.__proto__ || Object.getPrototypeOf(t.prototype), "updateAnimationConstraints", this).call(this), this.hasDuration = this.start !== this.end
                    }
                }, {
                    key: "reset",
                    value: function (e) {
                        this.isApplied = !1, this.isCurrentlyInRange = !1, o(t.prototype.__proto__ || Object.getPrototypeOf(t.prototype), "reset", this).call(this, e)
                    }
                }, {
                    key: "onDOMRead",
                    value: function (e, t) {}
                }, {
                    key: "reconcile",
                    value: function (e, t) {}
                }, {
                    key: "updateAnimatedValueConstraints",
                    value: function () {}
                }]), t
            }(l);
        u.DATA_ATTRIBUTE = "data-anim-event", t.exports = u
    }, {
        "../Model/AnimSystemModel.js": 96,
        "./Keyframe": 92
    }],
    96: [function (e, t, n) {
        "use strict";

        function i(e, t) {
            if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }
        var r = {
            GUI_INSTANCE: null,
            ANIM_INSTANCE: null,
            VIEWPORT_EMITTER_ELEMENT: void 0,
            LOCAL_STORAGE_KEYS: {
                GuiPosition: "GuiPosition-0",
                scrollY: "scrollY",
                path: "path"
            },
            RESIZE_TIMEOUT: -1,
            BREAKPOINTS: [{
                name: "S",
                mediaQuery: "only screen and (max-width: 735px)"
            }, {
                name: "M",
                mediaQuery: "only screen and (max-width: 1068px)"
            }, {
                name: "L",
                mediaQuery: "only screen and (min-width: 1442px)"
            }, {
                name: "L",
                mediaQuery: "only screen and (min-width: 1069px)"
            }],
            getBreakpoint: function () {
                for (var e = 0; e < r.BREAKPOINTS.length; e++) {
                    var t = r.BREAKPOINTS[e],
                        n = window.matchMedia(t.mediaQuery);
                    if (n.matches) return t.name
                }
            },
            KeyframeDefaults: {
                ease: 1,
                epsilon: .05,
                easeFunctionString: "linear",
                easeFunction: "linear",
                hold: !1,
                snapAtCreation: !1,
                toggle: !1,
                breakpointMask: "SMLX",
                event: "",
                disabledWhen: [],
                cssClass: ""
            },
            KeyframeTypes: {
                Interpolation: 0,
                InterpolationForward: 1,
                CSSClass: 2,
                Event: 3
            },
            EVENTS: {
                ON_DOM_KEYFRAMES_CREATED: "ON_DOM_KEYFRAMES_CREATED",
                ON_DOM_GROUPS_CREATED: "ON_DOM_GROUPS_CREATED",
                ON_GROUP_CREATED: "ON_GROUP_CREATED",
                ON_KEYFRAME_UPDATED: "ON_KEYFRAME_UPDATED",
                ON_TIMELINE_START: "ON_TIMELINE_START",
                ON_TIMELINE_UPDATE: "ON_TIMELINE_UPDATE",
                ON_TIMELINE_COMPLETE: "ON_TIMELINE_COMPLETE"
            },
            PageEvents: {
                ON_SCROLL: "ON_SCROLL",
                ON_RESIZE_IMMEDIATE: "ON_RESIZE_IMMEDIATE",
                ON_RESIZE_DEBOUNCED: "ON_RESIZE_DEBOUNCED",
                ON_BREAKPOINT_CHANGE: "ON_BREAKPOINT_CHANGE"
            },
            KeyframeJSONReservedWords: ["event", "cssClass", "style", "anchors", "start", "end", "epsilon", "easeFunction", "ease", "breakpointMask", "disabledWhen"],
            TweenProps: function s() {
                i(this, s)
            },
            TargetValue: function a(e, t, n) {
                i(this, a), this.epsilon = parseFloat(t), this.snapAtCreation = n, this.initialValue = e, this.target = e, this.current = e, this.previousValue = e, this.isActive = !1
            },
            AnimInfo: function (e, t) {
                var n = arguments.length > 2 && void 0 !== arguments[2] && arguments[2];
                this.isGroup = n, this.group = e, this.controller = t, this.controllers = []
            },
            Progress: function () {
                this.local = 0, this.localUnclamped = 0, this.lastPosition = 0
            },
            ViewableRange: function (e, t) {
                this.a = e.top - t, this.a < 0 && (this.a = e.top), this.b = e.top, this.d = e.bottom, this.c = Math.max(this.d - t, this.b)
            },
            pageMetrics: new function () {
                this.scrollX = 0, this.scrollY = 0, this.windowWidth = 0, this.windowHeight = 0, this.documentOffsetX = 0, this.documentOffsetY = 0, this.previousBreakpoint = "", this.breakpoint = ""
            },
            EventObject: function (e) {
                this.controller = e, this.element = this.controller.element, this.keyframe = null, this.event = "", this.tweenProps = this.controller.tweenProps
            },
            KeyframeComparison: function (e, t) {
                return e.start < t.start ? -1 : e.start > t.start ? 1 : 0
            }
        };
        t.exports = r
    }, {}],
    97: [function (e, t, n) {
        "use strict";

        function i(e, t) {
            if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }
        var r = function s() {
            i(this, s), this.linear = function (e) {
                return e
            }, this.easeInQuad = function (e) {
                return e * e
            }, this.easeOutQuad = function (e) {
                return e * (2 - e)
            }, this.easeInOutQuad = function (e) {
                return e < .5 ? 2 * e * e : -1 + (4 - 2 * e) * e
            }, this.easeInSin = function (e) {
                return 1 + Math.sin(Math.PI / 2 * e - Math.PI / 2)
            }, this.easeOutSin = function (e) {
                return Math.sin(Math.PI / 2 * e)
            }, this.easeInOutSin = function (e) {
                return (1 + Math.sin(Math.PI * e - Math.PI / 2)) / 2
            }, this.easeInElastic = function (e) {
                return 0 === e ? e : (.04 - .04 / e) * Math.sin(25 * e) + 1
            }, this.easeOutElastic = function (e) {
                return .04 * e / --e * Math.sin(25 * e)
            }, this.easeInOutElastic = function (e) {
                return (e -= .5) < 0 ? (.02 + .01 / e) * Math.sin(50 * e) : (.02 - .01 / e) * Math.sin(50 * e) + 1
            }, this.easeOutBack = function (e) {
                return e -= 1, e * e * (2.70158 * e + 1.70158) + 1
            }, this.easeInCubic = function (e) {
                return e * e * e
            }, this.easeOutCubic = function (e) {
                return --e * e * e + 1
            }, this.easeInOutCubic = function (e) {
                return e < .5 ? 4 * e * e * e : (e - 1) * (2 * e - 2) * (2 * e - 2) + 1
            }, this.easeInQuart = function (e) {
                return e * e * e * e
            }, this.easeOutQuart = function (e) {
                return 1 - --e * e * e * e
            }, this.easeInOutQuart = function (e) {
                return e < .5 ? 8 * e * e * e * e : 1 - 8 * --e * e * e * e
            }, this.easeInQuint = function (e) {
                return e * e * e * e * e
            }, this.easeOutQuint = function (e) {
                return 1 + --e * e * e * e * e
            }, this.easeInOutQuint = function (e) {
                return e < .5 ? 16 * e * e * e * e * e : 1 + 16 * --e * e * e * e * e
            }
        };
        t.exports = new r
    }, {}],
    98: [function (e, t, n) {
        "use strict";

        function i(e, t) {
            if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }
        var r = function () {
                function e(e, t) {
                    for (var n = 0; n < t.length; n++) {
                        var i = t[n];
                        i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i)
                    }
                }
                return function (t, n, i) {
                    return n && e(t.prototype, n), i && e(t, i), t
                }
            }(),
            s = e("./AnimSystemModel"),
            a = function (e, t) {
                return void 0 === e || null === e ? t : e
            },
            o = function () {
                function e() {
                    i(this, e), this._metrics = new WeakMap
                }
                return r(e, [{
                    key: "destroy",
                    value: function () {
                        this._metrics = null
                    }
                }, {
                    key: "add",
                    value: function (e) {
                        var t = this._metrics.get(e);
                        if (t) return t;
                        var n = new l(e);
                        return this._metrics.set(e, n), this._refreshMetrics(e, n)
                    }
                }, {
                    key: "get",
                    value: function (e) {
                        return this._metrics.get(e)
                    }
                }, {
                    key: "refreshCollection",
                    value: function (e) {
                        var t = this;
                        e.forEach(function (e) {
                            return t._refreshMetrics(e, null)
                        })
                    }
                }, {
                    key: "refreshMetrics",
                    value: function (e) {
                        return this._refreshMetrics(e)
                    }
                }, {
                    key: "_refreshMetrics",
                    value: function (e, t) {
                        if (t = t || this._metrics.get(e), !(e instanceof Element)) return t.width = a(e.width, 0), t.height = a(e.height, 0), t.top = a(e.top, 0), t.left = a(e.left, 0), t.right = t.left + t.width, t.bottom = t.top + t.height, t;
                        if (void 0 === e.offsetWidth) {
                            var n = e.getBoundingClientRect();
                            return t.width = n.width, t.height = n.height, t.top = s.pageMetrics.scrollY + n.top, t.left = s.pageMetrics.scrollX + n.left, t.right = t.left + t.width, t.bottom = t.top + t.height, t
                        }
                        t.width = e.offsetWidth, t.height = e.offsetHeight, t.top = s.pageMetrics.documentOffsetY, t.left = s.pageMetrics.documentOffsetX;
                        for (var i = e; i;) t.top += i.offsetTop, t.left += i.offsetLeft, i = i.offsetParent;
                        return t.right = t.left + t.width, t.bottom = t.top + t.height, t
                    }
                }]), e
            }(),
            l = function () {
                function e(t) {
                    i(this, e), this.top = 0, this.bottom = 0, this.left = 0, this.right = 0, this.height = 0, this.width = 0
                }
                return r(e, [{
                    key: "toString",
                    value: function () {
                        return "top:" + this.top + ", bottom:" + this.bottom + ", left:" + this.left + ", right:" + this.right + ", height:" + this.height + ", width:" + this.width
                    }
                }, {
                    key: "toObject",
                    value: function () {
                        return {
                            top: this.top,
                            bottom: this.bottom,
                            left: this.left,
                            right: this.right,
                            height: this.height,
                            width: this.width
                        }
                    }
                }]), e
            }();
        t.exports = o
    }, {
        "./AnimSystemModel": 96
    }],
    99: [function (e, t, n) {
        "use strict";
        var i = e("./AnimSystemModel"),
            r = e("../Keyframes/Keyframe"),
            s = e("../Keyframes/KeyframeDiscreteEvent"),
            a = e("../Keyframes/KeyframeCSSClass"),
            o = function (e) {
                for (var t in e) {
                    var n = e[t];
                    if (i.KeyframeJSONReservedWords.indexOf(t) === -1 && Array.isArray(n)) return !0
                }
                return !1
            };
        t.exports = function (e) {
            if (void 0 !== e.cssClass || void 0 !== e.style) {
                if (o(e)) throw "CSS Keyframes cannot tween values, please use multiple keyframes instead";
                return a
            }
            if (o(e)) return r;
            if (e.event) return s;
            throw delete e.anchors, "Could not determine tween type based on " + JSON.stringify(e)
        }
    }, {
        "../Keyframes/Keyframe": 92,
        "../Keyframes/KeyframeCSSClass": 93,
        "../Keyframes/KeyframeDiscreteEvent": 95,
        "./AnimSystemModel": 96
    }],
    100: [function (e, t, n) {
        "use strict";
        t.exports = function () {
            for (var e = "", t = 0; t < 8; t++) {
                var n = 16 * Math.random() | 0;
                8 !== t && 12 !== t && 16 !== t && 20 !== t || (e += "-"), e += (12 === t ? 4 : 16 === t ? 3 & n | 8 : n).toString(16)
            }
            return e
        }
    }, {}],
    101: [function (e, t, n) {
        "use strict";

        function i(e, t) {
            if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }
        var r = function () {
                function e(e, t) {
                    for (var n = 0; n < t.length; n++) {
                        var i = t[n];
                        i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i)
                    }
                }
                return function (t, n, i) {
                    return n && e(t.prototype, n), i && e(t, i), t
                }
            }(),
            s = 1e-5,
            a = Math.abs,
            o = 5,
            l = function () {
                function e(t, n, r, s) {
                    i(this, e), this.cp = new Float32Array(6), this.cp[0] = 3 * t, this.cp[1] = 3 * (r - t) - this.cp[0], this.cp[2] = 1 - this.cp[0] - this.cp[1], this.cp[3] = 3 * n, this.cp[4] = 3 * (s - n) - this.cp[3], this.cp[5] = 1 - this.cp[3] - this.cp[4]
                }
                return r(e, [{
                    key: "sampleCurveX",
                    value: function (e) {
                        return ((this.cp[2] * e + this.cp[1]) * e + this.cp[0]) * e
                    }
                }, {
                    key: "sampleCurveY",
                    value: function (e) {
                        return ((this.cp[5] * e + this.cp[4]) * e + this.cp[3]) * e
                    }
                }, {
                    key: "sampleCurveDerivativeX",
                    value: function (e) {
                        return (3 * this.cp[2] * e + 2 * this.cp[1]) * e + this.cp[0]
                    }
                }, {
                    key: "solveCurveX",
                    value: function (e) {
                        var t, n, i, r, l, c;
                        for (i = e, c = 0; c < o; c++) {
                            if (r = this.sampleCurveX(i) - e, a(r) < s) return i;
                            if (l = this.sampleCurveDerivativeX(i), a(l) < s) break;
                            i -= r / l
                        }
                        if (t = 0, n = 1, i = e, i < t) return t;
                        if (i > n) return n;
                        for (; t < n;) {
                            if (r = this.sampleCurveX(i), a(r - e) < s) return i;
                            e > r ? t = i : n = i, i = .5 * (n - t) + t
                        }
                        return i
                    }
                }, {
                    key: "solve",
                    value: function (e) {
                        return this.sampleCurveY(this.solveCurveX(e))
                    }
                }]), e
            }(),
            c = /\d*\.?\d+/g;
        l.fromCSSString = function (e) {
            var t = e.match(c);
            if (4 !== t.length) throw "UnitBezier could not convert " + e + " to cubic-bezier";
            var n = t.map(Number),
                i = new l(n[0], n[1], n[2], n[3]);
            return i.solve.bind(i)
        }, t.exports = l
    }, {}],
    102: [function (e, t, n) {
        "use strict";

        function i(e, t) {
            if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }
        var r = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (e) {
                return typeof e
            } : function (e) {
                return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e
            },
            s = function () {
                function e(e, t) {
                    for (var n = 0; n < t.length; n++) {
                        var i = t[n];
                        i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i)
                    }
                }
                return function (t, n, i) {
                    return n && e(t.prototype, n), i && e(t, i), t
                }
            }(),
            a = e("./Interpreter"),
            o = new(e("../Model/ElementMetricsLookup")),
            l = function () {
                function e(t) {
                    i(this, e), this.group = t, this.data = {
                        target: null,
                        anchors: null,
                        metrics: this.group.metrics
                    }
                }
                return s(e, [{
                    key: "parseArray",
                    value: function (e, t) {
                        return [this.parseExpression(e, t[0]), this.parseExpression(e, t[1])]
                    }
                }, {
                    key: "parseExpression",
                    value: function (t, n) {
                        if (!n) return null;
                        if ("number" == typeof n) return n;
                        if ("string" != typeof n) throw "Expression must be a string, received " + ("undefined" == typeof n ? "undefined" : r(n));
                        return this.data.target = t.controller.element, this.data.anchors = t.anchors, this.data.keyframe = t.keyframe, e._parse(n, this.data)
                    }
                }, {
                    key: "destroy",
                    value: function () {
                        this.group = null
                    }
                }], [{
                    key: "_parse",
                    value: function (e, t) {
                        return a.Parse(e).execute(t)
                    }
                }, {
                    key: "parse",
                    value: function (t, n) {
                        return n = n || {}, n && (n.target && o.add(n.target), n.anchors && n.anchors.forEach(function (e) {
                            return o.add(e)
                        })), n.metrics = o, e._parse(t, n)
                    }
                }]), e
            }();
        l.programs = a.programs, window.ExpressionParser = l, t.exports = l
    }, {
        "../Model/ElementMetricsLookup": 98,
        "./Interpreter": 103
    }],
    103: [function (e, t, n) {
        "use strict";

        function i(e, t) {
            if (!e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
            return !t || "object" != typeof t && "function" != typeof t ? e : t
        }

        function r(e, t) {
            if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function, not " + typeof t);
            e.prototype = Object.create(t && t.prototype, {
                constructor: {
                    value: e,
                    enumerable: !1,
                    writable: !0,
                    configurable: !0
                }
            }), t && (Object.setPrototypeOf ? Object.setPrototypeOf(e, t) : e.__proto__ = t)
        }

        function s(e, t) {
            if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }
        var a = function () {
                function e(e, t) {
                    for (var n = 0; n < t.length; n++) {
                        var i = t[n];
                        i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i)
                    }
                }
                return function (t, n, i) {
                    return n && e(t.prototype, n), i && e(t, i), t
                }
            }(),
            o = e("../Model/AnimSystemModel"),
            l = e("@marcom/sm-math-utils"),
            c = {},
            u = {
                smoothstep: function (e, t, n) {
                    return n = u.clamp((n - e) / (t - e), 0, 1), n * n * (3 - 2 * n)
                },
                deg: function (e) {
                    return 180 * e / Math.PI
                },
                rad: function (e) {
                    return e * Math.PI / 180
                },
                random: function (e, t) {
                    return Math.random() * (t - e) + e
                },
                atan: Math.atan2
            };
        Object.getOwnPropertyNames(Math).forEach(function (e) {
            return u[e] ? null : u[e.toLowerCase()] = Math[e]
        }), Object.getOwnPropertyNames(l).forEach(function (e) {
            return u[e] ? null : u[e.toLowerCase()] = l[e]
        });
        var h = null,
            m = {
                ANCHOR_CONST: "a",
                ALPHA: "ALPHA",
                LPAREN: "(",
                RPAREN: ")",
                PLUS: "PLUS",
                MINUS: "MINUS",
                MUL: "MUL",
                DIV: "DIV",
                INTEGER_CONST: "INTEGER_CONST",
                FLOAT_CONST: "FLOAT_CONST",
                COMMA: ",",
                EOF: "EOF"
            },
            d = {
                NUMBERS: /\d|\d\.\d/,
                DIGIT: /\d/,
                OPERATOR: /[-+*\/]/,
                PAREN: /[()]/,
                WHITE_SPACE: /\s/,
                ALPHA: /[a-zA-Z]|%/,
                ALPHANUMERIC: /[a-zA-Z0-9]/,
                OBJECT_UNIT: /^(t|l|b|r|%w|%h|%|h|w)$/,
                GLOBAL_METRICS_UNIT: /^(px|vh|vw)$/,
                ANY_UNIT: /^(t|l|b|r|%w|%h|%|h|w|px|vh|vw)$/,
                MATH_FUNCTION: new RegExp("\\b(" + Object.keys(u).join("|") + ")\\b", "i")
            },
            f = {
                round: 1,
                clamp: 3,
                lerp: 3,
                random: 2,
                atan: 2,
                floor: 1,
                ceil: 1,
                abs: 1,
                cos: 1,
                sin: 1,
                smoothstep: 3,
                rad: 1,
                deg: 1,
                pow: 2,
                calc: 1
            },
            p = function k(e, t) {
                s(this, k), this.type = e, this.value = t
            };
        p.ONE = new p("100", 100), p.EOF = new p(m.EOF, null);
        var v = function P(e) {
                s(this, P), this.type = e
            },
            y = function (e) {
                function t(e, n) {
                    s(this, t);
                    var r = i(this, (t.__proto__ || Object.getPrototypeOf(t)).call(this, "UnaryOp"));
                    return r.token = r.op = e, r.expr = n, r
                }
                return r(t, e), t
            }(v),
            _ = function (e) {
                function t(e, n, r) {
                    s(this, t);
                    var a = i(this, (t.__proto__ || Object.getPrototypeOf(t)).call(this, "BinOp"));
                    return a.left = e, a.op = n, a.right = r, a
                }
                return r(t, e), t
            }(v),
            b = function (e) {
                function t(e, n) {
                    s(this, t);
                    var r = i(this, (t.__proto__ || Object.getPrototypeOf(t)).call(this, "MathOp"));
                    if (r.op = e, r.list = n, f[e.value] && n.length !== f[e.value]) throw new Error("Incorrect number of arguments for '" + e.value + "'. Received " + n.length + ", expected " + f[e.value]);
                    return r
                }
                return r(t, e), t
            }(v),
            g = function (e) {
                function t(e) {
                    s(this, t);
                    var n = i(this, (t.__proto__ || Object.getPrototypeOf(t)).call(this, "Num"));
                    return n.token = e, n.value = e.value, n
                }
                return r(t, e), t
            }(v),
            E = (function (e) {
                function t(e) {
                    s(this, t);
                    var n = i(this, (t.__proto__ || Object.getPrototypeOf(t)).call(this, "Unit"));
                    return n.token = e, n.value = e.value, n
                }
                return r(t, e), t
            }(v), function (e) {
                function t(e, n, r) {
                    s(this, t);
                    var a = i(this, (t.__proto__ || Object.getPrototypeOf(t)).call(this, "RefValue"));
                    return a.num = e, a.ref = n, a.unit = r, a
                }
                return r(t, e), t
            }(v)),
            w = function (e) {
                function t(e, n) {
                    s(this, t);
                    var r = i(this, (t.__proto__ || Object.getPrototypeOf(t)).call(this, "CSSValue"));
                    return r.ref = e, r.propertyName = n, r
                }
                return r(t, e), t
            }(v),
            A = function (e) {
                function t(e, n) {
                    s(this, t);
                    var r = i(this, (t.__proto__ || Object.getPrototypeOf(t)).call(this, "PropValue"));
                    return r.ref = e, r.propertyName = n, r
                }
                return r(t, e), t
            }(v),
            O = function () {
                function e(t) {
                    s(this, e), this.text = t, this.pos = 0, this["char"] = this.text[this.pos], this.tokens = [];
                    for (var n = void 0;
                        (n = this.getNextToken()) && n !== p.EOF;) this.tokens.push(n);
                    this.tokens.push(n)
                }
                return a(e, [{
                    key: "advance",
                    value: function () {
                        this["char"] = this.text[++this.pos]
                    }
                }, {
                    key: "skipWhiteSpace",
                    value: function () {
                        for (; null != this["char"] && d.WHITE_SPACE.test(this["char"]);) this.advance()
                    }
                }, {
                    key: "name",
                    value: function () {
                        for (var e = ""; null != this["char"] && d.ALPHA.test(this["char"]);) e += this["char"], this.advance();
                        return new p(m.ALPHA, e)
                    }
                }, {
                    key: "number",
                    value: function () {
                        for (var e = ""; null != this["char"] && d.DIGIT.test(this["char"]);) e += this["char"], this.advance();
                        if (null != this["char"] && "." === this["char"]) {
                            for (e += this["char"], this.advance(); null != this["char"] && d.DIGIT.test(this["char"]);) e += this["char"], this.advance();
                            return new p(m.FLOAT_CONST, parseFloat(e))
                        }
                        return new p(m.INTEGER_CONST, parseInt(e))
                    }
                }, {
                    key: "getNextToken",
                    value: function () {
                        for (; null != this["char"];)
                            if (d.WHITE_SPACE.test(this["char"])) this.skipWhiteSpace();
                            else {
                                if (d.DIGIT.test(this["char"])) return this.number();
                                if ("," === this["char"]) return this.advance(), new p(m.COMMA, ",");
                                if (d.OPERATOR.test(this["char"])) {
                                    var e = "",
                                        t = this["char"];
                                    switch (t) {
                                        case "+":
                                            e = m.PLUS;
                                            break;
                                        case "-":
                                            e = m.MINUS;
                                            break;
                                        case "*":
                                            e = m.MUL;
                                            break;
                                        case "/":
                                            e = m.DIV
                                    }
                                    return this.advance(), new p(e, t)
                                }
                                if (d.PAREN.test(this["char"])) {
                                    var n = "",
                                        i = this["char"];
                                    switch (i) {
                                        case "(":
                                            n = m.LPAREN;
                                            break;
                                        case ")":
                                            n = m.RPAREN
                                    }
                                    return this.advance(), new p(n, i)
                                }
                                if (d.ALPHA.test(this["char"])) return this.name();
                                this.error("Unexpected character " + this["char"])
                            } return p.EOF
                    }
                }]), e
            }(),
            S = function () {
                function e(t) {
                    s(this, e), this.lexer = t, this.pos = 0
                }
                return a(e, [{
                    key: "error",
                    value: function t(e) {
                        var n = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : "",
                            i = this.lexer.text.slice(this.pos - 3, this.pos + 3),
                            t = new Error(e + ' in "' + this.lexer.text + '" near "' + i + '". ' + n + " ");
                        throw console.error(t.message, h ? h.keyframe || h.target : ""), t
                    }
                }, {
                    key: "consume",
                    value: function (e) {
                        var t = this.currentToken;
                        return t.type === e ? this.pos += 1 : this.error("Invalid token " + this.currentToken.value + ", expected " + e), t
                    }
                }, {
                    key: "consumeList",
                    value: function (e) {
                        e.includes(this.currentToken) ? this.pos += 1 : this.error("Invalid token " + this.currentToken.value + ", expected " + tokenType)
                    }
                }, {
                    key: "expr",
                    value: function () {
                        for (var e = this.term(); this.currentToken.type === m.PLUS || this.currentToken.type === m.MINUS;) {
                            var t = this.currentToken;
                            switch (t.value) {
                                case "+":
                                    this.consume(m.PLUS);
                                    break;
                                case "-":
                                    this.consume(m.MINUS)
                            }
                            e = new _(e, t, this.term())
                        }
                        return e
                    }
                }, {
                    key: "term",
                    value: function () {
                        for (var e = this.factor(); this.currentToken.type === m.MUL || this.currentToken.type === m.DIV;) {
                            var t = this.currentToken;
                            switch (t.value) {
                                case "*":
                                    this.consume(m.MUL);
                                    break;
                                case "/":
                                    this.consume(m.DIV)
                            }
                            e = new _(e, t, this.factor())
                        }
                        return e
                    }
                }, {
                    key: "factor",
                    value: function () {
                        if (this.currentToken.type === m.PLUS) return new y(this.consume(m.PLUS), this.factor());
                        if (this.currentToken.type === m.MINUS) return new y(this.consume(m.MINUS), this.factor());
                        if (this.currentToken.type === m.INTEGER_CONST || this.currentToken.type === m.FLOAT_CONST) {
                            var e = new g(this.currentToken);
                            if (this.pos += 1, d.OPERATOR.test(this.currentToken.value) || this.currentToken.type === m.RPAREN || this.currentToken.type === m.COMMA || this.currentToken.type === m.EOF) return e;
                            if (this.currentToken.type === m.ALPHA && this.currentToken.value === m.ANCHOR_CONST) return this.consume(m.ALPHA), new E(e, this.anchorIndex(), this.unit(d.ANY_UNIT));
                            if (this.currentToken.type === m.ALPHA) return "%a" === this.currentToken.value && this.error("%a is invalid, try removing the %"), new E(e, null, this.unit());
                            this.error("Expected a scaling unit type", "Such as 'h' / 'w'")
                        } else {
                            if (d.OBJECT_UNIT.test(this.currentToken.value)) return new E(new g(p.ONE), null, this.unit());
                            if (this.currentToken.value === m.ANCHOR_CONST) {
                                this.consume(m.ALPHA);
                                var t = this.anchorIndex();
                                if (d.OBJECT_UNIT.test(this.currentToken.value)) return new E(new g(p.ONE), t, this.unit())
                            } else if (this.currentToken.type === m.ALPHA) {
                                if ("css" === this.currentToken.value || "prop" === this.currentToken.value) {
                                    var n = "css" === this.currentToken.value ? w : A;
                                    this.consume(m.ALPHA), this.consume(m.LPAREN);
                                    var i = this.propertyName(),
                                        r = null;
                                    return this.currentToken.type === m.COMMA && (this.consume(m.COMMA), this.consume(m.ALPHA), r = this.anchorIndex()), this.consume(m.RPAREN), new n(r, i)
                                }
                                if (d.MATH_FUNCTION.test(this.currentToken.value)) {
                                    var s = this.currentToken.value.toLowerCase();
                                    if ("number" == typeof u[s]) return this.consume(m.ALPHA), new g(new p(m.ALPHA, u[s]));
                                    var a = p[s] || new p(s, s),
                                        o = [];
                                    this.consume(m.ALPHA), this.consume(m.LPAREN);
                                    var l = null;
                                    do this.currentToken.value === m.COMMA && this.consume(m.COMMA), l = this.expr(), o.push(l); while (this.currentToken.value === m.COMMA);
                                    return this.consume(m.RPAREN), new b(a, o)
                                }
                            } else if (this.currentToken.type === m.LPAREN) {
                                this.consume(m.LPAREN);
                                var c = this.expr();
                                return this.consume(m.RPAREN), c
                            }
                        }
                        this.error("Unexpected token " + this.currentToken.value)
                    }
                }, {
                    key: "propertyName",
                    value: function () {
                        for (var e = ""; this.currentToken.type === m.ALPHA || this.currentToken.type === m.MINUS;) e += this.currentToken.value, this.pos += 1;
                        return e
                    }
                }, {
                    key: "unit",
                    value: function () {
                        var e = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : d.ANY_UNIT,
                            t = this.currentToken;
                        return t.type === m.ALPHA && e.test(t.value) ? (this.consume(m.ALPHA), new p(m.ALPHA, t.value = t.value.replace(/%(h|w)/, "$1").replace("%", "h"))) : void this.error("Expected unit type")
                    }
                }, {
                    key: "anchorIndex",
                    value: function () {
                        var e = this.currentToken;
                        return e.type === m.INTEGER_CONST ? (this.consume(m.INTEGER_CONST), new g(e)) : void this.error("Invalid anchor reference", ". Should be something like a0, a1, a2")
                    }
                }, {
                    key: "parse",
                    value: function () {
                        var e = this.expr();
                        return this.currentToken !== p.EOF && this.error("Unexpected token " + this.currentToken.value), e
                    }
                }, {
                    key: "currentToken",
                    get: function () {
                        return this.lexer.tokens[this.pos]
                    }
                }]), e
            }(),
            T = function () {
                function e(t) {
                    s(this, e), this.parser = t, this.root = t.parse()
                }
                return a(e, [{
                    key: "visit",
                    value: function (e) {
                        var t = this[e.type];
                        if (!t) throw new Error("No visit method named, " + t);
                        var n = t.call(this, e);
                        return n
                    }
                }, {
                    key: "BinOp",
                    value: function (e) {
                        switch (e.op.type) {
                            case m.PLUS:
                                return this.visit(e.left) + this.visit(e.right);
                            case m.MINUS:
                                return this.visit(e.left) - this.visit(e.right);
                            case m.MUL:
                                return this.visit(e.left) * this.visit(e.right);
                            case m.DIV:
                                return this.visit(e.left) / this.visit(e.right)
                        }
                    }
                }, {
                    key: "RefValue",
                    value: function (e) {
                        var t = this.unwrapReference(e),
                            n = e.unit.value,
                            i = e.num.value,
                            r = h.metrics.get(t);
                        switch (n) {
                            case "h":
                                return .01 * i * r.height;
                            case "t":
                                return .01 * i * r.top;
                            case "vh":
                                return .01 * i * o.pageMetrics.windowHeight;
                            case "vw":
                                return .01 * i * o.pageMetrics.windowWidth;
                            case "px":
                                return i;
                            case "w":
                                return .01 * i * r.width;
                            case "b":
                                return .01 * i * r.bottom;
                            case "l":
                                return .01 * i * r.left;
                            case "r":
                                return .01 * i * r.right
                        }
                    }
                }, {
                    key: "PropValue",
                    value: function (e) {
                        var t = null === e.ref ? h.target : h.anchors[e.ref.value];
                        return t[e.propertyName]
                    }
                }, {
                    key: "CSSValue",
                    value: function (t) {
                        var n = this.unwrapReference(t),
                            i = getComputedStyle(n).getPropertyValue(t.propertyName);
                        return "" === i ? 0 : e.Parse(i).execute(h)
                    }
                }, {
                    key: "Num",
                    value: function (e) {
                        return e.value
                    }
                }, {
                    key: "UnaryOp",
                    value: function (e) {
                        return e.op.type === m.PLUS ? +this.visit(e.expr) : e.op.type === m.MINUS ? -this.visit(e.expr) : void 0
                    }
                }, {
                    key: "MathOp",
                    value: function (e) {
                        var t = this,
                            n = e.list.map(function (e) {
                                return t.visit(e)
                            });
                        return u[e.op.value].apply(null, n)
                    }
                }, {
                    key: "unwrapReference",
                    value: function (e) {
                        return null === e.ref ? h.target : (e.ref.value >= h.anchors.length && console.error("Not enough anchors supplied for expression " + this.parser.lexer.text, h.target), h.anchors[e.ref.value])
                    }
                }, {
                    key: "execute",
                    value: function (e) {
                        return h = e, this.visit(this.root)
                    }
                }], [{
                    key: "Parse",
                    value: function (t) {
                        return c[t] || (c[t] = new e(new S(new O(t))))
                    }
                }]), e
            }();
        T.programs = c, t.exports = T
    }, {
        "../Model/AnimSystemModel": 96,
        "@marcom/sm-math-utils": 113
    }],
    104: [function (e, t, n) {
        "use strict";

        function i(e, t) {
            if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }
        var r = function () {
                function e(e, t) {
                    for (var n = 0; n < t.length; n++) {
                        var i = t[n];
                        i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i)
                    }
                }
                return function (t, n, i) {
                    return n && e(t.prototype, n), i && e(t, i), t
                }
            }(),
            s = function () {
                function e(t) {
                    i(this, e), this.group = t
                }
                return r(e, [{
                    key: "parse",
                    value: function (e, t) {
                        if ("number" == typeof t) return t;
                        var n = this.group.expressionParser.parseExpression(e, t);
                        return this.group.convertScrollPositionToTValue(n)
                    }
                }, {
                    key: "destroy",
                    value: function () {
                        this.group = null
                    }
                }]), e
            }();
        t.exports = s
    }, {}],
    105: [function (e, t, n) {
        "use strict";

        function i(e, t) {
            if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }

        function r(e, t) {
            if (!e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
            return !t || "object" != typeof t && "function" != typeof t ? e : t
        }

        function s(e, t) {
            if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function, not " + typeof t);
            e.prototype = Object.create(t && t.prototype, {
                constructor: {
                    value: e,
                    enumerable: !1,
                    writable: !0,
                    configurable: !0
                }
            }), t && (Object.setPrototypeOf ? Object.setPrototypeOf(e, t) : e.__proto__ = t)
        }
        var a = function () {
                function e(e, t) {
                    for (var n = 0; n < t.length; n++) {
                        var i = t[n];
                        i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i)
                    }
                }
                return function (t, n, i) {
                    return n && e(t.prototype, n), i && e(t, i), t
                }
            }(),
            o = function _(e, t, n) {
                null === e && (e = Function.prototype);
                var i = Object.getOwnPropertyDescriptor(e, t);
                if (void 0 === i) {
                    var r = Object.getPrototypeOf(e);
                    return null === r ? void 0 : _(r, t, n)
                }
                if ("value" in i) return i.value;
                var s = i.get;
                if (void 0 !== s) return s.call(n)
            },
            l = e("@marcom/ac-event-emitter-micro").EventEmitterMicro,
            c = e("@marcom/sm-math-utils"),
            u = e("./utils/arrayToObject"),
            h = e("./Model/AnimSystemModel"),
            m = e("./Model/ElementMetricsLookup"),
            d = e("./Parsing/ExpressionParser"),
            f = e("./Parsing/TimeParser"),
            p = e("./Keyframes/KeyframeController"),
            v = {
                create: e("@marcom/ac-raf-emitter/RAFEmitter"),
                update: e("@marcom/ac-raf-emitter/update"),
                draw: e("@marcom/ac-raf-emitter/draw")
            },
            y = function (e) {
                function t(e, n) {
                    i(this, t);
                    var s = r(this, (t.__proto__ || Object.getPrototypeOf(t)).call(this));
                    return s.anim = n, s.element = e, s.name = s.name || e.getAttribute("data-anim-scroll-group"), s.isEnabled = !0, s.position = new h.Progress, s.metrics = new m, s.metrics.add(s.element), s.expressionParser = new d(s), s.timeParser = new f(s), s.boundsMin = 0, s.boundsMax = 0, s.timelineUpdateRequired = !1, s._keyframesDirty = !1, s.viewableRange = s.createViewableRange(), s.defaultEase = h.KeyframeDefaults.ease, s.keyframeControllers = [], s.updateProgress(s.getPosition()), s.onDOMRead = s.onDOMRead.bind(s), s.onDOMWrite = s.onDOMWrite.bind(s), s.gui = null, s.finalizeInit(), s
                }
                return s(t, e), a(t, [{
                    key: "finalizeInit",
                    value: function () {
                        this.element._animInfo = new h.AnimInfo(this, null, (!0)), this.setupRAFEmitter()
                    }
                }, {
                    key: "destroy",
                    value: function () {
                        this.expressionParser.destroy(), this.expressionParser = null, this.timeParser.destroy(), this.timeParser = null;
                        for (var e = 0, n = this.keyframeControllers.length; e < n; e++) this.keyframeControllers[e].destroy();
                        this.keyframeControllers = null, this.position = null, this.viewableRange = null, this.gui && (this.gui.destroy(), this.gui = null), this.metrics.destroy(), this.metrics = null, this.rafEmitter.destroy(), this.rafEmitter = null, this.anim = null, this.element._animInfo && this.element._animInfo.group === this && (this.element._animInfo.group = null, this.element._animInfo = null),
                            this.element = null, this.isEnabled = !1, o(t.prototype.__proto__ || Object.getPrototypeOf(t.prototype), "destroy", this).call(this)
                    }
                }, {
                    key: "removeKeyframeController",
                    value: function (e) {
                        var t = this;
                        if (!this.keyframeControllers.includes(e)) return Promise.resolve();
                        var n = e._allKeyframes;
                        return e._allKeyframes = [], this.keyframesDirty = !0, new Promise(function (i) {
                            v.draw(function () {
                                var r = t.keyframeControllers.indexOf(e);
                                return r === -1 ? void i() : (t.keyframeControllers.splice(r, 1), e.onDOMWrite(), n.forEach(function (e) {
                                    return e.destroy()
                                }), e.destroy(), t.gui && t.gui.create(), void i())
                            }, !0)
                        })
                    }
                }, {
                    key: "remove",
                    value: function () {
                        return this.anim.removeGroup(this)
                    }
                }, {
                    key: "setupRAFEmitter",
                    value: function (e) {
                        var t = this;
                        this.rafEmitter && this.rafEmitter.destroy(), this.rafEmitter = e || new v.create, this.rafEmitter.on("update", this.onDOMRead), this.rafEmitter.on("draw", this.onDOMWrite), this.rafEmitter.once("external", function () {
                            return t.reconcile()
                        })
                    }
                }, {
                    key: "requestDOMChange",
                    value: function () {
                        return !!this.isEnabled && this.rafEmitter.run()
                    }
                }, {
                    key: "onDOMRead",
                    value: function () {
                        this.keyframesDirty && this.onKeyframesDirty();
                        for (var e = 0, t = this.keyframeControllers.length; e < t; e++) this.keyframeControllers[e].onDOMRead(this.position)
                    }
                }, {
                    key: "onDOMWrite",
                    value: function () {
                        for (var e = 0, t = this.keyframeControllers.length; e < t; e++) this.keyframeControllers[e].onDOMWrite(this.position);
                        this.needsUpdate() && this.requestDOMChange()
                    }
                }, {
                    key: "needsUpdate",
                    value: function () {
                        if (this._keyframesDirty) return !0;
                        for (var e = 0, t = this.keyframeControllers.length; e < t; e++)
                            if (this.keyframeControllers[e].needsUpdate()) return !0;
                        return !1
                    }
                }, {
                    key: "addKeyframe",
                    value: function (e, t) {
                        var n = this.getControllerForTarget(e);
                        return null === n && (n = new p(this, e), this.keyframeControllers.push(n)), this.keyframesDirty = !0, n.addKeyframe(t)
                    }
                }, {
                    key: "forceUpdate",
                    value: function () {
                        var e = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : {},
                            t = e.waitForNextUpdate,
                            n = void 0 === t || t,
                            i = e.silent,
                            r = void 0 !== i && i;
                        this.isEnabled && (this.refreshMetrics(), this.timelineUpdateRequired = !0, n ? this.keyframesDirty = !0 : this.onKeyframesDirty({
                            silent: r
                        }))
                    }
                }, {
                    key: "onKeyframesDirty",
                    value: function () {
                        var e = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : {},
                            t = e.silent,
                            n = void 0 !== t && t;
                        this.determineActiveKeyframes(), this.keyframesDirty = !1;
                        for (var i = 0, r = this.keyframeControllers.length; i < r; i++) this.keyframeControllers[i].updateAnimationConstraints();
                        this.updateProgress(this.getPosition()), this.updateBounds(), n || this._onScroll(), this.gui && this.gui.create()
                    }
                }, {
                    key: "refreshMetrics",
                    value: function () {
                        var e = new Set([this.element]);
                        this.keyframeControllers.forEach(function (t) {
                            e.add(t.element), t._allKeyframes.forEach(function (t) {
                                return t.anchors.forEach(function (t) {
                                    return e.add(t)
                                })
                            })
                        }), this.metrics.refreshCollection(e), this.viewableRange = this.createViewableRange()
                    }
                }, {
                    key: "reconcile",
                    value: function () {
                        for (var e = 0, t = this.keyframeControllers.length; e < t; e++) this.keyframeControllers[e].reconcile()
                    }
                }, {
                    key: "determineActiveKeyframes",
                    value: function (e) {
                        e = e || u(Array.from(document.documentElement.classList));
                        for (var t = 0, n = this.keyframeControllers.length; t < n; t++) this.keyframeControllers[t].determineActiveKeyframes(e)
                    }
                }, {
                    key: "updateBounds",
                    value: function () {
                        if (0 === this.keyframeControllers.length) return this.boundsMin = 0, void(this.boundsMax = 0);
                        for (var e = {
                                min: Number.POSITIVE_INFINITY,
                                max: Number.NEGATIVE_INFINITY
                            }, t = 0, n = this.keyframeControllers.length; t < n; t++) this.keyframeControllers[t].getBounds(e);
                        var i = this.convertTValueToScrollPosition(e.min),
                            r = this.convertTValueToScrollPosition(e.max);
                        r - i < h.pageMetrics.windowHeight ? (e.min = this.convertScrollPositionToTValue(i - .5 * h.pageMetrics.windowHeight), e.max = this.convertScrollPositionToTValue(r + .5 * h.pageMetrics.windowHeight)) : (e.min -= .001, e.max += .001), this.boundsMin = e.min, this.boundsMax = e.max, this.timelineUpdateRequired = !0
                    }
                }, {
                    key: "createViewableRange",
                    value: function () {
                        return new h.ViewableRange(this.metrics.get(this.element), h.pageMetrics.windowHeight)
                    }
                }, {
                    key: "_onBreakpointChange",
                    value: function (e, t) {
                        this.keyframesDirty = !0, this.determineActiveKeyframes()
                    }
                }, {
                    key: "updateProgress",
                    value: function (e) {
                        return this.hasDuration() ? (this.position.localUnclamped = (e - this.viewableRange.a) / (this.viewableRange.d - this.viewableRange.a), void(this.position.local = c.clamp(this.position.localUnclamped, this.boundsMin, this.boundsMax))) : void(this.position.local = this.position.localUnclamped = 0)
                    }
                }, {
                    key: "performTimelineDispatch",
                    value: function () {
                        for (var e = 0, t = this.keyframeControllers.length; e < t; e++) this.keyframeControllers[e].updateLocalProgress(this.position.local);
                        this.trigger(h.EVENTS.ON_TIMELINE_UPDATE, this.position.local), this.timelineUpdateRequired = !1, this.position.lastPosition !== this.position.local && (this.position.lastPosition <= this.boundsMin && this.position.localUnclamped > this.boundsMin ? this.trigger(h.EVENTS.ON_TIMELINE_START, this) : this.position.lastPosition >= this.boundsMin && this.position.localUnclamped < this.boundsMin ? this.trigger(h.EVENTS.ON_TIMELINE_START + ":reverse", this) : this.position.lastPosition <= this.boundsMax && this.position.localUnclamped >= this.boundsMax ? this.trigger(h.EVENTS.ON_TIMELINE_COMPLETE, this) : this.position.lastPosition >= this.boundsMax && this.position.localUnclamped < this.boundsMax && this.trigger(h.EVENTS.ON_TIMELINE_COMPLETE + ":reverse", this)), null !== this.gui && this.gui.onScrollUpdate(this.position)
                    }
                }, {
                    key: "_onScroll",
                    value: function (e) {
                        if (!this.isEnabled) return !1;
                        void 0 === e && (e = this.getPosition()), this.updateProgress(e);
                        var t = this.position.lastPosition === this.boundsMin || this.position.lastPosition === this.boundsMax,
                            n = this.position.localUnclamped === this.boundsMin || this.position.localUnclamped === this.boundsMax;
                        if (!this.timelineUpdateRequired && t && n && this.position.lastPosition === e) return void(this.position.local = this.position.localUnclamped);
                        if (this.timelineUpdateRequired || this.position.localUnclamped > this.boundsMin && this.position.localUnclamped < this.boundsMax) return this.performTimelineDispatch(), this.requestDOMChange(), void(this.position.lastPosition = this.position.localUnclamped);
                        var i = this.position.lastPosition > this.boundsMin && this.position.lastPosition < this.boundsMax,
                            r = this.position.localUnclamped <= this.boundsMin || this.position.localUnclamped >= this.boundsMax;
                        if (i && r) return this.performTimelineDispatch(), this.requestDOMChange(), void(this.position.lastPosition = this.position.localUnclamped);
                        var s = this.position.lastPosition < this.boundsMin && this.position.localUnclamped > this.boundsMax,
                            a = this.position.lastPosition > this.boundsMax && this.position.localUnclamped < this.boundsMax;
                        (s || a) && (this.performTimelineDispatch(), this.requestDOMChange(), this.position.lastPosition = this.position.localUnclamped), null !== this.gui && this.gui.onScrollUpdate(this.position)
                    }
                }, {
                    key: "convertScrollPositionToTValue",
                    value: function (e) {
                        return this.hasDuration() ? c.map(e, this.viewableRange.a, this.viewableRange.d, 0, 1) : 0
                    }
                }, {
                    key: "convertTValueToScrollPosition",
                    value: function (e) {
                        return this.hasDuration() ? c.map(e, 0, 1, this.viewableRange.a, this.viewableRange.d) : 0
                    }
                }, {
                    key: "hasDuration",
                    value: function () {
                        return this.viewableRange.a !== this.viewableRange.d
                    }
                }, {
                    key: "getPosition",
                    value: function () {
                        return h.pageMetrics.scrollY
                    }
                }, {
                    key: "getControllerForTarget",
                    value: function (e) {
                        if (!e._animInfo || !e._animInfo.controllers) return null;
                        if (e._animInfo.controller && e._animInfo.controller.group === this) return e._animInfo.controller;
                        for (var t = e._animInfo.controllers, n = 0, i = t.length; n < i; n++)
                            if (t[n].group === this) return t[n];
                        return null
                    }
                }, {
                    key: "trigger",
                    value: function (e, t) {
                        if ("undefined" != typeof this._events[e])
                            for (var n = this._events[e].length - 1; n >= 0; n--) void 0 !== t ? this._events[e][n](t) : this._events[e][n]()
                    }
                }, {
                    key: "keyframesDirty",
                    set: function (e) {
                        this._keyframesDirty = e, this._keyframesDirty && this.requestDOMChange()
                    },
                    get: function () {
                        return this._keyframesDirty
                    }
                }, {
                    key: "keyFrames",
                    get: function () {
                        return this.viewableRange
                    }
                }]), t
            }(l);
        t.exports = y
    }, {
        "./Keyframes/KeyframeController": 94,
        "./Model/AnimSystemModel": 96,
        "./Model/ElementMetricsLookup": 98,
        "./Parsing/ExpressionParser": 102,
        "./Parsing/TimeParser": 104,
        "./utils/arrayToObject": 107,
        "@marcom/ac-event-emitter-micro": 35,
        "@marcom/ac-raf-emitter/RAFEmitter": 72,
        "@marcom/ac-raf-emitter/draw": 81,
        "@marcom/ac-raf-emitter/update": 85,
        "@marcom/sm-math-utils": 113
    }],
    106: [function (e, t, n) {
        "use strict";

        function i(e, t) {
            if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }

        function r(e, t) {
            if (!e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
            return !t || "object" != typeof t && "function" != typeof t ? e : t
        }

        function s(e, t) {
            if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function, not " + typeof t);
            e.prototype = Object.create(t && t.prototype, {
                constructor: {
                    value: e,
                    enumerable: !1,
                    writable: !0,
                    configurable: !0
                }
            }), t && (Object.setPrototypeOf ? Object.setPrototypeOf(e, t) : e.__proto__ = t)
        }
        var a = function () {
                function e(e, t) {
                    for (var n = 0; n < t.length; n++) {
                        var i = t[n];
                        i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i)
                    }
                }
                return function (t, n, i) {
                    return n && e(t.prototype, n), i && e(t, i), t
                }
            }(),
            o = function d(e, t, n) {
                null === e && (e = Function.prototype);
                var i = Object.getOwnPropertyDescriptor(e, t);
                if (void 0 === i) {
                    var r = Object.getPrototypeOf(e);
                    return null === r ? void 0 : d(r, t, n)
                }
                if ("value" in i) return i.value;
                var s = i.get;
                if (void 0 !== s) return s.call(n)
            },
            l = e("./ScrollGroup"),
            c = e("@marcom/sm-math-utils"),
            u = 0,
            h = {
                create: e("@marcom/ac-raf-emitter/RAFEmitter")
            },
            m = function (e) {
                function t(e, n) {
                    i(this, t), e || (e = document.createElement("div"), e.className = "TimeGroup-" + u++);
                    var s = r(this, (t.__proto__ || Object.getPrototypeOf(t)).call(this, e, n));
                    return s.name = s.name || e.getAttribute("data-anim-time-group"), s._isPaused = !0, s._repeats = 0, s._isReversed = !1, s._timeScale = 1, s
                }
                return s(t, e), a(t, [{
                    key: "finalizeInit",
                    value: function () {
                        if (!this.anim) throw "TimeGroup not instantiated correctly. Please use `AnimSystem.createTimeGroup(el)`";
                        this.defaultEase = 1, this.onPlayTimeUpdate = this.onPlayTimeUpdate.bind(this), o(t.prototype.__proto__ || Object.getPrototypeOf(t.prototype), "finalizeInit", this).call(this)
                    }
                }, {
                    key: "progress",
                    value: function (e) {
                        if (void 0 === e) return 0 === this.boundsMax ? 0 : this.position.local / this.boundsMax;
                        var t = e * this.boundsMax;
                        this.timelineUpdateRequired = !0, this._onScroll(t)
                    }
                }, {
                    key: "time",
                    value: function (e) {
                        return void 0 === e ? this.position.local : (e = c.clamp(e, this.boundsMin, this.boundsMax), this.timelineUpdateRequired = !0, void this._onScroll(e))
                    }
                }, {
                    key: "play",
                    value: function (e) {
                        this.reversed(!1), this.isEnabled = !0, this._isPaused = !1, this.time(e), this._playheadEmitter.run()
                    }
                }, {
                    key: "reverse",
                    value: function (e) {
                        this.reversed(!0), this.isEnabled = !0, this._isPaused = !1, this.time(e), this._playheadEmitter.run()
                    }
                }, {
                    key: "reversed",
                    value: function (e) {
                        return void 0 === e ? this._isReversed : void(this._isReversed = e)
                    }
                }, {
                    key: "restart",
                    value: function () {
                        this._isReversed ? (this.progress(1), this.reverse(this.time())) : (this.progress(0), this.play(this.time()))
                    }
                }, {
                    key: "pause",
                    value: function (e) {
                        this.time(e), this._isPaused = !0
                    }
                }, {
                    key: "paused",
                    value: function (e) {
                        return void 0 === e ? this._isPaused : (this._isPaused = e, this._isPaused || this.play(), this)
                    }
                }, {
                    key: "onPlayTimeUpdate",
                    value: function (e) {
                        if (!this._isPaused) {
                            var n = c.clamp(e.delta / 1e3, 0, .5);
                            this._isReversed && (n = -n);
                            var i = this.time(),
                                r = i + n * this._timeScale;
                            if (this._repeats === t.REPEAT_FOREVER || this._repeats > 0) {
                                var s = !1;
                                !this._isReversed && r > this.boundsMax ? (r -= this.boundsMax, s = !0) : this._isReversed && r < 0 && (r = this.boundsMax + r, s = !0), s && (this._repeats = this._repeats === t.REPEAT_FOREVER ? t.REPEAT_FOREVER : this._repeats - 1)
                            }
                            this.time(r);
                            var a = !this._isReversed && this.position.local !== this.duration,
                                o = this._isReversed && 0 !== this.position.local;
                            a || o ? this._playheadEmitter.run() : this.paused(!0)
                        }
                    }
                }, {
                    key: "updateProgress",
                    value: function (e) {
                        return this.hasDuration() ? (this.position.localUnclamped = e, void(this.position.local = c.clamp(this.position.localUnclamped, this.boundsMin, this.boundsMax))) : void(this.position.local = this.position.localUnclamped = 0)
                    }
                }, {
                    key: "updateBounds",
                    value: function () {
                        if (0 === this.keyframeControllers.length) return this.boundsMin = 0, void(this.boundsMax = 0);
                        for (var e = {
                                min: Number.POSITIVE_INFINITY,
                                max: Number.NEGATIVE_INFINITY
                            }, t = 0, n = this.keyframeControllers.length; t < n; t++) this.keyframeControllers[t].getBounds(e);
                        this.boundsMin = 0, this.boundsMax = e.max, this.viewableRange.a = this.viewableRange.b = 0, this.viewableRange.c = this.viewableRange.d = this.boundsMax, this.timelineUpdateRequired = !0
                    }
                }, {
                    key: "setupRAFEmitter",
                    value: function (e) {
                        this._playheadEmitter = new h.create, this._playheadEmitter.on("update", this.onPlayTimeUpdate), o(t.prototype.__proto__ || Object.getPrototypeOf(t.prototype), "setupRAFEmitter", this).call(this, e)
                    }
                }, {
                    key: "needsUpdate",
                    value: function () {
                        return !0
                    }
                }, {
                    key: "timeScale",
                    value: function (e) {
                        return void 0 === e ? this._timeScale : (this._timeScale = e, this)
                    }
                }, {
                    key: "repeats",
                    value: function (e) {
                        return void 0 === e ? this._repeats : void(this._repeats = e)
                    }
                }, {
                    key: "getPosition",
                    value: function () {
                        return this.position.local
                    }
                }, {
                    key: "convertScrollPositionToTValue",
                    value: function (e) {
                        return e
                    }
                }, {
                    key: "convertTValueToScrollPosition",
                    value: function (e) {
                        return e
                    }
                }, {
                    key: "hasDuration",
                    value: function () {
                        return this.duration > 0
                    }
                }, {
                    key: "destroy",
                    value: function () {
                        this._playheadEmitter.destroy(), this._playheadEmitter = null, o(t.prototype.__proto__ || Object.getPrototypeOf(t.prototype), "destroy", this).call(this)
                    }
                }, {
                    key: "duration",
                    get: function () {
                        return this.keyframesDirty && this.onKeyframesDirty({
                            silent: !0
                        }), this.boundsMax
                    }
                }]), t
            }(l);
        m.REPEAT_FOREVER = -1, t.exports = m
    }, {
        "./ScrollGroup": 105,
        "@marcom/ac-raf-emitter/RAFEmitter": 72,
        "@marcom/sm-math-utils": 113
    }],
    107: [function (e, t, n) {
        "use strict";
        var i = function (e) {
            return e.reduce(function (e, t) {
                return e[t] = t, e
            }, {})
        };
        t.exports = i
    }, {}],
    108: [function (e, t, n) {
        "use strict";

        function i(e, t) {
            if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }

        function r(e, t) {
            if (!e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
            return !t || "object" != typeof t && "function" != typeof t ? e : t
        }

        function s(e, t) {
            if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function, not " + typeof t);
            e.prototype = Object.create(t && t.prototype, {
                constructor: {
                    value: e,
                    enumerable: !1,
                    writable: !0,
                    configurable: !0
                }
            }), t && (Object.setPrototypeOf ? Object.setPrototypeOf(e, t) : e.__proto__ = t)
        }
        var a = function () {
                function e(e, t) {
                    for (var n = 0; n < t.length; n++) {
                        var i = t[n];
                        i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i)
                    }
                }
                return function (t, n, i) {
                    return n && e(t.prototype, n), i && e(t, i), t
                }
            }(),
            o = function f(e, t, n) {
                null === e && (e = Function.prototype);
                var i = Object.getOwnPropertyDescriptor(e, t);
                if (void 0 === i) {
                    var r = Object.getPrototypeOf(e);
                    return null === r ? void 0 : f(r, t, n)
                }
                if ("value" in i) return i.value;
                var s = i.get;
                if (void 0 !== s) return s.call(n)
            },
            l = e("@marcom/ac-event-emitter-micro").EventEmitterMicro,
            c = e("@marcom/anim-system/Model/AnimSystemModel"),
            u = {
                create: e("@marcom/ac-raf-emitter/RAFEmitter"),
                update: e("@marcom/ac-raf-emitter/update"),
                draw: e("@marcom/ac-raf-emitter/draw")
            },
            h = function () {},
            m = 0,
            d = function (e) {
                function t(e) {
                    i(this, t);
                    var n = r(this, (t.__proto__ || Object.getPrototypeOf(t)).call(this));
                    return n.el = e.el, n.gum = e.gum, n.componentName = e.componentName, n._keyframeController = null, n
                }
                return s(t, e), a(t, [{
                    key: "destroy",
                    value: function () {
                        this.el = null, this.gum = null, this._keyframeController = null, o(t.prototype.__proto__ || Object.getPrototypeOf(t.prototype), "destroy", this).call(this)
                    }
                }, {
                    key: "addKeyframe",
                    value: function (e) {
                        var t = e.el || this.el;
                        return (e.group || this.anim).addKeyframe(t, e)
                    }
                }, {
                    key: "addDiscreteEvent",
                    value: function (e) {
                        e.event = e.event || "Generic-Event-Name-" + m++;
                        var t = void 0 !== e.end && e.end !== e.start,
                            n = this.addKeyframe(e);
                        return t ? (e.onEnterOnce && n.controller.once(e.event + ":enter", e.onEnterOnce), e.onExitOnce && n.controller.once(e.event + ":exit", e.onExitOnce), e.onEnter && n.controller.on(e.event + ":enter", e.onEnter), e.onExit && n.controller.on(e.event + ":exit", e.onExit)) : (e.onEventOnce && n.controller.once(e.event, e.onEventOnce), e.onEventReverseOnce && n.controller.once(e.event + ":reverse", e.onEventReverseOnce), e.onEvent && n.controller.on(e.event, e.onEvent), e.onEventReverse && n.controller.on(e.event + ":reverse", e.onEventReverse)), n
                    }
                }, {
                    key: "addRAFLoop",
                    value: function (e) {
                        var t = ["start", "end"];
                        if (!t.every(function (t) {
                                return e.hasOwnProperty(t)
                            })) return void console.log("BubbleGum.BaseComponent::addRAFLoop required options are missing: " + t.join(" "));
                        var n = new u.create;
                        n.on("update", e.onUpdate || h), n.on("draw", e.onDraw || h), n.on("draw", function () {
                            return n.run()
                        });
                        var i = e.onEnter,
                            r = e.onExit;
                        return e.onEnter = function () {
                            n.run(), i ? i() : 0
                        }, e.onExit = function () {
                            n.cancel(), r ? r() : 0
                        }, this.addDiscreteEvent(e)
                    }
                }, {
                    key: "addContinuousEvent",
                    value: function (e) {
                        e.onDraw || console.log("BubbleGum.BaseComponent::addContinuousEvent required option `onDraw` is missing. Consider using a regular keyframe if you do not need a callback"), e.event = e.event || "Generic-Event-Name-" + m++;
                        var t = this.addKeyframe(e);
                        return t.controller.on(e.event, e.onDraw), t
                    }
                }, {
                    key: "mounted",
                    value: function () {}
                }, {
                    key: "onResizeImmediate",
                    value: function (e) {}
                }, {
                    key: "onResizeDebounced",
                    value: function (e) {}
                }, {
                    key: "onBreakpointChange",
                    value: function (e) {}
                }, {
                    key: "anim",
                    get: function () {
                        return this.gum.anim
                    }
                }, {
                    key: "keyframeController",
                    get: function () {
                        return this._keyframeController || (this._keyframeController = this.anim.getControllerForTarget(this.el))
                    }
                }, {
                    key: "pageMetrics",
                    get: function () {
                        return c.pageMetrics
                    }
                }]), t
            }(l);
        t.exports = d
    }, {
        "@marcom/ac-event-emitter-micro": 35,
        "@marcom/ac-raf-emitter/RAFEmitter": 72,
        "@marcom/ac-raf-emitter/draw": 81,
        "@marcom/ac-raf-emitter/update": 85,
        "@marcom/anim-system/Model/AnimSystemModel": 96
    }],
    109: [function (e, t, n) {
        "use strict";

        function i(e, t) {
            if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }

        function r(e, t) {
            if (!e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
            return !t || "object" != typeof t && "function" != typeof t ? e : t
        }

        function s(e, t) {
            if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function, not " + typeof t);
            e.prototype = Object.create(t && t.prototype, {
                constructor: {
                    value: e,
                    enumerable: !1,
                    writable: !0,
                    configurable: !0
                }
            }), t && (Object.setPrototypeOf ? Object.setPrototypeOf(e, t) : e.__proto__ = t)
        }
        var a = function () {
                function e(e, t) {
                    for (var n = 0; n < t.length; n++) {
                        var i = t[n];
                        i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i)
                    }
                }
                return function (t, n, i) {
                    return n && e(t.prototype, n), i && e(t, i), t
                }
            }(),
            o = e("@marcom/ac-event-emitter-micro").EventEmitterMicro,
            l = e("@marcom/delayed-initializer"),
            c = e("@marcom/anim-system"),
            u = e("@marcom/anim-system/Model/AnimSystemModel"),
            h = e("./ComponentMap"),
            m = {},
            d = function (e) {
                function t(e) {
                    i(this, t);
                    var n = r(this, (t.__proto__ || Object.getPrototypeOf(t)).call(this));
                    return n.el = e, n.anim = c, n.components = [], n.el.getAttribute("data-anim-scroll-group") || n.el.setAttribute("data-anim-scroll-group", "bubble-gum-group"), c.on(u.EVENTS.ON_DOM_GROUPS_CREATED, function (e) {
                        n.componentsInitialized = !1, n.initComponents(), n.setupEvents()
                    }), c.on(u.EVENTS.ON_DOM_KEYFRAMES_CREATED, function () {
                        n.components.forEach(function (e) {
                            return e.mounted()
                        }), n.trigger(t.EVENTS.DOM_COMPONENTS_MOUNTED)
                    }), l.add(function () {
                        return c.initialize()
                    }), n
                }
                return s(t, e), a(t, [{
                    key: "initComponents",
                    value: function () {
                        var e = Array.prototype.slice.call(this.el.querySelectorAll("[data-component-list]"));
                        this.el.hasAttribute("data-component-list") && e.push(this.el);
                        for (var t = 0; t < e.length; t++)
                            for (var n = e[t], i = n.getAttribute("data-component-list"), r = i.split(" "), s = 0, a = r.length; s < a; s++) {
                                var o = r[s];
                                "" !== o && " " !== o && this.addComponent({
                                    el: n,
                                    componentName: o
                                })
                            }
                        this.componentsInitialized = !0
                    }
                }, {
                    key: "setupEvents",
                    value: function () {
                        this.onResizeDebounced = this.onResizeDebounced.bind(this), this.onResizeImmediate = this.onResizeImmediate.bind(this), this.onBreakpointChange = this.onBreakpointChange.bind(this), c.on(u.PageEvents.ON_RESIZE_IMMEDIATE, this.onResizeImmediate), c.on(u.PageEvents.ON_RESIZE_DEBOUNCED, this.onResizeDebounced), c.on(u.PageEvents.ON_BREAKPOINT_CHANGE, this.onBreakpointChange)
                    }
                }, {
                    key: "addComponent",
                    value: function (e) {
                        var n = e.el,
                            i = e.componentName,
                            r = e.data;
                        if (!h.hasOwnProperty(i)) throw "BubbleGum::addComponent could not add component to '" + n.className + "'. No component type '" + i + "' found!";
                        var s = h[i];
                        if (!t.componentIsSupported(s, i)) return void 0 === m[i] && (console.log("BubbleGum::addComponent unsupported component '" + i + "'. Reason: '" + i + ".IS_SUPPORTED' returned false"), m[i] = !0), null;
                        var a = n.dataset.componentList || "";
                        a.includes(i) || (n.dataset.componentList = a.split(" ").concat(i).join(" "));
                        var o = new s({
                            el: n,
                            data: r,
                            componentName: e.componentName,
                            gum: this,
                            pageMetrics: u.pageMetrics
                        });
                        return this.components.push(o), this.componentsInitialized && o.mounted(), o
                    }
                }, {
                    key: "removeComponent",
                    value: function (e) {
                        var t = this.components.indexOf(e);
                        t !== -1 && (this.components.splice(t, 1), e.el.dataset.componentList = e.el.dataset.componentList.split(" ").filter(function (t) {
                            return t !== e.componentName
                        }).join(" "), e.destroy())
                    }
                }, {
                    key: "getComponentOfType",
                    value: function (e) {
                        var t = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : document.documentElement,
                            n = "[data-component-list*=" + e + "]",
                            i = t.matches(n) ? t : t.querySelector(n);
                        return i ? this.components.find(function (t) {
                            return t instanceof h[e] && t.el === i
                        }) : null
                    }
                }, {
                    key: "getComponentsOfType",
                    value: function (e) {
                        var t = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : document.documentElement,
                            n = "[data-component-list*=" + e + "]",
                            i = t.matches(n) ? [t] : Array.from(t.querySelectorAll(n));
                        return this.components.filter(function (t) {
                            return t instanceof h[e] && i.includes(t.el)
                        })
                    }
                }, {
                    key: "getComponentsForElement",
                    value: function (e) {
                        return this.components.filter(function (t) {
                            return t.el === e
                        })
                    }
                }, {
                    key: "onResizeImmediate",
                    value: function () {
                        this.components.forEach(function (e) {
                            return e.onResizeImmediate(u.pageMetrics)
                        })
                    }
                }, {
                    key: "onResizeDebounced",
                    value: function () {
                        this.components.forEach(function (e) {
                            return e.onResizeDebounced(u.pageMetrics)
                        })
                    }
                }, {
                    key: "onBreakpointChange",
                    value: function () {
                        this.components.forEach(function (e) {
                            return e.onBreakpointChange(u.pageMetrics)
                        })
                    }
                }], [{
                    key: "componentIsSupported",
                    value: function (e, t) {
                        var n = e.IS_SUPPORTED;
                        if (void 0 === n) return !0;
                        if ("function" != typeof n) return console.error('BubbleGum::addComponent error in "' + t + '".IS_SUPPORTED - it should be a function which returns true/false'), !0;
                        var i = e.IS_SUPPORTED();
                        return void 0 === i ? (console.error('BubbleGum::addComponent error in "' + t + '".IS_SUPPORTED - it should be a function which returns true/false'), !0) : i
                    }
                }]), t
            }(o);
        d.EVENTS = {
            DOM_COMPONENTS_MOUNTED: "DOM_COMPONENTS_MOUNTED"
        }, t.exports = d
    }, {
        "./ComponentMap": 110,
        "@marcom/ac-event-emitter-micro": 35,
        "@marcom/anim-system": 91,
        "@marcom/anim-system/Model/AnimSystemModel": 96,
        "@marcom/delayed-initializer": 112
    }],
    110: [function (e, t, n) {
        "use strict";
        t.exports = {
            BaseComponent: e("./BaseComponent")
        }
    }, {
        "./BaseComponent": 108
    }],
    111: [function (e, t, n) {
        "use strict";
        var i = {
                create: e("gl-mat4/create"),
                invert: e("gl-mat4/invert"),
                clone: e("gl-mat4/clone"),
                transpose: e("gl-mat4/transpose")
            },
            r = {
                create: e("gl-vec3/create"),
                dot: e("gl-vec3/dot"),
                normalize: e("gl-vec3/normalize"),
                length: e("gl-vec3/length"),
                cross: e("gl-vec3/cross"),
                fromValues: e("gl-vec3/fromValues")
            },
            s = {
                create: e("gl-vec4/create"),
                transformMat4: e("gl-vec4/transformMat4"),
                fromValues: e("gl-vec4/fromValues")
            },
            a = (Math.PI / 180, 180 / Math.PI),
            o = 0,
            l = 1,
            c = 3,
            u = 4,
            h = 5,
            m = 7,
            d = 11,
            f = 12,
            p = 13,
            v = 15,
            y = function (e, t) {
                t = t || !1;
                for (var n = i.clone(e), o = r.create(), l = r.create(), u = r.create(), h = s.create(), f = s.create(), p = (r.create(), 0); p < 16; p++) n[p] /= n[v];
                var y = i.clone(n);
                y[c] = 0, y[m] = 0, y[d] = 0, y[v] = 1;
                var E = (n[3], n[7], n[11], n[12]),
                    w = n[13],
                    A = n[14],
                    O = (n[15], s.create());
                if (g(n[c]) && g(n[m]) && g(n[d])) h = s.fromValues(0, 0, 0, 1);
                else {
                    O[0] = n[c], O[1] = n[m], O[2] = n[d], O[3] = n[v];
                    var S = i.invert(i.create(), y),
                        T = i.transpose(i.create(), S);
                    h = s.transformMat4(h, O, T)
                }
                o[0] = E, o[1] = w, o[2] = A;
                var k = [r.create(), r.create(), r.create()];
                k[0][0] = n[0], k[0][1] = n[1], k[0][2] = n[2], k[1][0] = n[4], k[1][1] = n[5], k[1][2] = n[6], k[2][0] = n[8], k[2][1] = n[9], k[2][2] = n[10], l[0] = r.length(k[0]), r.normalize(k[0], k[0]), u[0] = r.dot(k[0], k[1]), k[1] = _(k[1], k[0], 1, -u[0]), l[1] = r.length(k[1]), r.normalize(k[1], k[1]), u[0] /= l[1], u[1] = r.dot(k[0], k[2]), k[2] = _(k[2], k[0], 1, -u[1]), u[2] = r.dot(k[1], k[2]), k[2] = _(k[2], k[1], 1, -u[2]), l[2] = r.length(k[2]), r.normalize(k[2], k[2]), u[1] /= l[2], u[2] /= l[2];
                var P = r.cross(r.create(), k[1], k[2]);
                if (r.dot(k[0], P) < 0)
                    for (p = 0; p < 3; p++) l[p] *= -1, k[p][0] *= -1, k[p][1] *= -1, k[p][2] *= -1;
                f[0] = .5 * Math.sqrt(Math.max(1 + k[0][0] - k[1][1] - k[2][2], 0)), f[1] = .5 * Math.sqrt(Math.max(1 - k[0][0] + k[1][1] - k[2][2], 0)), f[2] = .5 * Math.sqrt(Math.max(1 - k[0][0] - k[1][1] + k[2][2], 0)), f[3] = .5 * Math.sqrt(Math.max(1 + k[0][0] + k[1][1] + k[2][2], 0)), k[2][1] > k[1][2] && (f[0] = -f[0]), k[0][2] > k[2][0] && (f[1] = -f[1]), k[1][0] > k[0][1] && (f[2] = -f[2]);
                var C = s.fromValues(f[0], f[1], f[2], 2 * Math.acos(f[3])),
                    x = b(f);
                return t && (u[0] = Math.round(u[0] * a * 100) / 100, u[1] = Math.round(u[1] * a * 100) / 100, u[2] = Math.round(u[2] * a * 100) / 100, x[0] = Math.round(x[0] * a * 100) / 100, x[1] = Math.round(x[1] * a * 100) / 100, x[2] = Math.round(x[2] * a * 100) / 100, C[3] = Math.round(C[3] * a * 100) / 100), {
                    translation: o,
                    scale: l,
                    skew: u,
                    perspective: h,
                    quaternion: f,
                    eulerRotation: x,
                    axisAngle: C
                }
            },
            _ = function (e, t, n, i) {
                var s = r.create();
                return s[0] = n * e[0] + i * t[0], s[1] = n * e[1] + i * t[1], s[2] = n * e[2] + i * t[2], s
            },
            b = function (e) {
                var t, n, i, s = e[3] * e[3],
                    a = e[0] * e[0],
                    o = e[1] * e[1],
                    l = e[2] * e[2],
                    c = a + o + l + s,
                    u = e[0] * e[1] + e[2] * e[3];
                return u > .499 * c ? (n = 2 * Math.atan2(e[0], e[3]), i = Math.PI / 2, t = 0, r.fromValues(t, n, i)) : u < -.499 * c ? (n = -2 * Math.atan2(e[0], e[3]), i = -Math.PI / 2, t = 0, r.fromValues(t, n, i)) : (n = Math.atan2(2 * e[1] * e[3] - 2 * e[0] * e[2], a - o - l + s), i = Math.asin(2 * u / c), t = Math.atan2(2 * e[0] * e[3] - 2 * e[1] * e[2], -a + o - l + s), r.fromValues(t, n, i))
            },
            g = function (e) {
                return Math.abs(e) < 1e-4
            },
            E = function (e) {
                var t = String(getComputedStyle(e).transform).trim(),
                    n = i.create();
                if ("none" === t || "" === t) return n;
                var r, s, a = t.slice(0, t.indexOf("("));
                if ("matrix3d" === a)
                    for (r = t.slice(9, -1).split(","), s = 0; s < r.length; s++) n[s] = parseFloat(r[s]);
                else {
                    if ("matrix" !== a) throw new TypeError("Invalid Matrix Value");
                    for (r = t.slice(7, -1).split(","), s = r.length; s--;) r[s] = parseFloat(r[s]);
                    n[o] = r[0], n[l] = r[1], n[f] = r[4], n[u] = r[2], n[h] = r[3], n[p] = r[5]
                }
                return n
            };
        t.exports = function (e, t) {
            var n = E(e);
            return y(n, t)
        }
    }, {
        "gl-mat4/clone": 116,
        "gl-mat4/create": 117,
        "gl-mat4/invert": 118,
        "gl-mat4/transpose": 123,
        "gl-vec3/create": 124,
        "gl-vec3/cross": 125,
        "gl-vec3/dot": 126,
        "gl-vec3/fromValues": 127,
        "gl-vec3/length": 128,
        "gl-vec3/normalize": 129,
        "gl-vec4/create": 130,
        "gl-vec4/fromValues": 131,
        "gl-vec4/transformMat4": 132
    }],
    112: [function (e, t, n) {
        "use strict";
        var i = !1,
            r = !1,
            s = [];
        t.exports = {
            NUMBER_OF_FRAMES_TO_WAIT: 30,
            add: function (e) {
                var t = this;
                if (r && e(), s.push(e), !i) {
                    i = !0;
                    var n = document.documentElement.scrollHeight,
                        a = 0,
                        o = function l() {
                            var e = document.documentElement.scrollHeight;
                            if (n !== e) a = 0;
                            else if (a++, a >= t.NUMBER_OF_FRAMES_TO_WAIT) return void s.forEach(function (e) {
                                return e()
                            });
                            n = e, requestAnimationFrame(l)
                        };
                    requestAnimationFrame(o)
                }
            }
        }
    }, {}],
    113: [function (e, t, n) {
        "use strict";
        t.exports = {
            lerp: function (e, t, n) {
                return t + (n - t) * e
            },
            map: function (e, t, n, i, r) {
                return i + (r - i) * (e - t) / (n - t)
            },
            mapClamp: function (e, t, n, i, r) {
                var s = i + (r - i) * (e - t) / (n - t);
                return Math.max(i, Math.min(r, s))
            },
            norm: function (e, t, n) {
                return (e - t) / (n - t)
            },
            clamp: function (e, t, n) {
                return Math.max(t, Math.min(n, e))
            },
            randFloat: function (e, t) {
                return Math.random() * (t - e) + e
            },
            randInt: function (e, t) {
                return Math.floor(Math.random() * (t - e) + e)
            }
        }
    }, {}],
    114: [function (e, t, n) {
        "use strict";

        function i(e, t) {
            r.call(this), this._id = e || a.ID, this._options = Object.assign({}, a.OPTIONS, t), this._allowDOMEventDispatch = !1, this._allowElementStateData = !1, this._options.removeNamespace = "boolean" != typeof this._options.removeNamespace || this._options.removeNamespace, this._el = this._initViewportEl(this._id), this._resizing = !1, this._mediaQueryLists = {
                resolution: {
                    retina: window.matchMedia(c.RETINA)
                },
                orientation: {
                    portrait: window.matchMedia(c.PORTRAIT),
                    landscape: window.matchMedia(c.LANDSCAPE)
                }
            }, this._viewport = this._getViewport(this._options.removeNamespace), this._retina = this._getRetina(this._mediaQueryLists.resolution.retina), this._orientation = this._initOrientation(), this._addListeners(), this._updateElementStateData()
        }
        var r = e("@marcom/ac-event-emitter-micro").EventEmitterMicro,
            s = e("@marcom/ac-raf-emitter/update"),
            a = {
                ID: "viewport-emitter",
                OPTIONS: {
                    removeNamespace: !0
                }
            },
            o = {
                DOM_DISPATCH: "data-viewport-emitter-dispatch",
                STATE: "data-viewport-emitter-state"
            },
            l = "::before",
            c = {
                RETINA: "only screen and (-webkit-min-device-pixel-ratio: 1.5), screen and (min-resolution: 1.5dppx), screen and (min-resolution: 144dpi)",
                PORTRAIT: "only screen and (orientation: portrait)",
                LANDSCAPE: "only screen and (orientation: landscape)"
            },
            u = {
                any: "change:any",
                orientation: "change:orientation",
                retina: "change:retina",
                viewport: "change:viewport"
            };
        Object.defineProperty(i, "DOM_DISPATCH_ATTRIBUTE", {
            get: function () {
                return o.DOM_DISPATCH
            }
        }), Object.defineProperty(i, "DOM_STATE_ATTRIBUTE", {
            get: function () {
                return o.STATE
            }
        });
        var h = i.prototype = Object.create(r.prototype);
        Object.defineProperty(h, "id", {
            get: function () {
                return this._id
            }
        }), Object.defineProperty(h, "element", {
            get: function () {
                return this._el
            }
        }), Object.defineProperty(h, "mediaQueryLists", {
            get: function () {
                return this._mediaQueryLists
            }
        }), Object.defineProperty(h, "viewport", {
            get: function () {
                return this._viewport
            }
        }), Object.defineProperty(h, "retina", {
            get: function () {
                return this._retina
            }
        }), Object.defineProperty(h, "orientation", {
            get: function () {
                return this._orientation
            }
        }), Object.defineProperty(h, "hasDomDispatch", {
            get: function () {
                return this._allowDOMEventDispatch
            }
        }), h.destroy = function () {
            this._removeListeners();
            for (var e in this._options) this._options[e] = null;
            for (var t in this._mediaQueryLists) {
                var n = this._mediaQueryLists[t];
                for (var i in n) n[i] = null
            }
            this._id = null, this._el = null, this._viewport = null, this._retina = null, this._orientation = null, r.prototype.destroy.call(this)
        }, h._initViewportEl = function (e) {
            var t = document.getElementById(e);
            return t || (t = document.createElement("div"), t.id = e, t = document.body.appendChild(t)), t.hasAttribute(o.DOM_DISPATCH) || (t.setAttribute(o.DOM_DISPATCH, ""), this._allowDOMEventDispatch = !0), t.hasAttribute(o.STATE) || (this._allowElementStateData = !0), t
        }, h._dispatch = function (e, t) {
            var n = {
                viewport: this._viewport,
                orientation: this._orientation,
                retina: this._retina
            };
            if (this._allowDOMEventDispatch) {
                var i = new CustomEvent(e, {
                        detail: t
                    }),
                    r = new CustomEvent(u.any, {
                        detail: n
                    });
                this._el.dispatchEvent(i), this._el.dispatchEvent(r)
            }
            this.trigger(e, t), this.trigger(u.any, n)
        }, h._addListeners = function () {
            this._onOrientationChange = this._onOrientationChange.bind(this), this._onRetinaChange = this._onRetinaChange.bind(this), this._onViewportChange = this._onViewportChange.bind(this), this._onViewportChangeUpdate = this._onViewportChangeUpdate.bind(this), this._mediaQueryLists.orientation.portrait.addListener(this._onOrientationChange), this._mediaQueryLists.orientation.landscape.addListener(this._onOrientationChange), this._mediaQueryLists.resolution.retina.addListener(this._onRetinaChange), window.addEventListener("resize", this._onViewportChange)
        }, h._removeListeners = function () {
            this._mediaQueryLists.orientation.portrait.removeListener(this._onOrientationChange), this._mediaQueryLists.orientation.landscape.removeListener(this._onOrientationChange), this._mediaQueryLists.resolution.retina.removeListener(this._onRetinaChange), window.removeEventListener("resize", this._onViewportChange)
        }, h._updateElementStateData = function () {
            if (this._allowElementStateData) {
                var e = JSON.stringify({
                    viewport: this._viewport,
                    orientation: this._orientation,
                    retina: this._retina
                });
                this._el.setAttribute(o.STATE, e)
            }
        }, h._getViewport = function (e) {
            var t = window.getComputedStyle(this._el, l).content;
            return t ? (t = t.replace(/["']/g, ""), e ? t.split(":").pop() : t) : null
        }, h._getRetina = function (e) {
            return e.matches
        }, h._getOrientation = function (e) {
            var t = this._orientation;
            if (e.matches) {
                var n = /portrait|landscape/;
                return e.media.match(n)[0]
            }
            return t
        }, h._initOrientation = function () {
            var e = this._getOrientation(this._mediaQueryLists.orientation.portrait);
            return e ? e : this._getOrientation(this._mediaQueryLists.orientation.landscape)
        }, h._onViewportChange = function () {
            this._resizing || (this._resizing = !0, s(this._onViewportChangeUpdate))
        }, h._onViewportChangeUpdate = function () {
            var e = this._viewport;
            if (this._viewport = this._getViewport(this._options.removeNamespace), e !== this._viewport) {
                var t = {
                    from: e,
                    to: this._viewport
                };
                this._updateElementStateData(), this._dispatch(u.viewport, t)
            }
            this._resizing = !1
        }, h._onRetinaChange = function (e) {
            var t = this._retina;
            if (this._retina = this._getRetina(e), t !== this._retina) {
                var n = {
                    from: t,
                    to: this._retina
                };
                this._updateElementStateData(), this._dispatch(u.retina, n)
            }
        }, h._onOrientationChange = function (e) {
            var t = this._orientation;
            if (this._orientation = this._getOrientation(e), t !== this._orientation) {
                var n = {
                    from: t,
                    to: this._orientation
                };
                this._updateElementStateData(), this._dispatch(u.orientation, n)
            }
        }, t.exports = i
    }, {
        "@marcom/ac-event-emitter-micro": 35,
        "@marcom/ac-raf-emitter/update": 85
    }],
    115: [function (e, t, n) {
        "use strict";
        var i = e("./ViewportEmitter");
        t.exports = new i
    }, {
        "./ViewportEmitter": 114
    }],
    116: [function (e, t, n) {
        function i(e) {
            var t = new Float32Array(16);
            return t[0] = e[0], t[1] = e[1], t[2] = e[2], t[3] = e[3], t[4] = e[4], t[5] = e[5], t[6] = e[6], t[7] = e[7], t[8] = e[8], t[9] = e[9], t[10] = e[10], t[11] = e[11], t[12] = e[12], t[13] = e[13], t[14] = e[14], t[15] = e[15], t
        }
        t.exports = i
    }, {}],
    117: [function (e, t, n) {
        function i() {
            var e = new Float32Array(16);
            return e[0] = 1, e[1] = 0, e[2] = 0, e[3] = 0, e[4] = 0, e[5] = 1, e[6] = 0, e[7] = 0, e[8] = 0, e[9] = 0, e[10] = 1, e[11] = 0, e[12] = 0, e[13] = 0, e[14] = 0, e[15] = 1, e
        }
        t.exports = i
    }, {}],
    118: [function (e, t, n) {
        function i(e, t) {
            var n = t[0],
                i = t[1],
                r = t[2],
                s = t[3],
                a = t[4],
                o = t[5],
                l = t[6],
                c = t[7],
                u = t[8],
                h = t[9],
                m = t[10],
                d = t[11],
                f = t[12],
                p = t[13],
                v = t[14],
                y = t[15],
                _ = n * o - i * a,
                b = n * l - r * a,
                g = n * c - s * a,
                E = i * l - r * o,
                w = i * c - s * o,
                A = r * c - s * l,
                O = u * p - h * f,
                S = u * v - m * f,
                T = u * y - d * f,
                k = h * v - m * p,
                P = h * y - d * p,
                C = m * y - d * v,
                x = _ * C - b * P + g * k + E * T - w * S + A * O;
            return x ? (x = 1 / x, e[0] = (o * C - l * P + c * k) * x, e[1] = (r * P - i * C - s * k) * x, e[2] = (p * A - v * w + y * E) * x, e[3] = (m * w - h * A - d * E) * x, e[4] = (l * T - a * C - c * S) * x, e[5] = (n * C - r * T + s * S) * x, e[6] = (v * g - f * A - y * b) * x, e[7] = (u * A - m * g + d * b) * x, e[8] = (a * P - o * T + c * O) * x, e[9] = (i * T - n * P - s * O) * x, e[10] = (f * w - p * g + y * _) * x, e[11] = (h * g - u * w - d * _) * x, e[12] = (o * S - a * k - l * O) * x, e[13] = (n * k - i * S + r * O) * x, e[14] = (p * b - f * E - v * _) * x, e[15] = (u * E - h * b + m * _) * x, e) : null
        }
        t.exports = i
    }, {}],
    119: [function (e, t, n) {
        function i(e, t, n) {
            var i = Math.sin(n),
                r = Math.cos(n),
                s = t[4],
                a = t[5],
                o = t[6],
                l = t[7],
                c = t[8],
                u = t[9],
                h = t[10],
                m = t[11];
            return t !== e && (e[0] = t[0], e[1] = t[1], e[2] = t[2], e[3] = t[3], e[12] = t[12], e[13] = t[13], e[14] = t[14], e[15] = t[15]), e[4] = s * r + c * i, e[5] = a * r + u * i, e[6] = o * r + h * i, e[7] = l * r + m * i, e[8] = c * r - s * i, e[9] = u * r - a * i, e[10] = h * r - o * i, e[11] = m * r - l * i, e
        }
        t.exports = i
    }, {}],
    120: [function (e, t, n) {
        function i(e, t, n) {
            var i = Math.sin(n),
                r = Math.cos(n),
                s = t[0],
                a = t[1],
                o = t[2],
                l = t[3],
                c = t[8],
                u = t[9],
                h = t[10],
                m = t[11];
            return t !== e && (e[4] = t[4], e[5] = t[5], e[6] = t[6], e[7] = t[7], e[12] = t[12], e[13] = t[13], e[14] = t[14], e[15] = t[15]), e[0] = s * r - c * i, e[1] = a * r - u * i, e[2] = o * r - h * i, e[3] = l * r - m * i, e[8] = s * i + c * r, e[9] = a * i + u * r, e[10] = o * i + h * r, e[11] = l * i + m * r, e
        }
        t.exports = i
    }, {}],
    121: [function (e, t, n) {
        function i(e, t, n) {
            var i = Math.sin(n),
                r = Math.cos(n),
                s = t[0],
                a = t[1],
                o = t[2],
                l = t[3],
                c = t[4],
                u = t[5],
                h = t[6],
                m = t[7];
            return t !== e && (e[8] = t[8], e[9] = t[9], e[10] = t[10], e[11] = t[11], e[12] = t[12], e[13] = t[13], e[14] = t[14], e[15] = t[15]), e[0] = s * r + c * i, e[1] = a * r + u * i, e[2] = o * r + h * i, e[3] = l * r + m * i, e[4] = c * r - s * i, e[5] = u * r - a * i, e[6] = h * r - o * i, e[7] = m * r - l * i, e
        }
        t.exports = i
    }, {}],
    122: [function (e, t, n) {
        function i(e, t, n) {
            var i = n[0],
                r = n[1],
                s = n[2];
            return e[0] = t[0] * i, e[1] = t[1] * i, e[2] = t[2] * i, e[3] = t[3] * i, e[4] = t[4] * r, e[5] = t[5] * r, e[6] = t[6] * r, e[7] = t[7] * r, e[8] = t[8] * s, e[9] = t[9] * s, e[10] = t[10] * s, e[11] = t[11] * s, e[12] = t[12], e[13] = t[13], e[14] = t[14], e[15] = t[15], e
        }
        t.exports = i
    }, {}],
    123: [function (e, t, n) {
        function i(e, t) {
            if (e === t) {
                var n = t[1],
                    i = t[2],
                    r = t[3],
                    s = t[6],
                    a = t[7],
                    o = t[11];
                e[1] = t[4], e[2] = t[8], e[3] = t[12], e[4] = n, e[6] = t[9], e[7] = t[13], e[8] = i, e[9] = s, e[11] = t[14], e[12] = r, e[13] = a, e[14] = o
            } else e[0] = t[0], e[1] = t[4], e[2] = t[8], e[3] = t[12], e[4] = t[1], e[5] = t[5], e[6] = t[9], e[7] = t[13], e[8] = t[2], e[9] = t[6], e[10] = t[10], e[11] = t[14], e[12] = t[3], e[13] = t[7], e[14] = t[11], e[15] = t[15];
            return e
        }
        t.exports = i
    }, {}],
    124: [function (e, t, n) {
        function i() {
            var e = new Float32Array(3);
            return e[0] = 0, e[1] = 0, e[2] = 0, e
        }
        t.exports = i
    }, {}],
    125: [function (e, t, n) {
        function i(e, t, n) {
            var i = t[0],
                r = t[1],
                s = t[2],
                a = n[0],
                o = n[1],
                l = n[2];
            return e[0] = r * l - s * o, e[1] = s * a - i * l, e[2] = i * o - r * a, e
        }
        t.exports = i
    }, {}],
    126: [function (e, t, n) {
        function i(e, t) {
            return e[0] * t[0] + e[1] * t[1] + e[2] * t[2]
        }
        t.exports = i
    }, {}],
    127: [function (e, t, n) {
        function i(e, t, n) {
            var i = new Float32Array(3);
            return i[0] = e, i[1] = t, i[2] = n, i
        }
        t.exports = i
    }, {}],
    128: [function (e, t, n) {
        function i(e) {
            var t = e[0],
                n = e[1],
                i = e[2];
            return Math.sqrt(t * t + n * n + i * i)
        }
        t.exports = i
    }, {}],
    129: [function (e, t, n) {
        function i(e, t) {
            var n = t[0],
                i = t[1],
                r = t[2],
                s = n * n + i * i + r * r;
            return s > 0 && (s = 1 / Math.sqrt(s), e[0] = t[0] * s, e[1] = t[1] * s, e[2] = t[2] * s), e
        }
        t.exports = i
    }, {}],
    130: [function (e, t, n) {
        function i() {
            var e = new Float32Array(4);
            return e[0] = 0, e[1] = 0, e[2] = 0, e[3] = 0, e
        }
        t.exports = i
    }, {}],
    131: [function (e, t, n) {
        function i(e, t, n, i) {
            var r = new Float32Array(4);
            return r[0] = e, r[1] = t, r[2] = n, r[3] = i, r
        }
        t.exports = i
    }, {}],
    132: [function (e, t, n) {
        function i(e, t, n) {
            var i = t[0],
                r = t[1],
                s = t[2],
                a = t[3];
            return e[0] = n[0] * i + n[4] * r + n[8] * s + n[12] * a, e[1] = n[1] * i + n[5] * r + n[9] * s + n[13] * a, e[2] = n[2] * i + n[6] * r + n[10] * s + n[14] * a, e[3] = n[3] * i + n[7] * r + n[11] * s + n[15] * a, e
        }
        t.exports = i
    }, {}],
    133: [function (e, t, n) {
        "use strict";

        function i(e, t) {
            if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }

        function r(e, t) {
            if (!e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
            return !t || "object" != typeof t && "function" != typeof t ? e : t
        }

        function s(e, t) {
            if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function, not " + typeof t);
            e.prototype = Object.create(t && t.prototype, {
                constructor: {
                    value: e,
                    enumerable: !1,
                    writable: !0,
                    configurable: !0
                }
            }), t && (Object.setPrototypeOf ? Object.setPrototypeOf(e, t) : e.__proto__ = t)
        }
        var a = function () {
                function e(e, t) {
                    for (var n = 0; n < t.length; n++) {
                        var i = t[n];
                        i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i)
                    }
                }
                return function (t, n, i) {
                    return n && e(t.prototype, n), i && e(t, i), t
                }
            }(),
            o = e("@marcom/bubble-gum/BaseComponent"),
            l = e("@marcom/anim-system"),
            c = (e("@marcom/anim-system/Model/AnimSystemModel"), e("@marcom/ac-raf-emitter/draw"), function (e) {
                function t(e) {
                    i(this, t);
                    var n = r(this, (t.__proto__ || Object.getPrototypeOf(t)).call(this, e));
                    return n.startframe = n.el.querySelector(".image-tilt-startframe"), n.moreBtn = n.el.querySelector(".feature-toggle"), n.dataClick = n.moreBtn.getAttribute("data-analytics-click"), n.video = n.el.querySelector("video"), n.loopCount = 0, n.onEnded = n.onEnded.bind(n), n.onPlaying = n.onPlaying.bind(n), n.video.addEventListener("ended", n.onEnded), n.video.addEventListener("playing", n.onPlaying), n.video.addEventListener("pause", function () {
                        n.fadeAnimation().then(function () {
                            n.video.currentTime = 0, setTimeout(function () {
                                n.timeline.progress(0)
                            }, 100)
                        })
                    }), n.moreBtn.addEventListener("change", function (e) {
                        n.moreBtn.checked ? (n.moreBtn.removeAttribute("data-analytics-click"), n.loopCount = 2, n.video.pause(), n.onEnded()) : n.moreBtn.setAttribute("data-analytics-click", n.dataClick)
                    }), n
                }
                return s(t, e), a(t, [{
                    key: "mounted",
                    value: function () {
                        this.playback = this.gum.getComponentsForElement(this.el.querySelector("video"))[0], this.autoPlay()
                    }
                }, {
                    key: "fadeAnimation",
                    value: function () {
                        this.timeline = l.createTimeGroup();
                        var e = void 0,
                            t = new Promise(function (t) {
                                e = t
                            }),
                            n = this.timeline.addKeyframe(this.startframe, {
                                start: .1,
                                end: .3,
                                opacity: [0, 1],
                                event: "onUpdate"
                            });
                        return n.controller.on("onUpdate", function (t) {
                            1 === t.tweenProps.opacity.current && e()
                        }), this.timeline.play(), t
                    }
                }, {
                    key: "isLooped",
                    value: function () {
                        return this.hasAutoplayed ? this.loopCount > 0 : this.loopCount > 1
                    }
                }, {
                    key: "onEnded",
                    value: function () {
                        return this.loopCount++, this.isLooped() ? (this.loopCount = 0, this.hasAutoplayed = !0, this.ended = !0, this.el.classList.add("show-replay"), void this.el.classList.remove("hover-control")) : (this.timeline.pause(), this.timeline.progress(1), void this.playback.queueVideoPlayback())
                    }
                }, {
                    key: "onPlaying",
                    value: function () {
                        this.ended = !1, this.el.classList.remove("show-replay"), this.el.classList.add("hover-control")
                    }
                }, {
                    key: "playVideo",
                    value: function () {
                        this.moreBtn.checked || this.ended || (this.anim.activeVideo && this.anim.activeVideo !== this.playback && this.pauseVideo(this.anim.activeVideo), this.anim.activeVideo = this.playback, this.playback.queueVideoPlayback(), this.startframe.style.opacity = 0)
                    }
                }, {
                    key: "pauseVideo",
                    value: function (e) {
                        this.ended || (e = e || this.playback, e.pauseVideoPlayback(), this.anim.activeVideo = null)
                    }
                }, {
                    key: "autoPlay",
                    value: function () {
                        var e = this,
                            t = void 0,
                            n = {
                                large: {
                                    start: null,
                                    end: "a0t + 50a0h - css(margin-bottom, a0)"
                                },
                                small: {
                                    start: null,
                                    end: "a0t + 50a0h - css(margin-bottom, a0)"
                                }
                            };
                        document.querySelectorAll("section.feature")[0] == this.el ? (t = [this.el], n.large.start = "a0t + 50a0h - 70vh", n.small.start = "a0t - 30vh") : (t = [this.el.previousElementSibling, this.el], n.large.start = n.large.end, n.large.end = "a1t + 50a1h", n.small.start = n.small.end, n.small.end = "a1t + 50a1h");
                        var i = l.addKeyframe(this.video, {
                                start: n.large.start,
                                end: n.large.end,
                                anchors: t,
                                breakpointMask: "MLX",
                                event: "autoPlay"
                            }),
                            r = (l.addKeyframe(this.video, {
                                start: n.small.start,
                                end: n.small.end,
                                anchors: t,
                                breakpointMask: "S",
                                event: "autoPlay"
                            }), [i]);
                        r.forEach(function (t) {
                            t.controller.on("autoPlay:enter", function () {
                                e.playVideo()
                            }), t.controller.on("autoPlay:exit", function () {
                                e.pauseVideo()
                            })
                        })
                    }
                }], [{
                    key: "IS_SUPPORTED",
                    value: function () {
                        return !document.documentElement.classList.contains("reduced-motion")
                    }
                }]), t
            }(o));
        t.exports = c
    }, {
        "@marcom/ac-raf-emitter/draw": 81,
        "@marcom/anim-system": 91,
        "@marcom/anim-system/Model/AnimSystemModel": 96,
        "@marcom/bubble-gum/BaseComponent": 108
    }],
    134: [function (e, t, n) {
        "use strict";

        function i(e, t) {
            if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }

        function r(e, t) {
            if (!e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
            return !t || "object" != typeof t && "function" != typeof t ? e : t
        }

        function s(e, t) {
            if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function, not " + typeof t);
            e.prototype = Object.create(t && t.prototype, {
                constructor: {
                    value: e,
                    enumerable: !1,
                    writable: !0,
                    configurable: !0
                }
            }), t && (Object.setPrototypeOf ? Object.setPrototypeOf(e, t) : e.__proto__ = t)
        }
        var a = function () {
                function e(e, t) {
                    for (var n = 0; n < t.length; n++) {
                        var i = t[n];
                        i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i)
                    }
                }
                return function (t, n, i) {
                    return n && e(t.prototype, n), i && e(t, i), t
                }
            }(),
            o = e("@marcom/bubble-gum/BaseComponent"),
            l = e("@marcom/ac-modal").createStandardModal,
            c = function (e) {
                function t(e) {
                    i(this, t);
                    var n = r(this, (t.__proto__ || Object.getPrototypeOf(t)).call(this, e));
                    return n.modal = l(), n.triggers = n.el.querySelectorAll(".feature-toggle"), n.labels = n.el.querySelectorAll("label"), n.openModal = n.openModal.bind(n), n.closeModal = n.closeModal.bind(n), n.currentBreakpoint = e.pageMetrics.breakpoint, n
                }
                return s(t, e), a(t, [{
                    key: "mounted",
                    value: function () {
                        var e = this;
                        this.triggers.forEach(function (t, n) {
                            t.addEventListener("change", e.openModal);
                            var i = t.parentElement.querySelector(".feature-copy");
                            t.modalContent = i.cloneNode(!0), e.labels[n].addEventListener("keypress", function (e) {
                                13 !== e.keyCode && 32 !== e.keyCode || (e.preventDefault(), t.checked = !t.checked, t.dispatchEvent(new Event("change")))
                            })
                        }), this.modal.on("willclose", this.closeModal)
                    }
                }, {
                    key: "openModal",
                    value: function (e) {
                        e.currentTarget.checked && (this.activeTrigger = e.currentTarget, this.activeClassName = "section-" + e.currentTarget.closest("section").id, this.modal.modalElement.classList.add(this.activeClassName)), "L" !== this.currentBreakpoint && e.currentTarget.checked && (this.currentContent && this.modal.removeContent(this.currentContent), this.currentContent = this.activeTrigger.modalContent, this.modal.appendContent(this.activeTrigger.modalContent), this.modal.open())
                    }
                }, {
                    key: "closeModal",
                    value: function () {
                        if (this.modal.modalElement.classList.remove(this.activeClassName), this.activeTrigger) {
                            this.activeTrigger.checked = !1;
                            var e = new Event("change");
                            this.activeTrigger.dispatchEvent(e), this.activeTrigger = null
                        }
                    }
                }, {
                    key: "onBreakpointChange",
                    value: function (e) {
                        this.currentBreakpoint = e.breakpoint, this.activeTrigger && (this.closeModal(), this.modal.close())
                    }
                }], [{
                    key: "IS_SUPPORTED",
                    value: function () {
                        return !0
                    }
                }]), t
            }(o);
        t.exports = c
    }, {
        "@marcom/ac-modal": 46,
        "@marcom/bubble-gum/BaseComponent": 108
    }],
    135: [function (e, t, n) {
        "use strict";

        function i(e, t) {
            if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }

        function r(e, t) {
            if (!e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
            return !t || "object" != typeof t && "function" != typeof t ? e : t
        }

        function s(e, t) {
            if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function, not " + typeof t);
            e.prototype = Object.create(t && t.prototype, {
                constructor: {
                    value: e,
                    enumerable: !1,
                    writable: !0,
                    configurable: !0
                }
            }), t && (Object.setPrototypeOf ? Object.setPrototypeOf(e, t) : e.__proto__ = t)
        }
        var a = function () {
                function e(e, t) {
                    for (var n = 0; n < t.length; n++) {
                        var i = t[n];
                        i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i)
                    }
                }
                return function (t, n, i) {
                    return n && e(t.prototype, n), i && e(t, i), t
                }
            }(),
            o = e("@marcom/bubble-gum/BaseComponent"),
            l = e("@marcom/anim-system"),
            c = function (e) {
                function t(e) {
                    return i(this, t), r(this, (t.__proto__ || Object.getPrototypeOf(t)).call(this, e))
                }
                return s(t, e), a(t, [{
                    key: "mounted",
                    value: function () {
                        return !this.el.classList.contains("section-safari") && (l.addKeyframe(this.el, {
                            start: "t - 100vh",
                            end: "b - 100vh",
                            y: ["100px", "0"],
                            breakpointMask: "MLX",
                            easingFunction: "easeOutQuad"
                        }), void l.addKeyframe(this.el, {
                            start: "a0t - 100vh",
                            end: "a0t - 30vh",
                            scale: [.7, 1],
                            anchors: [this.el.querySelector("video")],
                            breakpointMask: "S",
                            easingFunction: "easeOutQuad",
                            hold: !0
                        }))
                    }
                }], [{
                    key: "IS_SUPPORTED",
                    value: function () {
                        return !document.documentElement.classList.contains("reduced-motion")
                    }
                }]), t
            }(o);
        t.exports = c
    }, {
        "@marcom/anim-system": 91,
        "@marcom/bubble-gum/BaseComponent": 108
    }],
    136: [function (e, t, n) {
        "use strict";

        function i(e, t) {
            if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }

        function r(e, t) {
            if (!e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
            return !t || "object" != typeof t && "function" != typeof t ? e : t
        }

        function s(e, t) {
            if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function, not " + typeof t);
            e.prototype = Object.create(t && t.prototype, {
                constructor: {
                    value: e,
                    enumerable: !1,
                    writable: !0,
                    configurable: !0
                }
            }), t && (Object.setPrototypeOf ? Object.setPrototypeOf(e, t) : e.__proto__ = t)
        }

        function a(e) {
            e && e.reason && "NotAllowedError" === e.reason.name && 0 === e.reason.stack.indexOf("play") && (u = !0)
        }
        var o = function () {
                function e(e, t) {
                    for (var n = 0; n < t.length; n++) {
                        var i = t[n];
                        i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i)
                    }
                }
                return function (t, n, i) {
                    return n && e(t.prototype, n), i && e(t, i), t
                }
            }(),
            l = e("@marcom/bubble-gum/BaseComponent"),
            c = e("@marcom/ac-raf-emitter/draw"),
            u = !1;
        window.addEventListener("unhandledrejection", a), window.addEventListener("rejectionhandled", a);
        var h = function (e) {
            function t(e) {
                i(this, t);
                var n = r(this, (t.__proto__ || Object.getPrototypeOf(t)).call(this, e));
                e.data = e.data || {}, n.loadingLabel = e.data.loadingLabel || n.el.dataset.loadingLabel, n.pauseLabel = e.data.pauseLabel || n.el.dataset.pauseLabel, n.playLabel = e.data.playLabel || n.el.dataset.playLabel;
                var s = void 0;
                return Object.defineProperty(n, "mediaElement", {
                    get: function () {
                        return s
                    },
                    set: function (e) {
                        n.mediaElementDelegate && n.mediaElementDelegate.destroy(), e instanceof HTMLMediaElement && (n.mediaElementDelegate = new m(n, e))
                    }
                }), n.el.dataset["for"] && (n.mediaElement = document.getElementById(n.el.dataset["for"])), n.analyticsClickTemplate = e.data.analyticsClickTemplate || n.el.dataset.analyticsClickTemplate, n.analyticsTitleTemplate = e.data.analyticsTitleTemplate || n.el.dataset.analyticsTitleTemplate, n._mediaStateWasPlaying = !1, n._appearanceInitialized = !1, n.mediaState = e.initialState || t.DefaultMediaState, n.onClick = n.onClick.bind(n), n.el.addEventListener("click", n.onClick, !1), n
            }
            return s(t, e), o(t, [{
                key: "destroy",
                value: function () {
                    this.mediaElementDelegate.destroy(), this.mediaElementDelegate = null, this.el.removeEventListener("click", this.onClick)
                }
            }, {
                key: "onClick",
                value: function () {
                    this.toggle(), this.mediaState === t.MediaState.Playing ? (this.mediaElementDelegate && this.mediaElementDelegate.onPlayControlActivated(), this.trigger("play")) : this.mediaState === t.MediaState.Paused && this.trigger("pause"), this.updateAppearance()
                }
            }, {
                key: "toggle",
                value: function () {
                    this.mediaState === t.MediaState.Paused ? this.mediaState = t.MediaState.Playing : this.mediaState === t.MediaState.Playing && (this.mediaState = t.MediaState.Paused)
                }
            }, {
                key: "updateAppearance",
                value: function () {
                    var e = this;
                    c(function () {
                        e._appearanceInitialized || (e.el.removeAttribute("aria-hidden"), e.el.setAttribute("aria-live", "polite"));
                        var n = e.mediaState === t.MediaState.Loading;
                        e.el.disabled = n, e.el.setAttribute("aria-label", e.ariaLabel), e.el.classList.toggle("is-loading", n), e.el.classList.toggle("is-playing", e.mediaState === t.MediaState.Playing), e.el.classList.toggle("is-paused", e.mediaState === t.MediaState.Paused), e.setAnalyticsAttributes()
                    })
                }
            }, {
                key: "setAnalyticsAttributes",
                value: function () {
                    var e = void 0;
                    return this.mediaState === t.MediaState.Playing ? e = "pause" : this.mediaState === t.MediaState.Paused && (e = "play"), e ? (this.el.setAttribute("data-analytics-click", this.analyticsClickTemplate.replace("{controlState}", e)), void this.el.setAttribute("data-analytics-title", this.analyticsTitleTemplate.replace("{controlState}", e))) : (this.el.removeAttribute("data-analytics-click"), void this.el.removeAttribute("data-analytics-title"))
                }
            }, {
                key: "mediaState",
                get: function () {
                    return this._mediaState
                },
                set: function (e) {
                    if ("boolean" == typeof e || e || (e = t.MediaState.Loading), !t.validMediaStates.includes(e)) {
                        var n = t.validMediaStates.map(function (e) {
                            return "'" + e + "'"
                        }).join(", ");
                        throw new Error(this.constructor.name + " 'mediaState' must be one of: " + n)
                    }
                    e === t.MediaState.Playing && (this._mediaStateWasPlaying = !0), this._mediaState = e, this.updateAppearance()
                }
            }, {
                key: "ariaLabel",
                get: function () {
                    switch (this.mediaState) {
                        case t.MediaState.Loading:
                            return this.loadingLabel;
                        case t.MediaState.Playing:
                            return this.pauseLabel;
                        case t.MediaState.Paused:
                            return this.playLabel;
                        default:
                            return ""
                    }
                }
            }]), t
        }(l);
        h.MediaState = Object.freeze({
            Loading: "loading",
            Playing: "playing",
            Paused: "paused"
        }), h.validMediaStates = Object.freeze(Object.keys(h.MediaState).map(function (e) {
            return h.MediaState[e]
        }));
        var m = function () {
            function e(t, n) {
                i(this, e), this.element = n, this.controls = t, this.lastUserInitiatedState = null, this.lastEventWasUserInteraction = !1, this.onMediaLoading = this.onMediaLoading.bind(this), this.onMediaPlay = this.onMediaPlay.bind(this), this.onMediaPause = this.onMediaPause.bind(this), this.onPlayControlActivated = this.onPlayControlActivated.bind(this), this.onPauseControlActivated = this.onPauseControlActivated.bind(this), this.element.addEventListener("emptied", this.onMediaLoading), this.element.addEventListener("waiting", this.onMediaLoading), this.element.addEventListener("canplay", this.onMediaPause), this.element.addEventListener("playing", this.onMediaPlay), this.element.addEventListener("pause", this.onMediaPause), this.controls.on("play", this.onPlayControlActivated), this.controls.on("pause", this.onPauseControlActivated), this.element.readyState >= 3 && (this.element.paused ? this.onMediaPause() : this.onMediaPlay()), this.onMediaLoading()
            }
            return o(e, [{
                key: "onMediaLoading",
                value: function () {
                    this.controls.mediaState = h.MediaState.Loading
                }
            }, {
                key: "onMediaPlay",
                value: function () {
                    !this.lastEventWasUserInteraction && ("pause" === this.lastUserInitiatedState || u && "play" !== this.lastUserInitiatedState) ? this.element.pause() : this.controls.mediaState = h.MediaState.Playing, this.lastEventWasUserInteraction = !1
                }
            }, {
                key: "onMediaPause",
                value: function () {
                    this.controls.mediaState = h.MediaState.Paused, this.lastEventWasUserInteraction = !1
                }
            }, {
                key: "onPlayControlActivated",
                value: function () {
                    this.lastEventWasUserInteraction = !0, this.lastUserInitiatedState = "play", this.element.play()
                }
            }, {
                key: "onPauseControlActivated",
                value: function () {
                    this.lastEventWasUserInteraction = !0, this.lastUserInitiatedState = "pause", this.element.pause()
                }
            }, {
                key: "destroy",
                value: function () {
                    this.element.removeEventListener("emptied", this.onMediaLoading), this.element.removeEventListener("waiting", this.onMediaLoading), this.element.removeEventListener("canplay", this.onMediaPause), this.element.removeEventListener("playing", this.onMediaPlay), this.element.removeEventListener("pause", this.onMediaPause), this.controls.off("play", this.onPlayControlActivated), this.controls.off("pause", this.onPauseControlActivated), this.element = null, this.controls = null
                }
            }]), e
        }();
        t.exports = h
    }, {
        "@marcom/ac-raf-emitter/draw": 81,
        "@marcom/bubble-gum/BaseComponent": 108
    }],
    137: [function (e, t, n) {
        "use strict";

        function i(e) {
            return e && e.__esModule ? e : {
                "default": e
            }
        }

        function r(e, t) {
            if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }

        function s(e, t) {
            if (!e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
            return !t || "object" != typeof t && "function" != typeof t ? e : t
        }

        function a(e, t) {
            if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function, not " + typeof t);
            e.prototype = Object.create(t && t.prototype, {
                constructor: {
                    value: e,
                    enumerable: !1,
                    writable: !0,
                    configurable: !0
                }
            }), t && (Object.setPrototypeOf ? Object.setPrototypeOf(e, t) : e.__proto__ = t)
        }
        var o = function () {
                function e(e, t) {
                    for (var n = 0; n < t.length; n++) {
                        var i = t[n];
                        i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i)
                    }
                }
                return function (t, n, i) {
                    return n && e(t.prototype, n), i && e(t, i), t
                }
            }(),
            l = e("@marcom/bubble-gum/BaseComponent"),
            c = i(l),
            u = e("@marcom/ac-progressive-image-loader/ProgressiveImageLoader"),
            h = i(u),
            m = e("@marcom/ac-raf-emitter"),
            d = {
                PROGRESSIVE_IMAGE: "data-progressive-image",
                IMAGE_LOADED: "data-progressive-image-loaded"
            },
            f = function (e) {
                function t() {
                    r(this, t);
                    var e = s(this, (t.__proto__ || Object.getPrototypeOf(t)).apply(this, arguments));
                    return e.imageLoader = new h["default"]({
                        container: e.el,
                        includeContainer: !0
                    }), e._loadOptions = {
                        imageDataAttribute: d.PROGRESSIVE_IMAGE,
                        imageAnimate: !1
                    }, e._onProgressiveImageLoad = e._onProgressiveImageLoad.bind(e), e._onImageLoad = e._onImageLoad.bind(e), e._onComplete = e._onComplete.bind(e), e
                }
                return a(t, e), o(t, [{
                    key: "name",
                    value: function () {
                        return "ProgressiveImage"
                    }
                }, {
                    key: "mounted",
                    value: function () {
                        var e = this;
                        (0, m.update)(function () {
                            (0, m.draw)(function () {
                                e._initialize()
                            })
                        })
                    }
                }, {
                    key: "_initialize",
                    value: function () {
                        this._setupEvents(), this.addDiscreteEvent({
                            start: "t - 200vh",
                            allowRTL: !0,
                            event: "ProgressiveImageLoad"
                        }), this.keyframeController.on("ProgressiveImageLoad", this._onProgressiveImageLoad)
                    }
                }, {
                    key: "_setupEvents",
                    value: function () {
                        this.imageLoader.on(h["default"].Events.ImageLoad, this._onImageLoad), this.imageLoader.on(h["default"].Events.Complete, this._onComplete)
                    }
                }, {
                    key: "_onProgressiveImageLoad",
                    value: function () {
                        this.imageLoader.load(this._loadOptions)
                    }
                }, {
                    key: "_onImageLoad",
                    value: function (e) {
                        e.setAttribute(d.IMAGE_LOADED, ""), this.gum.trigger(t.EVENTS.ImageLoad, e)
                    }
                }, {
                    key: "_onComplete",
                    value: function () {
                        this.gum.trigger(t.EVENTS.Complete)
                    }
                }, {
                    key: "destroy",
                    value: function () {
                        this.imageLoader.destroy(), this.imageLoader = null
                    }
                }], [{
                    key: "EVENTS",
                    value: function () {
                        return {
                            ImageLoad: "progressive-image-load",
                            Complete: "progressive-image-complete"
                        }
                    }
                }, {
                    key: "IS_SUPPORTED",
                    value: function () {
                        var e = document.documentElement;
                        return e.classList.contains("progressive-image")
                    }
                }]), t
            }(c["default"]);
        t.exports = f
    }, {
        "@marcom/ac-progressive-image-loader/ProgressiveImageLoader": 64,
        "@marcom/ac-raf-emitter": 71,
        "@marcom/bubble-gum/BaseComponent": 108
    }],
    138: [function (e, t, n) {
        "use strict";

        function i(e, t) {
            if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }

        function r(e, t) {
            if (!e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
            return !t || "object" != typeof t && "function" != typeof t ? e : t
        }

        function s(e, t) {
            if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function, not " + typeof t);
            e.prototype = Object.create(t && t.prototype, {
                constructor: {
                    value: e,
                    enumerable: !1,
                    writable: !0,
                    configurable: !0
                }
            }), t && (Object.setPrototypeOf ? Object.setPrototypeOf(e, t) : e.__proto__ = t)
        }
        var a = function () {
                function e(e, t) {
                    for (var n = 0; n < t.length; n++) {
                        var i = t[n];
                        i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i)
                    }
                }
                return function (t, n, i) {
                    return n && e(t.prototype, n), i && e(t, i), t
                }
            }(),
            o = e("@marcom/bubble-gum/BaseComponent"),
            l = (e("@marcom/anim-system"), e("@marcom/viewport-emitter")),
            c = e("@marcom/ac-raf-emitter/draw"),
            u = {
                L: "large",
                M: "medium",
                S: "small"
            },
            h = function (e) {
                function t(e) {
                    i(this, t);
                    var n = r(this, (t.__proto__ || Object.getPrototypeOf(t)).call(this, e));
                    return n.isRetina = l._retina, n.currentBreakPoint = e.pageMetrics.breakpoint, n
                }
                return s(t, e), a(t, [{
                    key: "mounted",
                    value: function () {
                        var e = this;
                        this.loadJSON().then(function (t) {
                            e.playSequence(t)
                        })
                    }
                }, {
                    key: "loadJSON",
                    value: function () {
                        var e = this.isRetina ? "_2x" : "";
                        return fetch("json/lock_" + u[this.currentBreakPoint] + e + ".json").then(function (e) {
                            return e.json()
                        })
                    }
                }, {
                    key: "waitFrame",
                    value: function (e, t) {
                        var n = this.el,
                            i = 0,
                            r = this.isRetina ? 2 : 1,
                            s = void 0,
                            a = new Promise(function (e) {
                                s = e
                            }),
                            o = function l() {
                                i++, i < e ? c(l) : (n.style.backgroundPosition = "-" + t.frame.x / r + "px -" + t.frame.y / r + "px", s())
                            };
                        return c(o), a
                    }
                }, {
                    key: "playSequence",
                    value: function (e) {
                        var t = this,
                            n = Object.keys(e.frames),
                            i = function r() {
                                var i = e.frames[n.shift()];
                                void 0 !== i && t.waitFrame(1, i).then(r)
                            };
                        i()
                    }
                }, {
                    key: "onBreakpointChange",
                    value: function (e) {
                        var t = this;
                        c(function () {
                            t.el.style.backgroundPosition = "0 0"
                        }), this.currentBreakPoint = e.breakpoint, this.isRetina = l._retina, this.loadJSON().then(function (e) {
                            t.playSequence(e)
                        })
                    }
                }], [{
                    key: "IS_SUPPORTED",
                    value: function () {
                        return !0
                    }
                }]), t
            }(o);
        t.exports = h
    }, {
        "@marcom/ac-raf-emitter/draw": 81,
        "@marcom/anim-system": 91,
        "@marcom/bubble-gum/BaseComponent": 108,
        "@marcom/viewport-emitter": 115
    }],
    139: [function (e, t, n) {
        "use strict";

        function i(e, t) {
            if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }

        function r(e, t) {
            if (!e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
            return !t || "object" != typeof t && "function" != typeof t ? e : t
        }

        function s(e, t) {
            if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function, not " + typeof t);
            e.prototype = Object.create(t && t.prototype, {
                constructor: {
                    value: e,
                    enumerable: !1,
                    writable: !0,
                    configurable: !0
                }
            }), t && (Object.setPrototypeOf ? Object.setPrototypeOf(e, t) : e.__proto__ = t)
        }

        function a(e) {
            return {
                X: "xlarge",
                L: "large",
                M: "medium",
                S: "small"
            } [e]
        }
        var o = function () {
                function e(e, t) {
                    var n = [],
                        i = !0,
                        r = !1,
                        s = void 0;
                    try {
                        for (var a, o = e[Symbol.iterator](); !(i = (a = o.next()).done) && (n.push(a.value), !t || n.length !== t); i = !0);
                    } catch (l) {
                        r = !0, s = l
                    } finally {
                        try {
                            !i && o["return"] && o["return"]()
                        } finally {
                            if (r) throw s
                        }
                    }
                    return n
                }
                return function (t, n) {
                    if (Array.isArray(t)) return t;
                    if (Symbol.iterator in Object(t)) return e(t, n);
                    throw new TypeError("Invalid attempt to destructure non-iterable instance")
                }
            }(),
            l = function () {
                function e(e, t) {
                    for (var n = 0; n < t.length; n++) {
                        var i = t[n];
                        i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i)
                    }
                }
                return function (t, n, i) {
                    return n && e(t.prototype, n), i && e(t, i), t
                }
            }(),
            c = function f(e, t, n) {
                null === e && (e = Function.prototype);
                var i = Object.getOwnPropertyDescriptor(e, t);
                if (void 0 === i) {
                    var r = Object.getPrototypeOf(e);
                    return null === r ? void 0 : f(r, t, n)
                }
                if ("value" in i) return i.value;
                var s = i.get;
                if (void 0 !== s) return s.call(n)
            },
            u = e("@marcom/bubble-gum/BaseComponent"),
            h = e("@marcom/viewport-emitter"),
            m = 3,
            d = function (e) {
                function t(e) {
                    i(this, t);
                    var n = r(this, (t.__proto__ || Object.getPrototypeOf(t)).call(this, e));
                    if (n.video = n.el, n.options = e.data || {}, n.enableXLarge = n.el.hasAttribute("data-src-xlarge"), n.enableRetina = n.el.hasAttribute("data-enable-retina"), n.enableRetina && (n.isRetina = h._retina, h.on("change:retina", function (e) {
                            n.isRetina = e.to === !0, n.load(n.currentViewport)
                        })), n.enablePortrait = n.el.hasAttribute("data-enable-portrait"), n.enablePortrait) {
                        var s = h.orientation;
                        n.video.parentElement.classList.add("video-" + s), "portrait" === s && (n.isPortrait = !0), h.on("change:orientation", function (e) {
                            n.isPortrait = "portrait" === e.to, n.video.parentElement.classList.remove("video-" + e.from), n.video.parentElement.classList.add("video-" + e.to), n.load(n.currentViewport)
                        })
                    }
                    n.sources = {}, t.OBJECT_ENTRIES(n.video.dataset).filter(function (e) {
                        var t = o(e, 1),
                            n = t[0];
                        return 0 === n.indexOf("src")
                    }).forEach(function (e) {
                        var t = o(e, 2),
                            i = t[0],
                            r = t[1],
                            s = i.replace(/^src/, "").toLowerCase();
                        n.sources[s] = r
                    });
                    var l = void 0;
                    return Object.defineProperty(n, "currentViewport", {
                        set: function (e) {
                            l = a(e), n.load(l)
                        },
                        get: function () {
                            return l
                        }
                    }), n.currentViewport = e.pageMetrics.breakpoint, n.previousSource = null, n.inLoadArea = !1, n
                }
                return s(t, e), l(t, [{
                    key: "mounted",
                    value: function () {
                        var e = this;
                        c(t.prototype.__proto__ || Object.getPrototypeOf(t.prototype), "mounted", this).call(this), this.addDiscreteEvent({
                            event: "Video: Load",
                            start: this.options.loadAreaStart || "t - 200vh",
                            end: this.options.loadAreaEnd || "b + 200vh",
                            onEnter: function () {
                                e.inLoadArea = !0, e.load()
                            },
                            onExit: function () {
                                e.inLoadArea = !1
                            }
                        })
                    }
                }, {
                    key: "onBreakpointChange",
                    value: function (e) {
                        this.currentViewport = e.breakpoint
                    }
                }, {
                    key: "load",
                    value: function (e) {
                        if (this.inLoadArea) {
                            e = e || this.currentViewport;
                            var t = this.sources[e];
                            this.enableXLarge || "xlarge" !== e || (t = this.sources.large), this.isRetina && t && (t = t.replace(/.mp4/gi, "_2x.mp4")), this.isPortrait && t && (t = t.replace(/.mp4/gi, "_portrait.mp4")), t && t !== this.previousSource && (this.video.autoplay = this.video.readyState >= m && !this.video.paused, this.video.src = this.previousSource = t, this.video.load())
                        }
                    }
                }, {
                    key: "queueVideoPlayback",
                    value: function () {
                        var e = this;
                        "function" == typeof this._onCanPlay && this.video.removeEventListener("canplay", this._onCanPlay), this.video.readyState < m ? (this._onCanPlay = function () {
                            e.video.play(), e.video.removeEventListener("canplay", e._onCanPlay)
                        }, this.video.addEventListener("canplay", this._onCanPlay)) : this.video.play()
                    }
                }, {
                    key: "pauseVideoPlayback",
                    value: function () {
                        this.video.paused || this.video.pause()
                    }
                }], [{
                    key: "OBJECT_ENTRIES",
                    value: function (e) {
                        for (var t = Object.keys(e), n = t.length, i = new Array(n); n--;) i[n] = [t[n], e[t[n]]];
                        return i
                    }
                }]), t
            }(u);
        t.exports = d
    }, {
        "@marcom/bubble-gum/BaseComponent": 108,
        "@marcom/viewport-emitter": 115
    }],
    140: [function (e, t, n) {
        "use strict";
        var i = e("@marcom/bubble-gum"),
            r = e("@marcom/bubble-gum/ComponentMap"),
            s = e("./model/SiteComponentMap"),
            a = e("@marcom/ac-accessibility/TextZoom"),
            o = {
                initialize: function () {
                    Object.assign(r, s), a.detect();
                    var e = document.querySelector(".page-overview .main"),
                        t = new i(document.querySelector("body"));
                    e && t.on(i.EVENTS.DOM_COMPONENTS_MOUNTED, function () {
                        t.addComponent({
                            componentName: "Modal",
                            el: e
                        })
                    })
                }
            };
        o.initialize()
    }, {
        "./model/SiteComponentMap": 141,
        "@marcom/ac-accessibility/TextZoom": 2,
        "@marcom/bubble-gum": 109,
        "@marcom/bubble-gum/ComponentMap": 110
    }],
    141: [function (e, t, n) {
        "use strict";
        t.exports = {
            OverviewFeature: e("../components/OverviewFeature"),
            AutoPlay: e("../components/AutoPlay"),
            VideoViewportSource: e("../components/VideoViewportSource"),
            PlaybackControl: e("../components/PlaybackControl"),
            ProgressiveImage: e("../components/ProgressiveImage"),
            SequencePlayer: e("../components/SequencePlayer"),
            Modal: e("../components/Modal")
        }
    }, {
        "../components/AutoPlay": 133,
        "../components/Modal": 134,
        "../components/OverviewFeature": 135,
        "../components/PlaybackControl": 136,
        "../components/ProgressiveImage": 137,
        "../components/SequencePlayer": 138,
        "../components/VideoViewportSource": 139
    }]
}, {}, [140]);
