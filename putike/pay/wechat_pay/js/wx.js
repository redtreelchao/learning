function hash(e) {
    var t = 5381;
    for (var n = 0; n < e.length; n++) t = (t << 5) + t + e.charCodeAt(n),
    t &= 2147483647;
    return t;
}

function trim(e) {
    return e.replace(/^\s*|\s*$/g, "");
}

function ajax(e) {
    var t = (e.type || "GET").toUpperCase(),
    n = e.url,
    r = typeof e.async == "undefined" ? !0 : e.async,
    i = typeof e.data == "string" ? e.data: null,
    s = new XMLHttpRequest,
    o = null;
    s.open(t, n, r),
    s.onreadystatechange = function() {
        s.readyState == 3 && e.received && e.received(s),
        s.readyState == 4 && (s.status >= 200 && s.status < 400 && (clearTimeout(o), e.success && e.success(s.responseText)), e.complete && e.complete(), e.complete = null);
    },
    t == "POST" && s.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8"),
    s.setRequestHeader("X-Requested-With", "XMLHttpRequest"),
    s.send(i),
    typeof e.timeout != "undefined" && (o = setTimeout(function() {
        s.abort("timeout"),
        e.complete && e.complete(),
        e.complete = null;
    },
    e.timeout));
}

function report_article() {
    var e = sourceurl == "" ? location.href: sourceurl,
    t = [nickname, location.href, title, e].join("|WXM|");
    location.href = "/mp/readtemplate?t=wxm-appmsg-inform&__biz=" + biz + "&info=" + encodeURIComponent(t) + "#wechat_redirect";
}

function viewSource() {
    var redirectUrl = sourceurl.indexOf("://") < 0 ? "http://" + sourceurl: sourceurl;
    redirectUrl = "http://" + location.host + "/mp/redirect?url=" + encodeURIComponent(sourceurl);
    var opt = {
        url: "/mp/advertisement_report" + location.search + "&report_type=3&action_type=0&url=" + encodeURIComponent(sourceurl) + "&uin=" + uin + "&key=" + key + "&__biz=" + biz + "&r=" + Math.random(),
        type: "GET",
        async: !1
    };
    return tid ? opt.success = function(res) {
        try {
            res = eval("(" + res + ")");
        } catch(e) {
            res = {};
        }
        res && res.ret == 0 ? location.href = redirectUrl: viewSource();
    }: (opt.timeout = 2e3, opt.complete = function() {
        location.href = redirectUrl;
    }),
    ajax(opt),
    !1;
}

function parseParams(e) {
    if (!e) return {};
    var t = e.split("&"),
    n = {},
    r = "";
    for (var i = 0,
    s = t.length; i < s; i++) r = t[i].split("="),
    n[r[0]] = r[1];
    return n;
}

