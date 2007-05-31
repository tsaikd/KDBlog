<?php
/*
$name:
	"cssInside"
	"jsInside"
*/
function getCacheIndex($name) {
	global $CONF;

	$cInfo = array();
	$cInfo["enable"] = $CONF["cache"][$name]["enable"];
	$cInfo["cachePath"] = $CONF["path"]["cache"]."/".$name.".cache";
	$cInfo["preShowProc"] = "preShowData";
	if ($name == "cssInside")
		$cInfo["showDataProc"] = "showCssData";
	else if ($name == "jsInside")
		$cInfo["showDataProc"] = "showJsData";
	else
		return;

	return getGenCache($cInfo);
}

function preShowData($cInfo) {
	global $CONF;

	if ($CONF["func"]["debug"]["enable"]) {
		header('Cache-Control: no-cache');
		header('Pragma: no-cache');
		header('Expires: 0');
		return;
	}

	if ($cInfo["doCache"])
		$ftime = time();
	else
		$ftime = filectime($cInfo["cachePath"]) - 5;
	header('Last-Modified: '.date(DATE_RFC2822, $ftime));
	header('Expires: '.date(DATE_RFC2822, $ftime+86400));
}

function showCssData($cInfo) {
	include_once("php/getDir.php");

	$path = "css";
	$farray = getDir($path);
	foreach ($farray as $f) {
		if (strtolower(substr($f, -4)) != ".css")
			continue;
		logecho(file_get_contents("$path/$f"));
	}
}

function showJsData($cInfo) {
	include_once("php/getDir.php");

	$path = "js";
	$farray = getDir($path);
	foreach ($farray as $f) {
		if (strtolower(substr($f, -3)) != ".js")
			continue;
		logecho(file_get_contents("$path/$f"));
	}
}

?>
