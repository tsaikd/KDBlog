function selectArticle(obj) {
	if (obj == conf.currentArticle)
		return;
	unSelectAllArticle();
	if (obj) {
		conf.currentArticle = obj;
		conf.currentArticle.className = "article selected";
	}
}

function unSelectAllArticle() {
	if (conf.currentArticle) {
		conf.currentArticle.className = "article";
		conf.currentArticle = null;
	}
}

