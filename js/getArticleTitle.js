function getArticleTitle(xmldoc, fpath) {
	var title;
	var showObj = document.createElement("h1");
	var node = xmldoc.getElementsByTagName('title')[0];
	if (node) {
		title = getNodeText(node);
		if (title != "") {
			showObj.innerHTML = title;
			return showObj;
		}
	}
	showObj.innerHTML = fpath+": "+blog.lang.article.notitle;
	return showObj;
}

