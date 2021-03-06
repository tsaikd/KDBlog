<?php

include_once("php/transPath.php");
/*
class CXmlArticle {
	var $parser;

	function CXmlArticle() {
		$this->parser = xml_parser_create();
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, 0);
		xml_set_object($this->parser, &$this);
		xml_set_element_handler($this->parser, "tag_open", "tag_close");
		xml_set_character_data_handler($this->parser, "cdata");
	}

	function parse($data) {
		xml_parse($this->parser, $data);
	}

	function parse_file($path) {
echo "<![CDATA[";
		$this->parse(file_get_contents($path));
echo "]]>";
	}

	function tag_open($parser, $tag, $attributes) {
//var_dump("tag_open", $tag, $attributes);
		echo "<$tag";
		foreach ($attributes as $k => $v)
			echo " $k=\"$v\"";
		echo ">";
		switch ($tag) {
		case "article":
			break;
		}
	}

	function tag_close($parser, $tag) {
//var_dump("tag_close", $tag);
		echo "</$tag>";
	}

	function cdata($parser, $cdata) {
//var_dump("cdata", $cdata);
		echo $cdata;
	}
}
*/

function showMacroTag($node, $type) {
	$func = "macro_".$node["tag"];
	if (is_callable($func))
		return $func($node, $type);
	$funcpath = "php/".$func.".php";
	if (file_exists($funcpath)) {
		include_once($funcpath);
		if (is_callable($func))
			return $func($node, $type);
	}
}

function echoXmlOpenTag($node) {
	$tag = $node["tag"];

	$res = "<".$tag;
	if ($node["attributes"])
		foreach ($node["attributes"] as $k => $v)
			$res .= " ".$k."=\"".$v."\"";
	$res .= ">";

	return $res;
}

