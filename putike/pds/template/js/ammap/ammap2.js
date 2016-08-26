(function() {
    var d;
    window.AmCharts ? d = window.AmCharts :(d = {}, window.AmCharts = d, d.themes = {},
    d.maps = {}, d.inheriting = {}, d.charts = [], d.onReadyArray = [], d.useUTC = !1,
    d.updateRate = 30, d.uid = 0, d.lang = {}, d.translations = {}, d.mapTranslations = {},
    d.windows = {}, d.initHandlers = []);
    d.Class = function(a) {
        var b = function() {
            arguments[0] !== d.inheriting && (this.events = {}, this.construct.apply(this, arguments));
        };
        a.inherits ? (b.prototype = new a.inherits(d.inheriting), b.base = a.inherits.prototype,
        delete a.inherits) :(b.prototype.createEvents = function() {
            for (var a = 0, b = arguments.length; a < b; a++) this.events[arguments[a]] = [];
        }, b.prototype.listenTo = function(a, b, c) {
            this.removeListener(a, b, c);
            a.events[b].push({
                handler:c,
                scope:this
            });
        }, b.prototype.addListener = function(a, b, c) {
            this.removeListener(this, a, b);
            this.events[a].push({
                handler:b,
                scope:c
            });
        }, b.prototype.removeListener = function(a, b, c) {
            if (a && a.events) for (a = a.events[b], b = a.length - 1; 0 <= b; b--) a[b].handler === c && a.splice(b, 1);
        }, b.prototype.fire = function(a, b) {
            for (var c = this.events[a], d = 0, k = c.length; d < k; d++) {
                var l = c[d];
                l.handler.call(l.scope, b);
            }
        });
        for (var c in a) b.prototype[c] = a[c];
        return b;
    };
    d.addChart = function(a) {
        d.updateInt || (d.updateInt = setInterval(function() {
            d.update();
        }, Math.round(1e3 / d.updateRate)));
        d.charts.push(a);
    };
    d.removeChart = function(a) {
        for (var b = d.charts, c = b.length - 1; 0 <= c; c--) b[c] == a && b.splice(c, 1);
        0 === b.length && d.updateInt && clearInterval(d.updateInt);
    };
    d.isModern = !0;
    d.getIEVersion = function() {
        var a = 0, b, c;
        "Microsoft Internet Explorer" == navigator.appName && (b = navigator.userAgent,
        c = /MSIE ([0-9]{1,}[.0-9]{0,})/, null !== c.exec(b) && (a = parseFloat(RegExp.$1)));
        return a;
    };
    d.applyLang = function(a, b) {
        var c = d.translations;
        b.dayNames = d.extend({}, d.dayNames);
        b.shortDayNames = d.extend({}, d.shortDayNames);
        b.monthNames = d.extend({}, d.monthNames);
        b.shortMonthNames = d.extend({}, d.shortMonthNames);
        c && (c = c[a]) && (d.lang = c, c.monthNames && (b.dayNames = d.extend({}, c.dayNames),
        b.shortDayNames = d.extend({}, c.shortDayNames), b.monthNames = d.extend({}, c.monthNames),
        b.shortMonthNames = d.extend({}, c.shortMonthNames)));
    };
    d.IEversion = d.getIEVersion();
    9 > d.IEversion && 0 < d.IEversion && (d.isModern = !1, d.isIE = !0);
    d.dx = 0;
    d.dy = 0;
    if (document.addEventListener || window.opera) d.isNN = !0, d.isIE = !1, d.dx = .5,
    d.dy = .5;
    document.attachEvent && (d.isNN = !1, d.isIE = !0, d.isModern || (d.dx = 0, d.dy = 0));
    window.chrome && (d.chrome = !0);
    d.handleMouseUp = function(a) {
        for (var b = d.charts, c = 0; c < b.length; c++) {
            var h = b[c];
            h && h.handleReleaseOutside && h.handleReleaseOutside(a);
        }
    };
    d.handleMouseMove = function(a) {
        for (var b = d.charts, c = 0; c < b.length; c++) {
            var h = b[c];
            h && h.handleMouseMove && h.handleMouseMove(a);
        }
    };
    d.handleWheel = function(a) {
        for (var b = d.charts, c = 0; c < b.length; c++) {
            var h = b[c];
            if (h && h.mouseIsOver) {
                h.mouseWheelScrollEnabled || h.mouseWheelZoomEnabled ? h.handleWheel && h.handleWheel(a) :a.stopPropagation && a.stopPropagation();
                break;
            }
        }
    };
    d.resetMouseOver = function() {
        for (var a = d.charts, b = 0; b < a.length; b++) {
            var c = a[b];
            c && (c.mouseIsOver = !1);
        }
    };
    d.ready = function(a) {
        d.onReadyArray.push(a);
    };
    d.handleLoad = function() {
        d.isReady = !0;
        for (var a = d.onReadyArray, b = 0; b < a.length; b++) {
            var c = a[b];
            isNaN(d.processDelay) ? c() :setTimeout(c, d.processDelay * b);
        }
    };
    d.addInitHandler = function(a, b) {
        d.initHandlers.push({
            method:a,
            types:b
        });
    };
    d.callInitHandler = function(a) {
        var b = d.initHandlers;
        if (d.initHandlers) for (var c = 0; c < b.length; c++) {
            var h = b[c];
            h.types ? d.isInArray(h.types, a.type) && h.method(a) :h.method(a);
        }
    };
    d.getUniqueId = function() {
        d.uid++;
        return "AmChartsEl-" + d.uid;
    };
    d.isNN && (document.addEventListener("mousemove", d.handleMouseMove, !0), document.addEventListener("mouseup", d.handleMouseUp, !0),
    window.addEventListener("load", d.handleLoad, !0), window.addEventListener("DOMMouseScroll", d.handleWheel, !0),
    document.addEventListener("mousewheel", d.handleWheel, !0));
    d.isIE && (document.attachEvent("onmousemove", d.handleMouseMove), document.attachEvent("onmouseup", d.handleMouseUp),
    window.attachEvent("onload", d.handleLoad));
    d.clear = function() {
        var a = d.charts;
        if (a) for (var b = 0; b < a.length; b++) a[b].clear();
        d.updateInt && clearInterval(d.updateInt);
        d.charts = null;
        d.isNN && (document.removeEventListener("mousemove", d.handleMouseMove, !0), document.removeEventListener("mouseup", d.handleMouseUp, !0),
        window.removeEventListener("load", d.handleLoad, !0), window.removeEventListener("DOMMouseScroll", d.handleWheel, !0),
        document.removeEventListener("mousewheel", d.handleWheel, !0));
        d.isIE && (document.detachEvent("onmousemove", d.handleMouseMove), document.detachEvent("onmouseup", d.handleMouseUp),
        window.detachEvent("onload", d.handleLoad));
    };
    d.makeChart = function(a, b, c) {
        var h = b.type, f = b.theme;
        d.isString(f) && (f = d.themes[f], b.theme = f);
        var e;
        switch (h) {
          case "serial":
            e = new d.AmSerialChart(f);
            break;

          case "xy":
            e = new d.AmXYChart(f);
            break;

          case "pie":
            e = new d.AmPieChart(f);
            break;

          case "radar":
            e = new d.AmRadarChart(f);
            break;

          case "gauge":
            e = new d.AmAngularGauge(f);
            break;

          case "funnel":
            e = new d.AmFunnelChart(f);
            break;

          case "map":
            e = new d.AmMap(f);
            break;

          case "stock":
            e = new d.AmStockChart(f);
            break;

          case "gantt":
            e = new d.AmGanttChart(f);
        }
        d.extend(e, b);
        d.isReady ? isNaN(c) ? e.write(a) :setTimeout(function() {
            d.realWrite(e, a);
        }, c) :d.ready(function() {
            isNaN(c) ? e.write(a) :setTimeout(function() {
                d.realWrite(e, a);
            }, c);
        });
        return e;
    };
    d.realWrite = function(a, b) {
        a.write(b);
    };
    d.updateCount = 0;
    d.validateAt = Math.round(d.updateRate / 5);
    d.update = function() {
        var a = d.charts;
        d.updateCount++;
        var b = !1;
        d.updateCount == d.validateAt && (b = !0, d.updateCount = 0);
        if (a) for (var c = 0; c < a.length; c++) a[c].update && a[c].update(), b && a[c].validateSize && a[c].validateSize();
    };
    d.bezierX = 3;
    d.bezierY = 6;
})();

(function() {
    var d = window.AmCharts;
    d.toBoolean = function(a, b) {
        if (void 0 === a) return b;
        switch (String(a).toLowerCase()) {
          case "true":
          case "yes":
          case "1":
            return !0;

          case "false":
          case "no":
          case "0":
          case null:
            return !1;

          default:
            return Boolean(a);
        }
    };
    d.removeFromArray = function(a, b) {
        var c;
        if (void 0 !== b && void 0 !== a) for (c = a.length - 1; 0 <= c; c--) a[c] == b && a.splice(c, 1);
    };
    d.isInArray = function(a, b) {
        for (var c = 0; c < a.length; c++) if (a[c] == b) return !0;
        return !1;
    };
    d.getDecimals = function(a) {
        var b = 0;
        isNaN(a) || (a = String(a), -1 != a.indexOf("e-") ? b = Number(a.split("-")[1]) :-1 != a.indexOf(".") && (b = a.split(".")[1].length));
        return b;
    };
    d.wrappedText = function(a, b, c, h, f, e, g, k, l) {
        var m = d.text(a, b, c, h, f, e, g), n = "\n";
        d.isModern || (n = "<br>");
        if (10 < l) return m;
        if (m) {
            var p = m.getBBox();
            if (p.width > k) {
                p = Math.ceil(p.width / k);
                m.remove();
                for (var m = [], u = 0; -1 < (u = b.indexOf(" ", u)); ) m.push(u), u += 1;
                for (var r, u = 0; u < m.length; u += Math.ceil(m.length / p)) r = m[u], b = b.substr(0, r) + n + b.substr(r + 1);
                if (isNaN(r)) {
                    if (0 === l) for (u = 1; u < p; u++) r = Math.round(b.length / p * u), b = b.substr(0, r) + n + b.substr(r);
                    return d.text(a, b, c, h, f, e, g);
                }
                return d.wrappedText(a, b, c, h, f, e, g, k, l + 1);
            }
            return m;
        }
    };
    d.getStyle = function(a, b) {
        var c = "";
        document.defaultView && document.defaultView.getComputedStyle ? c = document.defaultView.getComputedStyle(a, "").getPropertyValue(b) :a.currentStyle && (b = b.replace(/\-(\w)/g, function(a, b) {
            return b.toUpperCase();
        }), c = a.currentStyle[b]);
        return c;
    };
    d.removePx = function(a) {
        if (void 0 !== a) return Number(a.substring(0, a.length - 2));
    };
    d.getURL = function(a, b) {
        if (a) if ("_self" != b && b) if ("_top" == b && window.top) window.top.location.href = a; else if ("_parent" == b && window.parent) window.parent.location.href = a; else if ("_blank" == b) window.open(a); else {
            var c = document.getElementsByName(b)[0];
            c ? c.src = a :(c = d.windows[b]) ? c.opener && !c.opener.closed ? c.location.href = a :d.windows[b] = window.open(a) :d.windows[b] = window.open(a);
        } else window.location.href = a;
    };
    d.ifArray = function(a) {
        return a && 0 < a.length ? !0 :!1;
    };
    d.callMethod = function(a, b) {
        var c;
        for (c = 0; c < b.length; c++) {
            var h = b[c];
            if (h) {
                if (h[a]) h[a]();
                var d = h.length;
                if (0 < d) {
                    var e;
                    for (e = 0; e < d; e++) {
                        var g = h[e];
                        if (g && g[a]) g[a]();
                    }
                }
            }
        }
    };
    d.toNumber = function(a) {
        return "number" == typeof a ? a :Number(String(a).replace(/[^0-9\-.]+/g, ""));
    };
    d.toColor = function(a) {
        if ("" !== a && void 0 !== a) if (-1 != a.indexOf(",")) {
            a = a.split(",");
            var b;
            for (b = 0; b < a.length; b++) {
                var c = a[b].substring(a[b].length - 6, a[b].length);
                a[b] = "#" + c;
            }
        } else a = a.substring(a.length - 6, a.length), a = "#" + a;
        return a;
    };
    d.toCoordinate = function(a, b, c) {
        var h;
        void 0 !== a && (a = String(a), c && c < b && (b = c), h = Number(a), -1 != a.indexOf("!") && (h = b - Number(a.substr(1))),
        -1 != a.indexOf("%") && (h = b * Number(a.substr(0, a.length - 1)) / 100));
        return h;
    };
    d.fitToBounds = function(a, b, c) {
        a < b && (a = b);
        a > c && (a = c);
        return a;
    };
    d.isDefined = function(a) {
        return void 0 === a ? !1 :!0;
    };
    d.stripNumbers = function(a) {
        return a.replace(/[0-9]+/g, "");
    };
    d.roundTo = function(a, b) {
        if (0 > b) return a;
        var c = Math.pow(10, b);
        return Math.round(a * c) / c;
    };
    d.toFixed = function(a, b) {
        var c = String(Math.round(a * Math.pow(10, b)));
        if (0 < b) {
            var h = c.length;
            if (h < b) {
                var d;
                for (d = 0; d < b - h; d++) c = "0" + c;
            }
            h = c.substring(0, c.length - b);
            "" === h && (h = 0);
            return h + "." + c.substring(c.length - b, c.length);
        }
        return String(c);
    };
    d.formatDuration = function(a, b, c, h, f, e) {
        var g = d.intervals, k = e.decimalSeparator;
        if (a >= g[b].contains) {
            var l = a - Math.floor(a / g[b].contains) * g[b].contains;
            "ss" == b && (l = d.formatNumber(l, e), 1 == l.split(k)[0].length && (l = "0" + l));
            ("mm" == b || "hh" == b) && 10 > l && (l = "0" + l);
            c = l + "" + h[b] + "" + c;
            a = Math.floor(a / g[b].contains);
            b = g[b].nextInterval;
            return d.formatDuration(a, b, c, h, f, e);
        }
        "ss" == b && (a = d.formatNumber(a, e), 1 == a.split(k)[0].length && (a = "0" + a));
        ("mm" == b || "hh" == b) && 10 > a && (a = "0" + a);
        c = a + "" + h[b] + "" + c;
        if (g[f].count > g[b].count) for (a = g[b].count; a < g[f].count; a++) b = g[b].nextInterval,
        "ss" == b || "mm" == b || "hh" == b ? c = "00" + h[b] + "" + c :"DD" == b && (c = "0" + h[b] + "" + c);
        ":" == c.charAt(c.length - 1) && (c = c.substring(0, c.length - 1));
        return c;
    };
    d.formatNumber = function(a, b, c, h, f) {
        a = d.roundTo(a, b.precision);
        isNaN(c) && (c = b.precision);
        var e = b.decimalSeparator;
        b = b.thousandsSeparator;
        var g;
        g = 0 > a ? "-" :"";
        a = Math.abs(a);
        var k = String(a), l = !1;
        -1 != k.indexOf("e") && (l = !0);
        0 <= c && !l && (k = d.toFixed(a, c));
        var m = "";
        if (l) m = k; else {
            var k = k.split("."), l = String(k[0]), n;
            for (n = l.length; 0 <= n; n -= 3) m = n != l.length ? 0 !== n ? l.substring(n - 3, n) + b + m :l.substring(n - 3, n) + m :l.substring(n - 3, n);
            void 0 !== k[1] && (m = m + e + k[1]);
            void 0 !== c && 0 < c && "0" != m && (m = d.addZeroes(m, e, c));
        }
        m = g + m;
        "" === g && !0 === h && 0 !== a && (m = "+" + m);
        !0 === f && (m += "%");
        return m;
    };
    d.addZeroes = function(a, b, c) {
        a = a.split(b);
        void 0 === a[1] && 0 < c && (a[1] = "0");
        return a[1].length < c ? (a[1] += "0", d.addZeroes(a[0] + b + a[1], b, c)) :void 0 !== a[1] ? a[0] + b + a[1] :a[0];
    };
    d.scientificToNormal = function(a) {
        var b;
        a = String(a).split("e");
        var c;
        if ("-" == a[1].substr(0, 1)) {
            b = "0.";
            for (c = 0; c < Math.abs(Number(a[1])) - 1; c++) b += "0";
            b += a[0].split(".").join("");
        } else {
            var h = 0;
            b = a[0].split(".");
            b[1] && (h = b[1].length);
            b = a[0].split(".").join("");
            for (c = 0; c < Math.abs(Number(a[1])) - h; c++) b += "0";
        }
        return b;
    };
    d.toScientific = function(a, b) {
        if (0 === a) return "0";
        var c = Math.floor(Math.log(Math.abs(a)) * Math.LOG10E), h = String(h).split(".").join(b);
        return String(h) + "e" + c;
    };
    d.randomColor = function() {
        return "#" + ("00000" + (16777216 * Math.random() << 0).toString(16)).substr(-6);
    };
    d.hitTest = function(a, b, c) {
        var h = !1, f = a.x, e = a.x + a.width, g = a.y, k = a.y + a.height, l = d.isInRectangle;
        h || (h = l(f, g, b));
        h || (h = l(f, k, b));
        h || (h = l(e, g, b));
        h || (h = l(e, k, b));
        h || !0 === c || (h = d.hitTest(b, a, !0));
        return h;
    };
    d.isInRectangle = function(a, b, c) {
        return a >= c.x - 5 && a <= c.x + c.width + 5 && b >= c.y - 5 && b <= c.y + c.height + 5 ? !0 :!1;
    };
    d.isPercents = function(a) {
        if (-1 != String(a).indexOf("%")) return !0;
    };
    d.findPosX = function(a) {
        var b = a, c = a.offsetLeft;
        if (a.offsetParent) {
            for (;a = a.offsetParent; ) c += a.offsetLeft;
            for (;(b = b.parentNode) && b != document.body; ) c -= b.scrollLeft || 0;
        }
        return c;
    };
    d.findPosY = function(a) {
        var b = a, c = a.offsetTop;
        if (a.offsetParent) {
            for (;a = a.offsetParent; ) c += a.offsetTop;
            for (;(b = b.parentNode) && b != document.body; ) c -= b.scrollTop || 0;
        }
        return c;
    };
    d.findIfFixed = function(a) {
        if (a.offsetParent) for (;a = a.offsetParent; ) if ("fixed" == d.getStyle(a, "position")) return !0;
        return !1;
    };
    d.findIfAuto = function(a) {
        return a.style && "auto" == d.getStyle(a, "overflow") ? !0 :a.parentNode ? d.findIfAuto(a.parentNode) :!1;
    };
    d.findScrollLeft = function(a, b) {
        a.scrollLeft && (b += a.scrollLeft);
        return a.parentNode ? d.findScrollLeft(a.parentNode, b) :b;
    };
    d.findScrollTop = function(a, b) {
        a.scrollTop && (b += a.scrollTop);
        return a.parentNode ? d.findScrollTop(a.parentNode, b) :b;
    };
    d.formatValue = function(a, b, c, h, f, e, g, k) {
        if (b) {
            void 0 === f && (f = "");
            var l;
            for (l = 0; l < c.length; l++) {
                var m = c[l], n = b[m];
                void 0 !== n && (n = e ? d.addPrefix(n, k, g, h) :d.formatNumber(n, h), a = a.replace(new RegExp("\\[\\[" + f + "" + m + "\\]\\]", "g"), n));
            }
        }
        return a;
    };
    d.formatDataContextValue = function(a, b) {
        if (a) {
            var c = a.match(/\[\[.*?\]\]/g), h;
            for (h = 0; h < c.length; h++) {
                var d = c[h], d = d.substr(2, d.length - 4);
                void 0 !== b[d] && (a = a.replace(new RegExp("\\[\\[" + d + "\\]\\]", "g"), b[d]));
            }
        }
        return a;
    };
    d.massReplace = function(a, b) {
        for (var c in b) if (b.hasOwnProperty(c)) {
            var d = b[c];
            void 0 === d && (d = "");
            a = a.replace(c, d);
        }
        return a;
    };
    d.cleanFromEmpty = function(a) {
        return a.replace(/\[\[[^\]]*\]\]/g, "");
    };
    d.addPrefix = function(a, b, c, h, f) {
        var e = d.formatNumber(a, h), g = "", k, l, m;
        if (0 === a) return "0";
        0 > a && (g = "-");
        a = Math.abs(a);
        if (1 < a) for (k = b.length - 1; -1 < k; k--) {
            if (a >= b[k].number && (l = a / b[k].number, m = Number(h.precision), 1 > m && (m = 1),
            c = d.roundTo(l, m), m = d.formatNumber(c, {
                precision:-1,
                decimalSeparator:h.decimalSeparator,
                thousandsSeparator:h.thousandsSeparator
            }), !f || l == c)) {
                e = g + "" + m + "" + b[k].prefix;
                break;
            }
        } else for (k = 0; k < c.length; k++) if (a <= c[k].number) {
            l = a / c[k].number;
            m = Math.abs(Math.round(Math.log(l) * Math.LOG10E));
            l = d.roundTo(l, m);
            e = g + "" + l + "" + c[k].prefix;
            break;
        }
        return e;
    };
    d.remove = function(a) {
        a && a.remove();
    };
    d.getEffect = function(a) {
        ">" == a && (a = "easeOutSine");
        "<" == a && (a = "easeInSine");
        "elastic" == a && (a = "easeOutElastic");
        return a;
    };
    d.getObjById = function(a, b) {
        var c, d;
        for (d = 0; d < a.length; d++) {
            var f = a[d];
            f.id == b && (c = f);
        }
        return c;
    };
    d.applyTheme = function(a, b, c) {
        b || (b = d.theme);
        b && b[c] && d.extend(a, b[c]);
    };
    d.isString = function(a) {
        return "string" == typeof a ? !0 :!1;
    };
    d.extend = function(a, b, c) {
        var d;
        a || (a = {});
        for (d in b) c ? a.hasOwnProperty(d) || (a[d] = b[d]) :a[d] = b[d];
        return a;
    };
    d.copyProperties = function(a, b) {
        for (var c in a) a.hasOwnProperty(c) && "events" != c && void 0 !== a[c] && "function" != typeof a[c] && "cname" != c && (b[c] = a[c]);
    };
    d.processObject = function(a, b, c, h) {
        !1 === a instanceof b && (a = h ? d.extend(new b(c), a) :d.extend(a, new b(c), !0));
        return a;
    };
    d.fixNewLines = function(a) {
        var b = RegExp("\\n", "g");
        a && (a = a.replace(b, "<br />"));
        return a;
    };
    d.fixBrakes = function(a) {
        if (d.isModern) {
            var b = RegExp("<br>", "g");
            a && (a = a.replace(b, "\n"));
        } else a = d.fixNewLines(a);
        return a;
    };
    d.deleteObject = function(a, b) {
        if (a) {
            if (void 0 === b || null === b) b = 20;
            if (0 !== b) if ("[object Array]" === Object.prototype.toString.call(a)) for (var c = 0; c < a.length; c++) d.deleteObject(a[c], b - 1),
            a[c] = null; else if (a && !a.tagName) try {
                for (c in a) a[c] && ("object" == typeof a[c] && d.deleteObject(a[c], b - 1), "function" != typeof a[c] && (a[c] = null));
            } catch (h) {}
        }
    };
    d.bounce = function(a, b, c, d, f) {
        return (b /= f) < 1 / 2.75 ? 7.5625 * d * b * b + c :b < 2 / 2.75 ? d * (7.5625 * (b -= 1.5 / 2.75) * b + .75) + c :b < 2.5 / 2.75 ? d * (7.5625 * (b -= 2.25 / 2.75) * b + .9375) + c :d * (7.5625 * (b -= 2.625 / 2.75) * b + .984375) + c;
    };
    d.easeInSine = function(a, b, c, d, f) {
        return -d * Math.cos(b / f * (Math.PI / 2)) + d + c;
    };
    d.easeOutSine = function(a, b, c, d, f) {
        return d * Math.sin(b / f * (Math.PI / 2)) + c;
    };
    d.easeOutElastic = function(a, b, c, d, f) {
        a = 1.70158;
        var e = 0, g = d;
        if (0 === b) return c;
        if (1 == (b /= f)) return c + d;
        e || (e = .3 * f);
        g < Math.abs(d) ? (g = d, a = e / 4) :a = e / (2 * Math.PI) * Math.asin(d / g);
        return g * Math.pow(2, -10 * b) * Math.sin(2 * (b * f - a) * Math.PI / e) + d + c;
    };
    d.fixStepE = function(a) {
        a = a.toExponential(0).split("e");
        var b = Number(a[1]);
        9 == Number(a[0]) && b++;
        return d.generateNumber(1, b);
    };
    d.generateNumber = function(a, b) {
        var c = "", d;
        d = 0 > b ? Math.abs(b) - 1 :Math.abs(b);
        var f;
        for (f = 0; f < d; f++) c += "0";
        return 0 > b ? Number("0." + c + String(a)) :Number(String(a) + c);
    };
    d.setCN = function(a, b, c, d) {
        if (a.addClassNames && b && (b = b.node) && c) {
            var f = b.getAttribute("class");
            a = a.classNamePrefix + "-";
            d && (a = "");
            f ? b.setAttribute("class", f + " " + a + c) :b.setAttribute("class", a + c);
        }
    };
    d.parseDefs = function(a, b) {
        for (var c in a) {
            var h = typeof a[c];
            if (0 < a[c].length && "object" == h) for (var f = 0; f < a[c].length; f++) h = document.createElementNS(d.SVG_NS, c),
            b.appendChild(h), d.parseDefs(a[c][f], h); else "object" == h ? (h = document.createElementNS(d.SVG_NS, c),
            b.appendChild(h), d.parseDefs(a[c], h)) :b.setAttribute(c, a[c]);
        }
    };
})();

