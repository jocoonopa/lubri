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
// 所以特別寫下這個 2在晚上八點以後到隔天早上七點不執行資料判斷
// 之後若是換電腦運行可取消這段判斷程式碼
// 
// - Jocoonopa@20160412
BoardMarq.prototype.atOnboardTime = function () {
    var date = new Date();
    var curHour = date.getHours();
    var curMinutes = date.getMinutes();
    
    if ((21 === parseInt(curHour) && 59 <= curMinutes) || (22 === parseInt(curHour) && 6 >= curMinutes)) {
        return false;
    }

    return true;
};

BoardMarq.prototype.setLocationHref = function () {
    return window.location.href= '/board/marq?offset=' + this.offset + '&timeout=' + this.timeout;
};

BoardMarq.prototype.run = function (specTimeoutSeconds) {
    var self = this;
    var specTimeoutSeconds = specTimeoutSeconds || self.timeout;

    return setTimeout(function () { return self.refreshPage();}, specTimeoutSeconds * 1000);
};