/*
type:
	"rss"
	"html"
*/
function showArticleItem($fpath, $type) {
	global $CONF;
	global $LANG;

	if (!is_file($fpath))
		return;

	include_once("php/parseXml.php");
	list($index, $vals) = parseXml($fpath);

	if (!$index["contents"]) {
		if ($type == "html") {
			$vpath = transPathR2V($fpath, "auto");
			echo "<div class='errorMsg'>".$LANG["article"]["invalidData"];
			echo "<br />".$vpath."</div>";
		}
		return;
	}

	$amacro = array();
	$amacroReplace = array();

	// add default keyword to replace macro (configure at config.php)
	foreach ($CONF["func"]["macro_kw"]["src"] as $i => $j) {
		$aBuf = array();
		$aBuf[0] = $CONF["func"]["macro_kw"]["src"][$i];
		$aBuf[1] = $CONF["func"]["macro_kw"]["tar"][$i];
		array_push($amacroReplace, $aBuf);
	}

	$xmlkey = "macro";
	if ($index[$xmlkey]) {
		$rtoday = $CONF["link"]."misc/".transPath2Date($fpath);
		// macro define must be complete type, exclude "replace"
		foreach ($index[$xmlkey] as $i) {
			$macroName = $vals[$i]["attributes"]["name"];
			if ($macroName == "replace") {
				if ($vals[$i]["type"] != "open")
					continue;
				$today = $vals[$i]["attributes"]["today"];
				$i++;
				$aBuf = array();
				while ($vals[$i]["type"] != "close") {
					if ($vals[$i]["tag"] == "from") {
						$aBuf[0] = $vals[$i]["value"];
					} else if ($vals[$i]["tag"] == "to") {
						$aBuf[1] = $vals[$i]["value"];
						if ($today)
							$aBuf[1] = str_replace($today, $rtoday, $aBuf[1]);
					}
					$i++;
				}
				if (count($aBuf) == 2)
					array_unshift($amacroReplace, $aBuf);
			} else if ($macroName == "nokw") {
				foreach ($amacroReplace as $j => $k) {
					if ($k[0] == $vals[$i]["value"])
						array_splice($amacroReplace, $j, 1);
				}
			} else if ($macroName) {
				array_push($amacro, $macroName);
			}
		}
	}

	$vpath = transPathR2V($fpath, "auto");

	if ($type == "rss") {
		logecho("<item>");
	} else { // $type == "html"
		$id = transPathV2Id($vpath);
		logecho("<div class='article' id='$id' onmouseover='selectArticle(this)'>");
		include_once("php/getArticleToolBar.php");
		logecho(getArticleToolBar($id));

		// show articlemenu
		logecho("<span class='articlemenu'>");
		// show tags
		$xmlkey = "tag";
		if ($index[$xmlkey]) {
			logecho("<div class='tagsmenu'>");
			logecho($LANG["article"]["tags"].":<br />");
			foreach ($index[$xmlkey] as $i) {
				logecho("<a class='tags'");
				logecho(" href=\"javascript:chgMenuTag('menutab_Tags', 'tags/".$vals[$i]["value"]."/".dirname(substr($vpath, 5))."')\">");
				logecho($vals[$i]["value"]."</a>");
			}
			logecho("</div>");
		}

		// show date
		$buf = transPath2Date($vpath);
		if ($buf) {
			logecho("<a class='articledate'");
			logecho(" href='javascript:chgMenuTag(\"menutab_All\", \"data/$buf\")'");
			logecho(">".$buf."</a>");
		}
		logecho("</span>");
	}

	// show title
	$xmlkey = "title";
	if ($index[$xmlkey]) {
		$i = $index[$xmlkey][0];
		if ($type == "rss") {
			logecho("<title>".$vals[$i]["value"]."</title>");
		} else { // $type == "html"
			logecho("<h1>");
			logecho($vals[$i]["value"]);
			logecho("</h1>");
		}
	}

	if ($type == "rss") {
		logecho("<link>".$CONF["link"]."?fpath=".$vpath."</link>");
		logecho("<guid>".$CONF["link"]."?fpath=".$vpath."</guid>");

		$ftime = filectime($fpath);
		if ($ftime)
			logecho("<pubDate>".strftime("%a, %d %b %Y %T %z", $ftime)."</pubDate>");
	}

	if ($type == "rss") {
		logecho("<description><![CDATA[<pre>");
	} else { // $type == "html"
		logecho("<div class='contents'><pre>");
	}

	$xmlkey = "contents";
	$c = count($index[$xmlkey]);
	$c = $index[$xmlkey][$c-1];
	for ($i=$index[$xmlkey][0] ; $i<=$c ; $i++) {
		$node = $vals[$i];
		$tag = $node["tag"];

		if (in_array($tag, $amacro)) {
			$node["value"] = showMacroTag($node, $type);
			$node["type"] = "";
		}

		foreach ($amacroReplace as $amac) {
			$s = str_replace($amac[0], "   $amac[0]   ", $node["value"]);
			$pmac = str_replace("/", "\\/", $amac[0]);
			$s = preg_replace("/(<.*?)   $pmac   (.*?".">)/", "\$1"."$amac[0]"."\$2", $s);
			$node["value"] = str_replace("   $amac[0]   ", $amac[1], $s);
		}

		switch ($node["type"]) {
		case "open":
			if ($node["tag"] != $xmlkey)
				logecho(echoXmlOpenTag($node));
			logecho($node["value"]);
			break;
		case "close":
			if ($node["tag"] != $xmlkey)
				logecho("</".$tag.">");
			break;
		case "complete":
			if ($node["tag"] != $xmlkey)
				logecho(echoXmlOpenTag($node));
			logecho($node["value"]);
			if ($node["tag"] != $xmlkey)
				logecho("</".$tag.">");
			break;
		default: // "cdata"
			logecho($node["value"]);
			break;
		}
	}

	if ($type == "rss") {
		logecho("</pre>]]></description>");
		logecho("</item>");
	} else { // $type == "html"
		logecho("</pre>");

		// show runSpecFile link
		if ($index["spectype"] && $index["code"]
			&& ($vals[$index["spectype"][0]]["value"]=="php")) {
			logecho("<a href='javascript:runSpecFile(\"$fpath\")'>".$vals[$index["title"][0]]["value"]."</a>");
		}

		// show Comment
		include_once("php/getArticleCommentPath.php");
		$aCommentPath = getArticleCommentPath($vpath);
		if (count($aCommentPath)) {
			logecho("<div name='comments' class='comments'>");

			foreach ($aCommentPath as $v) {
				list($index, $vals) = parseXml($v);
				if (!$index["comment"])
					continue;

				$data = $vals[$index["comment"][0]];
				// comment Start
				logecho("<div class='comment'>");

				// comment Header
				logecho("<div class='commentHeader'>");
				// comment From
				logecho("<span class='commentFrom'>");
				if ($data["attributes"]["user"])
					logecho("From: ".$data["attributes"]["user"]." (".$data["attributes"]["ip"].")");
				else
					logecho("From: ".$data["attributes"]["ip"]);
				logecho("</span>");
				// comment Time
				logecho("<span class='commentTime'>");
				logecho(strftime("%Y/%m/%d %T", (int)$data["attributes"]["time"]));
				logecho("</span>");
				// comment Header End
				logecho("</div>");

				// comment Data
				logecho("<div class='commentData'>");
				logecho(substr($data["value"], 1, -1));
				logecho("</div>");

				// comment End
				logecho("</div>");
			}

			logecho("</div>");
		}

		logecho("</div>");
		logecho("</div>");
	}
}

function isValidCache_Article($cInfo) {
	if (filectime($cInfo["fpath"]) > filectime($cInfo["cachePath"]))
		return false;

	include_once("php/getArticleCommentPath.php");
	$aCommentPath = getArticleCommentPath($cInfo["vpath"]);
	if (count($aCommentPath)) {
		$fpath = array_pop($aCommentPath);
		if (filectime($fpath) > filectime($cInfo["cachePath"]))
			return false;
	}

	return true;
}

function showData_Article($cInfo) {
	showArticleItem($cInfo["fpath"], $cInfo["articleType"]);
}

function getCacheArticle($vpath, $type) {
	global $CONF;

	$id = transPathV2Id($vpath);
	$fpath = transPathV2R($vpath);

	$cInfo = array();
	if ($type == "rss")
		$cInfo["enable"] = $CONF["cache"]["articleRss"]["enable"];
	else if ($type == "html")
		$cInfo["enable"] = $CONF["cache"]["articleHtml"]["enable"];
	else
		return;
	$cInfo["cachePath"] = $CONF["path"]["cache"]."/".$CONF["language"].".".$id.".".$type.".cache";
	$cInfo["isValidCacheProc"] = "isValidCache_Article";
	$cInfo["showDataProc"] = "showData_Article";
	$cInfo["articleType"] = $type;
	$cInfo["fpath"] = $fpath;
	$cInfo["vpath"] = $vpath;

	return getGenCache($cInfo);
}

?>
