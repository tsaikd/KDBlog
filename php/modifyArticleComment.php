<?php
include_once("php/getArticleCommentPath.php");
include_once("php/parseXml.php");
/*
$info["action"] (string)
	"unnotify" := disable email notify
$info["cname"] := (string) comment path name
*/
function modifyArticleComment($vArticlePath, $info) {
	list($fCommentPath) = getArticleCommentPath($vArticlePath, 0x02, $info);
	if (!file_exists($fCommentPath))
		return false;

	list($index, $vals) = parseXml($fCommentPath);
	$i = $index["comment"][0];

	switch($info["action"]) {
	case "unnotify":
		include_once("php/writeXml.php");
		if ($vals[$i]["attributes"]["notify"] != "notify")
			return false;
		unset($vals[$i]["attributes"]["notify"]);
		writeXml($fCommentPath, $vals);
		return true;
	default:
		return false;
	}
}
?>
