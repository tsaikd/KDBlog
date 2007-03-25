<?php
include_once("php/getDir.php");
include_once("php/isValidArticlePathName.php");

function getRecentArticlePath($path, $numLimit, $level=3, &$aRes=null) {
	if ($aRes == null)
		$aRes = array();
	if ((count($aRes) >= $numLimit) && ($numLimit >= 0))
		return $aRes;
	if (!is_dir($path))
		return $aRes;

	$aBuf = getDir($path);

	while ($f = array_pop($aBuf)) {
		if ($level <= 1) {
			if (!isValidArticlePathName($f))
				continue;
			array_push($aRes, "$path/$f");
			if ((count($aRes) >= $numLimit) && ($numLimit >= 0))
				return $aRes;
		} else {
			getRecentArticlePath("$path/$f", $numLimit, $level-1, $aRes);
		}
	}

	return $aRes;
}

?>