(function() {
    var d = window.AmCharts;
    d.AmDraw = d.Class({
        construct:function(a, b, c, h) {
            d.SVG_NS = "http://www.w3.org/2000/svg";
            d.SVG_XLINK = "http://www.w3.org/1999/xlink";
            d.hasSVG = !!document.createElementNS && !!document.createElementNS(d.SVG_NS, "svg").createSVGRect;
            1 > b && (b = 10);
            1 > c && (c = 10);
            this.div = a;
            this.width = b;
            this.height = c;
            this.rBin = document.createElement("div");
            d.hasSVG ? (d.SVG = !0, b = this.createSvgElement("svg"), a.appendChild(b), this.container = b,
            this.addDefs(h), this.R = new d.SVGRenderer(this)) :d.isIE && d.VMLRenderer && (d.VML = !0,
            d.vmlStyleSheet || (document.namespaces.add("amvml", "urn:schemas-microsoft-com:vml"),
            31 > document.styleSheets.length ? (b = document.createStyleSheet(), b.addRule(".amvml", "behavior:url(#default#VML); display:inline-block; antialias:true"),
            d.vmlStyleSheet = b) :document.styleSheets[0].addRule(".amvml", "behavior:url(#default#VML); display:inline-block; antialias:true")),
            this.container = a, this.R = new d.VMLRenderer(this, h), this.R.disableSelection(a));
        },
        createSvgElement:function(a) {
            return document.createElementNS(d.SVG_NS, a);
        },
        circle:function(a, b, c, h) {
            var f = new d.AmDObject("circle", this);
            f.attr({
                r:c,
                cx:a,
                cy:b
            });
            this.addToContainer(f.node, h);
            return f;
        },
        ellipse:function(a, b, c, h, f) {
            var e = new d.AmDObject("ellipse", this);
            e.attr({
                rx:c,
                ry:h,
                cx:a,
                cy:b
            });
            this.addToContainer(e.node, f);
            return e;
        },
        setSize:function(a, b) {
            0 < a && 0 < b && (this.container.style.width = a + "px", this.container.style.height = b + "px");
        },
        rect:function(a, b, c, h, f, e, g) {
            var k = new d.AmDObject("rect", this);
            d.VML && (f = Math.round(100 * f / Math.min(c, h)), c += 2 * e, h += 2 * e, k.bw = e,
            k.node.style.marginLeft = -e, k.node.style.marginTop = -e);
            1 > c && (c = 1);
            1 > h && (h = 1);
            k.attr({
                x:a,
                y:b,
                width:c,
                height:h,
                rx:f,
                ry:f,
                "stroke-width":e
            });
            this.addToContainer(k.node, g);
            return k;
        },
        image:function(a, b, c, h, f, e) {
            var g = new d.AmDObject("image", this);
            g.attr({
                x:b,
                y:c,
                width:h,
                height:f
            });
            this.R.path(g, a);
            this.addToContainer(g.node, e);
            return g;
        },
        addToContainer:function(a, b) {
            b || (b = this.container);
            b.appendChild(a);
        },
        text:function(a, b, c) {
            return this.R.text(a, b, c);
        },
        path:function(a, b, c, h) {
            var f = new d.AmDObject("path", this);
            h || (h = "100,100");
            f.attr({
                cs:h
            });
            c ? f.attr({
                dd:a
            }) :f.attr({
                d:a
            });
            this.addToContainer(f.node, b);
            return f;
        },
        set:function(a) {
            return this.R.set(a);
        },
        remove:function(a) {
            if (a) {
                var b = this.rBin;
                b.appendChild(a);
                b.innerHTML = "";
            }
        },
        renderFix:function() {
            var a = this.container, b = a.style, c;
            try {
                c = a.getScreenCTM() || a.createSVGMatrix();
            } catch (d) {
                c = a.createSVGMatrix();
            }
            a = 1 - c.e % 1;
            c = 1 - c.f % 1;
            .5 < a && --a;
            .5 < c && --c;
            a && (b.left = a + "px");
            c && (b.top = c + "px");
        },
        update:function() {
            this.R.update();
        },
        addDefs:function(a) {
            if (d.hasSVG) {
                var b = this.createSvgElement("desc"), c = this.container;
                c.setAttribute("version", "1.1");
                c.style.position = "absolute";
                this.setSize(this.width, this.height);
                d.rtl && (c.setAttribute("direction", "rtl"), c.style.left = "auto", c.style.right = "0px");
                b.appendChild(document.createTextNode("JavaScript chart by amCharts " + a.version));
                c.appendChild(b);
                a.defs && (b = this.createSvgElement("defs"), c.appendChild(b), d.parseDefs(a.defs, b),
                this.defs = b);
            }
        }
    });
})();

(function() {
    var d = window.AmCharts;
    d.AmDObject = d.Class({
        construct:function(a, b) {
            this.D = b;
            this.R = b.R;
            this.node = this.R.create(this, a);
            this.y = this.x = 0;
            this.scale = 1;
        },
        attr:function(a) {
            this.R.attr(this, a);
            return this;
        },
        getAttr:function(a) {
            return this.node.getAttribute(a);
        },
        setAttr:function(a, b) {
            this.R.setAttr(this, a, b);
            return this;
        },
        clipRect:function(a, b, c, d) {
            this.R.clipRect(this, a, b, c, d);
        },
        translate:function(a, b, c, d) {
            d || (a = Math.round(a), b = Math.round(b));
            this.R.move(this, a, b, c);
            this.x = a;
            this.y = b;
            this.scale = c;
            this.angle && this.rotate(this.angle);
        },
        rotate:function(a, b) {
            this.R.rotate(this, a, b);
            this.angle = a;
        },
        animate:function(a, b, c) {
            for (var h in a) if (a.hasOwnProperty(h)) {
                var f = h, e = a[h];
                c = d.getEffect(c);
                this.R.animate(this, f, e, b, c);
            }
        },
        push:function(a) {
            if (a) {
                var b = this.node;
                b.appendChild(a.node);
                var c = a.clipPath;
                c && b.appendChild(c);
                (a = a.grad) && b.appendChild(a);
            }
        },
        text:function(a) {
            this.R.setText(this, a);
        },
        remove:function() {
            this.R.remove(this);
        },
        clear:function() {
            var a = this.node;
            if (a.hasChildNodes()) for (;1 <= a.childNodes.length; ) a.removeChild(a.firstChild);
        },
        hide:function() {
            this.setAttr("visibility", "hidden");
        },
        show:function() {
            this.setAttr("visibility", "visible");
        },
        getBBox:function() {
            return this.R.getBBox(this);
        },
        toFront:function() {
            var a = this.node;
            if (a) {
                this.prevNextNode = a.nextSibling;
                var b = a.parentNode;
                b && b.appendChild(a);
            }
        },
        toPrevious:function() {
            var a = this.node;
            a && this.prevNextNode && (a = a.parentNode) && a.insertBefore(this.prevNextNode, null);
        },
        toBack:function() {
            var a = this.node;
            if (a) {
                this.prevNextNode = a.nextSibling;
                var b = a.parentNode;
                if (b) {
                    var c = b.firstChild;
                    c && b.insertBefore(a, c);
                }
            }
        },
        mouseover:function(a) {
            this.R.addListener(this, "mouseover", a);
            return this;
        },
        mouseout:function(a) {
            this.R.addListener(this, "mouseout", a);
            return this;
        },
        click:function(a) {
            this.R.addListener(this, "click", a);
            return this;
        },
        dblclick:function(a) {
            this.R.addListener(this, "dblclick", a);
            return this;
        },
        mousedown:function(a) {
            this.R.addListener(this, "mousedown", a);
            return this;
        },
        mouseup:function(a) {
            this.R.addListener(this, "mouseup", a);
            return this;
        },
        touchstart:function(a) {
            this.R.addListener(this, "touchstart", a);
            return this;
        },
        touchend:function(a) {
            this.R.addListener(this, "touchend", a);
            return this;
        },
        contextmenu:function(a) {
            this.node.addEventListener ? this.node.addEventListener("contextmenu", a, !0) :this.R.addListener(this, "contextmenu", a);
            return this;
        },
        stop:function() {
            d.removeFromArray(this.R.animations, this.an_x);
            d.removeFromArray(this.R.animations, this.an_y);
        },
        length:function() {
            return this.node.childNodes.length;
        },
        gradient:function(a, b, c) {
            this.R.gradient(this, a, b, c);
        },
        pattern:function(a, b) {
            a && this.R.pattern(this, a, b);
        }
    });
})();

(function() {
    var d = window.AmCharts;
    d.SVGRenderer = d.Class({
        construct:function(a) {
            this.D = a;
            this.animations = [];
        },
        create:function(a, b) {
            return document.createElementNS(d.SVG_NS, b);
        },
        attr:function(a, b) {
            for (var c in b) b.hasOwnProperty(c) && this.setAttr(a, c, b[c]);
        },
        setAttr:function(a, b, c) {
            void 0 !== c && a.node.setAttribute(b, c);
        },
        animate:function(a, b, c, h, f) {
            var e = a.node;
            a["an_" + b] && d.removeFromArray(this.animations, a["an_" + b]);
            "translate" == b ? (e = (e = e.getAttribute("transform")) ? String(e).substring(10, e.length - 1) :"0,0",
            e = e.split(", ").join(" "), e = e.split(" ").join(","), 0 === e && (e = "0,0")) :e = Number(e.getAttribute(b));
            c = {
                obj:a,
                frame:0,
                attribute:b,
                from:e,
                to:c,
                time:h,
                effect:f
            };
            this.animations.push(c);
            a["an_" + b] = c;
        },
        update:function() {
            var a, b = this.animations;
            for (a = b.length - 1; 0 <= a; a--) {
                var c = b[a], h = 1e3 * c.time / d.updateRate, f = c.frame + 1, e = c.obj, g = c.attribute, k, l, m;
                f <= h ? (c.frame++, "translate" == g ? (k = c.from.split(","), g = Number(k[0]),
                k = Number(k[1]), isNaN(k) && (k = 0), l = c.to.split(","), m = Number(l[0]), l = Number(l[1]),
                m = 0 === m - g ? m :Math.round(d[c.effect](0, f, g, m - g, h)), c = 0 === l - k ? l :Math.round(d[c.effect](0, f, k, l - k, h)),
                g = "transform", c = "translate(" + m + "," + c + ")") :(l = Number(c.from), k = Number(c.to),
                m = k - l, c = d[c.effect](0, f, l, m, h), isNaN(c) && (c = k), 0 === m && this.animations.splice(a, 1)),
                this.setAttr(e, g, c)) :("translate" == g ? (l = c.to.split(","), m = Number(l[0]),
                l = Number(l[1]), e.translate(m, l)) :(k = Number(c.to), this.setAttr(e, g, k)),
                this.animations.splice(a, 1));
            }
        },
        getBBox:function(a) {
            if (a = a.node) try {
                return a.getBBox();
            } catch (b) {}
            return {
                width:0,
                height:0,
                x:0,
                y:0
            };
        },
        path:function(a, b) {
            a.node.setAttributeNS(d.SVG_XLINK, "xlink:href", b);
        },
        clipRect:function(a, b, c, h, f) {
            var e = a.node, g = a.clipPath;
            g && this.D.remove(g);
            var k = e.parentNode;
            k && (e = document.createElementNS(d.SVG_NS, "clipPath"), g = d.getUniqueId(), e.setAttribute("id", g),
            this.D.rect(b, c, h, f, 0, 0, e), k.appendChild(e), b = "#", d.baseHref && !d.isIE && (b = this.removeTarget(window.location.href) + b),
            this.setAttr(a, "clip-path", "url(" + b + g + ")"), this.clipPathC++, a.clipPath = e);
        },
        text:function(a, b, c) {
            var h = new d.AmDObject("text", this.D);
            a = String(a).split("\n");
            var f = b["font-size"], e;
            for (e = 0; e < a.length; e++) {
                var g = this.create(null, "tspan");
                g.appendChild(document.createTextNode(a[e]));
                g.setAttribute("y", (f + 2) * e + Math.round(f / 2));
                g.setAttribute("x", 0);
                g.style.fontSize = f + "px";
                h.node.appendChild(g);
            }
            h.node.setAttribute("y", Math.round(f / 2));
            this.attr(h, b);
            this.D.addToContainer(h.node, c);
            return h;
        },
        setText:function(a, b) {
            var c = a.node;
            c && (c.removeChild(c.firstChild), c.appendChild(document.createTextNode(b)));
        },
        move:function(a, b, c, d) {
            isNaN(b) && (b = 0);
            isNaN(c) && (c = 0);
            b = "translate(" + b + "," + c + ")";
            d && (b = b + " scale(" + d + ")");
            this.setAttr(a, "transform", b);
        },
        rotate:function(a, b) {
            var c = a.node.getAttribute("transform"), d = "rotate(" + b + ")";
            c && (d = c + " " + d);
            this.setAttr(a, "transform", d);
        },
        set:function(a) {
            var b = new d.AmDObject("g", this.D);
            this.D.container.appendChild(b.node);
            if (a) {
                var c;
                for (c = 0; c < a.length; c++) b.push(a[c]);
            }
            return b;
        },
        addListener:function(a, b, c) {
            a.node["on" + b] = c;
        },
        gradient:function(a, b, c, h) {
            var f = a.node, e = a.grad;
            e && this.D.remove(e);
            b = document.createElementNS(d.SVG_NS, b);
            e = d.getUniqueId();
            b.setAttribute("id", e);
            if (!isNaN(h)) {
                var g = 0, k = 0, l = 0, m = 0;
                90 == h ? l = 100 :270 == h ? m = 100 :180 == h ? g = 100 :0 === h && (k = 100);
                b.setAttribute("x1", g + "%");
                b.setAttribute("x2", k + "%");
                b.setAttribute("y1", l + "%");
                b.setAttribute("y2", m + "%");
            }
            for (h = 0; h < c.length; h++) g = document.createElementNS(d.SVG_NS, "stop"), k = 100 * h / (c.length - 1),
            0 === h && (k = 0), g.setAttribute("offset", k + "%"), g.setAttribute("stop-color", c[h]),
            b.appendChild(g);
            f.parentNode.appendChild(b);
            c = "#";
            d.baseHref && !d.isIE && (c = this.removeTarget(window.location.href) + c);
            f.setAttribute("fill", "url(" + c + e + ")");
            a.grad = b;
        },
        removeTarget:function(a) {
            return a.split("#")[0];
        },
        pattern:function(a, b, c) {
            var h = a.node;
            isNaN(c) && (c = 1);
            var f = a.patternNode;
            f && this.D.remove(f);
            var f = document.createElementNS(d.SVG_NS, "pattern"), e = d.getUniqueId(), g = b;
            b.url && (g = b.url);
            var k = Number(b.width);
            isNaN(k) && (k = 4);
            var l = Number(b.height);
            isNaN(l) && (l = 4);
            k /= c;
            l /= c;
            c = b.x;
            isNaN(c) && (c = 0);
            var m = -Math.random() * Number(b.randomX);
            isNaN(m) || (c = m);
            m = b.y;
            isNaN(m) && (m = 0);
            var n = -Math.random() * Number(b.randomY);
            isNaN(n) || (m = n);
            f.setAttribute("id", e);
            f.setAttribute("width", k);
            f.setAttribute("height", l);
            f.setAttribute("patternUnits", "userSpaceOnUse");
            f.setAttribute("xlink:href", g);
            b.color && (n = document.createElementNS(d.SVG_NS, "rect"), n.setAttributeNS(null, "height", k),
            n.setAttributeNS(null, "width", l), n.setAttributeNS(null, "fill", b.color), f.appendChild(n));
            this.D.image(g, 0, 0, k, l, f).translate(c, m);
            g = "#";
            d.baseHref && !d.isIE && (g = this.removeTarget(window.location.href) + g);
            h.setAttribute("fill", "url(" + g + e + ")");
            a.patternNode = f;
            h.parentNode.appendChild(f);
        },
        remove:function(a) {
            a.clipPath && this.D.remove(a.clipPath);
            a.grad && this.D.remove(a.grad);
            a.patternNode && this.D.remove(a.patternNode);
            this.D.remove(a.node);
        }
    });
})();

