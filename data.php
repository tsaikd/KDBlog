<?php
include_once("config.php");
include_once("php/isValidPath.php");
include_once("php/isValidArticlePathName.php");
include_once("php/transPath.php");

$ftype = $_REQUEST["ftype"];
if ($_REQUEST["firstmonth"] == "true")
	$firstmonth = true;
else
	$firstmonth = false;

function listDataDir($type, $vpath, $parentType) {
	global $BLOGCONF;
	global $BLOGLANG;
	global $firstmonth;

	$path = transPathV2R($vpath);

	if (!file_exists($path))
		return;

	$darray = array();
	$d = dir($path);
	while ($f = $d->read()) {
		if($f[0] == ".")
			continue;
		array_push($darray, $f);
	}
	$d->close();
	sort($darray);

	switch ($type) {
	case "menutags";
		while ($f = array_pop($darray)) {
			$id = "$parentType$type"."_$f";
			$pid = $id."_";
			logecho("<a onfocus='this.blur()' class='tagstext' href=\"javascript:showMenuTabAll('$id', 'menutab_Tags_forceTag', '$vpath/$f')\">$f</a>");
			logecho("<div class='$type' id='$id' style='display: none; margin-left: 1em;'>");
			logecho("</div>");
		}
		break;
	case "menuyear":
		while ($f = array_pop($darray)) {
			$id = "$parentType$type"."_$f";
			$pid = $id."_";
			logecho("<a onfocus='this.blur()' class='yeartext' href=\"javascript:showMenuTabAll('$id', 'menutab_All_forceYear', '$vpath/$f')\">$f</a>");
			if ($firstmonth) {
				logecho("<div class='$type' id='$id' style='display: block;'>");
				listDataDir("menumonth", "$vpath/$f", $pid);
			} else {
				logecho("<div class='$type' id='$id' style='display: none;'>");
			}
			logecho("</div>");
		}
		break;
	case "menumonth":
		while ($f = array_pop($darray)) {
			$id = "$parentType$type"."_$f";
			$pid = $id."_";
			logecho("<a onfocus='this.blur()' class='monthtext' href=\"javascript:showMenuTabAll('$id', 'menutab_All_forceMonth', '$vpath/$f')\">$f</a>");
			if ($firstmonth) {
				$firstmonth = false;
				logecho("<div class='$type' id='$id' style='display: block;'>");
				listDataDir("menuday", "$vpath/$f", $pid);
			} else {
				logecho("<div class='$type' id='$id' style='display: none;'>");
			}
			logecho("</div>");
		}
		break;
	case "menuday":
		while ($f = array_pop($darray)) {
			if (!isValidArticlePathName($f))
				continue;
			if (!file_exists("$path/$f")) {
				// some file path(link) is not valid anymore
				touch($BLOGCONF["state"]["rebuildTags"]);
				continue;
			}

			$xml = xml_parser_create("UTF-8");
			xml_parse_into_struct($xml, file_get_contents("$path/$f"), $vals, $index);
			xml_parser_free($xml);

			$title = $vals[$index["TITLE"][0]]["value"];

			logecho("<a onfocus='this.blur()' class='$type' href='javascript:showArticle(\"$vpath/$f\", 1)'>".$f[0].$f[1]);
			if ($title)
				logecho(" - ".$title);
			logecho("</a>\n");
		}
		break;
	case "menuspec":
		if (count($darray) == 0) {
			logecho($BLOGLANG["mainmenu"]["mainmenuTabs"]["menutab_Spec_Msg"]["nospec"]);
		} else {
			foreach ($darray as $f) {
				if (!isValidArticlePathName($f))
					continue;

				$xml = xml_parser_create("UTF-8");
				xml_parse_into_struct($xml, file_get_contents("$path/$f"), $vals, $index);
				xml_parser_free($xml);

				$title = $vals[$index["TITLE"][0]]["value"];
				if (!$title)
					$title = $BLOGLANG["message"]["warn"].": '$f' ".$BLOGLANG["mainmenu"]["mainmenuTabs"]["menutab_Spec_Msg"]["notitle"];

				logecho("<a class='$type' href='javascript:showArticle(\"$vpath/$f\", 1)'>$title</a>");
			}
		}
		break;
	}
}

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

function isValidCache_menutab_All() {
	global $BLOGCONF;
	include_once("php/getRecentArticlePath.php");
	$cpath = $BLOGCONF["cache"]["menutab_All"]["cachePath"];
	$farray = getRecentArticlePath($BLOGCONF["datapath"], 2);
	$statetime = filectime($cpath);
	foreach ($farray as $path) {
		if (filectime($path) > $statetime)
			return false;
	}
	return true;
}

