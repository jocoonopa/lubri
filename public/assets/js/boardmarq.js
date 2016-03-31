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
        return window.location.href= '/board/marq?offset=' + self.offset + '&timeout=' + self.timeout;
    }, function () {
        return self.run();
    }, self.src);  
};

BoardMarq.prototype.run = function () {
    var self = this;

    return setTimeout(function () { return self.refreshPage();}, self.timeout * 1000);
};