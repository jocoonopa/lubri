function $blockUI (message, css) {
	var message = message || 'Please wait...';
	var css = css || { 
        border: 'none', 
        padding: '15px', 
        backgroundColor: '#009688', 
        '-webkit-border-radius': '10px', 
        '-moz-border-radius': '10px', 
        opacity: 1, 
        color: '#fff' 
    };

	return $.blockUI({message: message, css: css}); 
}

function $importBlockUI (message, css) {
    var message = message || 'Please wait...';
    var css = css || { 
        border: 'none', 
        padding: '15px', 
        backgroundColor: '#009688', 
        '-webkit-border-radius': '10px', 
        '-moz-border-radius': '10px', 
        opacity: 1, 
        color: '#fff' 
    };

    return $.blockUI({message: '<div class="progress progress-striped active"><div class="progress-bar progress-bar-warning" style="width: 45%"></div></div><p class="message">' + message + '</p>', css: css}); 
}