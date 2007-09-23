<?php
include_once("php/urlescape.php");
include_once("php/get_web_page.php");

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

	$imgurl  = $kdgurl."data.php?ftype=image&w=".$w."&h=".$h."&fpath=".urlescape($vpath);
	$imglink = $kdgurl."?picpath=".urlescape($vpath);

	$pinfo = get_web_page($kdgurl."data.php?ftype=picSize&fpath=".urlescape($vpath)."&w=$w&h=$h");
	$psize = $pinfo["content"];
	sscanf($psize, "%dx%d", &$rw, &$rh);
	if (($rw === null) || ($rh === null))
		$rw = $rh = 0;

/*
	if ($type == "rss") {
//*/
		if ($rw && $rh)
			$res = "<a href='$imglink'><img src='$imgurl' width='$rw' height='$rh' /></a>";
		else
			$res = "<a href='$imglink'><img src='$imgurl' /></a>";
/*
	} else { // $type == "html"
		$res = "<a href='$imglink'><img src='$imgurl' /></a>";
	}
//*/

	return $res;
}

?>
