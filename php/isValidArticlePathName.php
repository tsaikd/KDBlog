<?php
function isValidArticlePathName($pathName) {
	$pinfo = pathinfo($pathName);
	if ($pinfo["basename"][0] == ".")
		return false;
	if (strtolower($pinfo["extension"]) == "xml")
		return true;

	return false;
}
?>