(function() {
    var d = window.AmCharts;
    d.AmChart = d.Class({
        construct:function(a) {
            this.theme = a;
            this.classNamePrefix = "amcharts";
            this.addClassNames = !1;
            this.version = "3.14.1";
            d.addChart(this);
            this.createEvents("dataUpdated", "init", "rendered", "drawn", "failed", "resized");
            this.height = this.width = "100%";
            this.dataChanged = !0;
            this.chartCreated = !1;
            this.previousWidth = this.previousHeight = 0;
            this.backgroundColor = "#FFFFFF";
            this.borderAlpha = this.backgroundAlpha = 0;
            this.color = this.borderColor = "#000000";
            this.fontFamily = "Verdana";
            this.fontSize = 11;
            this.usePrefixes = !1;
            this.precision = -1;
            this.percentPrecision = 2;
            this.decimalSeparator = ".";
            this.thousandsSeparator = ",";
            this.labels = [];
            this.allLabels = [];
            this.titles = [];
            this.marginRight = this.marginLeft = this.autoMarginOffset = 0;
            this.timeOuts = [];
            this.creditsPosition = "top-left";
            var b = document.createElement("div"), c = b.style;
            c.overflow = "hidden";
            c.position = "relative";
            c.textAlign = "left";
            this.chartDiv = b;
            b = document.createElement("div");
            c = b.style;
            c.overflow = "hidden";
            c.position = "relative";
            c.textAlign = "left";
            this.legendDiv = b;
            this.titleHeight = 0;
            this.hideBalloonTime = 150;
            this.handDrawScatter = 2;
            this.handDrawThickness = 1;
            this.prefixesOfBigNumbers = [ {
                number:1e3,
                prefix:"k"
            }, {
                number:1e6,
                prefix:"M"
            }, {
                number:1e9,
                prefix:"G"
            }, {
                number:1e12,
                prefix:"T"
            }, {
                number:1e15,
                prefix:"P"
            }, {
                number:1e18,
                prefix:"E"
            }, {
                number:1e21,
                prefix:"Z"
            }, {
                number:1e24,
                prefix:"Y"
            } ];
            this.prefixesOfSmallNumbers = [ {
                number:1e-24,
                prefix:"y"
            }, {
                number:1e-21,
                prefix:"z"
            }, {
                number:1e-18,
                prefix:"a"
            }, {
                number:1e-15,
                prefix:"f"
            }, {
                number:1e-12,
                prefix:"p"
            }, {
                number:1e-9,
                prefix:"n"
            }, {
                number:1e-6,
                prefix:"��"
            }, {
                number:.001,
                prefix:"m"
            } ];
            this.panEventsEnabled = !0;
            this.product = "amcharts";
            this.animations = [];
            this.balloon = new d.AmBalloon(this.theme);
            this.balloon.chart = this;
            d.applyTheme(this, a, "AmChart");
        },
        drawChart:function() {
            this.drawBackground();
            this.redrawLabels();
            this.drawTitles();
            this.brr();
        },
        drawBackground:function() {
            d.remove(this.background);
            var a = this.container, b = this.backgroundColor, c = this.backgroundAlpha, h = this.set;
            d.isModern || 0 !== c || (c = .001);
            var f = this.updateWidth();
            this.realWidth = f;
            var e = this.updateHeight();
            this.realHeight = e;
            b = d.polygon(a, [ 0, f - 1, f - 1, 0 ], [ 0, 0, e - 1, e - 1 ], b, c, 1, this.borderColor, this.borderAlpha);
            d.setCN(this, b, "bg");
            this.background = b;
            h.push(b);
            if (b = this.backgroundImage) this.path && (b = this.path + b), a = a.image(b, 0, 0, f, e),
            d.setCN(this, b, "bg-image"), this.bgImg = a, h.push(a);
        },
        drawTitles:function() {
            var a = this.titles;
            if (d.ifArray(a)) {
                var b = 20, c;
                for (c = 0; c < a.length; c++) {
                    var h = a[c], h = d.processObject(h, d.Title, this.theme);
                    if (!1 !== h.enabled) {
                        var f = h.color;
                        void 0 === f && (f = this.color);
                        var e = h.size;
                        isNaN(e) && (e = this.fontSize + 2);
                        isNaN(h.alpha);
                        var g = this.marginLeft, f = d.text(this.container, h.text, f, this.fontFamily, e);
                        f.translate(g + (this.realWidth - this.marginRight - g) / 2, b);
                        f.node.style.pointerEvents = "none";
                        d.setCN(this, f, "title");
                        h.id && d.setCN(this, f, "title-" + h.id);
                        g = !0;
                        void 0 !== h.bold && (g = h.bold);
                        g && f.attr({
                            "font-weight":"bold"
                        });
                        f.attr({
                            opacity:h.alpha
                        });
                        b += e + 6;
                        this.freeLabelsSet.push(f);
                    }
                }
            }
        },
        write:function(a) {
            if (a = "object" != typeof a ? document.getElementById(a) :a) {
                for (;a.firstChild; ) a.removeChild(a.firstChild);
                this.div = a;
                a.style.overflow = "hidden";
                a.style.textAlign = "left";
                var b = this.chartDiv, c = this.legendDiv, h = this.legend, f = c.style, e = b.style;
                this.measure();
                this.previousHeight = this.divRealHeight;
                this.previousWidth = this.divRealWidth;
                var g, k = document.createElement("div");
                g = k.style;
                g.position = "relative";
                this.containerDiv = k;
                k.className = this.classNamePrefix + "-main-div";
                b.className = this.classNamePrefix + "-chart-div";
                a.appendChild(k);
                var l = this.exportConfig;
                l && d.AmExport && !this.AmExport && (this.AmExport = new d.AmExport(this, l));
                this.amExport && d.AmExport && (this.AmExport = d.extend(this.amExport, new d.AmExport(this), !0));
                this.AmExport && this.AmExport.init && this.AmExport.init();
                if (h) if (h = this.addLegend(h, h.divId), h.enabled) switch (h.position) {
                  case "bottom":
                    k.appendChild(b);
                    k.appendChild(c);
                    break;

                  case "top":
                    k.appendChild(c);
                    k.appendChild(b);
                    break;

                  case "absolute":
                    g.width = a.style.width;
                    g.height = a.style.height;
                    f.position = "absolute";
                    e.position = "absolute";
                    void 0 !== h.left && (f.left = h.left + "px");
                    void 0 !== h.right && (f.right = h.right + "px");
                    void 0 !== h.top && (f.top = h.top + "px");
                    void 0 !== h.bottom && (f.bottom = h.bottom + "px");
                    h.marginLeft = 0;
                    h.marginRight = 0;
                    k.appendChild(b);
                    k.appendChild(c);
                    break;

                  case "right":
                    g.width = a.style.width;
                    g.height = a.style.height;
                    f.position = "relative";
                    e.position = "absolute";
                    k.appendChild(b);
                    k.appendChild(c);
                    break;

                  case "left":
                    g.width = a.style.width;
                    g.height = a.style.height;
                    f.position = "absolute";
                    e.position = "relative";
                    k.appendChild(b);
                    k.appendChild(c);
                    break;

                  case "outside":
                    k.appendChild(b);
                } else k.appendChild(b); else k.appendChild(b);
                this.listenersAdded || (this.addListeners(), this.listenersAdded = !0);
                this.initChart();
            }
        },
        createLabelsSet:function() {
            d.remove(this.labelsSet);
            this.labelsSet = this.container.set();
            this.freeLabelsSet.push(this.labelsSet);
        },
        initChart:function() {
            this.initHC || (d.callInitHandler(this), this.initHC = !0);
            this.renderFix();
            d.applyLang(this.language, this);
            var a = this.numberFormatter;
            a && (isNaN(a.precision) || (this.precision = a.precision), void 0 !== a.thousandsSeparator && (this.thousandsSeparator = a.thousandsSeparator),
            void 0 !== a.decimalSeparator && (this.decimalSeparator = a.decimalSeparator));
            (a = this.percentFormatter) && !isNaN(a.precision) && (this.percentPrecision = a.precision);
            this.nf = {
                precision:this.precision,
                thousandsSeparator:this.thousandsSeparator,
                decimalSeparator:this.decimalSeparator
            };
            this.pf = {
                precision:this.percentPrecision,
                thousandsSeparator:this.thousandsSeparator,
                decimalSeparator:this.decimalSeparator
            };
            this.divIsFixed = d.findIfFixed(this.chartDiv);
            this.destroy();
            a = 0;
            document.attachEvent && !window.opera && (a = 1);
            this.dmouseX = this.dmouseY = 0;
            var b = document.getElementsByTagName("html")[0];
            b && window.getComputedStyle && (b = window.getComputedStyle(b, null)) && (this.dmouseY = d.removePx(b.getPropertyValue("margin-top")),
            this.dmouseX = d.removePx(b.getPropertyValue("margin-left")));
            this.mouseMode = a;
            (a = this.container) ? (a.container.innerHTML = "", a.width = this.realWidth, a.height = this.realHeight,
            a.addDefs(this), this.chartDiv.appendChild(a.container)) :a = new d.AmDraw(this.chartDiv, this.realWidth, this.realHeight, this);
            a.chart = this;
            d.VML || d.SVG ? (a.handDrawn = this.handDrawn, a.handDrawScatter = this.handDrawScatter,
            a.handDrawThickness = this.handDrawThickness, this.container = a, this.set && this.set.remove(),
            this.set = a.set(), this.gridSet && this.gridSet.remove(), this.gridSet = a.set(),
            this.cursorLineSet && this.cursorLineSet.remove(), this.cursorLineSet = a.set(),
            this.graphsBehindSet && this.graphsBehindSet.remove(), this.graphsBehindSet = a.set(),
            this.bulletBehindSet && this.bulletBehindSet.remove(), this.bulletBehindSet = a.set(),
            this.columnSet && this.columnSet.remove(), this.columnSet = a.set(), this.graphsSet && this.graphsSet.remove(),
            this.graphsSet = a.set(), this.trendLinesSet && this.trendLinesSet.remove(), this.trendLinesSet = a.set(),
            this.axesSet && this.axesSet.remove(), this.axesSet = a.set(), this.cursorSet && this.cursorSet.remove(),
            this.cursorSet = a.set(), this.scrollbarsSet && this.scrollbarsSet.remove(), this.scrollbarsSet = a.set(),
            this.bulletSet && this.bulletSet.remove(), this.bulletSet = a.set(), this.freeLabelsSet && this.freeLabelsSet.remove(),
            this.axesLabelsSet && this.axesLabelsSet.remove(), this.axesLabelsSet = a.set(),
            this.freeLabelsSet = a.set(), this.balloonsSet && this.balloonsSet.remove(), this.balloonsSet = a.set(),
            this.zoomButtonSet && this.zoomButtonSet.remove(), this.zoomButtonSet = a.set(),
            this.linkSet && this.linkSet.remove(), this.linkSet = a.set()) :this.fire("failed", {
                type:"failed",
                chart:this
            });
        },
        premeasure:function() {
            var a = this.div;
            if (a) {
                var b = a.offsetWidth, c = a.offsetHeight;
                a.clientHeight && (b = a.clientWidth, c = a.clientHeight);
                if (b != this.mw || c != this.mh) this.mw = b, this.mh = c, this.measure();
            }
        },
        measure:function() {
            var a = this.div;
            if (a) {
                var b = this.chartDiv, c = a.offsetWidth, h = a.offsetHeight, f = this.container;
                a.clientHeight && (c = a.clientWidth, h = a.clientHeight);
                var e = d.removePx(d.getStyle(a, "padding-left")), g = d.removePx(d.getStyle(a, "padding-right")), k = d.removePx(d.getStyle(a, "padding-top")), l = d.removePx(d.getStyle(a, "padding-bottom"));
                isNaN(e) || (c -= e);
                isNaN(g) || (c -= g);
                isNaN(k) || (h -= k);
                isNaN(l) || (h -= l);
                e = a.style;
                a = e.width;
                e = e.height;
                -1 != a.indexOf("px") && (c = d.removePx(a));
                -1 != e.indexOf("px") && (h = d.removePx(e));
                h = Math.round(h);
                c = Math.round(c);
                a = Math.round(d.toCoordinate(this.width, c));
                e = Math.round(d.toCoordinate(this.height, h));
                (c != this.previousWidth || h != this.previousHeight) && 0 < a && 0 < e && (b.style.width = a + "px",
                b.style.height = e + "px", f && f.setSize(a, e), this.balloon = d.processObject(this.balloon, d.AmBalloon, this.theme),
                this.balloon.setBounds(2, 2, a - 2, e));
                this.balloon.chart = this;
                this.realWidth = a;
                this.realHeight = e;
                this.divRealWidth = c;
                this.divRealHeight = h;
            }
        },
        destroy:function() {
            this.chartDiv.innerHTML = "";
            this.clearTimeOuts();
            this.legend && this.legend.destroy();
        },
        clearTimeOuts:function() {
            var a = this.timeOuts;
            if (a) {
                var b;
                for (b = 0; b < a.length; b++) clearTimeout(a[b]);
            }
            this.timeOuts = [];
        },
        clear:function(a) {
            d.callMethod("clear", [ this.chartScrollbar, this.scrollbarV, this.scrollbarH, this.chartCursor ]);
            this.chartCursor = this.scrollbarH = this.scrollbarV = this.chartScrollbar = null;
            this.clearTimeOuts();
            this.container && (this.container.remove(this.chartDiv), this.container.remove(this.legendDiv));
            a || d.removeChart(this);
            if (a = this.div) for (;a.firstChild; ) a.removeChild(a.firstChild);
            this.legend && this.legend.destroy();
        },
        setMouseCursor:function(a) {
            "auto" == a && d.isNN && (a = "default");
            this.chartDiv.style.cursor = a;
            this.legendDiv.style.cursor = a;
        },
        redrawLabels:function() {
            this.labels = [];
            var a = this.allLabels;
            this.createLabelsSet();
            var b;
            for (b = 0; b < a.length; b++) this.drawLabel(a[b]);
        },
        drawLabel:function(a) {
            if (this.container && !1 !== a.enabled) {
                a = d.processObject(a, d.Label, this.theme);
                var b = a.y, c = a.text, h = a.align, f = a.size, e = a.color, g = a.rotation, k = a.alpha, l = a.bold, m = d.toCoordinate(a.x, this.realWidth), b = d.toCoordinate(b, this.realHeight);
                m || (m = 0);
                b || (b = 0);
                void 0 === e && (e = this.color);
                isNaN(f) && (f = this.fontSize);
                h || (h = "start");
                "left" == h && (h = "start");
                "right" == h && (h = "end");
                "center" == h && (h = "middle", g ? b = this.realHeight - b + b / 2 :m = this.realWidth / 2 - m);
                void 0 === k && (k = 1);
                void 0 === g && (g = 0);
                b += f / 2;
                c = d.text(this.container, c, e, this.fontFamily, f, h, l, k);
                c.translate(m, b);
                d.setCN(this, c, "label");
                a.id && d.setCN(this, c, "label-" + a.id);
                0 !== g && c.rotate(g);
                a.url ? (c.setAttr("cursor", "pointer"), c.click(function() {
                    d.getURL(a.url);
                })) :c.node.style.pointerEvents = "none";
                this.labelsSet.push(c);
                this.labels.push(c);
            }
        },
        addLabel:function(a, b, c, d, f, e, g, k, l, m) {
            a = {
                x:a,
                y:b,
                text:c,
                align:d,
                size:f,
                color:e,
                alpha:k,
                rotation:g,
                bold:l,
                url:m,
                enabled:!0
            };
            this.container && this.drawLabel(a);
            this.allLabels.push(a);
        },
        clearLabels:function() {
            var a = this.labels, b;
            for (b = a.length - 1; 0 <= b; b--) a[b].remove();
            this.labels = [];
            this.allLabels = [];
        },
        updateHeight:function() {
            var a = this.divRealHeight, b = this.legend;
            if (b) {
                var c = this.legendDiv.offsetHeight, b = b.position;
                if ("top" == b || "bottom" == b) {
                    a -= c;
                    if (0 > a || isNaN(a)) a = 0;
                    this.chartDiv.style.height = a + "px";
                }
            }
            return a;
        },
        updateWidth:function() {
            var a = this.divRealWidth, b = this.divRealHeight, c = this.legend;
            if (c) {
                var d = this.legendDiv, f = d.offsetWidth;
                isNaN(c.width) || (f = c.width);
                c.ieW && (f = c.ieW);
                var e = d.offsetHeight, d = d.style, g = this.chartDiv.style, c = c.position;
                if ("right" == c || "left" == c) {
                    a -= f;
                    if (0 > a || isNaN(a)) a = 0;
                    g.width = a + "px";
                    "left" == c ? (g.left = f + "px", d.left = "0px") :(g.left = "0px", d.left = a + "px");
                    b > e && (d.top = (b - e) / 2 + "px");
                }
            }
            return a;
        },
        getTitleHeight:function() {
            var a = 0, b = this.titles, c = !0;
            if (0 < b.length) {
                var a = 20, d;
                for (d = 0; d < b.length; d++) {
                    var f = b[d];
                    !1 !== f.enabled && (c = !1, f = f.size, isNaN(f) && (f = this.fontSize + 2), a += f + 6);
                }
                c && (a = 0);
            }
            return a;
        },
        addTitle:function(a, b, c, d, f) {
            isNaN(b) && (b = this.fontSize + 2);
            a = {
                text:a,
                size:b,
                color:c,
                alpha:d,
                bold:f,
                enabled:!0
            };
            this.titles.push(a);
            return a;
        },
        handleWheel:function(a) {
            var b = 0;
            a || (a = window.event);
            a.wheelDelta ? b = a.wheelDelta / 120 :a.detail && (b = -a.detail / 3);
            b && this.handleWheelReal(b, a.shiftKey);
            a.preventDefault && a.preventDefault();
        },
        handleWheelReal:function() {},
        addListeners:function() {
            var a = this, b = a.chartDiv;
            document.addEventListener ? (a.panEventsEnabled && (b.style.msTouchAction = "none"),
            "ontouchstart" in document.documentElement && (b.addEventListener("touchstart", function(b) {
                a.handleTouchMove.call(a, b);
                a.handleTouchStart.call(a, b);
            }, !0), b.addEventListener("touchmove", function(b) {
                a.handleTouchMove.call(a, b);
            }, !0), b.addEventListener("touchend", function(b) {
                a.handleTouchEnd.call(a, b);
            }, !0)), b.addEventListener("mousedown", function(b) {
                a.mouseIsOver = !0;
                a.handleMouseMove.call(a, b);
                a.handleMouseDown.call(a, b);
            }, !0), b.addEventListener("mouseover", function(b) {
                a.handleMouseOver.call(a, b);
            }, !0), b.addEventListener("mouseout", function(b) {
                a.handleMouseOut.call(a, b);
            }, !0)) :(b.attachEvent("onmousedown", function(b) {
                a.handleMouseDown.call(a, b);
            }), b.attachEvent("onmouseover", function(b) {
                a.handleMouseOver.call(a, b);
            }), b.attachEvent("onmouseout", function(b) {
                a.handleMouseOut.call(a, b);
            }));
        },
        dispDUpd:function() {
            if (!this.skipEvents) {
                var a;
                this.dispatchDataUpdated && (this.dispatchDataUpdated = !1, a = "dataUpdated", this.fire(a, {
                    type:a,
                    chart:this
                }));
                this.chartCreated || (a = "init", this.fire(a, {
                    type:a,
                    chart:this
                }));
                this.chartRendered || (a = "rendered", this.fire(a, {
                    type:a,
                    chart:this
                }), this.chartRendered = !0);
                a = "drawn";
                this.fire(a, {
                    type:a,
                    chart:this
                });
            }
            this.skipEvents = !1;
        },
        validateSize:function() {
            var a = this;
            a.premeasure();
            if (a.divRealWidth != a.previousWidth || a.divRealHeight != a.previousHeight) {
                var b = a.legend;
                if (0 < a.realWidth && 0 < a.realHeight) {
                    a.sizeChanged = !0;
                    if (b) {
                        clearTimeout(a.legendInitTO);
                        var c = setTimeout(function() {
                            b.invalidateSize();
                        }, 100);
                        a.timeOuts.push(c);
                        a.legendInitTO = c;
                    }
                    "xy" != a.type ? a.marginsUpdated = !1 :(a.marginsUpdated = !0, a.selfZoom = !0);
                    clearTimeout(a.initTO);
                    c = setTimeout(function() {
                        a.initChart();
                    }, 10);
                    a.timeOuts.push(c);
                    a.initTO = c;
                }
                a.fire("resized", {
                    type:"resized",
                    chart:a
                });
                a.renderFix();
                b && b.renderFix && b.renderFix();
                a.previousHeight = a.divRealHeight;
                a.previousWidth = a.divRealWidth;
            }
        },
        invalidateSize:function() {
            this.previousHeight = this.previousWidth = NaN;
            this.invalidateSizeReal();
        },
        invalidateSizeReal:function() {
            var a = this;
            a.marginsUpdated = !1;
            clearTimeout(a.validateTO);
            var b = setTimeout(function() {
                a.validateSize();
            }, 5);
            a.timeOuts.push(b);
            a.validateTO = b;
        },
        validateData:function(a) {
            this.chartCreated && (this.dataChanged = !0, this.marginsUpdated = "xy" != this.type ? !1 :!0,
            this.initChart(a));
        },
        validateNow:function(a, b) {
            this.initTO && clearTimeout(this.initTO);
            a && (this.dataChanged = !0);
            this.skipEvents = b;
            this.chartRendered = !1;
            this.write(this.div);
        },
        showItem:function(a) {
            a.hidden = !1;
            this.initChart();
        },
        hideItem:function(a) {
            a.hidden = !0;
            this.initChart();
        },
        hideBalloon:function() {
            var a = this;
            clearTimeout(a.hoverInt);
            clearTimeout(a.balloonTO);
            a.hoverInt = setTimeout(function() {
                a.hideBalloonReal.call(a);
            }, a.hideBalloonTime);
        },
        cleanChart:function() {},
        hideBalloonReal:function() {
            var a = this.balloon;
            a && a.hide();
        },
        showBalloon:function(a, b, c, d, f) {
            var e = this;
            clearTimeout(e.balloonTO);
            clearTimeout(e.hoverInt);
            e.balloonTO = setTimeout(function() {
                e.showBalloonReal.call(e, a, b, c, d, f);
            }, 1);
        },
        showBalloonReal:function(a, b, c, d, f) {
            this.handleMouseMove();
            var e = this.balloon;
            e.enabled && (e.followCursor(!1), e.changeColor(b), !c || e.fixedPosition ? (e.setPosition(d, f),
            e.followCursor(!1)) :e.followCursor(!0), a && e.showBalloon(a));
        },
        handleTouchMove:function(a) {
            this.hideBalloon();
            var b = this.chartDiv;
            a.touches && (a = a.touches.item(0), this.mouseX = a.pageX - d.findPosX(b), this.mouseY = a.pageY - d.findPosY(b));
        },
        handleMouseOver:function() {
            this.outTO && clearTimeout(this.outTO);
            d.resetMouseOver();
            this.mouseIsOver = !0;
        },
        handleMouseOut:function() {
            var a = this;
            a.outTO && clearTimeout(a.outTO);
            a.outTO = setTimeout(function() {
                a.handleMouseOutReal();
            }, 10);
        },
        handleMouseOutReal:function() {
            d.resetMouseOver();
            this.mouseIsOver = !1;
        },
        handleMouseMove:function(a) {
            if (this.mouseIsOver) {
                var b = this.chartDiv;
                a || (a = window.event);
                var c, h;
                if (a) {
                    this.posX = d.findPosX(b);
                    this.posY = d.findPosY(b);
                    switch (this.mouseMode) {
                      case 1:
                        c = a.clientX - this.posX;
                        h = a.clientY - this.posY;
                        if (!this.divIsFixed) {
                            var b = document.body, f, e, g, k;
                            b && (f = b.scrollLeft, g = b.scrollTop);
                            if (b = document.documentElement) e = b.scrollLeft, k = b.scrollTop;
                            f = Math.max(f, e);
                            g = Math.max(g, k);
                            c += f;
                            h += g;
                        }
                        break;

                      case 0:
                        this.divIsFixed ? (c = a.clientX - this.posX, h = a.clientY - this.posY) :(c = a.pageX - this.posX,
                        h = a.pageY - this.posY);
                    }
                    a.touches && (a = a.touches.item(0), c = a.pageX - this.posX, h = a.pageY - this.posY);
                    this.mouseX = c - this.dmouseX;
                    this.mouseY = h - this.dmouseY;
                }
            }
        },
        handleTouchStart:function(a) {
            this.handleMouseDown(a);
        },
        handleTouchEnd:function(a) {
            d.resetMouseOver();
            this.handleReleaseOutside(a);
        },
        handleReleaseOutside:function() {},
        handleMouseDown:function(a) {
            d.resetMouseOver();
            this.mouseIsOver = !0;
            a && a.preventDefault && (this.panEventsEnabled ? a.preventDefault() :a.touches || a.preventDefault());
        },
        addLegend:function(a, b) {
            a = d.processObject(a, d.AmLegend, this.theme);
            a.divId = b;
            var c;
            c = "object" != typeof b && b ? document.getElementById(b) :b;
            this.legend = a;
            a.chart = this;
            c ? (a.div = c, a.position = "outside", a.autoMargins = !1) :a.div = this.legendDiv;
            c = this.handleLegendEvent;
            this.listenTo(a, "showItem", c);
            this.listenTo(a, "hideItem", c);
            this.listenTo(a, "clickMarker", c);
            this.listenTo(a, "rollOverItem", c);
            this.listenTo(a, "rollOutItem", c);
            this.listenTo(a, "rollOverMarker", c);
            this.listenTo(a, "rollOutMarker", c);
            this.listenTo(a, "clickLabel", c);
            return a;
        },
        removeLegend:function() {
            this.legend = void 0;
            this.legendDiv.innerHTML = "";
        },
        handleResize:function() {
            (d.isPercents(this.width) || d.isPercents(this.height)) && this.invalidateSizeReal();
            this.renderFix();
        },
        renderFix:function() {
            if (!d.VML) {
                var a = this.container;
                a && a.renderFix();
            }
        },
        getSVG:function() {
            if (d.hasSVG) return this.container;
        },
        animate:function(a, b, c, h, f, e, g) {
            a["an_" + b] && d.removeFromArray(this.animations, a["an_" + b]);
            c = {
                obj:a,
                frame:0,
                attribute:b,
                from:c,
                to:h,
                time:f,
                effect:e,
                suffix:g
            };
            a["an_" + b] = c;
            this.animations.push(c);
            return c;
        },
        setLegendData:function(a) {
            var b = this.legend;
            b && b.setData(a);
        },
        stopAnim:function(a) {
            d.removeFromArray(this.animations, a);
        },
        updateAnimations:function() {
            var a;
            this.container && this.container.update();
            if (this.animations) for (a = this.animations.length - 1; 0 <= a; a--) {
                var b = this.animations[a], c = 1e3 * b.time / d.updateRate, h = b.frame + 1, f = b.obj, e = b.attribute;
                if (h <= c) {
                    b.frame++;
                    var g = Number(b.from), k = Number(b.to) - g, c = d[b.effect](0, h, g, k, c);
                    0 === k ? (this.animations.splice(a, 1), f.node.style[e] = Number(b.to) + b.suffix) :f.node.style[e] = c + b.suffix;
                } else f.node.style[e] = Number(b.to) + b.suffix, this.animations.splice(a, 1);
            }
        },
        update:function() {
            this.updateAnimations();
        },
        inIframe:function() {
            try {
                return window.self !== window.top;
            } catch (a) {
                return !0;
            }
        },
        brr:function() {
            var a = window.location.hostname.split("."), b;
            2 <= a.length && (b = a[a.length - 2] + "." + a[a.length - 1]);
            this.amLink && (a = this.amLink.parentNode) && a.removeChild(this.amLink);
            a = this.creditsPosition;
            if ("amcharts.com" != b || !0 === this.inIframe()) {
                var c = b = 0, d = this.realWidth, f = this.realHeight, e = this.type;
                if ("serial" == e || "xy" == e || "gantt" == e) b = this.marginLeftReal, c = this.marginTopReal,
                d = b + this.plotAreaWidth, f = c + this.plotAreaHeight;
                var g = "http://www.amcharts.com/javascript-charts/", k = "JavaScript charts", l = "JS chart by amCharts";
                "ammap" == this.product && (g = "http://www.ammap.com/javascript-maps/", k = "Interactive JavaScript maps",
                l = "JS map by amCharts");
                e = document.createElement("a");
                l = document.createTextNode(l);
                e.setAttribute("href", g);
                e.setAttribute("title", k);
                e.appendChild(l);
                this.chartDiv.appendChild(e);
                this.amLink = e;
                g = e.style;
                g.position = "absolute";
                g.textDecoration = "none";
                g.color = this.color;
                g.fontFamily = this.fontFamily;
                g.fontSize = this.fontSize + "px";
                g.opacity = .7;
                g.display = "block";
                var k = e.offsetWidth, e = e.offsetHeight, l = 5 + b, m = c + 5;
                "bottom-left" == a && (l = 5 + b, m = f - e - 3);
                "bottom-right" == a && (l = d - k - 5, m = f - e - 3);
                "top-right" == a && (l = d - k - 5, m = c + 5);
                g.left = l + "px";
                g.top = m + "px";
            }
        }
    });
    d.Slice = d.Class({
        construct:function() {}
    });
    d.SerialDataItem = d.Class({
        construct:function() {}
    });
    d.GraphDataItem = d.Class({
        construct:function() {}
    });
    d.Guide = d.Class({
        construct:function(a) {
            this.cname = "Guide";
            d.applyTheme(this, a, this.cname);
        }
    });
    d.Title = d.Class({
        construct:function(a) {
            this.cname = "Title";
            d.applyTheme(this, a, this.cname);
        }
    });
    d.Label = d.Class({
        construct:function(a) {
            this.cname = "Label";
            d.applyTheme(this, a, this.cname);
        }
    });
})();

(function() {
    var d = window.AmCharts;
    d.AmBalloon = d.Class({
        construct:function(a) {
            this.cname = "AmBalloon";
            this.enabled = !0;
            this.fillColor = "#FFFFFF";
            this.fillAlpha = .8;
            this.borderThickness = 2;
            this.borderColor = "#FFFFFF";
            this.borderAlpha = 1;
            this.cornerRadius = 0;
            this.maxWidth = 220;
            this.horizontalPadding = 8;
            this.verticalPadding = 4;
            this.pointerWidth = 6;
            this.pointerOrientation = "V";
            this.color = "#000000";
            this.adjustBorderColor = !0;
            this.show = this.follow = this.showBullet = !1;
            this.bulletSize = 3;
            this.shadowAlpha = .4;
            this.shadowColor = "#000000";
            this.fadeOutDuration = this.animationDuration = .3;
            this.fixedPosition = !1;
            this.offsetY = 6;
            this.offsetX = 1;
            this.textAlign = "center";
            d.isModern || (this.offsetY *= 1.5);
            d.applyTheme(this, a, this.cname);
        },
        draw:function() {
            var a = this.pointToX, b = this.pointToY;
            this.deltaSignX = this.deltaSignY = 1;
            var c = this.chart;
            d.VML && (this.fadeOutDuration = 0);
            this.xAnim && c.stopAnim(this.xAnim);
            this.yAnim && c.stopAnim(this.yAnim);
            if (!isNaN(a)) {
                var h = this.follow, f = c.container, e = this.set;
                d.remove(e);
                this.removeDiv();
                e = f.set();
                e.node.style.pointerEvents = "none";
                this.set = e;
                c.balloonsSet.push(e);
                if (this.show) {
                    var g = this.l, k = this.t, l = this.r, m = this.b, n = this.balloonColor, p = this.fillColor, u = this.borderColor, r = p;
                    void 0 != n && (this.adjustBorderColor ? r = u = n :p = n);
                    var A = this.horizontalPadding, B = this.verticalPadding, w = this.pointerWidth, D = this.pointerOrientation, x = this.cornerRadius, y = c.fontFamily, q = this.fontSize;
                    void 0 == q && (q = c.fontSize);
                    var n = document.createElement("div"), v = c.classNamePrefix;
                    n.className = v + "-balloon-div";
                    this.className && (n.className = n.className + " " + v + "-balloon-div-" + this.className);
                    v = n.style;
                    v.pointerEvents = "none";
                    v.position = "absolute";
                    var t = this.minWidth, z = "";
                    isNaN(t) || (z = "min-width:" + (t - 2 * A) + "px; ");
                    n.innerHTML = "<div style='text-align:" + this.textAlign + "; " + z + "max-width:" + this.maxWidth + "px; font-size:" + q + "px; color:" + this.color + "; font-family:" + y + "'>" + this.text + "</div>";
                    c.chartDiv.appendChild(n);
                    this.textDiv = n;
                    q = n.offsetWidth;
                    y = n.offsetHeight;
                    n.clientHeight && (q = n.clientWidth, y = n.clientHeight);
                    y += 2 * B;
                    z = q + 2 * A;
                    !isNaN(t) && z < t && (z = t);
                    window.opera && (y += 2);
                    var C = !1, q = this.offsetY;
                    c.handDrawn && (q += c.handDrawScatter + 2);
                    "H" != D ? (t = a - z / 2, b < k + y + 10 && "down" != D ? (C = !0, h && (b += q),
                    q = b + w, this.deltaSignY = -1) :(h && (b -= q), q = b - y - w, this.deltaSignY = 1)) :(2 * w > y && (w = y / 2),
                    q = b - y / 2, a < g + (l - g) / 2 ? (t = a + w, this.deltaSignX = -1) :(t = a - z - w,
                    this.deltaSignX = 1));
                    q + y >= m && (q = m - y);
                    q < k && (q = k);
                    t < g && (t = g);
                    t + z > l && (t = l - z);
                    var k = q + B, m = t + A, B = this.shadowAlpha, G = this.shadowColor, A = this.borderThickness, E = this.bulletSize, F;
                    0 < x || 0 === w ? (0 < B && (a = d.rect(f, z, y, p, 0, A + 1, G, B, this.cornerRadius),
                    d.isModern ? a.translate(1, 1) :a.translate(4, 4), e.push(a)), p = d.rect(f, z, y, p, this.fillAlpha, A, u, this.borderAlpha, this.cornerRadius),
                    this.showBullet && (F = d.circle(f, E, r, this.fillAlpha), e.push(F))) :(r = [],
                    x = [], "H" != D ? (g = a - t, g > z - w && (g = z - w), g < w && (g = w), r = [ 0, g - w, a - t, g + w, z, z, 0, 0 ],
                    x = C ? [ 0, 0, b - q, 0, 0, y, y, 0 ] :[ y, y, b - q, y, y, 0, 0, y ]) :(r = b - q,
                    r > y - w && (r = y - w), r < w && (r = w), x = [ 0, r - w, b - q, r + w, y, y, 0, 0 ],
                    r = a < g + (l - g) / 2 ? [ 0, 0, t < a ? 0 :a - t, 0, 0, z, z, 0 ] :[ z, z, t + z > a ? z :a - t, z, z, 0, 0, z ]),
                    0 < B && (a = d.polygon(f, r, x, p, 0, A, G, B), a.translate(1, 1), e.push(a)),
                    p = d.polygon(f, r, x, p, this.fillAlpha, A, u, this.borderAlpha));
                    this.bg = p;
                    e.push(p);
                    p.toFront();
                    d.setCN(c, p, "balloon-bg");
                    this.className && d.setCN(c, p, "balloon-bg-" + this.className);
                    f = 1 * this.deltaSignX;
                    v.left = m + "px";
                    v.top = k + "px";
                    e.translate(t - f, q);
                    p = p.getBBox();
                    this.bottom = q + y + 1;
                    this.yPos = p.y + q;
                    F && F.translate(this.pointToX - t + f, b - q);
                    b = this.animationDuration;
                    0 < this.animationDuration && !h && !isNaN(this.prevX) && (e.translate(this.prevX, this.prevY),
                    e.animate({
                        translate:t - f + "," + q
                    }, b, "easeOutSine"), n && (v.left = this.prevTX + "px", v.top = this.prevTY + "px",
                    this.xAnim = c.animate({
                        node:n
                    }, "left", this.prevTX, m, b, "easeOutSine", "px"), this.yAnim = c.animate({
                        node:n
                    }, "top", this.prevTY, k, b, "easeOutSine", "px")));
                    this.prevX = t - f;
                    this.prevY = q;
                    this.prevTX = m;
                    this.prevTY = k;
                }
            }
        },
        followMouse:function() {
            if (this.follow && this.show) {
                var a = this.chart.mouseX - this.offsetX * this.deltaSignX, b = this.chart.mouseY;
                this.pointToX = a;
                this.pointToY = b;
                if (a != this.previousX || b != this.previousY) if (this.previousX = a, this.previousY = b,
                0 === this.cornerRadius) this.draw(); else {
                    var c = this.set;
                    if (c) {
                        var d = c.getBBox(), a = a - d.width / 2, f = b - d.height - 10;
                        a < this.l && (a = this.l);
                        a > this.r - d.width && (a = this.r - d.width);
                        f < this.t && (f = b + 10);
                        c.translate(a, f);
                        b = this.textDiv.style;
                        b.left = a + this.horizontalPadding + "px";
                        b.top = f + this.verticalPadding + "px";
                    }
                }
            }
        },
        changeColor:function(a) {
            this.balloonColor = a;
        },
        setBounds:function(a, b, c, d) {
            this.l = a;
            this.t = b;
            this.r = c;
            this.b = d;
            this.destroyTO && clearTimeout(this.destroyTO);
        },
        showBalloon:function(a) {
            this.text = a;
            this.show = !0;
            this.destroyTO && clearTimeout(this.destroyTO);
            a = this.chart;
            this.fadeAnim1 && a.stopAnim(this.fadeAnim1);
            this.fadeAnim2 && a.stopAnim(this.fadeAnim2);
            this.draw();
        },
        hide:function() {
            var a = this, b = a.fadeOutDuration, c = a.chart;
            if (0 < b) {
                a.destroyTO = setTimeout(function() {
                    a.destroy.call(a);
                }, 1e3 * b);
                a.follow = !1;
                a.show = !1;
                var d = a.set;
                d && (d.setAttr("opacity", a.fillAlpha), a.fadeAnim1 = d.animate({
                    opacity:0
                }, b, "easeInSine"));
                a.textDiv && (a.fadeAnim2 = c.animate({
                    node:a.textDiv
                }, "opacity", 1, 0, b, "easeInSine", ""));
            } else a.show = !1, a.follow = !1, a.destroy();
        },
        setPosition:function(a, b, c) {
            this.pointToX = a;
            this.pointToY = b;
            c && (a == this.previousX && b == this.previousY || this.draw());
            this.previousX = a;
            this.previousY = b;
        },
        followCursor:function(a) {
            var b = this;
            (b.follow = a) ? (b.pShowBullet = b.showBullet, b.showBullet = !1) :void 0 !== b.pShowBullet && (b.showBullet = b.pShowBullet);
            clearInterval(b.interval);
            var c = b.chart.mouseX, d = b.chart.mouseY;
            !isNaN(c) && a && (b.pointToX = c - b.offsetX * b.deltaSignX, b.pointToY = d, b.followMouse(),
            b.interval = setInterval(function() {
                b.followMouse.call(b);
            }, 40));
        },
        removeDiv:function() {
            if (this.textDiv) {
                var a = this.textDiv.parentNode;
                a && a.removeChild(this.textDiv);
            }
        },
        destroy:function() {
            clearInterval(this.interval);
            d.remove(this.set);
            this.removeDiv();
            this.set = null;
        }
    });
})();

