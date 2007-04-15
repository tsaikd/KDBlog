<?php
include_once("php/transPath.php");
include_once("php/parseXml.php");

function getArticleTitle($vpath) {
	global $BLOGLANG;

	$fpath = transPathV2R($vpath);
	$xml = parseXml($fpath);
	$index = $xml["index"];
	$vals = $xml["vals"];
	$title = $vals[$index["title"][0]]["value"];

	if (!$title)
		$title = $BLOGLANG["article"]["notitle"];

	return $title;
}
?>
