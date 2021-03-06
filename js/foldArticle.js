function foldArticle(id) {
	var obj = document.getElementById(id);
	if (!obj)
		return;

	var node;

	if (obj.attr_fold != true) {
		// change toolbar to unfold
		node = obj.getElementsByTagName("div")[0];
		node = findChildByName(node, "fold");
		node.innerHTML = lang.article.toolbar.unfold;

		// fold article
		// article menu
		if (obj.getElementsByTagName("span").length)
			obj.getElementsByTagName("span")[0].style.display = "none";
		// contents
		obj.getElementsByTagName("pre")[0].style.display = "none";
		// comments
		node = findChildByName(obj, "comments");
		if (node)
			node.style.display = "none";

		obj.attr_fold = true;
	} else {
		// change toolbar to fold
		node = obj.getElementsByTagName("div")[0];
		node = findChildByName(node, "fold");
		node.innerHTML = lang.article.toolbar.fold;

		// unfold article
		// article menu
		if (obj.getElementsByTagName("span").length)
			obj.getElementsByTagName("span")[0].style.display = "block";
		// contents
		obj.getElementsByTagName("pre")[0].style.display = "block";
		// comments
		node = findChildByName(obj, "comments");
		if (node)
			node.style.display = "block";

		obj.attr_fold = false;
	}
}