(function() {
    var d = window.AmCharts;
    d.circle = function(a, b, c, h, f, e, g, k, l) {
        0 >= b && (b = .001);
        if (void 0 == f || 0 === f) f = .01;
        void 0 === e && (e = "#000000");
        void 0 === g && (g = 0);
        h = {
            fill:c,
            stroke:e,
            "fill-opacity":h,
            "stroke-width":f,
            "stroke-opacity":g
        };
        a = isNaN(l) ? a.circle(0, 0, b).attr(h) :a.ellipse(0, 0, b, l).attr(h);
        k && a.gradient("radialGradient", [ c, d.adjustLuminosity(c, -.6) ]);
        return a;
    };
    d.text = function(a, b, c, h, f, e, g, k) {
        e || (e = "middle");
        "right" == e && (e = "end");
        "left" == e && (e = "start");
        isNaN(k) && (k = 1);
        void 0 !== b && (b = String(b), d.isIE && !d.isModern && (b = b.replace("&amp;", "&"),
        b = b.replace("&", "&amp;")));
        c = {
            fill:c,
            "font-family":h,
            "font-size":f,
            opacity:k
        };
        !0 === g && (c["font-weight"] = "bold");
        c["text-anchor"] = e;
        return a.text(b, c);
    };
    d.polygon = function(a, b, c, h, f, e, g, k, l, m, n) {
        isNaN(e) && (e = .01);
        isNaN(k) && (k = f);
        var p = h, u = !1;
        "object" == typeof p && 1 < p.length && (u = !0, p = p[0]);
        void 0 === g && (g = p);
        f = {
            fill:p,
            stroke:g,
            "fill-opacity":f,
            "stroke-width":e,
            "stroke-opacity":k
        };
        void 0 !== n && 0 < n && (f["stroke-dasharray"] = n);
        n = d.dx;
        e = d.dy;
        a.handDrawn && (c = d.makeHD(b, c, a.handDrawScatter), b = c[0], c = c[1]);
        g = Math.round;
        m && (g = d.doNothing);
        m = "M" + (g(b[0]) + n) + "," + (g(c[0]) + e);
        for (k = 1; k < b.length; k++) m += " L" + (g(b[k]) + n) + "," + (g(c[k]) + e);
        a = a.path(m + " Z").attr(f);
        u && a.gradient("linearGradient", h, l);
        return a;
    };
    d.rect = function(a, b, c, h, f, e, g, k, l, m, n) {
        if (isNaN(b) || isNaN(c)) return a.set();
        isNaN(e) && (e = 0);
        void 0 === l && (l = 0);
        void 0 === m && (m = 270);
        isNaN(f) && (f = 0);
        var p = h, u = !1;
        "object" == typeof p && (p = p[0], u = !0);
        void 0 === g && (g = p);
        void 0 === k && (k = f);
        b = Math.round(b);
        c = Math.round(c);
        var r = 0, A = 0;
        0 > b && (b = Math.abs(b), r = -b);
        0 > c && (c = Math.abs(c), A = -c);
        r += d.dx;
        A += d.dy;
        f = {
            fill:p,
            stroke:g,
            "fill-opacity":f,
            "stroke-opacity":k
        };
        void 0 !== n && 0 < n && (f["stroke-dasharray"] = n);
        a = a.rect(r, A, b, c, l, e).attr(f);
        u && a.gradient("linearGradient", h, m);
        return a;
    };
    d.bullet = function(a, b, c, h, f, e, g, k, l, m, n) {
        var p;
        "circle" == b && (b = "round");
        switch (b) {
          case "round":
            p = d.circle(a, c / 2, h, f, e, g, k);
            break;

          case "square":
            p = d.polygon(a, [ -c / 2, c / 2, c / 2, -c / 2 ], [ c / 2, c / 2, -c / 2, -c / 2 ], h, f, e, g, k, m - 180);
            break;

          case "rectangle":
            p = d.polygon(a, [ -c, c, c, -c ], [ c / 2, c / 2, -c / 2, -c / 2 ], h, f, e, g, k, m - 180);
            break;

          case "diamond":
            p = d.polygon(a, [ -c / 2, 0, c / 2, 0 ], [ 0, -c / 2, 0, c / 2 ], h, f, e, g, k);
            break;

          case "triangleUp":
            p = d.triangle(a, c, 0, h, f, e, g, k);
            break;

          case "triangleDown":
            p = d.triangle(a, c, 180, h, f, e, g, k);
            break;

          case "triangleLeft":
            p = d.triangle(a, c, 270, h, f, e, g, k);
            break;

          case "triangleRight":
            p = d.triangle(a, c, 90, h, f, e, g, k);
            break;

          case "bubble":
            p = d.circle(a, c / 2, h, f, e, g, k, !0);
            break;

          case "line":
            p = d.line(a, [ -c / 2, c / 2 ], [ 0, 0 ], h, f, e, g, k);
            break;

          case "yError":
            p = a.set();
            p.push(d.line(a, [ 0, 0 ], [ -c / 2, c / 2 ], h, f, e));
            p.push(d.line(a, [ -l, l ], [ -c / 2, -c / 2 ], h, f, e));
            p.push(d.line(a, [ -l, l ], [ c / 2, c / 2 ], h, f, e));
            break;

          case "xError":
            p = a.set(), p.push(d.line(a, [ -c / 2, c / 2 ], [ 0, 0 ], h, f, e)), p.push(d.line(a, [ -c / 2, -c / 2 ], [ -l, l ], h, f, e)),
            p.push(d.line(a, [ c / 2, c / 2 ], [ -l, l ], h, f, e));
        }
        p && p.pattern(n);
        return p;
    };
    d.triangle = function(a, b, c, d, f, e, g, k) {
        if (void 0 === e || 0 === e) e = 1;
        void 0 === g && (g = "#000");
        void 0 === k && (k = 0);
        d = {
            fill:d,
            stroke:g,
            "fill-opacity":f,
            "stroke-width":e,
            "stroke-opacity":k
        };
        b /= 2;
        var l;
        0 === c && (l = " M" + -b + "," + b + " L0," + -b + " L" + b + "," + b + " Z");
        180 == c && (l = " M" + -b + "," + -b + " L0," + b + " L" + b + "," + -b + " Z");
        90 == c && (l = " M" + -b + "," + -b + " L" + b + ",0 L" + -b + "," + b + " Z");
        270 == c && (l = " M" + -b + ",0 L" + b + "," + b + " L" + b + "," + -b + " Z");
        return a.path(l).attr(d);
    };
    d.line = function(a, b, c, h, f, e, g, k, l, m, n) {
        if (a.handDrawn && !n) return d.handDrawnLine(a, b, c, h, f, e, g, k, l, m, n);
        e = {
            fill:"none",
            "stroke-width":e
        };
        void 0 !== g && 0 < g && (e["stroke-dasharray"] = g);
        isNaN(f) || (e["stroke-opacity"] = f);
        h && (e.stroke = h);
        h = Math.round;
        m && (h = d.doNothing);
        m = d.dx;
        f = d.dy;
        g = "M" + (h(b[0]) + m) + "," + (h(c[0]) + f);
        for (k = 1; k < b.length; k++) g += " L" + (h(b[k]) + m) + "," + (h(c[k]) + f);
        if (d.VML) return a.path(g, void 0, !0).attr(e);
        l && (g += " M0,0 L0,0");
        return a.path(g).attr(e);
    };
    d.makeHD = function(a, b, c) {
        for (var d = [], f = [], e = 1; e < a.length; e++) for (var g = Number(a[e - 1]), k = Number(b[e - 1]), l = Number(a[e]), m = Number(b[e]), n = Math.sqrt(Math.pow(l - g, 2) + Math.pow(m - k, 2)), n = Math.round(n / 50) + 1, l = (l - g) / n, m = (m - k) / n, p = 0; p <= n; p++) {
            var u = g + p * l + Math.random() * c, r = k + p * m + Math.random() * c;
            d.push(u);
            f.push(r);
        }
        return [ d, f ];
    };
    d.handDrawnLine = function(a, b, c, h, f, e, g, k, l, m) {
        var n, p = a.set();
        for (n = 1; n < b.length; n++) for (var u = [ b[n - 1], b[n] ], r = [ c[n - 1], c[n] ], r = d.makeHD(u, r, a.handDrawScatter), u = r[0], r = r[1], A = 1; A < u.length; A++) p.push(d.line(a, [ u[A - 1], u[A] ], [ r[A - 1], r[A] ], h, f, e + Math.random() * a.handDrawThickness - a.handDrawThickness / 2, g, k, l, m, !0));
        return p;
    };
    d.doNothing = function(a) {
        return a;
    };
    d.wedge = function(a, b, c, h, f, e, g, k, l, m, n, p) {
        var u = Math.round;
        e = u(e);
        g = u(g);
        k = u(k);
        var r = u(g / e * k), A = d.VML, B = 359.5 + e / 100;
        359.94 < B && (B = 359.94);
        f >= B && (f = B);
        var w = 1 / 180 * Math.PI, B = b + Math.sin(h * w) * k, D = c - Math.cos(h * w) * r, x = b + Math.sin(h * w) * e, y = c - Math.cos(h * w) * g, q = b + Math.sin((h + f) * w) * e, v = c - Math.cos((h + f) * w) * g, t = b + Math.sin((h + f) * w) * k, w = c - Math.cos((h + f) * w) * r, z = {
            fill:d.adjustLuminosity(m.fill, -.2),
            "stroke-opacity":0,
            "fill-opacity":m["fill-opacity"]
        }, C = 0;
        180 < Math.abs(f) && (C = 1);
        h = a.set();
        var G;
        A && (B = u(10 * B), x = u(10 * x), q = u(10 * q), t = u(10 * t), D = u(10 * D),
        y = u(10 * y), v = u(10 * v), w = u(10 * w), b = u(10 * b), l = u(10 * l), c = u(10 * c),
        e *= 10, g *= 10, k *= 10, r *= 10, 1 > Math.abs(f) && 1 >= Math.abs(q - x) && 1 >= Math.abs(v - y) && (G = !0));
        f = "";
        var E;
        p && (z["fill-opacity"] = 0, z["stroke-opacity"] = m["stroke-opacity"] / 2, z.stroke = m.stroke);
        0 < l && (E = " M" + B + "," + (D + l) + " L" + x + "," + (y + l), A ? (G || (E += " A" + (b - e) + "," + (l + c - g) + "," + (b + e) + "," + (l + c + g) + "," + x + "," + (y + l) + "," + q + "," + (v + l)),
        E += " L" + t + "," + (w + l), 0 < k && (G || (E += " B" + (b - k) + "," + (l + c - r) + "," + (b + k) + "," + (l + c + r) + "," + t + "," + (l + w) + "," + B + "," + (l + D)))) :(E += " A" + e + "," + g + ",0," + C + ",1," + q + "," + (v + l) + " L" + t + "," + (w + l),
        0 < k && (E += " A" + k + "," + r + ",0," + C + ",0," + B + "," + (D + l))), E = a.path(E + " Z", void 0, void 0, "1000,1000").attr(z),
        h.push(E), E = a.path(" M" + B + "," + D + " L" + B + "," + (D + l) + " L" + x + "," + (y + l) + " L" + x + "," + y + " L" + B + "," + D + " Z", void 0, void 0, "1000,1000").attr(z),
        l = a.path(" M" + q + "," + v + " L" + q + "," + (v + l) + " L" + t + "," + (w + l) + " L" + t + "," + w + " L" + q + "," + v + " Z", void 0, void 0, "1000,1000").attr(z),
        h.push(E), h.push(l));
        A ? (G || (f = " A" + u(b - e) + "," + u(c - g) + "," + u(b + e) + "," + u(c + g) + "," + u(x) + "," + u(y) + "," + u(q) + "," + u(v)),
        e = " M" + u(B) + "," + u(D) + " L" + u(x) + "," + u(y) + f + " L" + u(t) + "," + u(w)) :e = " M" + B + "," + D + " L" + x + "," + y + (" A" + e + "," + g + ",0," + C + ",1," + q + "," + v) + " L" + t + "," + w;
        0 < k && (A ? G || (e += " B" + (b - k) + "," + (c - r) + "," + (b + k) + "," + (c + r) + "," + t + "," + w + "," + B + "," + D) :e += " A" + k + "," + r + ",0," + C + ",0," + B + "," + D);
        a.handDrawn && (b = d.line(a, [ B, x ], [ D, y ], m.stroke, m.thickness * Math.random() * a.handDrawThickness, m["stroke-opacity"]),
        h.push(b));
        a = a.path(e + " Z", void 0, void 0, "1000,1000").attr(m);
        if (n) {
            b = [];
            for (c = 0; c < n.length; c++) b.push(d.adjustLuminosity(m.fill, n[c]));
            0 < b.length && a.gradient("linearGradient", b);
        }
        a.pattern(p);
        h.wedge = a;
        h.push(a);
        return h;
    };
    d.adjustLuminosity = function(a, b) {
        a = String(a).replace(/[^0-9a-f]/gi, "");
        6 > a.length && (a = String(a[0]) + String(a[0]) + String(a[1]) + String(a[1]) + String(a[2]) + String(a[2]));
        b = b || 0;
        var c = "#", d, f;
        for (f = 0; 3 > f; f++) d = parseInt(a.substr(2 * f, 2), 16), d = Math.round(Math.min(Math.max(0, d + d * b), 255)).toString(16),
        c += ("00" + d).substr(d.length);
        return c;
    };
})();

(function() {
    var d = window.AmCharts;
    d.AmLegend = d.Class({
        construct:function(a) {
            this.enabled = !0;
            this.cname = "AmLegend";
            this.createEvents("rollOverMarker", "rollOverItem", "rollOutMarker", "rollOutItem", "showItem", "hideItem", "clickMarker", "rollOverItem", "rollOutItem", "clickLabel");
            this.position = "bottom";
            this.borderColor = this.color = "#000000";
            this.borderAlpha = 0;
            this.markerLabelGap = 5;
            this.verticalGap = 10;
            this.align = "left";
            this.horizontalGap = 0;
            this.spacing = 10;
            this.markerDisabledColor = "#AAB3B3";
            this.markerType = "square";
            this.markerSize = 16;
            this.markerBorderThickness = this.markerBorderAlpha = 1;
            this.marginBottom = this.marginTop = 0;
            this.marginLeft = this.marginRight = 20;
            this.autoMargins = !0;
            this.valueWidth = 50;
            this.switchable = !0;
            this.switchType = "x";
            this.switchColor = "#FFFFFF";
            this.rollOverColor = "#CC0000";
            this.reversedOrder = !1;
            this.labelText = "[[title]]";
            this.valueText = "[[value]]";
            this.useMarkerColorForLabels = !1;
            this.rollOverGraphAlpha = 1;
            this.textClickEnabled = !1;
            this.equalWidths = !0;
            this.dateFormat = "DD-MM-YYYY";
            this.backgroundColor = "#FFFFFF";
            this.backgroundAlpha = 0;
            this.useGraphSettings = !1;
            this.showEntries = !0;
            d.applyTheme(this, a, this.cname);
        },
        setData:function(a) {
            this.legendData = a;
            this.invalidateSize();
        },
        invalidateSize:function() {
            this.destroy();
            this.entries = [];
            this.valueLabels = [];
            var a = this.legendData;
            this.enabled && (d.ifArray(a) || d.ifArray(this.data)) && this.drawLegend();
        },
        drawLegend:function() {
            var a = this.chart, b = this.position, c = this.width, h = a.divRealWidth, f = a.divRealHeight, e = this.div, g = this.legendData;
            this.data && (g = this.data);
            isNaN(this.fontSize) && (this.fontSize = a.fontSize);
            if ("right" == b || "left" == b) this.maxColumns = 1, this.autoMargins && (this.marginLeft = this.marginRight = 10); else if (this.autoMargins) {
                this.marginRight = a.marginRight;
                this.marginLeft = a.marginLeft;
                var k = a.autoMarginOffset;
                "bottom" == b ? (this.marginBottom = k, this.marginTop = 0) :(this.marginTop = k,
                this.marginBottom = 0);
            }
            c = void 0 !== c ? d.toCoordinate(c, h) :a.realWidth;
            "outside" == b ? (c = e.offsetWidth, f = e.offsetHeight, e.clientHeight && (c = e.clientWidth,
            f = e.clientHeight)) :(isNaN(c) || (e.style.width = c + "px"), e.className = "amChartsLegend " + a.classNamePrefix + "-legend-div");
            this.divWidth = c;
            (b = this.container) ? (b.container.innerHTML = "", e.appendChild(b.container),
            b.width = c, b.height = f, b.addDefs(a)) :b = new d.AmDraw(e, c, f, a);
            this.container = b;
            this.lx = 0;
            this.ly = 8;
            f = this.markerSize;
            f > this.fontSize && (this.ly = f / 2 - 1);
            0 < f && (this.lx += f + this.markerLabelGap);
            this.titleWidth = 0;
            if (f = this.title) f = d.text(this.container, f, this.color, a.fontFamily, this.fontSize, "start", !0),
            d.setCN(a, f, "legend-title"), f.translate(this.marginLeft, this.marginTop + this.verticalGap + this.ly + 1),
            a = f.getBBox(), this.titleWidth = a.width + 15, this.titleHeight = a.height + 6;
            this.index = this.maxLabelWidth = 0;
            if (this.showEntries) {
                for (a = 0; a < g.length; a++) this.createEntry(g[a]);
                for (a = this.index = 0; a < g.length; a++) this.createValue(g[a]);
            }
            this.arrangeEntries();
            this.updateValues();
        },
        arrangeEntries:function() {
            var a = this.position, b = this.marginLeft + this.titleWidth, c = this.marginRight, h = this.marginTop, f = this.marginBottom, e = this.horizontalGap, g = this.div, k = this.divWidth, l = this.maxColumns, m = this.verticalGap, n = this.spacing, p = k - c - b, u = 0, r = 0, A = this.container;
            this.set && this.set.remove();
            var B = A.set();
            this.set = B;
            var w = A.set();
            B.push(w);
            var D = this.entries, x, y;
            for (y = 0; y < D.length; y++) {
                x = D[y].getBBox();
                var q = x.width;
                q > u && (u = q);
                x = x.height;
                x > r && (r = x);
            }
            var q = r = 0, v = e, t = 0, z = 0;
            for (y = 0; y < D.length; y++) {
                var C = D[y];
                this.reversedOrder && (C = D[D.length - y - 1]);
                x = C.getBBox();
                var G;
                this.equalWidths ? G = e + q * (u + n + this.markerLabelGap) :(G = v, v = v + x.width + e + n);
                x.height > z && (z = x.height);
                G + x.width > p && 0 < y && 0 !== q && (r++, q = 0, G = e, v = G + x.width + e + n,
                t = t + z + m, z = 0);
                C.translate(G, t);
                q++;
                !isNaN(l) && q >= l && (q = 0, r++, t = t + z + m, z = 0);
                w.push(C);
            }
            x = w.getBBox();
            l = x.height + 2 * m - 1;
            "left" == a || "right" == a ? (n = x.width + 2 * e, k = n + b + c, g.style.width = k + "px",
            this.ieW = k) :n = k - b - c - 1;
            c = d.polygon(this.container, [ 0, n, n, 0 ], [ 0, 0, l, l ], this.backgroundColor, this.backgroundAlpha, 1, this.borderColor, this.borderAlpha);
            d.setCN(this.chart, c, "legend-bg");
            B.push(c);
            B.translate(b, h);
            c.toBack();
            b = e;
            if ("top" == a || "bottom" == a || "absolute" == a || "outside" == a) "center" == this.align ? b = e + (n - x.width) / 2 :"right" == this.align && (b = e + n - x.width);
            w.translate(b, m + 1);
            this.titleHeight > l && (l = this.titleHeight);
            a = l + h + f + 1;
            0 > a && (a = 0);
            a > this.chart.divRealHeight && (g.style.top = "0px");
            g.style.height = Math.round(a) + "px";
            A.setSize(this.divWidth, a);
        },
        createEntry:function(a) {
            if (!1 !== a.visibleInLegend) {
                var b = this.chart, c = a.markerType;
                a.legendEntryWidth = this.markerSize;
                c || (c = this.markerType);
                var h = a.color, f = a.alpha;
                a.legendKeyColor && (h = a.legendKeyColor());
                a.legendKeyAlpha && (f = a.legendKeyAlpha());
                var e;
                !0 === a.hidden && (e = h = this.markerDisabledColor);
                var g = a.pattern, k = a.customMarker;
                k || (k = this.customMarker);
                var l = this.container, m = this.markerSize, n = 0, p = 0, u = m / 2;
                if (this.useGraphSettings) {
                    c = a.type;
                    this.switchType = void 0;
                    if ("line" == c || "step" == c || "smoothedLine" == c || "ohlc" == c) g = l.set(),
                    a.hidden || (h = a.lineColorR, e = a.bulletBorderColorR), n = d.line(l, [ 0, 2 * m ], [ m / 2, m / 2 ], h, a.lineAlpha, a.lineThickness, a.dashLength),
                    d.setCN(b, n, "graph-stroke"), g.push(n), a.bullet && (a.hidden || (h = a.bulletColorR),
                    n = d.bullet(l, a.bullet, a.bulletSize, h, a.bulletAlpha, a.bulletBorderThickness, e, a.bulletBorderAlpha)) && (d.setCN(b, n, "graph-bullet"),
                    n.translate(m + 1, m / 2), g.push(n)), u = 0, n = m, p = m / 3; else {
                        var r;
                        a.getGradRotation && (r = a.getGradRotation());
                        n = a.fillColorsR;
                        !0 === a.hidden && (n = h);
                        if (g = this.createMarker("rectangle", n, a.fillAlphas, a.lineThickness, h, a.lineAlpha, r, g)) u = m,
                        g.translate(u, m / 2);
                        n = m;
                    }
                    d.setCN(b, g, "graph-" + c);
                    d.setCN(b, g, "graph-" + a.id);
                } else k ? (b.path && (k = b.path + k), g = l.image(k, 0, 0, m, m)) :(g = this.createMarker(c, h, f, void 0, void 0, void 0, void 0, g)) && g.translate(m / 2, m / 2);
                d.setCN(b, g, "legend-marker");
                this.addListeners(g, a);
                l = l.set([ g ]);
                this.switchable && a.switchable && l.setAttr("cursor", "pointer");
                void 0 !== a.id && d.setCN(b, l, "legend-item-" + a.id);
                d.setCN(b, l, a.className, !0);
                (e = this.switchType) && "none" != e && ("x" == e ? (c = this.createX(), c.translate(m / 2, m / 2)) :c = this.createV(),
                c.dItem = a, !0 !== a.hidden ? "x" == e ? c.hide() :c.show() :"x" != e && c.hide(),
                this.switchable || c.hide(), this.addListeners(c, a), a.legendSwitch = c, l.push(c),
                d.setCN(b, c, "legend-switch"));
                e = this.color;
                a.showBalloon && this.textClickEnabled && void 0 !== this.selectedColor && (e = this.selectedColor);
                this.useMarkerColorForLabels && (e = h);
                !0 === a.hidden && (e = this.markerDisabledColor);
                h = d.massReplace(this.labelText, {
                    "[[title]]":a.title
                });
                c = this.fontSize;
                g && (m <= c && g.translate(u, m / 2 + this.ly - c / 2 + (c + 2 - m) / 2 - p), a.legendEntryWidth = g.getBBox().width);
                var A;
                h && (h = d.fixBrakes(h), a.legendTextReal = h, A = this.labelWidth, A = isNaN(A) ? d.text(this.container, h, e, b.fontFamily, c, "start") :d.wrappedText(this.container, h, e, b.fontFamily, c, "start", !1, A, 0),
                d.setCN(b, A, "legend-label"), A.translate(this.lx + n, this.ly), l.push(A), b = A.getBBox().width,
                this.maxLabelWidth < b && (this.maxLabelWidth = b));
                this.entries[this.index] = l;
                a.legendEntry = this.entries[this.index];
                a.legendLabel = A;
                this.index++;
            }
        },
        addListeners:function(a, b) {
            var c = this;
            a && a.mouseover(function(a) {
                c.rollOverMarker(b, a);
            }).mouseout(function(a) {
                c.rollOutMarker(b, a);
            }).click(function(a) {
                c.clickMarker(b, a);
            });
        },
        rollOverMarker:function(a, b) {
            this.switchable && this.dispatch("rollOverMarker", a, b);
            this.dispatch("rollOverItem", a, b);
        },
        rollOutMarker:function(a, b) {
            this.switchable && this.dispatch("rollOutMarker", a, b);
            this.dispatch("rollOutItem", a, b);
        },
        clickMarker:function(a, b) {
            this.switchable && (!0 === a.hidden ? this.dispatch("showItem", a, b) :this.dispatch("hideItem", a, b));
            this.dispatch("clickMarker", a, b);
        },
        rollOverLabel:function(a, b) {
            a.hidden || (this.textClickEnabled && a.legendLabel && a.legendLabel.attr({
                fill:this.rollOverColor
            }), this.dispatch("rollOverItem", a, b));
        },
        rollOutLabel:function(a, b) {
            if (!a.hidden) {
                if (this.textClickEnabled && a.legendLabel) {
                    var c = this.color;
                    void 0 !== this.selectedColor && a.showBalloon && (c = this.selectedColor);
                    this.useMarkerColorForLabels && (c = a.lineColor, void 0 === c && (c = a.color));
                    a.legendLabel.attr({
                        fill:c
                    });
                }
                this.dispatch("rollOutItem", a, b);
            }
        },
        clickLabel:function(a, b) {
            this.textClickEnabled ? a.hidden || this.dispatch("clickLabel", a, b) :this.switchable && (!0 === a.hidden ? this.dispatch("showItem", a, b) :this.dispatch("hideItem", a, b));
        },
        dispatch:function(a, b, c) {
            this.fire(a, {
                type:a,
                dataItem:b,
                target:this,
                event:c,
                chart:this.chart
            });
        },
        createValue:function(a) {
            var b = this, c = b.fontSize, h = b.chart;
            if (!1 !== a.visibleInLegend) {
                var f = b.maxLabelWidth;
                b.forceWidth && (f = b.labelWidth);
                b.equalWidths || (b.valueAlign = "left");
                "left" == b.valueAlign && (f = a.legendEntry.getBBox().width);
                var e = f;
                if (b.valueText && 0 < b.valueWidth) {
                    var g = b.color;
                    b.useMarkerColorForValues && (g = a.color, a.legendKeyColor && (g = a.legendKeyColor()));
                    !0 === a.hidden && (g = b.markerDisabledColor);
                    var k = b.valueText, f = f + b.lx + b.markerLabelGap + b.valueWidth, l = "end";
                    "left" == b.valueAlign && (f -= b.valueWidth, l = "start");
                    g = d.text(b.container, k, g, b.chart.fontFamily, c, l);
                    d.setCN(h, g, "legend-value");
                    g.translate(f, b.ly);
                    b.entries[b.index].push(g);
                    e += b.valueWidth + 2 * b.markerLabelGap;
                    g.dItem = a;
                    b.valueLabels.push(g);
                }
                b.index++;
                h = b.markerSize;
                h < c + 7 && (h = c + 7, d.VML && (h += 3));
                c = b.container.rect(a.legendEntryWidth, 0, e, h, 0, 0).attr({
                    stroke:"none",
                    fill:"#fff",
                    "fill-opacity":.005
                });
                c.dItem = a;
                b.entries[b.index - 1].push(c);
                c.mouseover(function(c) {
                    b.rollOverLabel(a, c);
                }).mouseout(function(c) {
                    b.rollOutLabel(a, c);
                }).click(function(c) {
                    b.clickLabel(a, c);
                });
            }
        },
        createV:function() {
            var a = this.markerSize;
            return d.polygon(this.container, [ a / 5, a / 2, a - a / 5, a / 2 ], [ a / 3, a - a / 5, a / 5, a / 1.7 ], this.switchColor);
        },
        createX:function() {
            var a = (this.markerSize - 4) / 2, b = {
                stroke:this.switchColor,
                "stroke-width":3
            }, c = this.container, h = d.line(c, [ -a, a ], [ -a, a ]).attr(b), a = d.line(c, [ -a, a ], [ a, -a ]).attr(b);
            return this.container.set([ h, a ]);
        },
        createMarker:function(a, b, c, h, f, e, g, k) {
            var l = this.markerSize, m = this.container;
            f || (f = this.markerBorderColor);
            f || (f = b);
            isNaN(h) && (h = this.markerBorderThickness);
            isNaN(e) && (e = this.markerBorderAlpha);
            return d.bullet(m, a, l, b, c, h, f, e, l, g, k);
        },
        validateNow:function() {
            this.invalidateSize();
        },
        updateValues:function() {
            var a = this.valueLabels, b = this.chart, c, d = this.data;
            for (c = 0; c < a.length; c++) {
                var f = a[c], e = f.dItem, g = " ";
                if (d) e.value ? f.text(e.value) :f.text(""); else {
                    var k;
                    if (void 0 !== e.type) {
                        k = e.currentDataItem;
                        var l = this.periodValueText;
                        e.legendPeriodValueText && (l = e.legendPeriodValueText);
                        k ? (g = this.valueText, e.legendValueText && (g = e.legendValueText), g = b.formatString(g, k)) :l && (g = b.formatPeriodString(l, e));
                    } else g = b.formatString(this.valueText, e);
                    if (l = this.valueFunction) k && (e = k), g = l(e, g);
                    f.text(g);
                }
            }
        },
        renderFix:function() {
            if (!d.VML) {
                var a = this.container;
                a && a.renderFix();
            }
        },
        destroy:function() {
            this.div.innerHTML = "";
            d.remove(this.set);
        }
    });
})();

