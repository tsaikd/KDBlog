<?php
include_once("config.php");
include_once("php/isValidPath.php");
include_once("php/isValidArticlePathName.php");
include_once("php/transPath.php");

function runSpecFile($fpath) {
	global $BLOGLANG;

	$xml = xml_parser_create("UTF-8");
	xml_parse_into_struct($xml, file_get_contents($fpath), $vals, $index);
	xml_parser_free($xml);

	echo '<?xml version="1.0" encoding="utf-8" ?>';

	if ($vals[$index["SPECTYPE"][0]]["value"] != "php") {
		echo "<root><error>".$BLOGLANG["special"]["badSpecType"]."</error></root>";
		return;
	}

	$contents = $vals[$index["CONTENTS"][0]]["value"];
	if (!$contents) {
		echo "<root><error>".$BLOGLANG["special"]["noSpecContents"]."</error></root>";
		return;
	}

	$code = $vals[$index["CODE"][0]]["value"];
	if (!$code) {
		echo "<root><error>".$BLOGLANG["special"]["noSpecCode"]."</error></root>";
		return;
	}

	echo "<root>";
	eval($code);
	echo "</root>";
}

function showDataError($ftype, $fpath, $msg) {
	header("Content-Type: text/xml");
	echo '<?xml version="1.0" encoding="utf-8" ?>';
	echo '<root>';
	echo "<error type='$ftype' path='$fpath'>".$msg."</error>";
	echo '</root>';
}

function getCacheMenuTab($name) {
	global $BLOGCONF;

	$cInfo = array();
	$cInfo["enable"] = $BLOGCONF["cache"][$name]["enable"];
	$cInfo["cachePath"] = $BLOGCONF["cachpath"]."/".$name.".cache";

	switch ($name) {
	case "menutab_Recent":
		$cInfo["limit"] = $BLOGCONF["numRecent"];
	case "menutab_All":
	case "menutab_Tags":
		$cInfo["isValidCacheProc"] = "isValidCache_".$name;
	case "menutab_Spec":
		$cInfo["showDataProc"] = "showData_".$name;
		break;
	default:
		return;
	}

	return getGenCache($cInfo);
}

function getCacheMenuTabShowDir() {
	include_once("php/transPath.php");
	global $BLOGCONF;

	$name = "menutab_showDir";
	$cInfo = array();
	$cInfo["enable"] = $BLOGCONF["cache"][$name]["enable"];
	$cInfo["vpath"] = $_REQUEST["fpath"];
	$cInfo["dpath"] = transPathV2R($cInfo["vpath"]);
	$cInfo["id"] = transPathV2Id($cInfo["vpath"]);
	$cInfo["cachePath"] = $BLOGCONF["cachpath"]."/".$cInfo["id"].".cache";
	$cInfo["isValidCacheProc"] = "isValidCache_".$name;
	$cInfo["showDataProc"] = "showData_".$name;

	return getGenCache($cInfo);
}

function isValidCache_menutab_Recent($cInfo) {
	include_once("php/getRecentArticlePath.php");
	$flag["realpath"] = true;
	$farray = getRecentArticlePath(2, $flag);
	$statetime = filectime($cInfo["cachePath"]);
	foreach ($farray as $path) {
		if (filectime($path) > $statetime)
			return false;
	}
	return true;
}

function isValidCache_menutab_All($cInfo) {
	return isValidCache_menutab_Recent($cInfo);
}

function isValidCache_menutab_Tags($cInfo) {
	if (is_state_old("rebuildTags") || is_state_old("scanTags"))
		return false;

	include_once("php/getRecentArticlePath.php");
	$flag["realpath"] = true;
	$farray = getRecentArticlePath(2, $flag);
	$statetime = filectime($cInfo["cachePath"]);
	foreach ($farray as $path) {
		if (filectime($path) > $statetime) {
			set_state_old("scanTags");
			return false;
		}
	}

	return true;
}

function isValidCache_menutab_showDir($cInfo) {
	include_once("php/getDir.php");

	$ctime = filectime($cInfo["cachePath"]);
	$farray = getDir($cInfo["dpath"]);
	foreach ($farray as $f) {
		$ftime = filectime($cInfo["dpath"]."/".$f);
		if ($ftime > $ctime)
			return false;
	}

	return true;
}

function showData_menutab_Recent($cInfo) {
	include_once("php/transPath.php");
	include_once("php/getRecentArticlePath.php");
	include_once("php/getArticleTitle.php");

	$limit = $cInfo["limit"];
	$farray = getRecentArticlePath($limit);
	$odate = null;
	logecho("<html><div>");
	foreach ($farray as $vpath) {
		$fdate = transPath2Date($vpath);
		if ($fdate != $odate) {
			logecho("</div>\n");
			logecho("<div class='menutext'>$fdate</div>\n");
			logecho("<div class='menudir'>\n");
		}

		logecho("<a class='menuRecentFile'");
		logecho(" onfocus='javascript:this.blur()'");
		logecho(" href='javascript:showArticle(\"$vpath\", 0x01)'>");
		$title = getArticleTitle($vpath);
		logecho($title);
		logecho("</a>\n");

		if ($fdate != $odate) {
			$odate = $fdate;
		}
	}
	logecho("</div></html>");
}

