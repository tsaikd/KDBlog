function getArticleToolBar(id) {
	var node = document.createElement("div");
	node.setAttribute("name", "toolbar");
	node.setAttribute("class", "toolbar");

	var showText = "";

	if (blog.conf.func.comment.enable)
	showText += "<a name='comment' onfocus='this.blur()' class='button' href='javascript:commentArticle(\""+id+"\")'>"+blog.lang.article.toolbar.comment+"<\/a> ";

	showText += "<a name='fold' onfocus='this.blur()' class='button' href='javascript:foldArticle(\""+id+"\")'>"+blog.lang.article.toolbar.fold+"<\/a> ";

	showText += "<a name='close' onfocus='this.blur()' class='button' href='javascript:closeArticle(\""+id+"\")'>"+blog.lang.article.toolbar.close+"<\/a>";

	node.innerHTML = showText;
	return node;
}

