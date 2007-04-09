<?php
include_once("config.php");

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

	if (!$cInfo["enable"] || !$BLOGCONF["cache"]["enable"])
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

?>
