/**
 * http://www.jquerybyexample.net/2012/06/get-url-parameters-using-jquery.html
 * 
 * @param  {string} sParam
 * @return {string}       
 */
var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};

/**
 * Check if server is online or not
 * 
 * @param  {function} ifOnline  online callback
 * @param  {function} ifOffline offline callback
 * @param  {string}   checkSrc  check img src
 * @return {boolean}           
 */
var ifServerOnline = function (ifOnline, ifOffline, checkSrc) {
    var img = document.body.appendChild(document.createElement('img'));
    
    img.style.display = 'none';

    img.onload = function() {
        return (ifOnline && Function === ifOnline.constructor) ? ifOnline() : false;        
    };

    img.onerror = function() {
        return (ifOffline && Function === ifOffline.constructor) ? ifOffline() : false;
    };

    img.src = checkSrc;        
};