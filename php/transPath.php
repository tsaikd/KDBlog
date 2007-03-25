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

/*
function transPathR2V($path, $type) {
	global $BLOGCONF;

	switch ($type) {
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
*/

?>
