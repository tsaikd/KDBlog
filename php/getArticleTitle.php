<?php
include_once("php/transPath.php");
include_once("php/parseXml.php");

function getArticleTitle($vpath) {
	global $LANG;

	$fpath = transPathV2R($vpath);
	list($index, $vals) = parseXml($fpath);
	$title = $vals[$index["title"][0]]["value"];

	if (!$title)
		$title = $LANG["article"]["notitle"];

	return $title;
}
?>
