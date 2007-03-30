function getArticleTagsMenu(xmldoc, fpath) {
	var node;
	var bufNode;
	var showObj = document.createElement("span");
	showObj.setAttribute("class", "articlemenu");

	// show tags
	node = xmldoc.getElementsByTagName('tag');
	if (node.length) {
		var tagsNode = document.createElement("div");
		tagsNode.setAttribute("class", "tagsmenu");
		tagsNode.innerHTML = blog.lang.article.tags+":";
		tagsNode.appendChild(document.createElement("br"));

		for (i=0 ; i<node.length ; i++) {
			buf = getNodeText(node[i]);
			bufNode = document.createElement("a");
			bufNode.setAttribute("class", "tags");
			bufNode.setAttribute("onfocus", "javascript:this.blur()");
			bufNode.setAttribute("href", "javascript:chgMenuTag('menutab_Tags', '"+buf+"')");
			bufNode.innerHTML = buf;
			tagsNode.appendChild(bufNode);
			tagsNode.appendChild(document.createElement("br"));
		}

		showObj.appendChild(tagsNode);
	}

	// show date
	buf = transPath2Date(fpath);
	if (buf) {
		bufNode = document.createElement("div");
		bufNode.setAttribute("class", "articledate");
		bufNode.innerHTML = buf;
		showObj.appendChild(bufNode);
	}

	return showObj;
}

