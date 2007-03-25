function scrollToArticle(obj) {
	var pTop = obj.offsetTop;
	var pBottom = pTop + obj.scrollHeight;
	var wTop = document.body.scrollTop;
	var wBottom = wTop + document.body.clientHeight;

	if ((pTop < wTop) || (pBottom > wBottom))
		document.body.scrollTop = pTop;
}