(function() {
    var d = window.AmCharts;
    d.AmMap = d.Class({
        inherits:d.AmChart,
        construct:function(a) {
            this.cname = "AmMap";
            this.type = "map";
            this.theme = a;
            this.version = "3.14.1";
            this.svgNotSupported = "This browser doesn't support SVG. Use Chrome, Firefox, Internet Explorer 9 or later.";
            this.createEvents("rollOverMapObject", "rollOutMapObject", "clickMapObject", "selectedObjectChanged", "homeButtonClicked", "zoomCompleted", "dragCompleted", "positionChanged", "writeDevInfo", "click");
            this.zoomDuration = 1;
            this.zoomControl = new d.ZoomControl(a);
            this.fitMapToContainer = !0;
            this.mouseWheelZoomEnabled = this.backgroundZoomsToTop = !1;
            this.allowClickOnSelectedObject = this.useHandCursorOnClickableOjects = this.showBalloonOnSelectedObject = !0;
            this.showObjectsAfterZoom = this.wheelBusy = !1;
            this.zoomOnDoubleClick = this.useObjectColorForBalloon = !0;
            this.allowMultipleDescriptionWindows = !1;
            this.dragMap = this.centerMap = this.linesAboveImages = !0;
            this.colorSteps = 5;
            this.showAreasInList = !0;
            this.showLinesInList = this.showImagesInList = !1;
            this.areasProcessor = new d.AreasProcessor(this);
            this.areasSettings = new d.AreasSettings(a);
            this.imagesProcessor = new d.ImagesProcessor(this);
            this.imagesSettings = new d.ImagesSettings(a);
            this.linesProcessor = new d.LinesProcessor(this);
            this.linesSettings = new d.LinesSettings(a);
            this.showDescriptionOnHover = !1;
            d.AmMap.base.construct.call(this, a);
            this.creditsPosition = "bottom-left";
            this.product = "ammap";
            this.areasClasses = {};
            d.applyTheme(this, a, this.cname);
        },
        initChart:function() {
            this.zoomInstantly = !0;
            var a = this.container;
            if (this.sizeChanged && d.hasSVG && this.chartCreated) {
                this.freeLabelsSet && this.freeLabelsSet.remove();
                this.freeLabelsSet = a.set();
                this.container.setSize(this.realWidth, this.realHeight);
                this.resizeMap();
                this.drawBackground();
                this.redrawLabels();
                this.drawTitles();
                this.processObjects();
                this.rescaleObjects();
                this.zoomControl.init(this, a);
                this.drawBg();
                var b = this.smallMap;
                b && b.init(this, a);
                (b = this.valueLegend) && b.init(this, a);
                this.sizeChanged = !1;
                this.zoomToLongLat(this.zLevelTemp, this.zLongTemp, this.zLatTemp, !0);
                this.previousWidth = this.realWidth;
                this.previousHeight = this.realHeight;
                this.updateSmallMap();
                this.linkSet.toFront();
            } else (d.AmMap.base.initChart.call(this), d.hasSVG) ? (this.dataChanged && (this.parseData(),
            this.dispatchDataUpdated = !0, this.dataChanged = !1, a = this.legend) && (a.position = "absolute",
            a.invalidateSize()), this.createDescriptionsDiv(), this.svgAreas = [], this.svgAreasById = {},
            this.drawChart()) :(this.chartDiv.style.textAlign = "", this.chartDiv.setAttribute("class", "ammapAlert"),
            this.chartDiv.innerHTML = this.svgNotSupported, this.fire("failed", {
                type:"failed",
                chart:this
            }));
        },
        storeTemp:function() {
            var a = this.zoomLongitude();
            isNaN(a) || (this.zLongTemp = a);
            a = this.zoomLatitude();
            isNaN(a) || (this.zLatTemp = a);
            a = this.zoomLevel();
            isNaN(a) || (this.zLevelTemp = a);
        },
        invalidateSize:function() {
            this.storeTemp();
            d.AmMap.base.invalidateSize.call(this);
        },
        validateSize:function() {
            d.hasSVG && this.storeTemp();
            d.AmMap.base.validateSize.call(this);
        },
        handleWheelReal:function(a) {
            if (!this.wheelBusy) {
                this.stopAnimation();
                var b = this.zoomLevel(), c = this.zoomControl, h = c.zoomFactor;
                this.wheelBusy = !0;
                a = d.fitToBounds(0 < a ? b * h :b / h, c.minZoomLevel, c.maxZoomLevel);
                h = this.mouseX / this.mapWidth;
                c = this.mouseY / this.mapHeight;
                h = (this.zoomX() - h) * (a / b) + h;
                b = (this.zoomY() - c) * (a / b) + c;
                this.zoomTo(a, h, b);
            }
        },
        addLegend:function(a, b) {
            a.position = "absolute";
            a.autoMargins = !1;
            a.valueWidth = 0;
            a.switchable = !1;
            d.AmMap.base.addLegend.call(this, a, b);
            void 0 === a.enabled && (a.enabled = !0);
            return a;
        },
        handleLegendEvent:function() {},
        createDescriptionsDiv:function() {
            if (!this.descriptionsDiv) {
                var a = document.createElement("div"), b = a.style;
                b.position = "absolute";
                b.left = "0px";
                b.top = "0px";
                this.descriptionsDiv = a;
            }
            this.containerDiv.appendChild(this.descriptionsDiv);
        },
        drawChart:function() {
            d.AmMap.base.drawChart.call(this);
            var a = this.dataProvider;
            this.dataProvider = a = d.extend(a, new d.MapData(), !0);
            this.areasSettings = d.processObject(this.areasSettings, d.AreasSettings, this.theme);
            this.imagesSettings = d.processObject(this.imagesSettings, d.ImagesSettings, this.theme);
            this.linesSettings = d.processObject(this.linesSettings, d.LinesSettings, this.theme);
            var b = this.container;
            this.mapContainer && this.mapContainer.remove();
            this.mapContainer = b.set();
            this.graphsSet.push(this.mapContainer);
            var c;
            a.map && (c = d.maps[a.map]);
            a.mapVar && (c = a.mapVar);
            c ? (this.svgData = c.svg, this.getBounds(), this.buildEverything()) :(a = a.mapURL) && this.loadXml(a);
            this.balloonsSet.toFront();
        },
        drawBg:function() {
            var a = this;
            a.background.click(function() {
                a.handleBackgroundClick();
            });
        },
        buildEverything:function() {
            if (0 < this.realWidth && 0 < this.realHeight) {
                var a = this.container;
                this.zoomControl = d.processObject(this.zoomControl, d.ZoomControl, this.theme);
                this.zoomControl.init(this, a);
                this.drawBg();
                this.buildSVGMap();
                var b = this.smallMap;
                b && (b = this.smallMap = d.processObject(this.smallMap, d.SmallMap, this.theme),
                b.init(this, a));
                b = this.dataProvider;
                isNaN(b.zoomX) && isNaN(b.zoomY) && isNaN(b.zoomLatitude) && isNaN(b.zoomLongitude) && (this.centerMap ? (b.zoomLatitude = this.coordinateToLatitude(this.mapHeight / 2),
                b.zoomLongitude = this.coordinateToLongitude(this.mapWidth / 2)) :(b.zoomX = 0,
                b.zoomY = 0), this.zoomInstantly = !0);
                this.selectObject(this.dataProvider);
                this.processAreas();
                if (b = this.valueLegend) this.valueLegend = b = d.processObject(b, d.ValueLegend, this.theme),
                b.init(this, a);
                this.objectList && (a = this.objectList = d.processObject(this.objectList, d.ObjectList)) && (this.clearObjectList(),
                a.init(this));
                this.dispDUpd();
                this.linkSet.toFront();
                this.chartCreated = !0;
            } else this.cleanChart();
        },
        hideGroup:function(a) {
            this.showHideGroup(a, !1);
        },
        showGroup:function(a) {
            this.showHideGroup(a, !0);
        },
        showHideGroup:function(a, b) {
            this.showHideReal(this.imagesProcessor.allObjects, a, b);
            this.showHideReal(this.areasProcessor.allObjects, a, b);
            this.showHideReal(this.linesProcessor.allObjects, a, b);
        },
        showHideReal:function(a, b, c) {
            var d;
            for (d = 0; d < a.length; d++) {
                var f = a[d];
                if (f.groupId == b) {
                    var e = f.displayObject;
                    e && (c ? (f.hidden = !1, e.show()) :(f.hidden = !0, e.hide()));
                }
            }
        },
        update:function() {
            d.hasSVG && (d.AmMap.base.update.call(this), this.zoomControl && this.zoomControl.update());
        },
        animateMap:function() {
            var a = this;
            a.totalFrames = 1e3 * a.zoomDuration / d.updateRate;
            a.totalFrames += 1;
            a.frame = 0;
            a.tweenPercent = 0;
            setTimeout(function() {
                a.updateSize.call(a);
            }, d.updateRate);
        },
        updateSize:function() {
            var a = this, b = a.totalFrames;
            a.preventHover = !0;
            a.frame <= b ? (a.frame++, b = d.easeOutSine(0, a.frame, 0, 1, b), 1 <= b ? (b = 1,
            a.preventHover = !1, a.wheelBusy = !1) :setTimeout(function() {
                a.updateSize.call(a);
            }, d.updateRate), .8 < b && (a.preventHover = !1)) :(b = 1, a.preventHover = !1,
            a.wheelBusy = !1);
            a.tweenPercent = b;
            a.rescaleMapAndObjects();
        },
        rescaleMapAndObjects:function() {
            var a = this.initialScale, b = this.initialX, c = this.initialY, d = this.tweenPercent, a = a + (this.finalScale - a) * d;
            this.mapContainer.translate(b + (this.finalX - b) * d, c + (this.finalY - c) * d, a);
            if (this.areasSettings.adjustOutlineThickness) for (b = this.dataProvider.areas,
            c = 0; c < b.length; c++) {
                var f = b[c], e = f.displayObject;
                e && e.setAttr("stroke-width", f.outlineThicknessReal / a);
            }
            this.rescaleObjects();
            this.positionChanged();
            this.updateSmallMap();
            1 == d && (d = {
                type:"zoomCompleted",
                chart:this
            }, this.fire(d.type, d));
        },
        updateSmallMap:function() {
            this.smallMap && this.smallMap.update();
        },
        rescaleObjects:function() {
            var a = this.mapContainer.scale, b = this.imagesProcessor.objectsToResize, c;
            for (c = 0; c < b.length; c++) {
                var d = b[c].image;
                d.translate(d.x, d.y, b[c].scale / a, !0);
            }
            b = this.linesProcessor;
            if (d = b.linesToResize) for (c = 0; c < d.length; c++) {
                var f = d[c];
                f.line.setAttr("stroke-width", f.thickness / a);
            }
            b = b.objectsToResize;
            for (c = 0; c < b.length; c++) d = b[c], d.translate(d.x, d.y, 1 / a);
        },
        handleTouchStart:function(a) {
            this.handleMouseMove(a);
            this.handleMouseDown(a);
        },
        handleTouchEnd:function(a) {
            this.previousDistance = NaN;
            this.handleReleaseOutside(a);
        },
        handleMouseDown:function(a) {
            d.resetMouseOver();
            this.mouseIsOver = !0;
            a && this.mouseIsOver && a.preventDefault && this.panEventsEnabled && a.preventDefault();
            if (this.chartCreated && !this.preventHover && (this.dragMap && (this.stopAnimation(),
            this.isDragging = !0, this.mapContainerClickX = this.mapContainer.x, this.mapContainerClickY = this.mapContainer.y),
            a || (a = window.event), a.shiftKey && !0 === this.developerMode && this.getDevInfo(),
            a && a.touches)) {
                var b = this.mouseX, c = this.mouseY, h = a.touches.item(1);
                h && (a = h.pageX - d.findPosX(this.div), h = h.pageY - d.findPosY(this.div), this.middleXP = (b + (a - b) / 2) / this.realWidth,
                this.middleYP = (c + (h - c) / 2) / this.realHeight);
            }
        },
        stopDrag:function() {
            this.isDragging = !1;
        },
        handleReleaseOutside:function() {
            if (d.isModern && !this.preventHover) {
                this.stopDrag();
                var a = this.zoomControl;
                a && a.draggerUp && a.draggerUp();
                this.mapWasDragged = !1;
                var a = this.mapContainer, b = this.mapContainerClickX, c = this.mapContainerClickY;
                isNaN(b) || isNaN(c) || !(2 < Math.abs(a.x - b) || Math.abs(a.y - c)) || (this.mapWasDragged = !0,
                a = {
                    type:"dragCompleted",
                    zoomX:this.zoomX(),
                    zoomY:this.zoomY(),
                    zoomLevel:this.zoomLevel(),
                    chart:this
                }, this.fire(a.type, a));
                !this.mouseIsOver || this.mapWasDragged || this.skipClick || (a = {
                    type:"click",
                    x:this.mouseX,
                    y:this.mouseY,
                    chart:this
                }, this.fire(a.type, a), this.skipClick = !1);
                this.mapContainerClickY = this.mapContainerClickX = NaN;
                this.objectWasClicked = !1;
                this.zoomOnDoubleClick && this.mouseIsOver && (a = new Date().getTime(), 200 > a - this.previousClickTime && 20 < a - this.previousClickTime && this.doDoubleClickZoom(),
                this.previousClickTime = a);
            }
        },
        handleTouchMove:function(a) {
            this.handleMouseMove(a);
        },
        resetPinch:function() {
            this.mapWasPinched = !1;
        },
        handleMouseMove:function(a) {
            var b = this;
            d.AmMap.base.handleMouseMove.call(b, a);
            b.panEventsEnabled && b.mouseIsOver && a && a.preventDefault && a.preventDefault();
            var c = b.previuosMouseX, h = b.previuosMouseY, f = b.mouseX, e = b.mouseY, g = b.zoomControl;
            isNaN(c) && (c = f);
            isNaN(h) && (h = e);
            b.mouse2X = NaN;
            b.mouse2Y = NaN;
            a && a.touches && (a = a.touches.item(1)) && (b.mouse2X = a.pageX - d.findPosX(b.div),
            b.mouse2Y = a.pageY - d.findPosY(b.div));
            if (a = b.mapContainer) {
                var k = b.mouse2X, l = b.mouse2Y;
                b.pinchTO && clearTimeout(b.pinchTO);
                b.pinchTO = setTimeout(function() {
                    b.resetPinch.call(b);
                }, 1e3);
                var m = b.realHeight, n = b.realWidth, p = b.mapWidth, u = b.mapHeight;
                if (!isNaN(k)) {
                    b.stopDrag();
                    var k = Math.sqrt(Math.pow(k - f, 2) + Math.pow(l - e, 2)), r = b.previousDistance, l = Math.max(b.realWidth, b.realHeight);
                    5 > Math.abs(r - k) && (b.isDragging = !0);
                    if (!isNaN(r)) {
                        var A = 5 * Math.abs(r - k) / l, l = a.scale, l = d.fitToBounds(r < k ? l + l * A :l - l * A, g.minZoomLevel, g.maxZoomLevel), g = b.zoomLevel(), B = b.middleXP, r = b.middleYP, A = m / u, w = n / p, B = (b.zoomX() - B * w) * (l / g) + B * w, r = (b.zoomY() - r * A) * (l / g) + r * A;
                        .1 < Math.abs(l - g) && (b.zoomTo(l, B, r, !0), b.mapWasPinched = !0, clearTimeout(b.pinchTO));
                    }
                    b.previousDistance = k;
                }
                k = a.scale;
                b.isDragging && (b.hideBalloon(), b.positionChanged(), c = a.x + (f - c), h = a.y + (e - h),
                b.preventDragOut && (u = -u * k + m / 2, m /= 2, c = d.fitToBounds(c, -p * k + n / 2, n / 2),
                h = d.fitToBounds(h, u, m)), a.translate(c, h, k), b.updateSmallMap());
                b.previuosMouseX = f;
                b.previuosMouseY = e;
            }
        },
        selectObject:function(a) {
            var b = this;
            a || (a = b.dataProvider);
            a.isOver = !1;
            var c = a.linkToObject;
            "string" == typeof c && (c = b.getObjectById(c));
            a.useTargetsZoomValues && c && (a.zoomX = c.zoomX, a.zoomY = c.zoomY, a.zoomLatitude = c.zoomLatitude,
            a.zoomLongitude = c.zoomLongitude, a.zoomLevel = c.zoomLevel);
            var d = b.selectedObject;
            d && b.returnInitialColor(d);
            b.selectedObject = a;
            var f = !1, e;
            "MapArea" == a.objectType && (a.autoZoomReal && (f = !0), e = b.areasSettings.selectedOutlineColor);
            if (c && !f && ("string" == typeof c && (c = b.getObjectById(c)), isNaN(a.zoomLevel) && isNaN(a.zoomX) && isNaN(a.zoomY))) {
                if (b.extendMapData(c)) return;
                b.selectObject(c);
                return;
            }
            b.allowMultipleDescriptionWindows || b.closeAllDescriptions();
            clearTimeout(b.selectedObjectTimeOut);
            clearTimeout(b.processObjectsTimeOut);
            c = b.zoomDuration;
            !f && isNaN(a.zoomLevel) && isNaN(a.zoomX) && isNaN(a.zoomY) ? (b.showDescriptionAndGetUrl(),
            b.processObjects()) :(b.selectedObjectTimeOut = setTimeout(function() {
                b.showDescriptionAndGetUrl.call(b);
            }, 1e3 * c + 200), b.showObjectsAfterZoom ? b.processObjectsTimeOut = setTimeout(function() {
                b.processObjects.call(b);
            }, 1e3 * c + 200) :b.processObjects());
            c = a.displayObject;
            f = a.selectedColorReal;
            if (c) {
                if (a.bringForwardOnHover && c.toFront(), !a.preserveOriginalAttributes) {
                    c.setAttr("stroke", a.outlineColorReal);
                    void 0 !== f && c.setAttr("fill", f);
                    void 0 !== e && c.setAttr("stroke", e);
                    if ("MapLine" == a.objectType) {
                        var g = a.lineSvg;
                        g && g.setAttr("stroke", f);
                        if (g = a.arrowSvg) g.setAttr("fill", f), g.setAttr("stroke", f);
                    }
                    if (g = a.imageLabel) {
                        var k = a.selectedLabelColorReal;
                        void 0 !== k && g.setAttr("fill", k);
                    }
                    a.selectable || (c.setAttr("cursor", "default"), g && g.setAttr("cursor", "default"));
                }
            } else b.returnInitialColorReal(a);
            if (c = a.groupId) for (g = b.getGroupById(c), k = 0; k < g.length; k++) {
                var l = g[k];
                l.isOver = !1;
                if (c = l.displayObject) {
                    var m = l.selectedColorReal;
                    void 0 !== e && c.setAttr("stroke", e);
                    void 0 !== m ? c.setAttr("fill", m) :b.returnInitialColor(l);
                    "MapLine" == l.objectType && ((c = l.lineSvg) && c.setAttr("stroke", f), c = l.arrowSvg) && (c.setAttr("fill", f),
                    c.setAttr("stroke", f));
                }
            }
            b.zoomToSelectedObject();
            d != a && (a = {
                type:"selectedObjectChanged",
                chart:b
            }, b.fire(a.type, a));
        },
        returnInitialColor:function(a, b) {
            this.returnInitialColorReal(a);
            b && (a.isFirst = !1);
            if (this.selectedObject.bringForwardOnHover) {
                var c = this.selectedObject.displayObject;
                c && c.toFront();
            }
            if (c = a.groupId) {
                var c = this.getGroupById(c), d;
                for (d = 0; d < c.length; d++) this.returnInitialColorReal(c[d]), b && (c[d].isFirst = !1);
            }
        },
        closeAllDescriptions:function() {
            this.descriptionsDiv.innerHTML = "";
        },
        returnInitialColorReal:function(a) {
            a.isOver = !1;
            var b = a.displayObject;
            if (b) {
                b.toPrevious();
                if ("MapImage" == a.objectType) {
                    var c = a.tempScale;
                    isNaN(c) || b.translate(b.x, b.y, c, !0);
                    a.tempScale = NaN;
                }
                c = a.colorReal;
                if ("MapLine" == a.objectType) {
                    var d = a.lineSvg;
                    d && d.setAttr("stroke", c);
                    if (d = a.arrowSvg) d.setAttr("fill", c), d.setAttr("stroke", c);
                }
                a.showAsSelected && (c = a.selectedColorReal);
                "bubble" == a.type && (c = void 0);
                void 0 !== c && b.setAttr("fill", c);
                (d = a.image) && d.setAttr("fill", c);
                b.setAttr("stroke", a.outlineColorReal);
                "MapArea" == a.objectType && (c = 1, this.areasSettings.adjustOutlineThickness && (c = this.zoomLevel()),
                b.setAttr("fill-opacity", a.alphaReal), b.setAttr("stroke-opacity", a.outlineAlphaReal),
                b.setAttr("stroke-width", a.outlineThicknessReal / c));
                (c = a.pattern) && b.pattern(c, this.mapScale);
                (b = a.imageLabel) && !a.labelInactive && b.setAttr("fill", a.labelColorReal);
            }
        },
        zoomToRectangle:function(a, b, c, h) {
            var f = this.realWidth, e = this.realHeight, g = this.mapSet.scale, k = this.zoomControl, f = d.fitToBounds(c / f > h / e ? .8 * f / (c * g) :.8 * e / (h * g), k.minZoomLevel, k.maxZoomLevel);
            this.zoomToMapXY(f, (a + c / 2) * g, (b + h / 2) * g);
        },
        zoomToLatLongRectangle:function(a, b, c, h) {
            var f = this.dataProvider, e = this.zoomControl, g = Math.abs(c - a), k = Math.abs(b - h), l = Math.abs(f.rightLongitude - f.leftLongitude), f = Math.abs(f.topLatitude - f.bottomLatitude), e = d.fitToBounds(g / l > k / f ? .8 * l / g :.8 * f / k, e.minZoomLevel, e.maxZoomLevel);
            this.zoomToLongLat(e, a + (c - a) / 2, h + (b - h) / 2);
        },
        getGroupById:function(a) {
            var b = [];
            this.getGroup(this.imagesProcessor.allObjects, a, b);
            this.getGroup(this.linesProcessor.allObjects, a, b);
            this.getGroup(this.areasProcessor.allObjects, a, b);
            return b;
        },
        zoomToGroup:function(a) {
            a = "object" == typeof a ? a :this.getGroupById(a);
            var b, c, d, f, e;
            for (e = 0; e < a.length; e++) {
                var g = a[e].displayObject;
                if (g) {
                    var k = g.getBBox(), g = k.y, l = k.y + k.height, m = k.x, k = k.x + k.width;
                    if (g < b || isNaN(b)) b = g;
                    if (l > f || isNaN(f)) f = l;
                    if (m < c || isNaN(c)) c = m;
                    if (k > d || isNaN(d)) d = k;
                }
            }
            a = this.mapSet.getBBox();
            c -= a.x;
            d -= a.x;
            f -= a.y;
            b -= a.y;
            this.zoomToRectangle(c, b, d - c, f - b);
        },
        getGroup:function(a, b, c) {
            if (a) {
                var d;
                for (d = 0; d < a.length; d++) {
                    var f = a[d];
                    f.groupId == b && c.push(f);
                }
            }
        },
        zoomToStageXY:function(a, b, c, h) {
            if (!this.objectWasClicked) {
                var f = this.zoomControl;
                a = d.fitToBounds(a, f.minZoomLevel, f.maxZoomLevel);
                f = this.zoomLevel();
                c = this.coordinateToLatitude((c - this.mapContainer.y) / f);
                b = this.coordinateToLongitude((b - this.mapContainer.x) / f);
                this.zoomToLongLat(a, b, c, h);
            }
        },
        zoomToLongLat:function(a, b, c, d) {
            b = this.longitudeToCoordinate(b);
            c = this.latitudeToCoordinate(c);
            this.zoomToMapXY(a, b, c, d);
        },
        zoomToMapXY:function(a, b, c, d) {
            var f = this.mapWidth, e = this.mapHeight;
            this.zoomTo(a, -(b / f) * a + this.realWidth / f / 2, -(c / e) * a + this.realHeight / e / 2, d);
        },
        zoomToObject:function(a) {
            if (a) {
                var b = a.zoomLatitude, c = a.zoomLongitude, h = a.zoomLevel, f = this.zoomInstantly, e = a.zoomX, g = a.zoomY, k = this.realWidth, l = this.realHeight;
                isNaN(h) || (isNaN(b) || isNaN(c) ? this.zoomTo(h, e, g, f) :this.zoomToLongLat(h, c, b, f));
                this.zoomInstantly = !1;
                "MapImage" == a.objectType && isNaN(a.zoomX) && isNaN(a.zoomY) && isNaN(a.zoomLatitude) && isNaN(a.zoomLongitude) && !isNaN(a.latitude) && !isNaN(a.longitude) && this.zoomToLongLat(a.zoomLevel, a.longitude, a.latitude);
                "MapArea" == a.objectType && (e = a.displayObject.getBBox(), b = this.mapScale,
                c = e.x * b, h = e.y * b, f = e.width * b, e = e.height * b, k = a.autoZoomReal && isNaN(a.zoomLevel) ? f / k > e / l ? .8 * k / f :.8 * l / e :a.zoomLevel,
                l = this.zoomControl, k = d.fitToBounds(k, l.minZoomLevel, l.maxZoomLevel), isNaN(a.zoomX) && isNaN(a.zoomY) && isNaN(a.zoomLatitude) && isNaN(a.zoomLongitude) && (a = this.mapSet.getBBox(),
                this.zoomToMapXY(k, -a.x * b + c + f / 2, -a.y * b + h + e / 2)));
            }
        },
        zoomToSelectedObject:function() {
            this.zoomToObject(this.selectedObject);
        },
        zoomTo:function(a, b, c, h) {
            var f = this.zoomControl;
            a = d.fitToBounds(a, f.minZoomLevel, f.maxZoomLevel);
            f = this.zoomLevel();
            isNaN(b) && (b = this.realWidth / this.mapWidth, b = (this.zoomX() - .5 * b) * (a / f) + .5 * b);
            isNaN(c) && (c = this.realHeight / this.mapHeight, c = (this.zoomY() - .5 * c) * (a / f) + .5 * c);
            this.stopAnimation();
            isNaN(a) || (f = this.mapContainer, this.initialX = f.x, this.initialY = f.y, this.initialScale = f.scale,
            this.finalX = this.mapWidth * b, this.finalY = this.mapHeight * c, this.finalScale = a,
            this.finalX != this.initialX || this.finalY != this.initialY || this.finalScale != this.initialScale ? h ? (this.tweenPercent = 1,
            this.rescaleMapAndObjects(), this.wheelBusy = !1) :this.animateMap() :this.wheelBusy = !1);
        },
        loadXml:function(a) {
            var b;
            window.XMLHttpRequest && (b = new XMLHttpRequest());
            b.overrideMimeType && b.overrideMimeType("text/xml");
            b.open("GET", a, !1);
            b.send();
            this.parseXMLObject(b.responseXML);
            this.svgData && this.buildEverything();
        },
        stopAnimation:function() {
            this.frame = this.totalFrames;
        },
        processObjects:function() {
            var a = this.container, b = this.stageImagesContainer;
            b && b.remove();
            this.stageImagesContainer = b = a.set();
            this.trendLinesSet.push(b);
            var c = this.stageLinesContainer;
            c && c.remove();
            this.stageLinesContainer = c = a.set();
            this.trendLinesSet.push(c);
            var d = this.mapImagesContainer;
            d && d.remove();
            this.mapImagesContainer = d = a.set();
            this.mapContainer.push(d);
            var f = this.mapLinesContainer;
            f && f.remove();
            this.mapLinesContainer = f = a.set();
            this.mapContainer.push(f);
            this.linesAboveImages ? (d.toFront(), b.toFront(), f.toFront(), c.toFront()) :(f.toFront(),
            c.toFront(), d.toFront(), b.toFront());
            if (a = this.selectedObject) this.imagesProcessor.reset(), this.linesProcessor.reset(),
            this.linesAboveImages ? (this.imagesProcessor.process(a), this.linesProcessor.process(a)) :(this.linesProcessor.process(a),
            this.imagesProcessor.process(a));
            this.rescaleObjects();
        },
        processAreas:function() {
            this.areasProcessor.process(this.dataProvider);
        },
        buildSVGMap:function() {
            var a = this.svgData.g.path, b = this.container, c = b.set();
            void 0 === a.length && (a = [ a ]);
            var d;
            for (d = 0; d < a.length; d++) {
                var f = a[d], e = f.d, g = f.title;
                f.titleTr && (g = f.titleTr);
                e = b.path(e);
                e.id = f.id;
                if (this.areasSettings.preserveOriginalAttributes) {
                    e.customAttr = {};
                    for (var k in f) "d" != k && "id" != k && "title" != k && (e.customAttr[k] = f[k]);
                }
                this.svgAreasById[f.id] = {
                    area:e,
                    title:g,
                    className:f["class"]
                };
                this.svgAreas.push(e);
                c.push(e);
            }
            this.mapSet = c;
            this.mapContainer.push(c);
            this.resizeMap();
        },
        addObjectEventListeners:function(a, b) {
            var c = this;
            a.mouseup(function(a) {
                c.clickMapObject(b, a);
            }).mouseover(function(a) {
                c.rollOverMapObject(b, !0, a);
            }).mouseout(function(a) {
                c.rollOutMapObject(b, a);
            }).touchend(function(a) {
                c.clickMapObject(b, a);
            }).touchstart(function(a) {
                c.rollOverMapObject(b, !0, a);
            });
        },
        checkIfSelected:function(a) {
            var b = this.selectedObject;
            if (b == a) return !0;
            if (b = b.groupId) {
                var b = this.getGroupById(b), c;
                for (c = 0; c < b.length; c++) if (b[c] == a) return !0;
            }
            return !1;
        },
        clearMap:function() {
            this.chartDiv.innerHTML = "";
            this.clearObjectList();
        },
        clearObjectList:function() {
            var a = this.objectList;
            a && a.div && (a.div.innerHTML = "");
        },
        checkIfLast:function(a) {
            if (a) {
                var b = a.parentNode;
                if (b && b.lastChild == a) return !0;
            }
            return !1;
        },
        showAsRolledOver:function(a) {
            var b = a.displayObject;
            if (!a.showAsSelected && b && !a.isOver) {
                b.node.onmouseout = function() {};
                b.node.onmouseover = function() {};
                b.node.onclick = function() {};
                !a.isFirst && a.bringForwardOnHover && (b.toFront(), a.isFirst = !0);
                var c = a.rollOverColorReal, d;
                a.preserveOriginalAttributes && (c = void 0);
                if (void 0 != c) if ("MapImage" == a.objectType) (d = a.image) && d.setAttr("fill", c); else if ("MapLine" == a.objectType) {
                    if ((d = a.lineSvg) && d.setAttr("stroke", c), d = a.arrowSvg) d.setAttr("fill", c),
                    d.setAttr("stroke", c);
                } else b.setAttr("fill", c);
                (c = a.imageLabel) && !a.labelInactive && (d = a.labelRollOverColorReal, void 0 != d && c.setAttr("fill", d));
                c = a.rollOverOutlineColorReal;
                void 0 != c && ("MapImage" == a.objectType ? (d = a.image) && d.setAttr("stroke", c) :b.setAttr("stroke", c));
                if ("MapArea" == a.objectType) {
                    c = this.areasSettings;
                    d = a.rollOverAlphaReal;
                    isNaN(d) || b.setAttr("fill-opacity", d);
                    d = c.rollOverOutlineAlpha;
                    isNaN(d) || b.setAttr("stroke-opacity", d);
                    d = 1;
                    this.areasSettings.adjustOutlineThickness && (d = this.zoomLevel());
                    var f = c.rollOverOutlineThickness;
                    isNaN(f) || b.setAttr("stroke-width", f / d);
                    (c = c.rollOverPattern) && b.pattern(c, this.mapScale);
                }
                "MapImage" == a.objectType && (c = a.rollOverScaleReal, isNaN(c) || 1 == c || (a.tempScale = b.scale,
                b.translate(b.x, b.y, b.scale * c, !0)));
                this.useHandCursorOnClickableOjects && this.checkIfClickable(a) && b.setAttr("cursor", "pointer");
                this.addObjectEventListeners(b, a);
                a.isOver = !0;
            }
        },
        rollOverMapObject:function(a, b, c) {
            if (this.chartCreated) {
                this.handleMouseMove();
                var d = this.previouslyHovered;
                d && d != a ? (!1 === this.checkIfSelected(d) && (this.returnInitialColor(d, !0),
                this.previouslyHovered = null), this.hideBalloon()) :clearTimeout(this.hoverInt);
                if (!this.preventHover) {
                    if (!1 === this.checkIfSelected(a)) {
                        if (d = a.groupId) {
                            var d = this.getGroupById(d), f;
                            for (f = 0; f < d.length; f++) d[f] != a && this.showAsRolledOver(d[f]);
                        }
                        this.showAsRolledOver(a);
                    } else (d = a.displayObject) && (this.allowClickOnSelectedObject ? d.setAttr("cursor", "pointer") :d.setAttr("cursor", "default"));
                    if (this.showDescriptionOnHover) this.showDescription(a); else if ((this.showBalloonOnSelectedObject || !this.checkIfSelected(a)) && !1 !== b && (f = this.balloon,
                    b = a.colorReal, d = "", void 0 !== b && this.useObjectColorForBalloon || (b = f.fillColor),
                    (f = a.balloonTextReal) && (d = this.formatString(f, a)), this.balloonLabelFunction && (d = this.balloonLabelFunction(a, this)),
                    d && "" !== d)) {
                        var e, g;
                        "MapArea" == a.objectType && (g = this.getAreaCenterLatitude(a), e = this.getAreaCenterLongitude(a),
                        g = this.latitudeToY(g), e = this.longitudeToX(e));
                        "MapImage" == a.objectType && (e = a.displayObject.x * this.zoomLevel() + this.mapContainer.x,
                        g = a.displayObject.y * this.zoomLevel() + this.mapContainer.y);
                        this.showBalloon(d, b, this.mouseIsOver, e, g);
                    }
                    c = {
                        type:"rollOverMapObject",
                        mapObject:a,
                        chart:this,
                        event:c
                    };
                    this.fire(c.type, c);
                    this.previouslyHovered = a;
                }
            }
        },
        longitudeToX:function(a) {
            return this.longitudeToCoordinate(a) * this.zoomLevel() + this.mapContainer.x;
        },
        latitudeToY:function(a) {
            return this.latitudeToCoordinate(a) * this.zoomLevel() + this.mapContainer.y;
        },
        rollOutMapObject:function(a, b) {
            this.hideBalloon();
            if (this.chartCreated && a.isOver) {
                this.checkIfSelected(a) || this.returnInitialColor(a);
                var c = {
                    type:"rollOutMapObject",
                    mapObject:a,
                    chart:this,
                    event:b
                };
                this.fire(c.type, c);
            }
        },
        formatString:function(a, b) {
            var c = this.nf, h = this.pf, f = b.title;
            b.titleTr && (f = b.titleTr);
            void 0 == f && (f = "");
            var e = b.value, e = isNaN(e) ? "" :d.formatNumber(e, c), c = b.percents, c = isNaN(c) ? "" :d.formatNumber(c, h), h = b.description;
            void 0 == h && (h = "");
            var g = b.customData;
            void 0 == g && (g = "");
            return a = d.massReplace(a, {
                "[[title]]":f,
                "[[value]]":e,
                "[[percent]]":c,
                "[[description]]":h,
                "[[customData]]":g
            });
        },
        clickMapObject:function(a, b) {
            this.hideBalloon();
            if (this.chartCreated && !this.preventHover && !this.mapWasDragged && this.checkIfClickable(a) && !this.mapWasPinched) {
                this.selectObject(a);
                var c = {
                    type:"clickMapObject",
                    mapObject:a,
                    chart:this,
                    event:b
                };
                this.fire(c.type, c);
                this.objectWasClicked = !0;
            }
        },
        checkIfClickable:function(a) {
            var b = this.allowClickOnSelectedObject;
            return this.selectedObject == a && b ? !0 :this.selectedObject != a || b ? !0 === a.selectable || "MapArea" == a.objectType && a.autoZoomReal || a.url || a.linkToObject || 0 < a.images.length || 0 < a.lines.length || !isNaN(a.zoomLevel) || !isNaN(a.zoomX) || !isNaN(a.zoomY) || a.description ? !0 :!1 :!1;
        },
        resizeMap:function() {
            var a = this.mapSet;
            if (a) {
                var b = 1, c = a.getBBox(), d = this.realWidth, f = this.realHeight, e = c.width, g = c.height;
                this.fitMapToContainer && (b = e / d > g / f ? d / e :f / g);
                a.translate(-c.x * b, -c.y * b, b);
                this.mapScale = b;
                this.mapHeight = g * b;
                this.mapWidth = e * b;
            }
        },
        zoomIn:function() {
            this.skipClick = !0;
            var a = this.zoomLevel() * this.zoomControl.zoomFactor;
            this.zoomTo(a);
        },
        zoomOut:function() {
            this.skipClick = !0;
            var a = this.zoomLevel() / this.zoomControl.zoomFactor;
            this.zoomTo(a);
        },
        moveLeft:function() {
            this.skipClick = !0;
            var a = this.zoomX() + this.zoomControl.panStepSize;
            this.zoomTo(this.zoomLevel(), a, this.zoomY());
        },
        moveRight:function() {
            this.skipClick = !0;
            var a = this.zoomX() - this.zoomControl.panStepSize;
            this.zoomTo(this.zoomLevel(), a, this.zoomY());
        },
        moveUp:function() {
            this.skipClick = !0;
            var a = this.zoomY() + this.zoomControl.panStepSize;
            this.zoomTo(this.zoomLevel(), this.zoomX(), a);
        },
        moveDown:function() {
            this.skipClick = !0;
            var a = this.zoomY() - this.zoomControl.panStepSize;
            this.zoomTo(this.zoomLevel(), this.zoomX(), a);
        },
        zoomX:function() {
            return this.mapSet ? Math.round(1e4 * this.mapContainer.x / this.mapWidth) / 1e4 :NaN;
        },
        zoomY:function() {
            return this.mapSet ? Math.round(1e4 * this.mapContainer.y / this.mapHeight) / 1e4 :NaN;
        },
        goHome:function() {
            this.selectObject(this.dataProvider);
            var a = {
                type:"homeButtonClicked",
                chart:this
            };
            this.fire(a.type, a);
        },
        zoomLevel:function() {
            return Math.round(1e5 * this.mapContainer.scale) / 1e5;
        },
        showDescriptionAndGetUrl:function() {
            var a = this.selectedObject;
            if (a) {
                this.showDescription();
                var b = a.url;
                if (b) d.getURL(b, a.urlTarget); else if (b = a.linkToObject) {
                    if ("string" == typeof b) {
                        var c = this.getObjectById(b);
                        if (c) {
                            this.selectObject(c);
                            return;
                        }
                    }
                    b && a.passZoomValuesToTarget && (b.zoomLatitude = this.zoomLatitude(), b.zoomLongitude = this.zoomLongitude(),
                    b.zoomLevel = this.zoomLevel());
                    this.extendMapData(b) || this.selectObject(b);
                }
            }
        },
        extendMapData:function(a) {
            var b = a.objectType;
            if ("MapImage" != b && "MapArea" != b && "MapLine" != b) return d.extend(a, new d.MapData(), !0),
            this.dataProvider = a, this.zoomInstantly = !0, this.validateData(), !0;
        },
        showDescription:function(a) {
            a || (a = this.selectedObject);
            this.allowMultipleDescriptionWindows || this.closeAllDescriptions();
            if (a.description) {
                var b = a.descriptionWindow;
                b && b.close();
                b = new d.DescriptionWindow();
                a.descriptionWindow = b;
                var c = a.descriptionWindowWidth, h = a.descriptionWindowHeight, f = a.descriptionWindowLeft, e = a.descriptionWindowTop, g = a.descriptionWindowRight, k = a.descriptionWindowBottom;
                isNaN(g) || (f = this.realWidth - g);
                isNaN(k) || (e = this.realHeight - k);
                var l = a.descriptionWindowX;
                isNaN(l) || (f = l);
                l = a.descriptionWindowY;
                isNaN(l) || (e = l);
                isNaN(f) && (f = this.mouseX, f = f > this.realWidth / 2 ? f - c - 20 :f + 20);
                isNaN(e) && (e = this.mouseY);
                b.maxHeight = h;
                l = a.title;
                a.titleTr && (l = a.titleTr);
                b.show(this, this.descriptionsDiv, a.description, l);
                a = b.div.style;
                a.position = "absolute";
                a.width = c + "px";
                a.maxHeight = h + "px";
                isNaN(k) || (e -= b.div.offsetHeight);
                isNaN(g) || (f -= b.div.offsetWidth);
                a.left = f + "px";
                a.top = e + "px";
            }
        },
        parseXMLObject:function(a) {
            var b = {
                root:{}
            };
            this.parseXMLNode(b, "root", a);
            this.svgData = b.root.svg;
            this.getBounds();
        },
        getBounds:function() {
            var a = this.dataProvider;
            try {
                var b = this.svgData.defs["amcharts:ammap"];
                a.leftLongitude = Number(b.leftLongitude);
                a.rightLongitude = Number(b.rightLongitude);
                a.topLatitude = Number(b.topLatitude);
                a.bottomLatitude = Number(b.bottomLatitude);
                a.projection = b.projection;
                var c = b.wrappedLongitudes;
                c && (a.rightLongitude += 360);
                a.wrappedLongitudes = c;
            } catch (d) {}
        },
        recalcLongitude:function(a) {
            var b = this.dataProvider.leftLongitude, c = this.dataProvider.wrappedLongitudes;
            return isNaN(a) && c ? a < b ? Number(a) + 360 :a :a;
        },
        latitudeToCoordinate:function(a) {
            var b, c = this.dataProvider;
            if (this.mapSet) {
                b = c.topLatitude;
                var d = c.bottomLatitude;
                "mercator" == c.projection && (a = this.mercatorLatitudeToCoordinate(a), b = this.mercatorLatitudeToCoordinate(b),
                d = this.mercatorLatitudeToCoordinate(d));
                b = (a - b) / (d - b) * this.mapHeight;
            }
            return b;
        },
        longitudeToCoordinate:function(a) {
            a = this.recalcLongitude(a);
            var b, c = this.dataProvider;
            this.mapSet && (b = c.leftLongitude, b = (a - b) / (c.rightLongitude - b) * this.mapWidth);
            return b;
        },
        mercatorLatitudeToCoordinate:function(a) {
            89.5 < a && (a = 89.5);
            -89.5 > a && (a = -89.5);
            a = d.degreesToRadians(a);
            a = .5 * Math.log((1 + Math.sin(a)) / (1 - Math.sin(a)));
            return d.radiansToDegrees(a / 2);
        },
        zoomLatitude:function() {
            if (this.mapContainer) return this.coordinateToLatitude((-this.mapContainer.y + this.previousHeight / 2) / this.zoomLevel());
        },
        zoomLongitude:function() {
            if (this.mapContainer) return this.coordinateToLongitude((-this.mapContainer.x + this.previousWidth / 2) / this.zoomLevel());
        },
        getAreaCenterLatitude:function(a) {
            a = a.displayObject.getBBox();
            var b = this.mapScale;
            a = -this.mapSet.getBBox().y * b + (a.y + a.height / 2) * b;
            return this.coordinateToLatitude(a);
        },
        getAreaCenterLongitude:function(a) {
            a = a.displayObject.getBBox();
            var b = this.mapScale;
            a = -this.mapSet.getBBox().x * b + (a.x + a.width / 2) * b;
            return this.coordinateToLongitude(a);
        },
        coordinateToLatitude:function(a) {
            var b;
            if (this.mapSet) {
                var c = this.dataProvider, h = c.bottomLatitude, f = c.topLatitude;
                b = this.mapHeight;
                "mercator" == c.projection ? (c = this.mercatorLatitudeToCoordinate(h), f = this.mercatorLatitudeToCoordinate(f),
                a = 2 * Math.atan(Math.exp(2 * (a * (c - f) / b + f) * Math.PI / 180)) - .5 * Math.PI,
                b = d.radiansToDegrees(a)) :b = a / b * (h - f) + f;
            }
            return Math.round(1e6 * b) / 1e6;
        },
        coordinateToLongitude:function(a) {
            var b, c = this.dataProvider;
            this.mapSet && (b = a / this.mapWidth * (c.rightLongitude - c.leftLongitude) + c.leftLongitude);
            return Math.round(1e6 * b) / 1e6;
        },
        milesToPixels:function(a) {
            var b = this.dataProvider;
            return this.mapWidth / (b.rightLongitude - b.leftLongitude) * a / 69.172;
        },
        kilometersToPixels:function(a) {
            var b = this.dataProvider;
            return this.mapWidth / (b.rightLongitude - b.leftLongitude) * a / 111.325;
        },
        handleBackgroundClick:function() {
            if (this.backgroundZoomsToTop && !this.mapWasDragged) {
                var a = this.dataProvider;
                if (this.checkIfClickable(a)) this.clickMapObject(a); else {
                    var b = a.zoomX, c = a.zoomY, d = a.zoomLongitude, f = a.zoomLatitude, a = a.zoomLevel;
                    isNaN(b) || isNaN(c) || this.zoomTo(a, b, c);
                    isNaN(d) || isNaN(f) || this.zoomToLongLat(a, d, f, !0);
                }
            }
        },
        parseXMLNode:function(a, b, c, d) {
            void 0 === d && (d = "");
            var f, e, g;
            if (c) {
                var k = c.childNodes.length;
                for (f = 0; f < k; f++) {
                    e = c.childNodes[f];
                    var l = e.nodeName, m = e.nodeValue ? this.trim(e.nodeValue) :"", n = !1;
                    e.attributes && 0 < e.attributes.length && (n = !0);
                    if (0 !== e.childNodes.length || "" !== m || !1 !== n) if (3 == e.nodeType || 4 == e.nodeType) {
                        if ("" !== m) {
                            e = 0;
                            for (g in a[b]) a[b].hasOwnProperty(g) && e++;
                            e ? a[b]["#text"] = m :a[b] = m;
                        }
                    } else if (1 == e.nodeType) {
                        var p;
                        void 0 !== a[b][l] ? void 0 === a[b][l].length ? (p = a[b][l], a[b][l] = [], a[b][l].push(p),
                        a[b][l].push({}), p = a[b][l][1]) :"object" == typeof a[b][l] && (a[b][l].push({}),
                        p = a[b][l][a[b][l].length - 1]) :(a[b][l] = {}, p = a[b][l]);
                        if (e.attributes && e.attributes.length) for (m = 0; m < e.attributes.length; m++) p[e.attributes[m].name] = e.attributes[m].value;
                        void 0 !== a[b][l].length ? this.parseXMLNode(a[b][l], a[b][l].length - 1, e, d + "  ") :this.parseXMLNode(a[b], l, e, d + "  ");
                    }
                }
                e = 0;
                c = "";
                for (g in a[b]) "#text" == g ? c = a[b][g] :e++;
                0 === e && void 0 === a[b].length && (a[b] = c);
            }
        },
        doDoubleClickZoom:function() {
            if (!this.mapWasDragged) {
                var a = this.zoomLevel() * this.zoomControl.zoomFactor;
                this.zoomToStageXY(a, this.mouseX, this.mouseY);
            }
        },
        getDevInfo:function() {
            var a = this.zoomLevel(), a = {
                chart:this,
                type:"writeDevInfo",
                zoomLevel:a,
                zoomX:this.zoomX(),
                zoomY:this.zoomY(),
                zoomLatitude:this.zoomLatitude(),
                zoomLongitude:this.zoomLongitude(),
                latitude:this.coordinateToLatitude((this.mouseY - this.mapContainer.y) / a),
                longitude:this.coordinateToLongitude((this.mouseX - this.mapContainer.x) / a),
                left:this.mouseX,
                top:this.mouseY,
                right:this.realWidth - this.mouseX,
                bottom:this.realHeight - this.mouseY,
                percentLeft:Math.round(this.mouseX / this.realWidth * 100) + "%",
                percentTop:Math.round(this.mouseY / this.realHeight * 100) + "%",
                percentRight:Math.round((this.realWidth - this.mouseX) / this.realWidth * 100) + "%",
                percentBottom:Math.round((this.realHeight - this.mouseY) / this.realHeight * 100) + "%"
            }, b = "zoomLevel:" + a.zoomLevel + ", zoomLongitude:" + a.zoomLongitude + ", zoomLatitude:" + a.zoomLatitude + "\n", b = b + ("zoomX:" + a.zoomX + ", zoomY:" + a.zoomY + "\n"), b = b + ("latitude:" + a.latitude + ", longitude:" + a.longitude + "\n"), b = b + ("left:" + a.left + ", top:" + a.top + "\n"), b = b + ("right:" + a.right + ", bottom:" + a.bottom + "\n"), b = b + ("left:" + a.percentLeft + ", top:" + a.percentTop + "\n"), b = b + ("right:" + a.percentRight + ", bottom:" + a.percentBottom + "\n");
            a.str = b;
            this.fire(a.type, a);
            return a;
        },
        getXY:function(a, b, c) {
            void 0 !== a && (-1 != String(a).indexOf("%") ? (a = Number(a.split("%").join("")),
            c && (a = 100 - a), a = Number(a) * b / 100) :c && (a = b - a));
            return a;
        },
        getObjectById:function(a) {
            var b = this.dataProvider;
            if (b.areas) {
                var c = this.getObject(a, b.areas);
                if (c) return c;
            }
            if (c = this.getObject(a, b.images)) return c;
            if (a = this.getObject(a, b.lines)) return a;
        },
        getObject:function(a, b) {
            if (b) {
                var c;
                for (c = 0; c < b.length; c++) {
                    var d = b[c];
                    if (d.id == a) return d;
                    if (d.areas) {
                        var f = this.getObject(a, d.areas);
                        if (f) return f;
                    }
                    if (f = this.getObject(a, d.images)) return f;
                    if (d = this.getObject(a, d.lines)) return d;
                }
            }
        },
        parseData:function() {
            var a = this.dataProvider;
            this.processObject(a.areas, a, "area");
            this.processObject(a.images, a, "image");
            this.processObject(a.lines, a, "line");
        },
        processObject:function(a, b, c) {
            if (a) {
                var h;
                for (h = 0; h < a.length; h++) {
                    var f = a[h];
                    f.parentObject = b;
                    "area" == c && d.extend(f, new d.MapArea(this.theme), !0);
                    "image" == c && (f = d.extend(f, new d.MapImage(this.theme), !0));
                    "line" == c && (f = d.extend(f, new d.MapLine(this.theme), !0));
                    a[h] = f;
                    f.areas && this.processObject(f.areas, f, "area");
                    f.images && this.processObject(f.images, f, "image");
                    f.lines && this.processObject(f.lines, f, "line");
                }
            }
        },
        positionChanged:function() {
            var a = {
                type:"positionChanged",
                zoomX:this.zoomX(),
                zoomY:this.zoomY(),
                zoomLevel:this.zoomLevel(),
                chart:this
            };
            this.fire(a.type, a);
        },
        getX:function(a, b) {
            return this.getXY(a, this.realWidth, b);
        },
        getY:function(a, b) {
            return this.getXY(a, this.realHeight, b);
        },
        trim:function(a) {
            if (a) {
                var b;
                for (b = 0; b < a.length; b++) if (-1 === " \n\r	\f?????????????\u2028\u2029��".indexOf(a.charAt(b))) {
                    a = a.substring(b);
                    break;
                }
                for (b = a.length - 1; 0 <= b; b--) if (-1 === " \n\r	\f?????????????\u2028\u2029��".indexOf(a.charAt(b))) {
                    a = a.substring(0, b + 1);
                    break;
                }
                return -1 === " \n\r	\f?????????????\u2028\u2029��".indexOf(a.charAt(0)) ? a :"";
            }
        },
        destroy:function() {
            d.AmMap.base.destroy.call(this);
        }
    });
})();

