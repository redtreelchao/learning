webpackJsonp([6],{5:function(e,t){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var r;t.AJAX_TEST="AJAX_TEST",t.TEST_API="http://wx.redtravel.cn",t.PAGE_DONE="PAGE_DONE",t.GET_MORE="GET_MORE",t.CAT_MORE="CAT_MORE",t.SCROLL_UP="SCROLL_UP",t.GET_DETAIL="GET_DETAIL",t.SEARCH_DONE="SEARCH_DONE",t.SHOW_SEARCH="SHOW_SEARCH",t.DEL_INDENT="DEL_INDENT",t.GO_INDENT="GO_INDENT",t.ADD_AMOUNT="ADD_AMOUNT",t.MINUS_AMOUNT="MINUS_AMOUNT",t.ORDER_TO_PAY="ORDER_TO_PAY",t.GET_COUPONS="GET_COUPONS",t.GET_ORDER_LIST="GET_ORDER_LIST",t.ORDER_DETAIL="ORDER_DETAIL",t.DO_SORT="DO_SORT",t.APPID="wx8f2d4ba8d79914d1",t.GET_KEYWORD="GET_KEYWORD",t.CLEAR_PRODUCT="CLEAR_PRODUCT",t.BIND_COUPONS="BIND_COUPONS",t.GET_HISTORY="GET_HISTORY";r="http://iapi.putike.cn";t.KEYWORD_URL=r+"/index/api?method=product&action=hotkeyword",t.COUPONS_URL=r+"/index/api?method=user&action=ticketlist",t.ORDER_LIST=r+"/index/api?method=user&action=orderlist",t.BIND_COUPONS_URL=r+"/index/api?method=user&action=bind_coupons",t.PAY_URL=r+"/index/api?method=order&action=detail",t.SEARCH_URL=r+"/index/api?method=product&action=index&page=",t.PRODUCTS_URL=r+"/index/api?method=product&action=index&page=",t.DETAIL_URL=r+"/index/api?method=product&action=detail",t.INDEX_API=r+"/index/api?"},9:function(e,t,r){r(12),e.exports=self.fetch.bind(self)},12:function(e,t){!function(e){"use strict";function t(e){if("string"!=typeof e&&(e=String(e)),/[^a-z0-9\-#$%&'*+.\^_`|~]/i.test(e))throw new TypeError("Invalid character in header field name");return e.toLowerCase()}function r(e){return"string"!=typeof e&&(e=String(e)),e}function i(e){var t={next:function(){var t=e.shift();return{done:void 0===t,value:t}}};return h.iterable&&(t[Symbol.iterator]=function(){return t}),t}function n(e){this.map={},e instanceof n?e.forEach(function(e,t){this.append(t,e)},this):e&&Object.getOwnPropertyNames(e).forEach(function(t){this.append(t,e[t])},this)}function o(e){return e.bodyUsed?Promise.reject(new TypeError("Already read")):void(e.bodyUsed=!0)}function a(e){return new Promise(function(t,r){e.onload=function(){t(e.result)},e.onerror=function(){r(e.error)}})}function s(e){var t=new FileReader;return t.readAsArrayBuffer(e),a(t)}function l(e){var t=new FileReader;return t.readAsText(e),a(t)}function u(){return this.bodyUsed=!1,this._initBody=function(e){if(this._bodyInit=e,"string"==typeof e)this._bodyText=e;else if(h.blob&&Blob.prototype.isPrototypeOf(e))this._bodyBlob=e;else if(h.formData&&FormData.prototype.isPrototypeOf(e))this._bodyFormData=e;else if(h.searchParams&&URLSearchParams.prototype.isPrototypeOf(e))this._bodyText=e.toString();else if(e){if(!h.arrayBuffer||!ArrayBuffer.prototype.isPrototypeOf(e))throw new Error("unsupported BodyInit type")}else this._bodyText="";this.headers.get("content-type")||("string"==typeof e?this.headers.set("content-type","text/plain;charset=UTF-8"):this._bodyBlob&&this._bodyBlob.type?this.headers.set("content-type",this._bodyBlob.type):h.searchParams&&URLSearchParams.prototype.isPrototypeOf(e)&&this.headers.set("content-type","application/x-www-form-urlencoded;charset=UTF-8"))},h.blob?(this.blob=function(){var e=o(this);if(e)return e;if(this._bodyBlob)return Promise.resolve(this._bodyBlob);if(this._bodyFormData)throw new Error("could not read FormData body as blob");return Promise.resolve(new Blob([this._bodyText]))},this.arrayBuffer=function(){return this.blob().then(s)},this.text=function(){var e=o(this);if(e)return e;if(this._bodyBlob)return l(this._bodyBlob);if(this._bodyFormData)throw new Error("could not read FormData body as text");return Promise.resolve(this._bodyText)}):this.text=function(){var e=o(this);return e?e:Promise.resolve(this._bodyText)},h.formData&&(this.formData=function(){return this.text().then(f)}),this.json=function(){return this.text().then(JSON.parse)},this}function d(e){var t=e.toUpperCase();return m.indexOf(t)>-1?t:e}function c(e,t){t=t||{};var r=t.body;if(c.prototype.isPrototypeOf(e)){if(e.bodyUsed)throw new TypeError("Already read");this.url=e.url,this.credentials=e.credentials,t.headers||(this.headers=new n(e.headers)),this.method=e.method,this.mode=e.mode,r||(r=e._bodyInit,e.bodyUsed=!0)}else this.url=e;if(this.credentials=t.credentials||this.credentials||"omit",!t.headers&&this.headers||(this.headers=new n(t.headers)),this.method=d(t.method||this.method||"GET"),this.mode=t.mode||this.mode||null,this.referrer=null,("GET"===this.method||"HEAD"===this.method)&&r)throw new TypeError("Body not allowed for GET or HEAD requests");this._initBody(r)}function f(e){var t=new FormData;return e.trim().split("&").forEach(function(e){if(e){var r=e.split("="),i=r.shift().replace(/\+/g," "),n=r.join("=").replace(/\+/g," ");t.append(decodeURIComponent(i),decodeURIComponent(n))}}),t}function _(e){var t=new n,r=(e.getAllResponseHeaders()||"").trim().split("\n");return r.forEach(function(e){var r=e.trim().split(":"),i=r.shift().trim(),n=r.join(":").trim();t.append(i,n)}),t}function p(e,t){t||(t={}),this.type="default",this.status=t.status,this.ok=this.status>=200&&this.status<300,this.statusText=t.statusText,this.headers=t.headers instanceof n?t.headers:new n(t.headers),this.url=t.url||"",this._initBody(e)}if(!e.fetch){var h={searchParams:"URLSearchParams"in e,iterable:"Symbol"in e&&"iterator"in Symbol,blob:"FileReader"in e&&"Blob"in e&&function(){try{return new Blob,!0}catch(e){return!1}}(),formData:"FormData"in e,arrayBuffer:"ArrayBuffer"in e};n.prototype.append=function(e,i){e=t(e),i=r(i);var n=this.map[e];n||(n=[],this.map[e]=n),n.push(i)},n.prototype["delete"]=function(e){delete this.map[t(e)]},n.prototype.get=function(e){var r=this.map[t(e)];return r?r[0]:null},n.prototype.getAll=function(e){return this.map[t(e)]||[]},n.prototype.has=function(e){return this.map.hasOwnProperty(t(e))},n.prototype.set=function(e,i){this.map[t(e)]=[r(i)]},n.prototype.forEach=function(e,t){Object.getOwnPropertyNames(this.map).forEach(function(r){this.map[r].forEach(function(i){e.call(t,i,r,this)},this)},this)},n.prototype.keys=function(){var e=[];return this.forEach(function(t,r){e.push(r)}),i(e)},n.prototype.values=function(){var e=[];return this.forEach(function(t){e.push(t)}),i(e)},n.prototype.entries=function(){var e=[];return this.forEach(function(t,r){e.push([r,t])}),i(e)},h.iterable&&(n.prototype[Symbol.iterator]=n.prototype.entries);var m=["DELETE","GET","HEAD","OPTIONS","POST","PUT"];c.prototype.clone=function(){return new c(this)},u.call(c.prototype),u.call(p.prototype),p.prototype.clone=function(){return new p(this._bodyInit,{status:this.status,statusText:this.statusText,headers:new n(this.headers),url:this.url})},p.error=function(){var e=new p(null,{status:0,statusText:""});return e.type="error",e};var y=[301,302,303,307,308];p.redirect=function(e,t){if(-1===y.indexOf(t))throw new RangeError("Invalid status code");return new p(null,{status:t,headers:{location:e}})},e.Headers=n,e.Request=c,e.Response=p,e.fetch=function(e,t){return new Promise(function(r,i){function n(){return"responseURL"in a?a.responseURL:/^X-Request-URL:/m.test(a.getAllResponseHeaders())?a.getResponseHeader("X-Request-URL"):void 0}var o;o=c.prototype.isPrototypeOf(e)&&!t?e:new c(e,t);var a=new XMLHttpRequest;a.onload=function(){var e={status:a.status,statusText:a.statusText,headers:_(a),url:n()},t="response"in a?a.response:a.responseText;r(new p(t,e))},a.onerror=function(){i(new TypeError("Network request failed"))},a.ontimeout=function(){i(new TypeError("Network request failed"))},a.open(o.method,o.url,!0),"include"===o.credentials&&(a.withCredentials=!0),"responseType"in a&&h.blob&&(a.responseType="blob"),o.headers.forEach(function(e,t){a.setRequestHeader(t,e)}),a.send("undefined"==typeof o._bodyInit?null:o._bodyInit)})},e.fetch.polyfill=!0}}("undefined"!=typeof self?self:this)},14:function(e,t,r){"use strict";function i(e){return e&&e.__esModule?e:{"default":e}}Object.defineProperty(t,"__esModule",{value:!0});var n=r(1),o=i(n),a=r(17),s=i(a),l=(r(7),o["default"].createClass({displayName:"MainHeader",contextTypes:{router:o["default"].PropTypes.object.isRequired},getInitialState:function(){return{}},goBack:function(e){e.preventDefault(),this.context.router.push("/catemap")},componentDidMount:function(){},componentWillUnmount:function(){},render:function(){var e,t=this.props.title_right;switch(e="取消"==t?o["default"].createElement("p",{className:s["default"].title_cancel_right,onClick:this.props.cancelRight},t):o["default"].createElement("p",{className:s["default"].title_bar_right,onClick:this.props.doRight},t),t){case"取消":e=o["default"].createElement("p",{className:s["default"].title_cancel_right,onClick:this.props.cancelRight},t);break;case"tel":e=o["default"].createElement("p",{className:s["default"].title_tel_right,onClick:this.props.doRight},o["default"].createElement("a",{href:"tel:400-600-800"}));break;case"coupon":e=o["default"].createElement("p",{className:s["default"].title_bar_right,onClick:this.props.doRight},t)}return o["default"].createElement("div",null,o["default"].createElement("div",{className:s["default"].MainHeader},o["default"].createElement("i",{className:s["default"].goBack,onClick:this.context.router.goBack}),o["default"].createElement("p",{className:s["default"].title},this.props.title),e))}}));t["default"]=l},15:function(e,t,r){"use strict";function i(e){return e&&e.__esModule?e:{"default":e}}Object.defineProperty(t,"__esModule",{value:!0});var n=r(14),o=i(n);t["default"]=o["default"]},16:function(e,t,r){"use strict";function i(e){return e&&e.__esModule?e:{"default":e}}t.__esModule=!0;var n=r(33),o=i(n);t["default"]=function(e,t,r){return t in e?(0,o["default"])(e,t,{value:r,enumerable:!0,configurable:!0,writable:!0}):e[t]=r,e}},17:function(e,t){e.exports={MainHeader:"MainHeader__MainHeader___3F1Zi",goBack:"MainHeader__goBack___2Eg4z",title:"MainHeader__title___FxDNi",title_bar_right:"MainHeader__title_bar_right___1K_KU",title_cancel_right:"MainHeader__title_cancel_right___XEvG7",title_tel_right:"MainHeader__title_tel_right___-Sjjl"}},214:function(e,t,r){"use strict";function i(e){return e&&e.__esModule?e:{"default":e}}function n(){var e=arguments.length<=0||void 0===arguments[0]?p:arguments[0],t=arguments[1],r=_[t.type];return r?r(e,t):e}Object.defineProperty(t,"__esModule",{value:!0}),t.actions=t.getOrderlist=void 0;var o=r(16),a=i(o),s=r(32),l=i(s);t["default"]=n;var u=r(9),d=i(u),c=r(5),f=t.getOrderlist=function(e){return function(t){return(0,d["default"])(c.ORDER_LIST+"&uid="+e).then(function(e){return e.json()}).then(function(e){return e.success?t({type:c.GET_ORDER_LIST,list:e.data}):void alert(e.msg)})}},_=(t.actions={getOrderlist:f},(0,a["default"])({},c.GET_ORDER_LIST,function(e,t){return(0,l["default"])({},e,{list:t.list})})),p={list:null}},338:function(e,t,r){"use strict";function i(e){return e&&e.__esModule?e:{"default":e}}Object.defineProperty(t,"__esModule",{value:!0});var n=r(1),o=i(n),a=r(7),s=r(490),l=i(s),u=r(15),d=i(u),c=o["default"].createClass({displayName:"Orderlist",contextTypes:{router:o["default"].PropTypes.object.isRequired},getInitialState:function(){return{title:"我的订单"}},componentDidMount:function(){var e=this.props.location.query.uid;this.props.getOrderlist(e)},render:function(){var e=this,t=this.props.orderlist.list||[],r=void 0,i={1:"支付后待确认",2:"待支付",3:"订单支付成功，未预约",4:"支付成功,发出预约等酒店确认",5:"预订失败",8:"酒店回传确认，预约成功",9:"订单完结",10:"退款申请",11:"退订退款完成",12:"拒绝退订",16:"部分退款"};return t&&(r=0!=t.length?t.map(function(t,r){return o["default"].createElement("div",{className:l["default"].info_block,key:r},o["default"].createElement("div",{className:l["default"].order_status},o["default"].createElement("span",null,"订单号 ",t.order),o["default"].createElement("span",{className:l["default"].right},i[t.stat])),o["default"].createElement("div",{className:l["default"].info_wrapper},o["default"].createElement("div",{className:l["default"].imgwrapper},o["default"].createElement(a.Link,{to:"/order?orderid="+t.order+"&uid="+e.props.location.query.uid},o["default"].createElement("img",{src:t.pic+"!/fw/125"}))),o["default"].createElement("div",{className:l["default"].proc_info},o["default"].createElement("p",{className:l["default"].maintitle},o["default"].createElement(a.Link,{to:"/order?orderid="+t.order+"&uid="+e.props.location.query.uid},t.title)),o["default"].createElement("p",{className:l["default"].subtitle},o["default"].createElement(a.Link,{to:"/order?orderid="+t.order+"&uid="+e.props.location.query.uid},t.name))),o["default"].createElement("div",{className:"clear"})))}):o["default"].createElement("div",{className:l["default"].empty_box},o["default"].createElement("p",null,"你还没有订单哦"))),o["default"].createElement("div",null,o["default"].createElement("div",{className:l["default"].listWrapper},o["default"].createElement(d["default"],{title:this.state.title}),o["default"].createElement("div",{className:l["default"].product_body},r),o["default"].createElement(a.Link,{to:"/"},o["default"].createElement("div",{className:l["default"].home_btn},o["default"].createElement("span",null,"去首页看看")))))}});t["default"]=c},339:function(e,t,r){"use strict";function i(e){return e&&e.__esModule?e:{"default":e}}Object.defineProperty(t,"__esModule",{value:!0});var n=r(338),o=i(n);t["default"]=o["default"]},361:function(e,t,r){"use strict";function i(e){return e&&e.__esModule?e:{"default":e}}Object.defineProperty(t,"__esModule",{value:!0});var n=r(19),o=r(214),a=r(339),s=i(a),l={getOrderlist:o.getOrderlist},u=function(e){return{orderlist:e.orderlist}};t["default"]=(0,n.connect)(u,l)(s["default"])},490:function(e,t){e.exports={listWrapper:"Orderlist__listWrapper___3lNQI",product_body:"Orderlist__product_body___N_KzY",left:"Orderlist__left___1GwvE",right:"Orderlist__right___3ewY-",info_block:"Orderlist__info_block___3e-TH",info_wrapper:"Orderlist__info_wrapper___1LZ5E",imgwrapper:"Orderlist__imgwrapper___2aRzE",proc_info:"Orderlist__proc_info___1tjmG",maintitle:"Orderlist__maintitle___1nbys",subtitle:"Orderlist__subtitle___3-jOV",order_status:"Orderlist__order_status___4zGkP",empty_box:"Orderlist__empty_box___1_FOz",home_btn:"Orderlist__home_btn___zbRKR"}}});