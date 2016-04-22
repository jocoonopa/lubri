/**
 * 業績廣播頁面刷新模組
 *
 * @depend jQuery, helper.js
 * 
 * @param  {object}
 * @return {this}
 */
var BoardMarqCti = (function (options) {
    this.minTimeout = 3; /*seconds*/

    return this.init(options);
});

BoardMarqCti.prototype.init = function (options) {
    var defaultOptions = {
        "timeout": 10,
        "offset": 0,
        "src": "/assets/image/favicon.png?v=" + Math.random(),
        "size": 0
    };
    var options = $.extend({}, defaultOptions, options);

    return this.setAttributes(options);
};

BoardMarqCti.prototype.setAttributes = function (options) {
    this.timeout = (this.minTimeout > options.timeout) ? this.minTimeout : options.timeout;
    this.offset = options.offset;
    this.src = options.src;
    this.size = options.size;

    return this;
};

BoardMarqCti.prototype.worker = function () {
    var self = this;

    return setTimeout(function () {
        var rowCount = $('tr._row').length;

        $('tr._row._show:lt(5)').addClass('hide').removeClass('_show');

        return (rowCount <= $('tr._row.hide').length) ? self.group() : self.worker();
    }, self.timeout * 1000);
};

BoardMarqCti.prototype.group = function () {
    var self = this;

    $('table._row').addClass('hide');
    $('table._group').removeClass('hide');

    return setTimeout(function () {
        return window.location.href = '/board/marq?timeout=' + self.timeout + '&size=' + self.size;
    }, self.timeout * 1000);
};

BoardMarqCti.prototype.resize = function () {
    if (0 === this.size) {
        $(window, 'body').resize(function () {
            var fontSize = 30;
            var windowWidth = $(window).width();

            if (windowWidth >= 1700) {
                fontSize = 70;
            } else if (windowWidth >= 1200) {
                fontSize = 50;
            } else if (windowWidth >= 800){
                fontSize = 30;
            } else if (windowWidth >= 450){
                fontSize = 18;
            } else {
                fontSize = 12;
            }

            return $('.container').css('font-size', fontSize);
        }).resize();
    }    
};