function htmlDecode(e) {
    return e.replace(/&#39;/g, "'").replace(/<br\s*(\/)?\s*>/g, "\n").replace(/&nbsp;/g, " ").replace(/&lt;/g, "<").replace(/&gt;/g, ">").replace(/&quot;/g, '"').replace(/&amp;/g, "&");
}

function report(e, t, n) {
    var r = e.split("?").pop();
    r = r.split("#").shift();
    if (r == "") return;
    var i = [r, "action_type=" + n, "uin=" + t].join("&");
    ajax({
        url: "/mp/appmsg/show",
        type: "POST",
        timeout: 2e3,
        data: i
    });
}

function reportTimeOnPage() {
    var e = location.href,
    t = e.split("?").pop();
    t = t.split("#").shift();
    if (t == "") return;
    var n = [t, "start_time=" + _wxao.begin, "end_time=" + (new Date).getTime(), "uin=" + fakeid, "title=" + encodeURIComponent(title), "action=pagetime"].join("&");
    ajax({
        url: "/mp/appmsg/show?" + n,
        async: !1,
        timeout: 2e3
    });
}

function share_scene(e, t) {
    var n = "";
    tid != "" && (n = "tid=" + tid + "&aid=" + 54);
    var r = e.split("?")[1] || "";
    r = r.split("#")[0];
    if (r == "") return;
    var i = [r, "scene=" + t];
    return n != "" && i.push(n),
    r = i.join("&"),
    e.split("?")[0] + "?" + r + "#" + (e.split("#")[1] || "");
}

function get_url(e, t) {
    t = t || "";
    var n = e.split("?")[1] || "";
    n = n.split("#")[0];
    if (n == "") return;
    var r = [n];
    return t != "" && r.push(t),
    n = r.join("&"),
    e.split("?")[0] + "?" + n + "#" + (e.split("#")[1] || "");
}

function viewProfile() {
    typeof WeixinJSBridge != "undefined" && WeixinJSBridge.invoke && WeixinJSBridge.invoke("profile", {
        username: user_name,
        scene: "57"
    });
}

function gdt_click(e, t, n, r, i, s, o) {
    if (has_click[i]) return;
    has_click[i] = !0;
    var u = document.getElementById("loading_" + i);
    u && (u.style.display = "inline");
    var a = +(new Date);
    ajax({
        url: "/mp/advertisement_report?report_type=2&type=" + e + "&url=" + encodeURIComponent(t) + "&tid=" + i + "&rl=" + encodeURIComponent(n) + "&uin=" + uin + "&key=" + key + "&__biz=" + biz + "&r=" + Math.random(),
        type: "GET",
        timeout: 1e3,
        complete: function(n) {
            has_click[i] = !1,
            u && (u.style.display = "none"),
            e == "5" ? location.href = "/mp/profile?source=from_ad&tousername=" + t + "&ticket=" + s + "&uin=" + uin + "&key=" + key + "&__biz=" + biz + "&mid=" + mid + "&idx=" + idx: location.href = get_url(t, "tid=" + i);
        },
        async: !0
    });
}

function addEvent(e, t, n) {
    window.addEventListener ? e.addEventListener(t, n, !1) : window.attachEvent ? e.attachEvent("on" + t,
    function(e) {
        return function(t) {
            n.call(e, t);
        };
    } (e)) : e["on" + t] = n;
}

function log(e) {
    var t = document.getElementById("log");
    if (t) {
        var n = t.innerHTML;
        t.innerHTML = n + "<div>" + e + "</div>";
    }
}

function initpicReport() {
    function e(e) {
        var t = [];
        for (var n in e) t.push(n + "=" + encodeURIComponent(e[n] || ""));
        return t.join("&");
    }
    if (!networkType) return;
    var t = window.performance || window.msPerformance || window.webkitPerformance,
    n = null;
    if (!t || typeof t.getEntries == "undefined") return;
    var r, i = 100,
    s = document.getElementsByTagName("img"),
    o = s.length,
    u = navigator.userAgent,
    a;
    /micromessenger\/(\d+\.\d+)/i.test(u),
    a = RegExp.$1;
    for (var f = 0,
    l = s.length; f < l; f++) {
        r = parseInt(Math.random() * 100);
        if (r > i) continue;
        var c = s[f].getAttribute("src");
        if (c.indexOf("mp.weixin.qq.com") >= 0) continue;
        var h = t.getEntries(),
        p;
        for (var d = 0; d < h.length; d++) {
            p = h[d];
            if (p.name == c) {
                ajax({
                    type: "POST",
                    url: "/mp/appmsgpicreport?__biz=" + biz + "&uin=" + uin + "&key=" + key + "#wechat_redirect",
                    data: e({
                        rnd: Math.random(),
                        uin: uin,
                        version: version,
                        client_version: a,
                        device: navigator.userAgent,
                        time_stamp: parseInt( + (new Date) / 1e3),
                        url: c,
                        img_size: s[f].fileSize || 0,
                        user_agent: navigator.userAgent,
                        net_type: networkType,
                        sample: o > 100 ? 100 : o,
                        delay_time: parseInt(p.duration)
                    })
                });
                break;
            }
        }
    }
}

var ISWP = !!navigator.userAgent.match(/Windows\sPhone/i),
sw = 0;

(function() {
    function e(e) {
        var t = 0;
        e.contentDocument && e.contentDocument.body.offsetHeight ? t = e.contentDocument.body.offsetHeight: e.Document && e.Document.body && e.Document.body.scrollHeight ? t = e.Document.body.scrollHeight: e.document && e.document.body && e.document.body.scrollHeight && (t = e.document.body.scrollHeight);
        var n = e.parentElement;
        if ( !! n) {
            n.style.height = t + "px";
            var r = n.childNodes;
            for (var i = 0,
            s = r.length; i < s; ++i) r[i].style.height = t + "px";
            t < 10 && (e.parentElement.style.overflow = "hidden");
        }
    }
    var t = document.getElementsByTagName("iframe"),
    n;
    for (var r = 0,
    i = t.length; r < i; ++r) {
        n = t[r];
        var s = n.getAttribute("data-src"); !! s && s.indexOf("http://mp.weixin.qq.com/mp/appmsgvote") == 0 && (n.setAttribute("src", s.replace("#wechat_redirect", ["&uin=", uin, "&key=", key].join(""))),
        function(t) {
            t.onload = function() {
                e(t);
            };
        } (n), n.appmsg_idx = r);
    }
    window.iframe_reload = function(r) {
        for (var i = 0,
        s = t.length; i < s; ++i) {
            n = t[i];
            var o = n.getAttribute("src"); !! o && o.indexOf("http://mp.weixin.qq.com/mp/appmsgvote") == 0 && e(n);
        }
    };
})();

if (ISWP) {
    var profile = document.getElementById("post-user");
    profile && profile.setAttribute("href", "weixin://profile/" + user_name);
}

var cookie = {
    get: function(e) {
        if (e == "") return "";
        var t = new RegExp(e + "=([^;]*)"),
        n = document.cookie.match(t);
        return n && n[1] || "";
    },
    set: function(e, t) {
        var n = new Date;
        n.setDate(n.getDate() + 1);
        var r = n.toGMTString();
        return document.cookie = e + "=" + t + ";expires=" + r,
        !0;
    }
},
title = trim(htmlDecode(msg_title)),
sourceurl = trim(htmlDecode(msg_source_url));

msg_link = htmlDecode(msg_link),
function() {
    function e() {
        var e = "",
        t = msg_cdn_url,
        n = msg_link,
        r = htmlDecode(msg_title),
        i = htmlDecode(msg_desc);
        i = i || n,
        WeixinJSBridge.call("hideToolbar"),
        "1" == is_limit_user && WeixinJSBridge.call("hideOptionMenu"),
        WeixinJSBridge.on("menu:share:appmessage",
        function(s) {
            var o = 1;
            s.scene == "favorite" && (o = 4),
            WeixinJSBridge.invoke("sendAppMessage", {
                appid: e,
                img_url: t,
                img_width: "640",
                img_height: "640",
                link: share_scene(n, o),
                desc: i,
                title: r
            },
            function(e) {
                report(n, fakeid, o);
            });
        }),
        WeixinJSBridge.on("menu:share:timeline",
        function(e) {
            report(n, fakeid, 2),
            WeixinJSBridge.invoke("shareTimeline", {
                img_url: t,
                img_width: "640",
                img_height: "640",
                link: share_scene(n, 2),
                desc: i,
                title: r
            },
            function(e) {});
        });
        var s = "";
        WeixinJSBridge.on("menu:share:weibo",
        function(e) {
            WeixinJSBridge.invoke("shareWeibo", {
                content: r + share_scene(n, 3),
                url: share_scene(n, 3)
            },
            function(e) {
                report(n, fakeid, 3);
            });
        }),
        WeixinJSBridge.on("menu:share:facebook",
        function(e) {
            report(n, fakeid, 4),
            WeixinJSBridge.invoke("shareFB", {
                img_url: t,
                img_width: "640",
                img_height: "640",
                link: share_scene(n, 4),
                desc: i,
                title: r
            },
            function(e) {});
        }),
        WeixinJSBridge.on("menu:general:share",
        function(s) {
            var o = 0;
            switch (s.shareTo) {
            case "friend":
                o = 1;
                break;
            case "timeline":
                o = 2;
                break;
            case "weibo":
                o = 3;
            }
            s.generalShare({
                appid: e,
                img_url: t,
                img_width: "640",
                img_height: "640",
                link: share_scene(n, o),
                desc: i,
                title: r
            },
            function(e) {
                report(n, fakeid, o);
            });
        });
        var o = {
            "network_type:fail": "fail",
            "network_type:edge": "2g",
            "network_type:wwan": "3g",
            "network_type:wifi": "wifi"
        };
        typeof WeixinJSBridge != "undefined" && WeixinJSBridge.invoke && WeixinJSBridge.invoke("getNetworkType", {},
        function(e) {
            networkType = o[e.err_msg],
            initpicReport();
        });
    }
    typeof WeixinJSBridge == "undefined" ? document.addEventListener ? document.addEventListener("WeixinJSBridgeReady", e, !1) : document.attachEvent && (document.attachEvent("WeixinJSBridgeReady", e), document.attachEvent("onWeixinJSBridgeReady", e)) : e();
} (),
function() {
    var e = null,
    t = 0,
    n = msg_link.split("?").pop(),
    r = hash(n);
    window.addEventListener ? (window.addEventListener("load",
    function() {
        t = cookie.get(r),
        window.scrollTo(0, t);
    },
    !1), window.addEventListener("unload",
    function() {
        cookie.set(r, t),
        reportTimeOnPage();
    },
    !1), window.addEventListener("scroll",
    function() {
        clearTimeout(e),
        e = setTimeout(function() {
            t = window.pageYOffset;
        },
        500);
    },
    !1), document.addEventListener("touchmove",
    function() {
        clearTimeout(e),
        e = setTimeout(function() {
            t = window.pageYOffset;
        },
        500);
    },
    !1)) : window.attachEvent && (window.attachEvent("load",
    function() {
        t = cookie.get(r),
        window.scrollTo(0, t);
    },
    !1), window.attachEvent("unload",
    function() {
        cookie.set(r, t),
        reportTimeOnPage();
    },
    !1), window.attachEvent("scroll",
    function() {
        clearTimeout(e),
        e = setTimeout(function() {
            t = window.pageYOffset;
        },
        500);
    },
    !1), document.attachEvent("touchmove",
    function() {
        clearTimeout(e),
        e = setTimeout(function() {
            t = window.pageYOffset;
        },
        500);
    },
    !1));
} ();