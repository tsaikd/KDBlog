<?php
include_once("config.php");
include_once("php/isValidPath.php");
include_once("php/isValidArticlePathName.php");
include_once("php/transPath.php");

function runSpecFile($fpath) {
	global $LANG;

	$xml = xml_parser_create("UTF-8");
	xml_parse_into_struct($xml, file_get_contents($fpath), $vals, $index);
	xml_parser_free($xml);

	echo '<?xml version="1.0" encoding="utf-8" ?>';

	if ($vals[$index["SPECTYPE"][0]]["value"] != "php") {
		echo "<root><error>".$LANG["special"]["badSpecType"]."</error></root>";
		return;
	}

	$contents = $vals[$index["CONTENTS"][0]]["value"];
	if (!$contents) {
		echo "<root><error>".$LANG["special"]["noSpecContents"]."</error></root>";
		return;
	}

	$code = $vals[$index["CODE"][0]]["value"];
	if (!$code) {
		echo "<root><error>".$LANG["special"]["noSpecCode"]."</error></root>";
		return;
	}

	echo "<root>";
	eval($code);
	echo "</root>";
}

function showMsgHtml($msg) {
	echo "<html>";
	echo "<h1>".$LANG["message"]["success"]."</h1>";
	echo "<h2>".$msg."</h2>";
	echo "</html>";
}

function showErrorHtml($msg) {
	echo "<html>";
	echo "<h1>".$LANG["message"]["error"]."</h1>";
	echo "<h2>".$msg."</h2>";
	echo "</html>";
}

function showDataError($ftype, $fpath, $msg) {
	header("Content-Type: text/xml");
	echo '<?xml version="1.0" encoding="utf-8" ?>';
	echo '<root>';
	echo "<error type='$ftype' path='$fpath'>".$msg."</error>";
	echo '</root>';
}

function getCacheMenuTab($name) {
	global $CONF;

	$cInfo = array();
	$cInfo["enable"] = $CONF["cache"][$name]["enable"];
	$cInfo["cachePath"] = $CONF["path"]["cache"]."/".$name.".cache";

	switch ($name) {
	case "menutab_Recent":
		$cInfo["limit"] = $CONF["numRecent"];
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
	global $CONF;

	$name = "menutab_showDir";
	$cInfo = array();
	$cInfo["enable"] = $CONF["cache"][$name]["enable"];
	$cInfo["vpath"] = $_REQUEST["fpath"];
	$cInfo["dpath"] = transPathV2R($cInfo["vpath"]);
	$cInfo["id"] = transPathV2Id($cInfo["vpath"]);
	$cInfo["cachePath"] = $CONF["path"]["cache"]."/".$cInfo["id"].".cache";
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
	global $CONF;

	if (lock_if_state_old("rebuildTags")) {
		include_once("php/cleanDir.php");
		include_once("php/rebuildTags.php");
		cleanDir($CONF["path"]["tags"]);
		rebuildTags();
		touch_state_file("scanTags");
		unlock_state_and_touch("rebuildTags");
	} else if (lock_if_state_old("scanTags")) {
		include_once("php/rebuildTags.php");
		rebuildTags();
		unlock_state_and_touch("scanTags");
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
		showDataError($ftype, $_REQUEST["fpath"], $LANG["message"]["invalidPath"]);
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
		showDataError($ftype, $_REQUEST["fpath"], $LANG["message"]["invalidPath"]);
	}
	break;
case "comment":
	session_start();
	header("Content-Type: text/xml");
	echo '<?xml version="1.0" encoding="utf-8" ?>';
	echo '<root>';

	if (!$CONF["func"]["comment"]["enable"]) {
		echo "<error type='$ftype' ename='funcOff'>".$LANG["message"]["funcOff"]."</error>";
		echo '</root>';
		break;
	}

	if ($_SESSION["reg_num_check"] != $_REQUEST["reg_num_check"]) {
		echo "<error type='$ftype' ename='reg_num_check'>".$LANG["comment"]["errmsg"]["reg_num_check"]."</error>";
		echo '</root>';
		break;
	}
	unset($_SESSION["reg_num_check"]);

	$fpath = transPathV2R($_REQUEST["fpath"]);
	if (!isValidArticlePath($_REQUEST["fpath"])
		|| !file_exists($fpath)) {
		echo "<error type='$ftype' ename='invalidPath'>".$LANG["message"]["invalidPath"]."</error>";
		echo '</root>';
		break;
	}

	include_once("php/strSafeHtml.php");
	$comment = strCommentHtml($_REQUEST["comment"]);
	if (strlen($comment) == 0) {
		echo "<error type='$ftype' ename='emptyComment'>".$LANG["comment"]["errmsg"]["emptyComment"]."</error>";
		echo '</root>';
		break;
	}

	$info = null;
	if (strlen($_REQUEST["user"]))
		$info["user"] = strSafeHtml($_REQUEST["user"]);
	if (strlen($_REQUEST["email"]))
		$info["email"] = strSafeHtml($_REQUEST["email"]);
	if ($_REQUEST["notify"] == "y")
		$info["notify"] = true;

	include_once("php/writeArticleComment.php");
	writeArticleComment($_REQUEST["fpath"], $comment, $info);
	echo '</root>';
	break;
case "commentUnNotify":
	header('Content-type: text/html; charset=utf-8');
	$vpath = $_REQUEST["fpath"];
	$fpath = transPathV2R($vpath);
	if (!isValidPath($vpath)
		|| !isValidArticlePath($vpath)
		|| !file_exists($fpath)) {
		showErrorHtml($LANG["message"]["invalidPath"]);
		break;
	}

	unset($info);
	$info["action"] = "unnotify";
	$info["cname"] = $_REQUEST["cname"];
	$info["ip"] = $_REQUEST["ip"];
	$info["time"] = $_REQUEST["time"];

	include_once("php/modifyArticleComment.php");
	if (modifyArticleComment($vpath, $info))
		showMsgHtml($LANG["comment"]["msg"]["unNotifyOk"]);
	else
		showErrorHtml($LANG["comment"]["errmsg"]["unNotifyFailed"]);
	break;
case "cssInside":
case "jsInside":
	include_once("php/indexHeader.php");
	if ($ftype == "cssInside")
		header('Content-type: text/css');
	else
		header('Content-type: text/javascript');
	getCacheIndex($ftype);
	break;
case "searchbot":
	if (!$CONF["func"]["sitemap"]["enable"]) {
		header('Content-type: text/html; charset=utf-8');
		echo '<html>';
		echo "<error type='$ftype' ename='funcOff'>".$LANG["message"]["funcOff"]."</error>";
		echo '</html>';
		break;
	}

	include_once("php/searchbot.php");
	header('Content-type: text/html; charset=utf-8');
	searchbot();
	break;
case "sitemap":
	if (!$CONF["func"]["sitemap"]["enable"]) {
		header('Content-type: text/html; charset=utf-8');
		echo '<html>';
		echo "<error type='$ftype' ename='funcOff'>".$LANG["message"]["funcOff"]."</error>";
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