function showData_menutab_All($cInfo) {
	include_once("php/showDataDir.php");
	logecho("<html>");
	showDataDir("data");
	logecho("</html>");
}

function showData_menutab_Tags($cInfo) {
	include_once("php/showDataDir.php");
	global $BLOGCONF;

	if (is_state_old("rebuildTags")) {
		include_once("php/rm_ex.php");
		include_once("php/cleanDir.php");
		include_once("php/rebuildTags.php");
		cleanDir($BLOGCONF["tagspath"]);
		rebuildTags();
		touch_state_file("rebuildTags");
		touch_state_file("scanTags");
	} else if (is_state_old("scanTags")) {
		include_once("php/rebuildTags.php");
		rebuildTags();
		touch_state_file("scanTags");
	}

	logecho("<html>");
	showDataDir("tags");
	logecho("</html>");
}

function showData_menutab_Spec($cInfo) {
	include_once("php/showDataDir.php");
	logecho("<html>");
	$flag = array();
	$flag["hideArticleNum"] = true;
	$flag["reverse"] = true;
	showDataDir("special", $flag);
	logecho("</html>");
}

function showData_menutab_showDir($cInfo) {
	include_once("php/showDataDir.php");
	logecho("<html>");
	showDataDir($cInfo["vpath"]);
	logecho("</html>");
}

$ftype = $_REQUEST["ftype"];
switch ($ftype) {
case "article":
	$vpath = $_REQUEST["fpath"];
	$fpath = transPathV2R($vpath);
	if (!isValidPath($vpath)
		|| !isValidArticlePath($vpath)
		|| !file_exists($fpath)) {
		showDataError($ftype, $_REQUEST["fpath"], $BLOGLANG["message"]["invalidPath"]);
		break;
	}

	header('Content-type: text/html; charset=utf-8');
	include_once("php/showArticle.php");
	getCacheArticle($vpath, "html");
	break;
case "menutab_Recent":
case "menutab_All":
case "menutab_Tags":
case "menutab_Spec":
	header('Content-type: text/html; charset=utf-8');
	getCacheMenuTab($ftype);
	break;
case "menutab_showDir":
	header('Content-type: text/html; charset=utf-8');
	getCacheMenuTabShowDir();
	break;
case "runspec":
	$vpath = $_REQUEST["fpath"];
	$fpath = transPathV2R($vpath);
	if (isValidPath($vpath) && file_exists($fpath)) {
		header("Content-Type: text/xml");
		runSpecFile($fpath);
	} else {
		showDataError($ftype, $_REQUEST["fpath"], $BLOGLANG["message"]["invalidPath"]);
	}
	break;
case "comment":
	session_start();
	header("Content-Type: text/xml");
	echo '<?xml version="1.0" encoding="utf-8" ?>';
	echo '<root>';

	if (!$BLOGCONF["func"]["comment"]["enable"]) {
		echo "<error type='$ftype' ename='funcOff'>".$BLOGLANG["message"]["funcOff"]."</error>";
		echo '</root>';
		break;
	}

	if ($_SESSION["reg_num_check"] != $_REQUEST["reg_num_check"]) {
		echo "<error type='$ftype' ename='reg_num_check'>".$BLOGLANG["comment"]["errmsg"]["reg_num_check"]."</error>";
		echo '</root>';
		break;
	}
	unset($_SESSION["reg_num_check"]);

	$fpath = transPathV2R($_REQUEST["fpath"]);
	if (!isValidArticlePath($_REQUEST["fpath"])
		|| !file_exists($fpath)) {
		echo "<error type='$ftype' ename='invalidPath'>".$BLOGLANG["message"]["invalidPath"]."</error>";
		echo '</root>';
		break;
	}

	include_once("php/strSafeHtml.php");
	$comment = strSafeHtml($_REQUEST["comment"]);
	if (strlen($comment) == 0) {
		echo "<error type='$ftype' ename='emptyComment'>".$BLOGLANG["comment"]["errmsg"]["emptyComment"]."</error>";
		echo '</root>';
		break;
	}

	if (strlen($_REQUEST["user"]))
		$user = strSafeHtml($_REQUEST["user"]);
	else
		$user = null;

	include_once("php/writeArticleComment.php");
	writeArticleComment($_REQUEST["fpath"], $comment, $user);
	echo '</root>';
	break;
case "searchbot":
	if (!$BLOGCONF["func"]["sitemap"]["enable"]) {
		header('Content-type: text/html; charset=utf-8');
		echo '<html>';
		echo "<error type='$ftype' ename='funcOff'>".$BLOGLANG["message"]["funcOff"]."</error>";
		echo '</html>';
		break;
	}

	include_once("php/searchbot.php");
	header('Content-type: text/html; charset=utf-8');
	searchbot();
	break;
case "sitemap":
	if (!$BLOGCONF["func"]["sitemap"]["enable"]) {
		header('Content-type: text/html; charset=utf-8');
		echo '<html>';
		echo "<error type='$ftype' ename='funcOff'>".$BLOGLANG["message"]["funcOff"]."</error>";
		echo '</html>';
		break;
	}

	include_once("php/searchbot.php");
	header("Content-Type: text/xml");
	sitemap();
	break;
default:
	header('Content-type: text/html; charset=utf-8');
	echo "<html>type('$ftype'): Not implement</html>";
	break;
}

?>