(function() {
    var d = window.AmCharts;
    d.ZoomControl = d.Class({
        construct:function(a) {
            this.cname = "ZoomControl";
            this.panStepSize = .1;
            this.zoomFactor = 2;
            this.maxZoomLevel = 64;
            this.minZoomLevel = 1;
            this.zoomControlEnabled = this.panControlEnabled = !0;
            this.buttonRollOverColor = "#CC0000";
            this.buttonFillColor = "#990000";
            this.buttonFillAlpha = 1;
            this.buttonBorderColor = "#FFFFFF";
            this.buttonIconAlpha = this.buttonBorderThickness = this.buttonBorderAlpha = 1;
            this.gridColor = "#FFFFFF";
            this.homeIconFile = "homeIcon.gif";
            this.gridBackgroundColor = "#000000";
            this.gridBackgroundAlpha = .15;
            this.gridAlpha = 1;
            this.buttonSize = 18;
            this.iconSize = 11;
            this.buttonCornerRadius = 0;
            this.gridHeight = 150;
            this.top = this.left = 10;
            d.applyTheme(this, a, this.cname);
        },
        init:function(a, b) {
            var c = this;
            c.chart = a;
            d.remove(c.set);
            var h = b.set();
            d.setCN(a, h, "zoom-control");
            var f = c.buttonSize, e = c.zoomControlEnabled, g = c.panControlEnabled, k = c.buttonFillColor, l = c.buttonFillAlpha, m = c.buttonBorderThickness, n = c.buttonBorderColor, p = c.buttonBorderAlpha, u = c.buttonCornerRadius, r = c.buttonRollOverColor, A = c.gridHeight, B = c.zoomFactor, w = c.minZoomLevel, D = c.maxZoomLevel, x = c.buttonIconAlpha, y = a.getX(c.left), q = a.getY(c.top);
            isNaN(c.right) || (y = a.getX(c.right, !0), y = g ? y - 3 * f :y - f);
            isNaN(c.bottom) || (q = a.getY(c.bottom, !0), e && (q -= A + 3 * f), q = g ? q - 3 * f :q + f);
            h.translate(y, q);
            c.previousDY = NaN;
            var v;
            if (e) {
                v = b.set();
                d.setCN(a, v, "zoom-control-zoom");
                h.push(v);
                c.set = h;
                c.zoomSet = v;
                q = d.rect(b, f + 6, A + 2 * f + 6, c.gridBackgroundColor, c.gridBackgroundAlpha, 0, 0, 0, 4);
                d.setCN(a, q, "zoom-bg");
                q.translate(-3, -3);
                q.mouseup(function() {
                    c.handleBgUp();
                }).touchend(function() {
                    c.handleBgUp();
                });
                v.push(q);
                q = new d.SimpleButton();
                q.setIcon(a.pathToImages + "plus.gif", c.iconSize);
                q.setClickHandler(a.zoomIn, a);
                q.init(b, f, f, k, l, m, n, p, u, r, x);
                d.setCN(a, q.set, "zoom-in");
                v.push(q.set);
                q = new d.SimpleButton();
                q.setIcon(a.pathToImages + "minus.gif", c.iconSize);
                q.setClickHandler(a.zoomOut, a);
                q.init(b, f, f, k, l, m, n, p, u, r, x);
                q.set.translate(0, A + f);
                d.setCN(a, q.set, "zoom-out");
                v.push(q.set);
                var y = Math.log(D / w) / Math.log(B) + 1, e = A / y, t;
                for (t = 1; t < y; t++) q = f + t * e, q = d.line(b, [ 1, f - 2 ], [ q, q ], c.gridColor, c.gridAlpha, 1),
                d.setCN(a, q, "zoom-grid"), v.push(q);
                q = new d.SimpleButton();
                q.setDownHandler(c.draggerDown, c);
                q.setClickHandler(c.draggerUp, c);
                q.init(b, f, e, k, l, m, n, p, u, r);
                d.setCN(a, q.set, "zoom-dragger");
                v.push(q.set);
                c.dragger = q.set;
                c.previousY = NaN;
                A -= e;
                w = Math.log(w / 100) / Math.log(B);
                B = Math.log(D / 100) / Math.log(B);
                c.realStepSize = A / (B - w);
                c.realGridHeight = A;
                c.stepMax = B;
            }
            g && (g = b.set(), d.setCN(a, g, "zoom-control-pan"), h.push(g), v && v.translate(f, 4 * f),
            v = new d.SimpleButton(), v.setIcon(a.pathToImages + "panLeft.gif", c.iconSize),
            v.setClickHandler(a.moveLeft, a), v.init(b, f, f, k, l, m, n, p, u, r, x), v.set.translate(0, f),
            d.setCN(a, v.set, "pan-left"), g.push(v.set), v = new d.SimpleButton(), v.setIcon(a.pathToImages + "panRight.gif", c.iconSize),
            v.setClickHandler(a.moveRight, a), v.init(b, f, f, k, l, m, n, p, u, r, x), v.set.translate(2 * f, f),
            d.setCN(a, v.set, "pan-right"), g.push(v.set), v = new d.SimpleButton(), v.setIcon(a.pathToImages + "panUp.gif", c.iconSize),
            v.setClickHandler(a.moveUp, a), v.init(b, f, f, k, l, m, n, p, u, r, x), v.set.translate(f, 0),
            d.setCN(a, v.set, "pan-up"), g.push(v.set), v = new d.SimpleButton(), v.setIcon(a.pathToImages + "panDown.gif", c.iconSize),
            v.setClickHandler(a.moveDown, a), v.init(b, f, f, k, l, m, n, p, u, r, x), v.set.translate(f, 2 * f),
            d.setCN(a, v.set, "pan-down"), g.push(v.set), l = new d.SimpleButton(), l.setIcon(a.pathToImages + c.homeIconFile, c.iconSize),
            l.setClickHandler(a.goHome, a), l.init(b, f, f, k, 0, 0, n, 0, u, r, x), l.set.translate(f, f),
            d.setCN(a, l.set, "pan-home"), g.push(l.set), h.push(g));
        },
        draggerDown:function() {
            this.chart.stopDrag();
            this.isDragging = !0;
        },
        draggerUp:function() {
            this.isDragging = !1;
        },
        handleBgUp:function() {
            var a = this.chart, b = 100 * Math.pow(this.zoomFactor, this.stepMax - (a.mouseY - this.zoomSet.y - this.set.y - this.buttonSize - this.realStepSize / 2) / this.realStepSize);
            a.zoomTo(b);
        },
        update:function() {
            var a, b = this.zoomFactor, c = this.realStepSize, h = this.stepMax, f = this.dragger, e = this.buttonSize, g = this.chart;
            this.isDragging ? (g.stopDrag(), a = f.y + (g.mouseY - this.previousY), a = d.fitToBounds(a, e, this.realGridHeight + e),
            c = 100 * Math.pow(b, h - (a - e) / c), g.zoomTo(c, NaN, NaN, !0)) : (a = Math.log(g.zoomLevel() / 100) / Math.log(b), a = (h - a) * c + e);
            this.previousY = g.mouseY;
            this.previousDY != a && f && (f.translate(0, a), this.previousDY = a);
        }
    });
})();