function showData_menutab_All() {
	logecho("<html>");
	listDataDir("menuyear", "data", "");
	logecho("</html>");
}

function showData_menutab_Tags() {
	logecho("<html>");
	listDataDir("menutags", "tags", "");
	logecho("</html>");
}

function showData_menutab_Spec() {
	logecho("<html>");
	listDataDir("menuspec", "special", "");
	logecho("</html>");
}

switch ($ftype) {
case "article":
	$vpath = $_REQUEST["fpath"];
	$fpath = transPathV2R($vpath);
	if (!isValidPath($vpath) || !isValidArticlePathName($vpath)
		|| !file_exists($fpath)) {
		showDataError($ftype, $_REQUEST["fpath"], $BLOGLANG["message"]["invalidPath"]);
		break;
	}

	header("Content-Type: text/xml");

	include_once("php/getArticleCommentPath.php");
	$aCommentPath = getArticleCommentPath($_REQUEST["fpath"]);
	if (count($aCommentPath) == 0) {
		readfile($fpath);
		break;
	}

	$fpath = transPathV2R($_REQUEST["fpath"]);
	$data = file_get_contents($fpath);
	$offset = strpos($data, "</article>");
	echo substr($data, 0, $offset);

	foreach ($aCommentPath as $v) {
		$data = file_get_contents($v);
		$iStart = strpos($data, "<comment");
		if ($iStart == false)
			continue;
		$iEnd = strpos($data, "</comment>", $iStart);
		if ($iEnd == false)
			continue;
		echo substr($data, $iStart, $iEnd-$iStart+10)."\n";
	}
	echo "</article>\n";

	break;
case "menutab_All":
	header("Content-Type: text/html");
	$firstmonth = true;
	getCache($ftype);
	break;
case "menutab_All_forceYear":
	$vpath = $_REQUEST["fpath"];
	if (isValidPath($vpath)) {
		header("Content-Type: text/html");
		echo "<html>";
		listDataDir("menumonth", $vpath, $_REQUEST["parentType"]);
		echo "</html>";
	} else {
		showDataError($ftype, $_REQUEST["fpath"], $BLOGLANG["message"]["invalidPath"]);
	}
	break;
case "menutab_All_forceMonth":
	$vpath = $_REQUEST["fpath"];
	if (isValidPath($vpath)) {
		header("Content-Type: text/html");
		echo "<html>";
		listDataDir("menuday", $vpath, $_REQUEST["parentType"]);
		echo "</html>";
	} else {
		showDataError($ftype, $_REQUEST["fpath"], $BLOGLANG["message"]["invalidPath"]);
	}
	break;
case "menutab_Tags":
	header("Content-Type: text/html");
	getCache($ftype);
	break;
case "menutab_Tags_forceTag":
	$vpath = $_REQUEST["fpath"];
	if (isValidPath($vpath)) {
		header("Content-Type: text/html");
		echo "<html>";
		listDataDir("menuyear", $vpath, $_REQUEST["parentType"]);
		echo "</html>";
	} else {
		showDataError($ftype, $_REQUEST["fpath"], $BLOGLANG["message"]["invalidPath"]);
	}
	break;
case "menutab_Spec":
	header("Content-Type: text/html");
	getCache($ftype);
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
	if ($_SESSION["reg_num_check"] != $_REQUEST["reg_num_check"]) {
		echo "<error type='$ftype' ename='reg_num_check'>".$BLOGLANG["comment"]["errmsg"]["reg_num_check"]."</error>";
		echo '</root>';
		break;
	}
	unset($_SESSION["reg_num_check"]);

	$comment = $_REQUEST["comment"];
	$fpath = transPathV2R($_REQUEST["fpath"]);
	if (!isValidArticlePath($_REQUEST["fpath"])
		|| !file_exists($fpath)) {
		echo "<error type='$ftype' ename='invalidPath'>".$BLOGLANG["message"]["invalidPath"]."</error>";
		echo '</root>';
		break;
	}

	include_once("php/writeArticleComment.php");
	writeArticleComment($_REQUEST["fpath"], $_REQUEST["comment"]);
	echo '</root>';
	break;
default:
	header("Content-Type: text/html");
	echo "<html>$ftype: Not implement</html>";
	break;
}

?>
