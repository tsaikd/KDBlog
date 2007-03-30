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
	$res = "";
	$tag = $node["tag"];

	switch ($tag) {
	case "quote":
		if ($type == "rss") {
			$style = " style='color: #206; background-color: #ffc; border: dotted 1px #888;'";
		} else { // $type == "html"
			$style = " class='macro_quote'";
		}
		$node["value"] = str_replace("<", "&lt;", $node["value"]);
		$node["value"] = str_replace(">", "&gt;", $node["value"]);
		switch ($node["type"]) {
		case "open":
			$res .= "<div$style>".$node["value"];
			break;
		case "close":
			$res .= "</div>";
			break;
		case "complete":
			$res .= "<div$style>".$node["value"]."</div>";
			break;
		default: // "cdata"
			$res .= $node["value"];
			break;
		}
		break;
	}
	return $res;
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
	global $BLOGCONF;
	global $BLOGLANG;

	if (!is_file($fpath))
		return;

	$xml = xml_parser_create("UTF-8");
	xml_parser_set_option($xml, XML_OPTION_CASE_FOLDING, 0);
	xml_parse_into_struct($xml, file_get_contents($fpath), $vals, $index);
	xml_parser_free($xml);

	if (!$index["contents"])
		return;

	$amacro = array();
	$amacroReplace = array();
	$xmlkey = "macro";
	if ($index[$xmlkey]) {
		$rtoday = $BLOGCONF["link"]."misc/".transPath2Date($fpath);
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
					array_push($amacroReplace, $aBuf);
			} else if ($macroName) {
				array_push($amacro, $macroName);
			}
		}
	}

	$vpath = transPathR2V($fpath, "data");

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
			logecho($BLOGLANG["article"]["tags"].":<br />");
			foreach ($index[$xmlkey] as $i) {
				logecho("<a class='tags' onfocus='javascript:this.blur()' href=\"javascript:chgMenuTag('menutab_Tags', '".$vals[$i]["value"]."')\">");
				logecho($vals[$i]["value"]."</a><br />");
			}
			logecho("</div>");
		}

		// show date
		logecho("<div class='articledate'>".transPathVData2Date($vpath)."</div>");
		logecho("</span>");
	}

	$xmlkey = "title";
	if ($index[$xmlkey]) {
		$i = $index[$xmlkey][0];
		if ($type == "rss") {
			logecho("<title>".$vals[$i]["value"]."</title>");
		} else { // $type == "html"
			logecho("<h1>".$vals[$i]["value"]."</h1>");
		}
	}

	if ($type == "rss") {
		logecho("<link>".$BLOGCONF["link"]."?fpath=".$vpath."</link>");
		logecho("<guid>".$BLOGCONF["link"]."?fpath=".$vpath."</guid>");

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

		foreach ($amacroReplace as $amac)
			$node["value"] = str_replace($amac[0], $amac[1], $node["value"]);

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
		logecho("</pre></div>");
	}
}

function isValidCache_Article($cInfo) {
	if (filectime($cInfo["fpath"]) > filectime($cInfo["cachePath"]))
		return false;

	return true;
}

function showData_Article($cInfo) {
	showArticleItem($cInfo["fpath"], $cInfo["articleType"]);
}

function getCacheArticle($vpath, $type) {
	global $BLOGCONF;

	$id = transPathV2Id($vpath);
	$fpath = transPathV2R($vpath);

	$cInfo = array();
	if ($type == "rss")
		$cInfo["enable"] = $BLOGCONF["cache"]["articleRss"]["enable"];
	else if ($type == "html")
		$cInfo["enable"] = $BLOGCONF["cache"]["articleHtml"]["enable"];
	else
		return;
	$cInfo["cachePath"] = $BLOGCONF["cachpath"]."/".$id.".".$type.".cache";
	$cInfo["isValidCacheProc"] = "isValidCache_Article";
	$cInfo["showDataProc"] = "showData_Article";
	$cInfo["articleType"] = $type;
	$cInfo["fpath"] = $fpath;

	return getGenCache($cInfo);
}

?>
