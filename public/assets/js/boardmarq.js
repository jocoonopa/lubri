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
        "src": "/assets/image/favicon.png?v=" + Math.random(),
        "size": 0
    };
    var options = $.extend({}, defaultOptions, options);

    return this.setAttributes(options).resize();
};

BoardMarq.prototype.setAttributes = function (options) {
    this.timeout = (this.minTimeout > options.timeout) ? this.minTimeout : options.timeout;
    this.offset = options.offset;
    this.src = options.src;
    this.size = options.size;

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

// 因為我的電腦0600 固定會噴500錯誤...
// 所以 21:55 ~ 22:10 停止刷新頁面的動作
// 之後若是換電腦運行可取消這段判斷程式碼
// 
// - Jocoonopa@20160412
BoardMarq.prototype.atOnboardTime = function () {
    var date       = new Date();
    var curHour    = parseInt(date.getHours());
    var curMinutes = parseInt(date.getMinutes());
    
    if (21 <= curHour && 8 >= curHour) {
        return false;
    }

    return true;
};

BoardMarq.prototype.setLocationHref = function () {
    return window.location.href= '/board/marq?offset=' + this.offset + '&timeout=' + this.timeout + '&size=' + this.size;
};

BoardMarq.prototype.run = function (specTimeoutSeconds) {
    var self = this;
    var specTimeoutSeconds = specTimeoutSeconds || self.timeout;

    return setTimeout(function () { return self.refreshPage();}, specTimeoutSeconds * 1000);
};

BoardMarq.prototype.resize = function () {
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
}
