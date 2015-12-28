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