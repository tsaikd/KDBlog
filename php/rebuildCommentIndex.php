<?php
include_once("php/makeCommentIndex.php");

/**
 * $limitCommentIndexNum == -1 : unlimit comment index number
 * $limitCommentIndexNum == -2 : load limit number from config.php
 * @note
 *   This function will not remove old indexes
 *   , you can remove index directory before run this function
 **/
function rebuildCommentIndex($limitCommentIndexNum = -2) {
	global $CONF;

	if ($limitCommentIndexNum == -2)
		$limitCommentIndexNum = $CONF["func"]["comment"]["indexNum"];

	$dpath = $CONF["func"]["comment"]["indexByTime"];
	if (!file_exists($dpath)) {
		include_once("php/mkdir_ex.php");
		mkdir_ex($dpath) || exit(1);
	}

	include_once("php/getRecentArticlePath.php");
	$faart = getRecentArticlePath();

	include_once("php/getArticleCommentPath.php");
	foreach ($faart as $fart) {
		$facom = getArticleCommentPath($fart);
		if (count($facom) < 1)
			continue;
		foreach ($facom as $fcom) {
			makeCommentIndex($fcom);
		}
	}
}
?>
