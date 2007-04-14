<?php
function macro_alink($node, $type) {
	if ($node["tag"] != "alink")
		return "";
	if ($node["type"] != "complete")
		return "";
	if (!$node["attributes"]["src"])
		return "";

	include_once("php/transPath.php");

	$vpath = $node["attributes"]["src"];
	$fpath = transPathV2R($vpath);
	if (!file_exists($fpath))
		return "";

	include_once("php/parseXml.php");

	$xml = parseXml($fpath);
	$index = $xml["index"];
	$vals = $xml["vals"];

	$title = $vals[$index["title"][0]]["value"];
	if (!$title)
		return "";

	global $BLOGCONF;

	if ($type == "rss") {
		$res = "<a href='".$BLOGCONF["link"]."?fpath=".$vpath."'>$title</a>";
	} else { // $type == "html"
		$res  = "<a onclick='javascript:";
		$res .= "showArticle(\"$vpath\", 0x01);";
		$res .= "this.blur();";
		$res .= "' href='javascript:;'>$title</a>";
	}

	return $res;
}

?>
