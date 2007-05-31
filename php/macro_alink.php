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
	list($index, $vals) = parseXml($fpath);
	$title = $vals[$index["title"][0]]["value"];
	if (!$title)
		return "";

	global $BLOGCONF;

	if ($type == "rss") {
		$res = "<a href='".$BLOGCONF["link"]."?fpath=".$vpath."'>$title</a>";
	} else { // $type == "html"
		$res  = "<a class='macro_alink'";
		$res .= " onfocus='javascript:this.blur()'";
		$res .= " href='javascript:showArticle(\"$vpath\", 0x01)'>";
		$res .= $title;
		$res .= "</a>";
	}

	return $res;
}

?>
