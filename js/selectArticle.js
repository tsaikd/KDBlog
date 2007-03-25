function selectArticle(obj) {
	if (obj == blog.conf.currentArticle)
		return;
	unSelectAllArticle();
	if (obj) {
		blog.conf.currentArticle = obj;
		blog.conf.currentArticle.className = "article selected";
	}
}

function unSelectAllArticle() {
	if (blog.conf.currentArticle) {
		blog.conf.currentArticle.className = "article";
		blog.conf.currentArticle = null;
	}
}

