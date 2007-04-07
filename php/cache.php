<?php
include_once("config.php");

function only_true() { return true; }
function only_false() { return false; }

function touch_state_file($name, $offset=2) {
	global $BLOGCONF;
	$path = $BLOGCONF["state"][$name];

	$t = time() + $offset;
	$fp = fopen($path, "w");
	fwrite($fp, $t);
	fclose($fp);
	touch($path, $t);
}

function is_state_old($name) {
	global $BLOGCONF;
	$path = $BLOGCONF["state"][$name];

	if (!file_exists($path))
		return true;

	$stime = (int)file_get_contents($path);
	$ftime = filectime($path);
	if ($ftime > $stime)
		return true;
	else
		return false;
}

function set_state_old($name) {
	global $BLOGCONF;
	$path = $BLOGCONF["state"][$name];
	touch($path);
}

/*
flag:
	0x01: log
	0x02: echo
*/
function logecho($text, $flag=0x03) {
	global $logfp;
	if ($logfp && ($flag & 0x01))
		fwrite($logfp, $text);
	if ($flag & 0x02)
		echo $text;
}

function isCache($name) {
	global $BLOGCONF;

	if (!$BLOGCONF["cache"]["enable"])
		return false;
	if (!$BLOGCONF["cache"][$name]["enable"])
		return false;

	return true;
}

function getCache($name) {
	global $BLOGCONF;
	global $logfp;

	$enableCache = isCache($name);
	$cpath = $BLOGCONF["cache"][$name]["cachePath"];
	$checkProc = $BLOGCONF["cache"][$name]["isValidCacheProc"];
	$showProc = $BLOGCONF["cache"][$name]["showDataProc"];

	if (!$enableCache)
		return $showProc();

	$doCache = false;
	if (!file_exists($cpath))
		$doCache = true;
	else if (!$checkProc())
		$doCache = true;

	if ($doCache) {
		$tmpfname = tempnam($BLOGCONF["cachpath"], "_cache_tmp_".$name);
		$logfp = fopen($tmpfname, "w");
		$showProc();
		fclose($logfp);
		rename($tmpfname, $cpath);
		$logfp = null;
		touch($cpath);
	} else {
		readfile($cpath);
	}
}

/*
$cInfo (array) include:
	"enable"			=> bool
	"cachePath"			=> string path
	"isValidCacheProc"	=> function callback with param $cInfo (option)
	"showDataProc"		=> function callback with param $cInfo
*/
function getGenCache($cInfo) {
	global $BLOGCONF;
	global $logfp;

	if (!$cInfo["enable"])
		return $cInfo["showDataProc"]($cInfo);

	if (!file_exists($cInfo["cachePath"]))
		$doCache = true;
	else if ($cInfo["isValidCacheProc"] && !$cInfo["isValidCacheProc"]($cInfo))
		$doCache = true;
	else
		$doCache = false;

	if ($doCache) {
		$tmpfname = tempnam($BLOGCONF["cachpath"], "_cache_tmp_");
		$logfp = fopen($tmpfname, "w");
		$cInfo["showDataProc"]($cInfo);
		fclose($logfp);
		$logfp = null;

		if (filesize($tmpfname)) {
			rename($tmpfname, $cInfo["cachePath"]);
			touch($cInfo["cachePath"]);
		} else {
			unlink($tmpfname);
		}
	} else {
		readfile($cInfo["cachePath"]);
	}
}

function cleanCache() {
	include_once("php/rm_ex.php");
	global $BLOGCONF;

	$path = $BLOGCONF["cachpath"];
	$res = true;

	$d = dir($path);
	while ($f = $d->read()) {
		if (substr($f, -6) == ".cache")
			rm_ex("$path/$f");
	}
	$d->close();

	return $res;
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
