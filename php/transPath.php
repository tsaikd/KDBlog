<?php
include_once("config.php");

function transPathV2R($path) {
	global $BLOGCONF;

	$pinfo = explode("/", $path);
	$f = array_shift($pinfo);

	switch ($f) {
	case "data":
		$res = $BLOGCONF["datapath"];
		break;
	case "tags":
		$res = $BLOGCONF["tagspath"];
		break;
	case "comment":
		$res = $BLOGCONF["cmntpath"];
		break;
	case "special":
		$res = $BLOGCONF["specpath"];
		break;
	default:
		return "";
	}

	while ($f = array_shift($pinfo))
		$res .= "/".$f;

	return $res;
}

function transPathR2V($path, $type) {
	global $BLOGCONF;

	switch ($type) {
	case "auto":
		$res = transPathR2V($path, "data");
		if ($res != "")
			return $res;
		$res = transPathR2V($path, "tags");
		if ($res != "")
			return $res;
		$res = transPathR2V($path, "special");
		if ($res != "")
			return $res;
		return "";
	case "data":
		$chkpath = $BLOGCONF["datapath"];
		$vpath = "data";
		break;
	case "tags":
		$chkpath = $BLOGCONF["tagspath"];
		$vpath = "tags";
		break;
	case "special":
		$chkpath = $BLOGCONF["specpath"];
		$vpath = "special";
		break;
	default:
		return "";
	}

	$chklen = strlen($chkpath);
	if (0 != strncmp($path, $chkpath, $chklen))
		return "";

	return $vpath.substr($path, $chklen);
}

function transPathV2Id($vpath) {
	$path = $vpath;
	$path = str_replace("/", "__", $path);
	$path = str_replace(".", "____", $path);
	return $path;
}

function transPathVTag2VData($path) {
	$pinfo = explode("/", $path);
	$f = array_shift($pinfo);
	if ($f != "tags")
		return "";

	array_shift($pinfo);
	$res = "data/".implode("/", $pinfo);
	return $res;
}

function transPathVData2Date($path) {
	$buf = explode("/", $path);
	return $buf[1]."/".$buf[2]."/".substr($buf[3], 0, 2);
}

?>
