// rewrite window function
function alert(msg, type, callback, container){
    if (type === undefined) type = 'error';
    var cla = 'alert-danger';
    var stxt = '错误! ';
    switch(type){
        case 'error': cla = 'alert-danger'; stxt = '错误! '; break;
        case 'warning': cla = 'alert-warning'; stxt = '警告! '; break;
        case 'success': cla = 'alert-success'; stxt = '成功! '; break;
    }

    var di = $("<div class=\"alert "+cla+"\" role=\"alert\" style=\"display:none\"></div>");
    di.html("<strong>" + stxt + "</strong>" + msg);
    di.children("a").addClass("alert-link");
    if (container === undefined){
        $("h1.page-header").after(di);
        var top = $("h1.page-header").offset().top;
    }else{
        $(container).prepend(di);
        var top = $(container).offset().top;
    }
    // scroll to top
    if($(window).scrollTop() > top) $(window).scrollTop(top);
    di.slideDown(300).delay(3000).slideUp(300, function(){
        if (callback !== undefined && callback) callback();
        $(this).remove();
    });
}

function history(a){
    var a = $(a), d = a.data(), li = a.parent();
    if (d.status == "loading") return;
    var tmp = $("<li class=\"list-group-item\"><span class=\"log\"><b></b></span><span class=\"time\"></span></li>");

    a.text("正在读取..").data("status", "loading");
    $.get(d.url, {page:d.page}, function(data){
        if (data.s == 0){
            for (x in data.rs){
                var t = data.rs[x],
                    c = tmp.clone();
                c.children(".time").text(t.time).prev(".log").text(t.intro).prepend("<b></b>").children("b").text(t.username+" ").data("history", t.id);
                li.before(c);
            }
            if (data.rs.length == 10)
                a.text("查看更多记录").data({status:"loaded", page:parseInt(d.page,10)+1});
            else
                a.after("<span class=\"c9\">无更多记录</span>").remove();
        }else{
            a.text("读取失败，请重试").data("status", "loaded");
        }
    }, "json");
}

$(function(){
    $(".sidebar-toggle").click(function(e) {
        e.preventDefault();
        var sidebar = $(".sidebar");
        if(sidebar.is(".active")){
            sidebar.removeClass("active");
            sidebar.delay(500).animate({"left":"-100%"}, 1, function(){ $(this).removeAttr("style"); });
        }else{
            sidebar.css("left", "0%");
            sidebar.addClass("active");
        }
    });

    $(".table .checked-all").click(function(){
        var _c = $(this);
        var table = _c.parents(".table").eq(0);
        var checked = _c.prop("checked");
        table.find("input.checkbox").prop("checked", checked);
    });

    $(".sidebar").click(function(e) {
        var sidebar = $(this);
        if (sidebar.is(".active")) {
            if($(e.target).is(".sidebar")) {
                sidebar.removeClass("active");
                sidebar.delay(500).animate({"left":"-100%"}, 1, function(){ $(this).removeAttr("style"); });
            }
        }
    });

    $(".sidebar .nav .hidden").remove();
    $(".sidebar .nav:not(:has(li))").remove();

    $(".pagination input").keyup(function(e){
        if (e.which == 13){
            var url = $(this).data("url");
            var page = parseInt($(this).val(), 10);
            if (isNaN(page)) return;
            location.href = url + page;
        }
    });

    $(document).on('keyup', '[data-enter]', function(e){
        var func = $(this).data("enter");
        if (e.which != 13) return;
        try{
            eval(func);
        }catch(e){
            console.log(e);
        }
    });

    if ($.fn.highlightRegex !== undefined) {
        $(document).on('keyup', '[data-search]', function(e){
            var target  = $(this).data("search");
            var val = $.trim(this.value);
            var regex   = new RegExp(val, "ig");
            $(target).highlightRegex({tagType:"mark"});

            if (val == "") return;
            if (regex !== undefined){
                $(target).highlightRegex(regex, {tagType:"mark"});
            }
        });

        var _searchInput = $('input[data-search]');
        if(_searchInput.length) {
            _searchInput.each(function(){
                var _this = $(this);
                var val = $.trim(this.value);
                if (val != "") {
                    var regex = new RegExp(val, "ig");
                    $(_this.data("search")).highlightRegex(regex, {tagType:"mark"});
                }
            });
        }
    }

});


;(function ($) {

// tag list
$.fn.tag = function(options){

    $.fn.tag.defaults = {insert:""};

    var opts = $.extend({}, $.fn.tag.defaults, options);

    var _initialize = function(ul){
        if(opts.insert) _bind($(ul).find(opts.insert+" input"), ul);
        $(ul).on("click", "span a", function(){ $(this).parents("li").eq(0).remove(); });
    }

    var _bind = function(inp, ul){
        inp.unbind("keydown").bind("keydown", function(evt){
            var stroke;
            stroke = evt.which;
            switch(stroke){
                case 13:
                    var str = inp.val();
                    str = str.replace(/，/ig, ",");
                    var tags = str.split(",");
                    for(x in tags){
                        if(tags[x] == "") continue;
                        var span = $("<span />").text(tags[x]).append('<input type="hidden" name="tag[]" value="" /><a href="javascript:;" class="glyphicon glyphicon-remove"></a>');
                        span.children("input").val(tags[x]);
                        $(ul).append($("<li />").append(span));
                    }
                    inp.val("");
            }
        });
    }

    return this.each(function(){
        _initialize(this);
    });
};



// category list
$.fn.category = function() {

    var _initialize = function(ul){
        $(ul).on("click", "li a", function(){
            var li = $(this).parent();
            var type = li.is(".on") ? 'off' : 'on';
            var lv = parseInt(li.data("level"), 10);
            li.nextAll("li").each(function(){
                var _li = $(this);
                var _lv = parseInt(_li.data("level"), 10);
                if(_lv <= lv){
                    return false;
                }

                if(type == 'off'){
                    _li.hide();
                }else{
                    if(_lv == lv + 1) _li.show();
                }
            });

            li.removeClass("on off").addClass(type);
            if(type == 'off')
                li.find(".fa-angle-up").removeClass("fa-angle-up").addClass("fa-angle-down");
            else
                li.find(".fa-angle-down").removeClass("fa-angle-down").addClass("fa-angle-up");
        });
    }

    return this.each(function(){
        _initialize(this);
    });

};

})(jQuery);