(function() {
    var d = window.AmCharts;
    d.SimpleButton = d.Class({
        construct:function() {},
        init:function(a, b, c, h, f, e, g, k, l, m, n) {
            var p = this;
            p.rollOverColor = m;
            p.color = h;
            m = a.set();
            p.set = m;
            h = d.rect(a, b, c, h, f, e, g, k, l);
            m.push(h);
            if (f = p.iconPath) e = p.iconSize, a = a.image(f, (b - e) / 2, (c - e) / 2, e, e),
            m.push(a), a.setAttr("opacity", n), a.mousedown(function() {
                p.handleDown();
            }).mouseup(function() {
                p.handleUp();
            }).mouseover(function() {
                p.handleOver();
            }).mouseout(function() {
                p.handleOut();
            });
            h.mousedown(function() {
                p.handleDown();
            }).touchstart(function() {
                p.handleDown();
            }).mouseup(function() {
                p.handleUp();
            }).touchend(function() {
                p.handleUp();
            }).mouseover(function() {
                p.handleOver();
            }).mouseout(function() {
                p.handleOut();
            });
            p.bg = h;
        },
        setIcon:function(a, b) {
            this.iconPath = a;
            this.iconSize = b;
        },
        setClickHandler:function(a, b) {
            this.clickHandler = a;
            this.scope = b;
        },
        setDownHandler:function(a, b) {
            this.downHandler = a;
            this.scope = b;
        },
        handleUp:function() {
            var a = this.clickHandler;
            a && a.call(this.scope);
        },
        handleDown:function() {
            var a = this.downHandler;
            a && a.call(this.scope);
        },
        handleOver:function() {
            this.bg.setAttr("fill", this.rollOverColor);
        },
        handleOut:function() {
            this.bg.setAttr("fill", this.color);
        }
    });
})();

(function() {
    var d = window.AmCharts;
    d.SmallMap = d.Class({
        construct:function(a) {
            this.cname = "SmallMap";
            this.mapColor = "#e6e6e6";
            this.rectangleColor = "#FFFFFF";
            this.top = this.right = 10;
            this.minimizeButtonWidth = 16;
            this.backgroundColor = "#9A9A9A";
            this.backgroundAlpha = 1;
            this.borderColor = "#FFFFFF";
            this.borderThickness = 3;
            this.borderAlpha = 1;
            this.size = .2;
            this.enabled = !0;
            d.applyTheme(this, a, this.cname);
        },
        init:function(a, b) {
            var c = this;
            if (c.enabled) {
                c.chart = a;
                c.container = b;
                c.width = a.realWidth * c.size;
                c.height = a.realHeight * c.size;
                d.remove(c.set);
                var h = b.set();
                c.set = h;
                d.setCN(a, h, "small-map");
                var f = b.set();
                c.allSet = f;
                h.push(f);
                c.buildSVGMap();
                var e = c.borderThickness, g = c.borderColor, k = d.rect(b, c.width + e, c.height + e, c.backgroundColor, c.backgroundAlpha, e, g, c.borderAlpha);
                d.setCN(a, k, "small-map-bg");
                k.translate(-e / 2, -e / 2);
                f.push(k);
                k.toBack();
                var l, m, k = c.minimizeButtonWidth, n = new d.SimpleButton();
                n.setIcon(a.pathToImages + "arrowDown.gif", k);
                n.setClickHandler(c.minimize, c);
                n.init(b, k, k, g, 1, 1, g, 1);
                d.setCN(a, n.set, "small-map-down");
                n = n.set;
                c.downButtonSet = n;
                h.push(n);
                var p = new d.SimpleButton();
                p.setIcon(a.pathToImages + "arrowUp.gif", k);
                p.setClickHandler(c.maximize, c);
                p.init(b, k, k, g, 1, 1, g, 1);
                d.setCN(a, p.set, "small-map-up");
                g = p.set;
                c.upButtonSet = g;
                g.hide();
                h.push(g);
                var u, r;
                isNaN(c.top) || (l = a.getY(c.top) + e, r = 0);
                isNaN(c.bottom) || (l = a.getY(c.bottom, !0) - c.height - e, r = c.height - k + e / 2);
                isNaN(c.left) || (m = a.getX(c.left) + e, u = -e / 2);
                isNaN(c.right) || (m = a.getX(c.right, !0) - c.width - e, u = c.width - k + e / 2);
                e = b.set();
                e.clipRect(1, 1, c.width, c.height);
                f.push(e);
                c.rectangleC = e;
                h.translate(m, l);
                n.translate(u, r);
                g.translate(u, r);
                f.mouseup(function() {
                    c.handleMouseUp();
                });
                c.drawRectangle();
            } else d.remove(c.allSet), d.remove(c.downButtonSet), d.remove(c.upButtonSet);
        },
        minimize:function() {
            this.downButtonSet.hide();
            this.upButtonSet.show();
            this.allSet.hide();
        },
        maximize:function() {
            this.downButtonSet.show();
            this.upButtonSet.hide();
            this.allSet.show();
        },
        buildSVGMap:function() {
            var a = this.chart, b = {
                fill:this.mapColor,
                stroke:this.mapColor,
                "stroke-opacity":1
            }, c = a.svgData.g.path, h = this.container, f = h.set();
            d.setCN(a, f, "small-map-image");
            var e;
            for (e = 0; e < c.length; e++) {
                var g = h.path(c[e].d).attr(b);
                f.push(g);
            }
            this.allSet.push(f);
            b = f.getBBox();
            c = this.size * a.mapScale;
            h = -b.x * c;
            e = -b.y * c;
            var k = g = 0;
            a.centerMap && (g = (this.width - b.width * c) / 2, k = (this.height - b.height * c) / 2);
            this.mapWidth = b.width * c;
            this.mapHeight = b.height * c;
            this.dx = g;
            this.dy = k;
            f.translate(h + g, e + k, c);
        },
        update:function() {
            var a = this.chart, b = a.zoomLevel(), c = this.width, d = a.mapContainer, a = c / (a.realWidth * b), c = c / b, b = this.height / b, f = this.rectangle;
            f.translate(-d.x * a + this.dx, -d.y * a + this.dy);
            0 < c && 0 < b && (f.setAttr("width", Math.ceil(c + 1)), f.setAttr("height", Math.ceil(b + 1)));
            this.rWidth = c;
            this.rHeight = b;
        },
        drawRectangle:function() {
            var a = this.rectangle;
            d.remove(a);
            a = d.rect(this.container, 10, 10, "#000", 0, 1, this.rectangleColor, 1);
            d.setCN(this.chart, a, "small-map-rectangle");
            this.rectangleC.push(a);
            this.rectangle = a;
        },
        handleMouseUp:function() {
            var a = this.chart, b = a.zoomLevel();
            a.zoomTo(b, -((a.mouseX - this.set.x - this.dx - this.rWidth / 2) / this.mapWidth) * b, -((a.mouseY - this.set.y - this.dy - this.rHeight / 2) / this.mapHeight) * b);
        }
    });
})();

(function() {
    var d = window.AmCharts;
    d.AreasProcessor = d.Class({
        construct:function(a) {
            this.chart = a;
        },
        process:function(a) {
            this.updateAllAreas();
            this.allObjects = [];
            a = a.areas;
            var b = this.chart, c, h = a.length, f, e, g = 0, k = b.svgAreasById, l = !1, m = !1, n = 0;
            for (f = 0; f < h; f++) {
                e = a[f];
                e = e.value;
                if (!1 === l || l < e) l = e;
                if (!1 === m || m > e) m = e;
                isNaN(e) || (g += Math.abs(e), n++);
            }
            isNaN(b.minValue) || (m = b.minValue);
            isNaN(b.maxValue) || (l = b.maxValue);
            b.maxValueReal = l;
            b.minValueReal = m;
            for (f = 0; f < h; f++) e = a[f], isNaN(e.value) ? e.percents = void 0 :(e.percents = (e.value - m) / g * 100,
            m == l && (e.percents = 100));
            for (f = 0; f < h; f++) {
                e = a[f];
                var p = k[e.id];
                c = b.areasSettings;
                p && p.className && (g = b.areasClasses[p.className]) && (c = g, c = d.processObject(c, d.AreasSettings, b.theme));
                var u = c.color, r = c.alpha, A = c.outlineThickness, B = c.rollOverColor, w = c.selectedColor, D = c.rollOverAlpha, x = c.outlineColor, y = c.outlineAlpha, q = c.balloonText, v = c.selectable, t = c.pattern, z = c.rollOverOutlineColor, C = c.bringForwardOnHover, G = c.preserveOriginalAttributes;
                this.allObjects.push(e);
                e.chart = b;
                e.baseSettings = c;
                e.autoZoomReal = void 0 == e.autoZoom ? c.autoZoom :e.autoZoom;
                g = e.color;
                void 0 == g && (g = u);
                n = e.alpha;
                isNaN(n) && (n = r);
                r = e.rollOverAlpha;
                isNaN(r) && (r = D);
                isNaN(r) && (r = n);
                D = e.rollOverColor;
                void 0 == D && (D = B);
                B = e.pattern;
                void 0 == B && (B = t);
                t = e.selectedColor;
                void 0 == t && (t = w);
                (w = e.balloonText) || (w = q);
                void 0 == c.colorSolid || isNaN(e.value) || (q = Math.floor((e.value - m) / ((l - m) / b.colorSteps)),
                q == b.colorSteps && q--, q *= 1 / (b.colorSteps - 1), l == m && (q = 1), e.colorReal = d.getColorFade(g, c.colorSolid, q));
                void 0 != e.color && (e.colorReal = e.color);
                void 0 == e.selectable && (e.selectable = v);
                void 0 == e.colorReal && (e.colorReal = u);
                u = e.outlineColor;
                void 0 == u && (u = x);
                x = e.outlineAlpha;
                isNaN(x) && (x = y);
                y = e.outlineThickness;
                isNaN(y) && (y = A);
                A = e.rollOverOutlineColor;
                void 0 == A && (A = z);
                void 0 == e.bringForwardOnHover && (e.bringForwardOnHover = C);
                void 0 == e.preserveOriginalAttributes && (e.preserveOriginalAttributes = G);
                e.alphaReal = n;
                e.rollOverColorReal = D;
                e.rollOverAlphaReal = r;
                e.balloonTextReal = w;
                e.selectedColorReal = t;
                e.outlineColorReal = u;
                e.outlineAlphaReal = x;
                e.rollOverOutlineColorReal = A;
                e.outlineThicknessReal = y;
                e.patternReal = B;
                d.processDescriptionWindow(c, e);
                if (p && (c = p.area, z = p.title, e.enTitle = p.title, z && !e.title && (e.title = z),
                (p = b.language) ? (z = d.mapTranslations) && (p = z[p]) && p[e.enTitle] && (e.titleTr = p[e.enTitle]) :e.titleTr = void 0,
                c)) {
                    e.displayObject = c;
                    e.mouseEnabled && b.addObjectEventListeners(c, e);
                    var E;
                    void 0 != g && (E = g);
                    void 0 != e.colorReal && (E = e.showAsSelected || b.selectedObject == e ? e.selectedColorReal :e.colorReal);
                    c.node.setAttribute("class", "");
                    d.setCN(b, c, "map-area");
                    d.setCN(b, c, "map-area-" + c.id);
                    e.preserveOriginalAttributes || (c.setAttr("fill", E), c.setAttr("stroke", u), c.setAttr("stroke-opacity", x),
                    c.setAttr("stroke-width", y), c.setAttr("fill-opacity", n));
                    B && c.pattern(B, b.mapScale);
                    e.hidden && c.hide();
                }
            }
        },
        updateAllAreas:function() {
            var a = this.chart, b = a.areasSettings, c = b.unlistedAreasColor, h = b.unlistedAreasAlpha, f = b.unlistedAreasOutlineColor, e = b.unlistedAreasOutlineAlpha, g = a.svgAreas, k = a.dataProvider, l = k.areas, m = {}, n;
            for (n = 0; n < l.length; n++) m[l[n].id] = l[n];
            for (n = 0; n < g.length; n++) {
                l = g[n];
                if (b.preserveOriginalAttributes) {
                    if (l.customAttr) for (var p in l.customAttr) l.setAttr(p, l.customAttr[p]);
                } else void 0 != c && l.setAttr("fill", c), isNaN(h) || l.setAttr("fill-opacity", h),
                void 0 != f && l.setAttr("stroke", f), isNaN(e) || l.setAttr("stroke-opacity", e),
                l.setAttr("stroke-width", b.outlineThickness);
                d.setCN(a, l, "map-area-unlisted");
                if (k.getAreasFromMap && !m[l.id]) {
                    var u = new d.MapArea(a.theme);
                    u.parentObject = k;
                    u.id = l.id;
                    k.areas.push(u);
                }
            }
        }
    });
})();

(function() {
    var d = window.AmCharts;
    d.AreasSettings = d.Class({
        construct:function(a) {
            this.cname = "AreasSettings";
            this.alpha = 1;
            this.autoZoom = !1;
            this.balloonText = "[[title]]";
            this.color = "#FFCC00";
            this.colorSolid = "#990000";
            this.unlistedAreasAlpha = 1;
            this.unlistedAreasColor = "#DDDDDD";
            this.outlineColor = "#FFFFFF";
            this.outlineAlpha = 1;
            this.outlineThickness = .5;
            this.selectedColor = this.rollOverOutlineColor = "#CC0000";
            this.unlistedAreasOutlineColor = "#FFFFFF";
            this.unlistedAreasOutlineAlpha = 1;
            this.descriptionWindowWidth = 250;
            this.adjustOutlineThickness = !1;
            this.bringForwardOnHover = !0;
            d.applyTheme(this, a, this.cname);
        }
    });
})();

