<?php
include_once("php/getDir.php");
include_once("php/isValidPath.php");
include_once("php/transPath.php");

/*
$flag["realpath"] := (bool) return real path array
*/
function getRecentArticlePath($limit=-1, $flag=null, $vpath="data") {
	$aRes = array();
	$dpath = transPathV2R($vpath);
	$aBuf = getDir($dpath);

	while ($f = array_pop($aBuf)) {
		if ((count($aRes) >= $limit) && ($limit >= 0))
			return $aRes;

		if (is_dir("$dpath/$f")) {
			$a = getRecentArticlePath($limit, $flag, "$vpath/$f");
			$aRes = array_merge($aRes, $a);
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
