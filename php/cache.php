<?php
include_once("config.php");

if (!isset($CONF["path"]["cache"]))
	die('$CONF["path"]["cache"] is not set');
if (!isset($CONF["func"]["cache"]["maxSize"]))
	die('$CONF["func"]["cache"]["maxSize"] is not set');

function touch_state_file($name, $offset=2) {
	global $CONF;
	$path = $CONF["state"][$name];

	$t = time() + $offset;
	$fp = fopen($path, "w");
	fwrite($fp, $t);
	fclose($fp);
	touch($path, $t);
}

function is_state_old($name) {
	global $CONF;
	$path = $CONF["state"][$name];

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
	global $CONF;
	$path = $CONF["state"][$name];
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
return false if regenerate cache file, else true
$cInfo (array) include:
input:
	"enable"			=> bool
	"cachePath"			=> string path
	"bSendHeader"		=> bool send cache header (option)
	"isValidCacheProc"	=> function callback with param $cInfo (option)
	"preShowProc"		=> function callback with param $cInfo (option)
	"showDataProc"		=> function callback with param $cInfo
addition:
	"doCache"			=> bool regenerate cache file or not
*/
function getGenCache(&$cInfo) {
	global $CONF;
	global $logfp;

	if (!$cInfo["enable"] || !$CONF["cache"]["enable"]) {
		if ($cInfo["bSendHeader"]) {
			header('Cache-Control: no-cache');
			header('Pragma: no-cache');
			header('Expires: 0');
		}
		return $cInfo["showDataProc"]($cInfo);
	}

	if (!file_exists($cInfo["cachePath"]))
		$doCache = true;
	else if ($cInfo["isValidCacheProc"] && !$cInfo["isValidCacheProc"]($cInfo))
		$doCache = true;
	else
		$doCache = false;
	$cInfo["doCache"] = $doCache;

	if ($cInfo["preShowProc"])
		$cInfo["preShowProc"]($cInfo);

	if ($cInfo["bSendHeader"]) {
		if ($doCache)
			$ftime = time();
		else
			$ftime = filectime($cInfo["cachepath"]);
		header('Last-Modified: '.date(DATE_RFC2822, $ftime));
		header('Expires: '.date(DATE_RFC2822, $ftime+86400));
	}

	if ($doCache) {
		if (file_exists($cInfo["cachePath"]))
			rmCacheInfo($cInfo["cachePath"]);
		$tmpfname = tempnam($CONF["path"]["cache"], "_cache_tmp_");
		$logfp = fopen($tmpfname, "w");
		$cInfo["showDataProc"]($cInfo);
		fclose($logfp);
		$logfp = null;

		if (filesize($tmpfname)) {
			rename($tmpfname, $cInfo["cachePath"]);
			touch($cInfo["cachePath"]);
			addCacheInfo($cInfo["cachePath"]);
		} else {
			unlink($tmpfname);
		}
	} else {
		readfile($cInfo["cachePath"]);
	}

	return !$doCache;
}

function cleanCache() {
	include_once("php/rm_ex.php");
	global $CONF;

	$path = $CONF["path"]["cache"];
	$res = true;

	$d = dir($path);
	while ($f = $d->read()) {
		if (substr($f, -6) == ".cache")
			rm_ex("$path/$f");
	}
	$d->close();

	return $res;
}

function addCacheInfo($cpath) {
	global $CONF;

	if (!file_exists($cpath))
		return false;

	$infopath = $CONF["path"]["cache"]."/_cache_info.cache";
	$lockpath = $infopath.".lock";
	$maxSize = $CONF["func"]["cache"]["maxSize"];
	$fsize = filesize($cpath);
	$tc = 0;

	if (!file_exists($infopath)) {
		$fp = fopen($infopath, "w");
		if (!$fp)
			return false;
		fwrite($fp, sprintf("%-19d\n", $fsize+20+strlen($cpath)+1));
		fwrite($fp, $cpath."\n");
		fclose($fp);
		return true;
	}

	while (file_exists($lockpath) && ($tc < 10)) {
		$tc++;
		sleep(1);
	}
	if ($tc >= 10)
		return false;

	touch($lockpath);
	$farray = file($infopath);
	$iTotalSize = (int)array_shift($farray) + $fsize + strlen($cpath)+1;

	if (($iTotalSize > $maxSize) && ($maxSize > 0)) {
		while (($iTotalSize > (int)($maxSize*2/3)) && (count($farray))) {
			$rmpath = trim(array_shift($farray));
			$iTotalSize = $iTotalSize - @filesize($rmpath) - strlen($rmpath)-1;
			@unlink($rmpath);
		}
	}

	$fp = fopen($infopath, "w");
	fwrite($fp, sprintf("%-19d\n", $iTotalSize));
	foreach ($farray as $f)
		fwrite($fp, $f);
	fwrite($fp, $cpath."\n");
	fclose($fp);
	unlink($lockpath);
	return true;
}

function rmCacheInfo($cpath) {
	global $CONF;

	if (!file_exists($cpath))
		return false;

	$infopath = $CONF["path"]["cache"]."/_cache_info.cache";
	$lockpath = $infopath.".lock";
	$fsize = filesize($cpath);
	$tc = 0;
	unlink($cpath);

	if (!file_exists($infopath)) 
		return false;

	while (file_exists($lockpath) && ($tc < 10)) {
		$tc++;
		sleep(1);
	}
	if ($tc >= 10)
		return false;

	touch($lockpath);
	$farray = file($infopath);
	$iTotalSize = (int)array_shift($farray) - $fsize - strlen($cpath)-1;
	$k = array_search($cpath."\n", $farray);
	if ($k === false) {
		unlink($lockpath);
		return false;
	}
	unset($farray[$k]);

	$fp = fopen($infopath, "w");
	fwrite($fp, sprintf("%-19d\n", $iTotalSize));
	foreach ($farray as $f)
		fwrite($fp, $f);
	fclose($fp);
	unlink($lockpath);
	return true;
}
?>
