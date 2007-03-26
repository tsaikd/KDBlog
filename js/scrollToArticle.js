function scrollToArticle(obj) {
	var pTop = obj.offsetTop;
	var pHeight = obj.scrollHeight;
	var pBottom = pTop + obj.scrollHeight;
	var wTop = document.body.scrollTop;
	var wHeight = document.body.clientHeight;
	var wBottom = wTop + document.body.clientHeight;

	if (pTop < wTop) {
		document.body.scrollTop = pTop;
	} else if (pBottom > wBottom) {
		if (pHeight > wHeight)
			document.body.scrollTop = pTop;
		else
			document.body.scrollTop = pTop + pHeight - wHeight;
	}
}

