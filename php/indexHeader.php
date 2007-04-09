<?php
/*
$name:
	"cssInside"
	"jsInside"
*/
function getCacheIndex($name) {
	global $BLOGCONF;

	$cInfo = array();
	$cInfo["enable"] = $BLOGCONF["cache"][$name]["enable"];
	$cInfo["cachePath"] = $BLOGCONF["cachpath"]."/".$name.".cache";
	if ($name == "cssInside")
		$cInfo["showDataProc"] = "showCssData";
	else if ($name == "jsInside")
		$cInfo["showDataProc"] = "showJsData";
	else
		return;

	return getGenCache($cInfo);
}

function showCssData() {
	include_once("php/getDir.php");

	logecho("<style type=\"text/css\">");

	$path = "css";
	$farray = getDir($path);
	foreach ($farray as $f) {
		if (strtolower(substr($f, -4)) != ".css")
			continue;
		logecho(file_get_contents("$path/$f"));
	}

	logecho("</style>");
}

function showJsData() {
	include_once("php/getDir.php");

	logecho("<script type=\"text/javascript\">");

	$path = "js";
	$farray = getDir($path);
	foreach ($farray as $f) {
		if (strtolower(substr($f, -3)) != ".js")
			continue;
		logecho(file_get_contents("$path/$f"));
	}

	logecho("</script>");
}

?>
