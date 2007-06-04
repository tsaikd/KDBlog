<?php
include_once("php/urlescape.php");

function macro_kdgallery($node, $type) {
	if ($node["tag"] != "kdgallery")
		return "";
	if ($node["type"] != "complete")
		return "";
	if (!$node["attributes"]["src"])
		return "";

	global $CONF;
	global $LANG;

	$kdgurl = $node["attributes"]["host"];
	if (!isset($kdgurl))
		$kdgurl = $CONF["func"]["kdgallery"]["url"];
	if (!isset($kdgurl))
		return "macro_kdgallery: ".$LANG["message"]["funcNotConf"];

	$w = $node["attributes"]["w"];
	if (!isset($w))
		$w = 640;

	$h = $node["attributes"]["h"];
	if (!isset($h))
		$h = 480;

	$vpath = $node["attributes"]["src"];

	$imgurl  = "http://pic.tsaikd.org/data.php?ftype=image&w=".$w."&h=".$h;
	$imgurl .= "&fpath=".urlescape($vpath);
	$imglink = "http://pic.tsaikd.org/?picpath=".urlescape($vpath);


/*
	if ($type == "rss") {
//*/
		$res = "<a href='$imglink'><img src='$imgurl' /></a>";
/*
	} else { // $type == "html"
		$res = "<a href='$imglink'><img src='$imgurl' /></a>";
	}
//*/

	return $res;
}

?>
