/*
 * pull page to other one (project qdplus)
 * Date:2014-02-25
 */

;(function($) {

    $.fn.mobileScroll = function(options) {

        $.fn.mobileScroll.defaults = {
            autohide : true,
            wheelctl : true,
            mousectl : true
        };

        var opts = $.extend({}, $.fn.mobileScroll.defaults, options);

        var scrollbar = $(this);

        var handle = scrollbar.children(".handle");

        var _content = $("body");

        var _container = $(window);

        var bar_width = scrollbar.width();

        // check position
        var checkPos = function(dom, _y){

            var pos_top = handle.position().top;
            if(pos_top <= 0 && (_y === undefined || _y < 0)) {
                handle.css("top", 0);
                return false;
            }

            var maxTop = scrollbar.height() - handle.height();
            if(pos_top >= maxTop && (_y === undefined || _y > 0)) {
                handle.css("top", maxTop);
                return false;
            }
            return true;
        }

        // click, record mouse position
        if (opts.mousectl) {
            handle.mousedown(function(event) {
                handle.data("_drag", 1);
                handle.data("_offset", handle.position());
                handle.data("_mouseY", event.pageY - _container.scrollTop());
            })
            // release
            .mouseup(function() {
                handle.data("_drag", 0);
                checkPos();
            })
            // moveout
            .mouseout(function() {
                handle.data("_drag", 0);
                checkPos();
            })
            // move
            .mousemove(function(event) {

                if (handle.data("_drag") == 1) {
                    var old_offset = handle.data("_offset");
                    var mousemoveY = event.pageY - handle.data("_mouseY") - _container.scrollTop();
                    var top = old_offset.top + mousemoveY;

                    if (!checkPos(this, mousemoveY)) return;

                    // get value
                    var contentH = Math.max(window.document.documentElement.scrollHeight, window.document.body.scrollHeight);
                    var paneH    = _container.height();
                    var handleH  = handle.height();
                    var rangeH   = scrollbar.height() - handleH;

                    // move
                    handle.css({top:top});

                    var pos_top = handle.position().top;
                    if (pos_top <= 0) pos_top = 0;
                    else if(pos_top >= rangeH) pos_top = rangeH;
                    var content_top =  pos_top * (contentH - paneH) / rangeH;

                    // record value
                    handle.data("_offset", handle.position());
                    handle.data("_mouseY", event.pageY - _container.scrollTop());

                    _container.scrollTop(content_top);

                    handle.focus();
                }
            });
        }

        // auto show/hide
        if (opts.autohide) {
            handle.delay(2000).animate({left:bar_width+"px"}, 100);

            scrollbar.mouseover(function(){
                handle.stop(true).animate({left:"0px"}, 100);
            });

            scrollbar.mouseout(function(){
                handle.delay(2000).animate({left:bar_width+"px"}, 100);
            });
        }


        // scroll by wheel
        if (opts.wheelctl) {
            var wheel = function(event){

                var delta = 0;
                if (!event) /* For IE. */
                    event = window.event;
                if (event.wheelDelta) { /* IE/Opera. */
                    delta = event.wheelDelta / 120;
                } else if (event.detail) {
                    delta = -event.detail / 3;
                }

                //Scroll dom
                if (delta){
                    //Scroll to
                    var t = $(window).scrollTop();
                    t -= delta * 100;

                    $(window).scrollTop(t);
                }

                scrollbar.get(0).reloadScrollHeight();

                if (opts.autohide)
                    handle.stop(true).animate({left:"0px"}, 100).delay(2000).animate({left:bar_width+"px"}, 100);

                if (event.preventDefault)
                    event.preventDefault();

                event.returnValue = false;
            }

            if (window.addEventListener) {
                /* DOMMouseScroll is for mozilla. */
                window.addEventListener('DOMMouseScroll', wheel, false);
            }

            /* IE/Opera. */
            if(window.onmousewheel)
                window.onmousewheel = wheel;
            else
                document.onmousewheel = wheel;
        }


        // add listener
        this[0].reloadScrollHeight = function() {
            var h = Math.max(window.document.documentElement.scrollHeight, window.document.body.scrollHeight);

            if( h == _container.height() ) {
                scrollbar.hide();
                return;
            }

            scrollbar.show();

            // size;
            scrollbar.height(_container.height());
            handle.height(_container.height() * _container.height() / h);

            // top;
            var paneH    = _container.height();
            var handleH  = handle.height();
            var rangeH   = scrollbar.height() - handleH;
            var top = _content.scrollTop() * rangeH / (h - paneH);
            handle.css("top", top);
        }

        this[0].reloadScrollHeight();
        return this;
    }

})(jQuery);