(function() {
    var d = window.AmCharts;
    d.ImagesProcessor = d.Class({
        construct:function(a) {
            this.chart = a;
            this.reset();
        },
        process:function(a) {
            var b = a.images, c;
            for (c = 0; c < b.length; c++) this.createImage(b[c], c);
            a.parentObject && a.remainVisible && this.process(a.parentObject);
        },
        createImage:function(a, b) {
            var c = this.chart, h = c.container, f = c.mapImagesContainer, e = c.stageImagesContainer, g = c.imagesSettings;
            a.remove && a.remove();
            var k = g.color, l = g.alpha, m = g.rollOverColor, n = g.selectedColor, p = g.balloonText, u = g.outlineColor, r = g.outlineAlpha, A = g.outlineThickness, B = g.selectedScale, w = g.labelPosition, D = g.labelColor, x = g.labelFontSize, y = g.bringForwardOnHover, q = g.labelRollOverColor, v = g.selectedLabelColor;
            a.index = b;
            a.chart = c;
            a.baseSettings = c.imagesSettings;
            var t = h.set();
            a.displayObject = t;
            var z = a.color;
            void 0 == z && (z = k);
            k = a.alpha;
            isNaN(k) && (k = l);
            void 0 == a.bringForwardOnHover && (a.bringForwardOnHover = y);
            l = a.outlineAlpha;
            isNaN(l) && (l = r);
            r = a.rollOverColor;
            void 0 == r && (r = m);
            m = a.selectedColor;
            void 0 == m && (m = n);
            (n = a.balloonText) || (n = p);
            p = a.outlineColor;
            void 0 == p && (p = u);
            void 0 == p && (p = z);
            u = a.outlineThickness;
            isNaN(u) && (u = A);
            (A = a.labelPosition) || (A = w);
            w = a.labelColor;
            void 0 == w && (w = D);
            D = a.labelRollOverColor;
            void 0 == D && (D = q);
            q = a.selectedLabelColor;
            void 0 == q && (q = v);
            v = a.labelFontSize;
            isNaN(v) && (v = x);
            x = a.selectedScale;
            isNaN(x) && (x = B);
            isNaN(a.rollOverScale);
            a.colorReal = z;
            a.alphaReal = k;
            a.rollOverColorReal = r;
            a.balloonTextReal = n;
            a.selectedColorReal = m;
            a.labelColorReal = w;
            a.labelRollOverColorReal = D;
            a.selectedLabelColorReal = q;
            a.labelFontSizeReal = v;
            a.labelPositionReal = A;
            a.selectedScaleReal = x;
            a.rollOverScaleReal = x;
            d.processDescriptionWindow(g, a);
            a.centeredReal = void 0 == a.centered ? g.centered :a.centered;
            v = a.type;
            q = a.imageURL;
            D = a.svgPath;
            r = a.width;
            w = a.height;
            B = a.scale;
            isNaN(a.percentWidth) || (r = a.percentWidth / 100 * c.realWidth);
            isNaN(a.percentHeight) || (w = a.percentHeight / 100 * c.realHeight);
            var C;
            q || v || D || (v = "circle", r = 1, l = k = 0);
            m = x = 0;
            g = a.selectedColorReal;
            if (v) {
                isNaN(r) && (r = 10);
                isNaN(w) && (w = 10);
                "kilometers" == a.widthAndHeightUnits && (r = c.kilometersToPixels(a.width), w = c.kilometersToPixels(a.height));
                "miles" == a.widthAndHeightUnits && (r = c.milesToPixels(a.width), w = c.milesToPixels(a.height));
                if ("circle" == v || "bubble" == v) w = r;
                C = this.createPredefinedImage(z, p, u, v, r, w);
                m = x = 0;
                a.centeredReal ? (isNaN(a.right) || (x = r * B), isNaN(a.bottom) || (m = w * B)) :(x = r * B / 2,
                m = w * B / 2);
                C.translate(x, m, B);
            } else q ? (isNaN(r) && (r = 10), isNaN(w) && (w = 10), C = h.image(q, 0, 0, r, w),
            C.node.setAttribute("preserveAspectRatio", "none"), C.setAttr("opacity", k), a.centeredReal && (x = isNaN(a.right) ? -r / 2 :r / 2,
            m = isNaN(a.bottom) ? -w / 2 :w / 2, C.translate(x, m))) :D && (C = h.path(D), p = C.getBBox(),
            a.centeredReal ? (x = -p.x * B - p.width * B / 2, isNaN(a.right) || (x = -x), m = -p.y * B - p.height * B / 2,
            isNaN(a.bottom) || (m = -m)) :x = m = 0, C.translate(x, m, B), C.x = x, C.y = m);
            C && (t.push(C), a.image = C, C.setAttr("stroke-opacity", l), C.setAttr("fill-opacity", k),
            C.setAttr("fill", z), d.setCN(c, C, "map-image"), void 0 != a.id && d.setCN(c, C, "map-image-" + a.id));
            z = a.labelColorReal;
            !a.showAsSelected && c.selectedObject != a || void 0 == g || (C.setAttr("fill", g),
            z = a.selectedLabelColorReal);
            C = null;
            void 0 !== a.label && (C = d.text(h, a.label, z, c.fontFamily, a.labelFontSizeReal, a.labelAlign),
            d.setCN(c, C, "map-image-label"), void 0 !== a.id && d.setCN(c, C, "map-image-label-" + a.id),
            z = a.labelBackgroundAlpha, (k = a.labelBackgroundColor) && 0 < z && (l = C.getBBox(),
            h = d.rect(h, l.width + 16, l.height + 10, k, z), d.setCN(c, h, "map-image-label-background"),
            void 0 != a.id && d.setCN(c, h, "map-image-label-background-" + a.id), t.push(h),
            a.labelBG = h), a.imageLabel = C, t.push(C), d.setCN(c, t, "map-image-container"),
            void 0 != a.id && d.setCN(c, t, "map-image-container-" + a.id));
            isNaN(a.latitude) || isNaN(a.longitude) ? e.push(t) :f.push(t);
            t && (t.rotation = a.rotation);
            this.updateSizeAndPosition(a);
            a.mouseEnabled && c.addObjectEventListeners(t, a);
            a.hidden && t.hide();
        },
        updateSizeAndPosition:function(a) {
            var b = this.chart, c = a.displayObject, d = b.getX(a.left), f = b.getY(a.top), e = a.image.getBBox();
            isNaN(a.right) || (d = b.getX(a.right, !0) - e.width * a.scale);
            isNaN(a.bottom) || (f = b.getY(a.bottom, !0) - e.height * a.scale);
            var g = a.longitude, k = a.latitude, e = this.objectsToResize;
            this.allSvgObjects.push(c);
            this.allObjects.push(a);
            var l = a.imageLabel;
            if (!isNaN(d) && !isNaN(f)) c.translate(d, f); else if (!isNaN(k) && !isNaN(g) && (d = b.longitudeToCoordinate(g),
            f = b.latitudeToCoordinate(k), c.translate(d, f, NaN, !0), a.fixedSize)) {
                d = 1;
                if (a.showAsSelected || b.selectedObject == a) d = a.selectedScaleReal;
                e.push({
                    image:c,
                    scale:d
                });
            }
            this.positionLabel(l, a, a.labelPositionReal);
        },
        positionLabel:function(a, b, c) {
            if (a) {
                var d = b.image, f = 0, e = 0, g = 0, k = 0;
                d && (k = d.getBBox(), e = d.y, f = d.x, g = k.width, k = k.height, b.svgPath && (g *= b.scale,
                k *= b.scale));
                var d = a.getBBox(), l = d.width, m = d.height;
                "right" == c && (f += g + l / 2 + 5, e += k / 2 - 2);
                "left" == c && (f += -l / 2 - 5, e += k / 2 - 2);
                "top" == c && (e -= m / 2 + 3, f += g / 2);
                "bottom" == c && (e += k + m / 2, f += g / 2);
                "middle" == c && (f += g / 2, e += k / 2);
                a.translate(f + b.labelShiftX, e + b.labelShiftY);
                b.labelBG && b.labelBG.translate(f - d.width / 2 + b.labelShiftX - 9, e + b.labelShiftY - d.height / 2 - 3);
            }
        },
        createPredefinedImage:function(a, b, c, h, f, e) {
            var g = this.chart.container, k;
            switch (h) {
              case "circle":
                k = d.circle(g, f / 2, a, 1, c, b, 1);
                break;

              case "rectangle":
                k = d.polygon(g, [ -f / 2, f / 2, f / 2, -f / 2 ], [ e / 2, e / 2, -e / 2, -e / 2 ], a, 1, c, b, 1);
                break;

              case "bubble":
                k = d.circle(g, f / 2, a, 1, c, b, 1, !0);
            }
            return k;
        },
        reset:function() {
            this.objectsToResize = [];
            this.allSvgObjects = [];
            this.allObjects = [];
            this.allLabels = [];
        }
    });
})();

(function() {
    var d = window.AmCharts;
    d.ImagesSettings = d.Class({
        construct:function(a) {
            this.cname = "ImagesSettings";
            this.balloonText = "[[title]]";
            this.alpha = 1;
            this.borderAlpha = 0;
            this.borderThickness = 1;
            this.labelPosition = "right";
            this.labelColor = "#000000";
            this.labelFontSize = 11;
            this.color = "#000000";
            this.labelRollOverColor = "#00CC00";
            this.centered = !0;
            this.rollOverScale = this.selectedScale = 1;
            this.descriptionWindowWidth = 250;
            this.bringForwardOnHover = !0;
            d.applyTheme(this, a, this.cname);
        }
    });
})();

(function() {
    var d = window.AmCharts;
    d.LinesProcessor = d.Class({
        construct:function(a) {
            this.chart = a;
            this.reset();
        },
        process:function(a) {
            var b = a.lines, c = this.chart, h = c.linesSettings, f = this.objectsToResize, e = c.mapLinesContainer, g = c.stageLinesContainer, k = h.thickness, l = h.dashLength, m = h.arrow, n = h.arrowSize, p = h.arrowColor, u = h.arrowAlpha, r = h.color, A = h.alpha, B = h.rollOverColor, w = h.selectedColor, D = h.rollOverAlpha, x = h.balloonText, y = h.bringForwardOnHover, q = c.container, v;
            for (v = 0; v < b.length; v++) {
                var t = b[v];
                t.chart = c;
                t.baseSettings = h;
                var z = q.set();
                t.displayObject = z;
                this.allSvgObjects.push(z);
                this.allObjects.push(t);
                t.mouseEnabled && c.addObjectEventListeners(z, t);
                if (t.remainVisible || c.selectedObject == t.parentObject) {
                    var C = t.thickness;
                    isNaN(C) && (C = k);
                    var G = t.dashLength;
                    isNaN(G) && (G = l);
                    var E = t.color;
                    void 0 == E && (E = r);
                    var F = t.alpha;
                    isNaN(F) && (F = A);
                    var H = t.rollOverAlpha;
                    isNaN(H) && (H = D);
                    isNaN(H) && (H = F);
                    var J = t.rollOverColor;
                    void 0 == J && (J = B);
                    var U = t.selectedColor;
                    void 0 == U && (U = w);
                    var S = t.balloonText;
                    S || (S = x);
                    var M = t.arrow;
                    if (!M || "none" == M && "none" != m) M = m;
                    var O = t.arrowColor;
                    void 0 == O && (O = p);
                    void 0 == O && (O = E);
                    var P = t.arrowAlpha;
                    isNaN(P) && (P = u);
                    isNaN(P) && (P = F);
                    var N = t.arrowSize;
                    isNaN(N) && (N = n);
                    t.alphaReal = F;
                    t.colorReal = E;
                    t.rollOverColorReal = J;
                    t.rollOverAlphaReal = H;
                    t.balloonTextReal = S;
                    t.selectedColorReal = U;
                    t.thicknessReal = C;
                    void 0 == t.bringForwardOnHover && (t.bringForwardOnHover = y);
                    d.processDescriptionWindow(h, t);
                    var H = this.processCoordinates(t.x, c.realWidth), J = this.processCoordinates(t.y, c.realHeight), L = t.longitudes, S = t.latitudes, K = L.length, Q;
                    if (0 < K) for (H = [], Q = 0; Q < K; Q++) H.push(c.longitudeToCoordinate(L[Q]));
                    K = S.length;
                    if (0 < K) for (J = [], Q = 0; Q < K; Q++) J.push(c.latitudeToCoordinate(S[Q]));
                    if (0 < H.length) {
                        d.dx = 0;
                        d.dy = 0;
                        L = d.line(q, H, J, E, 1, C, G, !1, !1, !0);
                        d.setCN(c, L, "map-line");
                        void 0 != t.id && d.setCN(c, L, "map-line-" + t.id);
                        G = d.line(q, H, J, E, .001, 3, G, !1, !1, !0);
                        d.dx = .5;
                        d.dy = .5;
                        z.push(L);
                        z.push(G);
                        z.setAttr("opacity", F);
                        if ("none" != M) {
                            var I, R, T;
                            if ("end" == M || "both" == M) F = H[H.length - 1], E = J[J.length - 1], 1 < H.length ? (K = H[H.length - 2],
                            I = J[J.length - 2]) :(K = F, I = E), I = 180 * Math.atan((E - I) / (F - K)) / Math.PI,
                            R = F, T = E, I = 0 > F - K ? I - 90 :I + 90;
                            "both" == M && (F = d.polygon(q, [ -N / 2, 0, N / 2 ], [ 1.5 * N, 0, 1.5 * N ], O, P, 1, O, P),
                            z.push(F), F.translate(R, T), F.rotate(I), d.setCN(c, L, "map-line-arrow"), void 0 != t.id && d.setCN(c, L, "map-line-arrow-" + t.id),
                            t.fixedSize && f.push(F));
                            if ("start" == M || "both" == M) F = H[0], T = J[0], 1 < H.length ? (E = H[1], R = J[1]) :(E = F,
                            R = T), I = 180 * Math.atan((T - R) / (F - E)) / Math.PI, R = F, I = 0 > F - E ? I - 90 :I + 90;
                            "middle" == M && (F = H[H.length - 1], E = J[J.length - 1], 1 < H.length ? (K = H[H.length - 2],
                            I = J[J.length - 2]) :(K = F, I = E), R = K + (F - K) / 2, T = I + (E - I) / 2,
                            I = 180 * Math.atan((E - I) / (F - K)) / Math.PI, I = 0 > F - K ? I - 90 :I + 90);
                            F = d.polygon(q, [ -N / 2, 0, N / 2 ], [ 1.5 * N, 0, 1.5 * N ], O, P, 1, O, P);
                            d.setCN(c, L, "map-line-arrow");
                            void 0 != t.id && d.setCN(c, L, "map-line-arrow-" + t.id);
                            z.push(F);
                            F.translate(R, T);
                            F.rotate(I);
                            t.fixedSize && f.push(F);
                            t.arrowSvg = F;
                        }
                        t.fixedSize && L && (this.linesToResize.push({
                            line:L,
                            thickness:C
                        }), this.linesToResize.push({
                            line:G,
                            thickness:3
                        }));
                        t.lineSvg = L;
                        t.showAsSelected && !isNaN(U) && L.setAttr("stroke", U);
                        0 < S.length ? e.push(z) :g.push(z);
                        t.hidden && z.hide();
                    }
                }
            }
            a.parentObject && a.remainVisible && this.process(a.parentObject);
        },
        processCoordinates:function(a, b) {
            var c = [], d;
            for (d = 0; d < a.length; d++) {
                var f = a[d], e = Number(f);
                isNaN(e) && (e = Number(f.replace("%", "")) * b / 100);
                isNaN(e) || c.push(e);
            }
            return c;
        },
        reset:function() {
            this.objectsToResize = [];
            this.allSvgObjects = [];
            this.allObjects = [];
            this.linesToResize = [];
        }
    });
})();

(function() {
    var d = window.AmCharts;
    d.LinesSettings = d.Class({
        construct:function(a) {
            this.cname = "LinesSettings";
            this.balloonText = "[[title]]";
            this.thickness = 1;
            this.dashLength = 0;
            this.arrowSize = 10;
            this.arrowAlpha = 1;
            this.arrow = "none";
            this.color = "#990000";
            this.descriptionWindowWidth = 250;
            this.bringForwardOnHover = !0;
            d.applyTheme(this, a, this.cname);
        }
    });
})();

(function() {
    var d = window.AmCharts;
    d.MapObject = d.Class({
        construct:function(a) {
            this.fixedSize = this.mouseEnabled = !0;
            this.images = [];
            this.lines = [];
            this.areas = [];
            this.remainVisible = !0;
            this.passZoomValuesToTarget = !1;
            this.objectType = this.cname;
            d.applyTheme(this, a, "MapObject");
        }
    });
})();

(function(d) {
    d = window.AmCharts;
    d.MapArea = d.Class({
        inherits:d.MapObject,
        construct:function(a) {
            this.cname = "MapArea";
            d.MapArea.base.construct.call(this, a);
            d.applyTheme(this, a, this.cname);
        }
    });
})();

(function() {
    var d = window.AmCharts;
    d.MapLine = d.Class({
        inherits:d.MapObject,
        construct:function(a) {
            this.cname = "MapLine";
            this.longitudes = [];
            this.latitudes = [];
            this.x = [];
            this.y = [];
            this.arrow = "none";
            d.MapLine.base.construct.call(this, a);
            d.applyTheme(this, a, this.cname);
        }
    });
})();

(function() {
    var d = window.AmCharts;
    d.MapImage = d.Class({
        inherits:d.MapObject,
        construct:function(a) {
            this.cname = "MapImage";
            this.scale = 1;
            this.widthAndHeightUnits = "pixels";
            this.labelShiftY = this.labelShiftX = 0;
            d.MapImage.base.construct.call(this, a);
            d.applyTheme(this, a, this.cname);
        },
        remove:function() {
            var a = this.displayObject;
            a && a.remove();
            (a = this.imageLabel) && a.remove();
        }
    });
})();

(function() {
    var d = window.AmCharts;
    d.degreesToRadians = function(a) {
        return a / 180 * Math.PI;
    };
    d.radiansToDegrees = function(a) {
        return a / Math.PI * 180;
    };
    d.getColorFade = function(a, b, c) {
        var h = d.hex2RGB(b);
        b = h[0];
        var f = h[1], h = h[2], e = d.hex2RGB(a);
        a = e[0];
        var g = e[1], e = e[2];
        a += Math.round((b - a) * c);
        g += Math.round((f - g) * c);
        e += Math.round((h - e) * c);
        return "rgb(" + a + "," + g + "," + e + ")";
    };
    d.hex2RGB = function(a) {
        return [ parseInt(a.substring(1, 3), 16), parseInt(a.substring(3, 5), 16), parseInt(a.substring(5, 7), 16) ];
    };
    d.processDescriptionWindow = function(a, b) {
        isNaN(b.descriptionWindowX) && (b.descriptionWindowX = a.descriptionWindowX);
        isNaN(b.descriptionWindowY) && (b.descriptionWindowY = a.descriptionWindowY);
        isNaN(b.descriptionWindowLeft) && (b.descriptionWindowLeft = a.descriptionWindowLeft);
        isNaN(b.descriptionWindowRight) && (b.descriptionWindowRight = a.descriptionWindowRight);
        isNaN(b.descriptionWindowTop) && (b.descriptionWindowTop = a.descriptionWindowTop);
        isNaN(b.descriptionWindowBottom) && (b.descriptionWindowBottom = a.descriptionWindowBottom);
        isNaN(b.descriptionWindowWidth) && (b.descriptionWindowWidth = a.descriptionWindowWidth);
        isNaN(b.descriptionWindowHeight) && (b.descriptionWindowHeight = a.descriptionWindowHeight);
    };
})();

(function() {
    var d = window.AmCharts;
    d.MapData = d.Class({
        inherits:d.MapObject,
        construct:function() {
            this.cname = "MapData";
            d.MapData.base.construct.call(this);
            this.projection = "mercator";
            this.topLatitude = 90;
            this.bottomLatitude = -90;
            this.leftLongitude = -180;
            this.rightLongitude = 180;
            this.zoomLevel = 1;
            this.getAreasFromMap = !1;
        }
    });
})();

(function() {
    var d = window.AmCharts;
    d.DescriptionWindow = d.Class({
        construct:function() {},
        show:function(a, b, c, d) {
            var f = this, e = document.createElement("div");
            e.style.position = "absolute";
            var g = a.classNamePrefix + "-description-";
            e.className = "ammapDescriptionWindow " + g + "div";
            f.div = e;
            b.appendChild(e);
            var k = document.createElement("img");
            k.className = "ammapDescriptionWindowCloseButton " + g + "close-img";
            k.src = a.pathToImages + "xIcon.gif";
            k.style.cssFloat = "right";
            k.onclick = function() {
                f.close();
            };
            k.onmouseover = function() {
                k.src = a.pathToImages + "xIconH.gif";
            };
            k.onmouseout = function() {
                k.src = a.pathToImages + "xIcon.gif";
            };
            e.appendChild(k);
            b = document.createElement("div");
            b.className = "ammapDescriptionTitle " + g + "title-div";
            b.onmousedown = function() {
                f.div.style.zIndex = 1e3;
            };
            e.appendChild(b);
            d = document.createTextNode(d);
            b.appendChild(d);
            d = b.offsetHeight;
            b = document.createElement("div");
            b.className = "ammapDescriptionText " + g + "text-div";
            b.style.maxHeight = f.maxHeight - d - 20 + "px";
            e.appendChild(b);
            b.innerHTML = c;
        },
        close:function() {
            try {
                this.div.parentNode.removeChild(this.div);
            } catch (a) {}
        }
    });
})();

(function() {
    var d = window.AmCharts;
    d.ValueLegend = d.Class({
        construct:function(a) {
            this.cname = "ValueLegend";
            this.enabled = !0;
            this.showAsGradient = !1;
            this.minValue = 0;
            this.height = 12;
            this.width = 200;
            this.bottom = this.left = 10;
            this.borderColor = "#FFFFFF";
            this.borderAlpha = this.borderThickness = 1;
            this.color = "#000000";
            this.fontSize = 11;
            d.applyTheme(this, a, this.cname);
        },
        init:function(a, b) {
            if (this.enabled) {
                var c = a.areasSettings.color, h = a.areasSettings.colorSolid, f = a.colorSteps;
                d.remove(this.set);
                var e = b.set();
                this.set = e;
                d.setCN(a, e, "value-legend");
                var g = 0, k = this.minValue, l = this.fontSize, m = a.fontFamily, n = this.color;
                void 0 == k && (k = a.minValueReal);
                void 0 !== k && (g = d.text(b, k, n, m, l, "left"), g.translate(0, l / 2 - 1), d.setCN(a, g, "value-legend-min-label"),
                e.push(g), g = g.getBBox().height);
                k = this.maxValue;
                void 0 === k && (k = a.maxValueReal);
                void 0 !== k && (g = d.text(b, k, n, m, l, "right"), g.translate(this.width, l / 2 - 1),
                d.setCN(a, g, "value-legend-max-label"), e.push(g), g = g.getBBox().height);
                if (this.showAsGradient) c = d.rect(b, this.width, this.height, [ c, h ], 1, this.borderThickness, this.borderColor, 1, 0, 0),
                d.setCN(a, c, "value-legend-gradient"), c.translate(0, g), e.push(c); else for (l = this.width / f,
                m = 0; m < f; m++) n = d.getColorFade(c, h, 1 * m / (f - 1)), n = d.rect(b, l, this.height, n, 1, this.borderThickness, this.borderColor, 1),
                d.setCN(a, n, "value-legend-color"), d.setCN(a, n, "value-legend-color-" + m), n.translate(l * m, g),
                e.push(n);
                h = c = 0;
                f = e.getBBox();
                g = a.getY(this.bottom, !0);
                l = a.getY(this.top);
                m = a.getX(this.right, !0);
                n = a.getX(this.left);
                isNaN(l) || (c = l);
                isNaN(g) || (c = g - f.height);
                isNaN(n) || (h = n);
                isNaN(m) || (h = m - f.width);
                e.translate(h, c);
            } else d.remove(this.set);
        }
    });
})();

(function() {
    var d = window.AmCharts;
    d.ObjectList = d.Class({
        construct:function(a) {
            this.divId = a;
        },
        init:function(a) {
            this.chart = a;
            var b = this.divId;
            this.container && (b = this.container);
            this.div = "object" != typeof b ? document.getElementById(b) :b;
            b = document.createElement("div");
            b.className = "ammapObjectList " + a.classNamePrefix + "-object-list-div";
            this.div.appendChild(b);
            this.addObjects(a.dataProvider, b);
        },
        addObjects:function(a, b) {
            var c = this.chart, d = document.createElement("ul");
            d.className = c.classNamePrefix + "-object-list-ul";
            var f;
            if (a.areas) for (f = 0; f < a.areas.length; f++) {
                var e = a.areas[f];
                void 0 === e.showInList && (e.showInList = c.showAreasInList);
                this.addObject(e, d);
            }
            if (a.images) for (f = 0; f < a.images.length; f++) e = a.images[f], void 0 === e.showInList && (e.showInList = c.showImagesInList),
            this.addObject(e, d);
            if (a.lines) for (f = 0; f < a.lines.length; f++) e = a.lines[f], void 0 === e.showInList && (e.showInList = c.showLinesInList),
            this.addObject(e, d);
            0 < d.childNodes.length && b.appendChild(d);
        },
        addObject:function(a, b) {
            var c = this;
            if (a.showInList && void 0 !== a.title) {
                var d = c.chart, f = document.createElement("li");
                f.className = d.classNamePrefix + "-object-list-li";
                var e = document.createTextNode(a.title), g = document.createElement("a");
                g.className = d.classNamePrefix + "-object-list-a";
                g.appendChild(e);
                f.appendChild(g);
                b.appendChild(f);
                this.addObjects(a, f);
                g.onmouseover = function() {
                    c.chart.rollOverMapObject(a, !1);
                };
                g.onmouseout = function() {
                    c.chart.rollOutMapObject(a);
                };
                g.onclick = function() {
                    c.chart.clickMapObject(a);
                };
            }
        }
    });
})();