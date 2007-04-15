<?php
include_once("php/getDir.php");
include_once("php/isValidPath.php");
include_once("php/transPath.php");

/*
$flag["realpath"] := (bool) return real path array
*/
function getRecentArticlePath($limit=-1, $flag=null, $vpath="data", &$aRes=null) {
	if ($aRes == null)
		$aRes = array();
	if ((count($aRes) >= $limit) && ($limit >= 0))
		return $aRes;

	$dpath = transPathV2R($vpath);
	$aBuf = getDir($dpath);

	while ($f = array_pop($aBuf)) {
		if (is_dir("$dpath/$f")) {
			getRecentArticlePath($limit, $flag, "$vpath/$f", $aRes);
		} else {
			if (!isValidArticlePath("$vpath/$f"))
				continue;
			if ($flag["realpath"])
				array_push($aRes, "$dpath/$f");
			else
				array_push($aRes, "$vpath/$f");
			if ((count($aRes) >= $limit) && ($limit >= 0))
				return $aRes;
		}
	}

	return $aRes;
}
?>
