/* CENTER ELEMENTS */
(function($) {
    "use strict";
    jQuery.fn.center = function(options) {
        options = $.extend(true, {}, options);

        if (options.parent) {
            parent = this.parent().css('position', 'relative');
        } else {
            parent = window;
        }
        this.css({
            // "position": "absolute",
            "top": options.top === 'top' ? 0 : options.top ? options.top : ((($(parent).height() - this.outerHeight()) / 2) + $(parent).scrollTop() + "px"),
            "left": options.left === 'left' ? 0 : options.left ? options.left : ((($(parent).width() - this.outerWidth()) / 2) + $(parent).scrollLeft() + "px")
        });
        return this;
    }
}(jQuery));

(function ($) {
    /* Twitter Bootstrap Message Helper
    ** Usage: Just select an element with `alert` class and then pass this object for options.
    ** Example: $("#messagebox").message("Hello world!", {type: "error"});
    ** Author: Afshin Mehrabani <afshin.meh@gmail.com>
    ** Date: Monday, 08 October 2012
    */
    $.fn.message = function(text, type, delay, callback) {
        //remove all previous bootstrap alert box classes
        var $this = this;
        var top;

        if(this.data('timer')) {
            clearTimeout(this.data('timer'));
            this.timer = null;
        }
        this[0].className = this[0].className.replace(/alert-(success|error|warning|info|danger)/g , '');
        this.html(text).addClass('alert-' + (type || 'error'));
        if($('header.header').size()) {
            top = $('header.header').height();
        }
        else {
            top = 'top';
        }
        this.fadeIn('fast').center({top:top});

        this.data('timer', setTimeout(function() {
            $this.fadeOut('fast');
        }, delay || 3000));
    };
})(jQuery);

