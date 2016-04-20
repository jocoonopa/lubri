/**
 * 業績廣播頁面刷新模組
 *
 * @depend jQuery, helper.js
 * 
 * @param  {object}
 * @return {this}
 */
var BoardMarq = (function (options) {
    this.minTimeout = 3; /*seconds*/

    return this.init(options);
});

BoardMarq.prototype.init = function (options) {
    var defaultOptions = {
        "timeout": 10,
        "offset": 0,
        "src": "/assets/image/favicon.png?v=" + Math.random()
    };
    var options = $.extend({}, defaultOptions, options);

    return this.setAttributes(options);
};

BoardMarq.prototype.setAttributes = function (options) {
    this.timeout = (this.minTimeout > options.timeout) ? this.minTimeout : options.timeout;
    this.offset = options.offset;
    this.src = options.src;

    return this;
};

BoardMarq.prototype.refreshPage = function () {
    var self = this;

    return ifServerOnline(function() {
        return true === self.atOnboardTime() ? self.setLocationHref() : self.run();
    }, function () {
        return self.run();
    }, self.src);  
};

// 因為我的電腦22:02 固定會噴500錯誤...
// 所以 21:55 ~ 22:10 停止刷新頁面的動作
// 之後若是換電腦運行可取消這段判斷程式碼
// 
// - Jocoonopa@20160412
BoardMarq.prototype.atOnboardTime = function () {
    var date       = new Date();
    var curHour    = parseInt(date.getHours());
    var curMinutes = parseInt(date.getMinutes());
    
    if ((21 === curHour && 55 <= curMinutes) || (22 === curHour && 10 >= curMinutes)) {
        return false;
    }

    return true;
};

BoardMarq.prototype.setLocationHref = function () {
    var self = this;

    return $.get('/is_alive').done(function (res) {
        if (1 === parseInt(res)) {
            return window.location.href= '/board/marq?offset=' + self.offset + '&timeout=' + self.timeout;
        }

        return self.run();
    }).fail(function (e) {
        console.log(e);
        return self.run();
    });    
};

BoardMarq.prototype.run = function (specTimeoutSeconds) {
    var self = this;
    var specTimeoutSeconds = specTimeoutSeconds || self.timeout;

    return setTimeout(function () { return self.refreshPage();}, specTimeoutSeconds * 1000);
};

$(window, 'body').resize(function () {
    var fontSize = 30;
    var windowWidth = $(window).width();

    if (windowWidth >= 1700) {
        fontSize = 70;
    } else if (windowWidth >= 1200) {
        fontSize = 50;
    } else if (windowWidth >= 800){
        fontSize = 30;
    } else if (windowWidth >= 400){
        fontSize = 18;
    } else {
        fontSize = 12;
    }

    return $('.container').css('font-size', fontSize);
}).resize();