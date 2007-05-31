<?php
include_once("config.php");

function transPathV2R($path) {
	global $CONF;

	$pinfo = explode("/", $path);
	$f = array_shift($pinfo);

	switch ($f) {
	case "data":
		$res = $CONF["path"]["data"];
		break;
	case "tags":
		$res = $CONF["path"]["tags"];
		break;
	case "comment":
		$res = $CONF["path"]["comment"];
		break;
	case "special":
		$res = $CONF["path"]["spec"];
		break;
	default:
		return "";
	}

	while ($f = array_shift($pinfo))
		$res .= "/".$f;

	return $res;
}

function transPathR2V($path, $type) {
	global $CONF;

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
		$chkpath = $CONF["path"]["data"];
		$vpath = "data";
		break;
	case "tags":
		$chkpath = $CONF["path"]["tags"];
		$vpath = "tags";
		break;
	case "special":
		$chkpath = $CONF["path"]["spec"];
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

function transPathId2V($id) {
	$res = $id;
	$res = str_replace("____", ".", $res);
	$res = str_replace("__", "/", $res);
	return $res;
}

function transPathVTag2VData($path) {
	$pinfo = explode("/", $path);
	if ($pinfo[0] != "tags")
		return "";

	$len = count($pinfo);
	if ($len < 5)
		return "";

	return "data/".$pinfo[$len-3]."/".$pinfo[$len-2]."/".$pinfo[$len-1];
}

function transPath2Date($path) {
	$buf = explode("/", $path);
	$len = count($buf);
	if ($len < 3)
		return "";
	return $buf[$len-3]."/".$buf[$len-2]."/".substr($buf[$len-1], 0, 2);
}

?>
