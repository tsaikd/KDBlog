<?php
include_once("php/transPath.php");
include_once("php/getDir.php");
include_once("php/getArticleTitle.php");
include_once("php/isValidPath.php");

/*
$flag["hideArticleNum"] := (bool) hide article number
$flag["reverse"] := (bool) reverse the array
*/
function showDataDir($vpath, $flag=null) {
	$dpath = transPathV2R($vpath);
	$farray = getDir($dpath);

	if ($flag["reverse"])
		rsort($farray);

	while ($f = array_pop($farray)) {
		if (is_file("$dpath/$f")) {
			if (substr($vpath, 0, 5) == "tags/")
				$vfpath = transPathVTag2VData("$vpath/$f");
			else
				$vfpath = "$vpath/$f";
			if (!isValidArticlePath("$vfpath"))
				continue;

			logecho("<a class='menufile'");
			logecho(" onfocus='javascript:this.blur()'");
			logecho(" href='javascript:showArticle(\"$vfpath\", 0x01)'>");
			if (!$flag["hideArticleNum"])
				logecho($f[0].$f[1]." - ");
			logecho(getArticleTitle("$vfpath"));
			logecho("</a>");
			continue;
		}

		logecho("<a class='menutext'");
		logecho(" onfocus='javascript:this.blur()'");
		logecho(" onclick='javascript:showMenuTabDir(this, \"$vpath/$f\")'");
		logecho(" href='javascript:;'>");
		logecho($f);
		logecho("</a>");
		logecho("<div class='menudir'></div>");
	}
}
?>
