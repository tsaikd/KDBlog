<?php

include_once("config.php");
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
function showMacroTag($node) {
	$tag = $node["tag"];

	switch ($tag) {
	case "quote":
		$style = " style='color: #206; background-color: #ffc; border: dotted 1px #888;'";
		$node["value"] = str_replace("<", "&lt;", $node["value"]);
		$node["value"] = str_replace(">", "&gt;", $node["value"]);
		switch ($node["type"]) {
		case "open":
			echo "<div$style>".$node["value"];
			break;
		case "close":
			echo "</div>";
			break;
		case "complete":
			echo "<div$style>".$node["value"]."</div>";
			break;
		default: // "cdata"
			echo $node["value"];
			break;
		}
		break;
	}
}

function echoXmlOpenTag($node) {
	$tag = $node["tag"];

	echo "<".$tag;
	if ($node["attributes"])
		foreach ($node["attributes"] as $k => $v)
			echo " ".$k."=\"".$v."\"";
	echo ">";
}

function showArticleItem($fpath) {
	global $BLOGCONF;

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
		// macro define must be complete type, exclude "replace"
		foreach ($index[$xmlkey] as $i) {
			$macroName = $vals[$i]["attributes"]["name"];
			if ($macroName == "replace") {
				if ($vals[$i]["type"] != "open")
					continue;
				$i++;
				$aBuf = array();
				while ($vals[$i]["type"] != "close") {
					if ($vals[$i]["tag"] == "from") {
						$aBuf[0] = $vals[$i]["value"];
					} else if ($vals[$i]["tag"] == "to") {
						$aBuf[1] = $vals[$i]["value"];
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

	echo "<item>";

	$xmlkey = "title";
	if ($index[$xmlkey]) {
		$i = $index[$xmlkey][0];
		echo "<title>".$vals[$i]["value"]."</title>";
	}

	echo "<link>".$BLOGCONF["link"]."?fpath=".$fpath."</link>";

	$ftime = filectime($fpath);
	if ($ftime)
		echo "<pubDate>".strftime("%a, %d %b %Y %T GMT%z", $ftime)."</pubDate>";

	$xmlkey = "contents";
	if ($index[$xmlkey]) {
		echo "<description><![CDATA[";

		$c = count($index[$xmlkey]);
		$c = $index[$xmlkey][$c-1];
		for ($i=$index[$xmlkey][0] ; $i<=$c ; $i++) {
			$node = $vals[$i];
			$tag = $node["tag"];

			foreach ($amacroReplace as $amac)
				$node["value"] = str_replace($amac[0], $amac[1], $node["value"]);

			if (in_array($tag, $amacro)) {
				showMacroTag($node);
				continue;
			}

			switch ($node["type"]) {
			case "open":
				if ($node["tag"] != $xmlkey)
					echoXmlOpenTag($node);
				echo $node["value"];
				break;
			case "close":
				if ($node["tag"] != $xmlkey)
					echo "</".$tag.">";
				break;
			case "complete":
				if ($node["tag"] != $xmlkey)
					echoXmlOpenTag($node);
				echo $node["value"];
				if ($node["tag"] != $xmlkey)
					echo "</".$tag.">";
				break;
			default: // "cdata"
				echo $node["value"];
				break;
			}
		}
		echo "]]></description>";
	}

	echo "</item>";
}

header("Content-Type: text/xml");
header("Pragma: no-cache");
header("Expires: 0");
echo '<?xml version="1.0" encoding="utf-8" ?>';
echo '<rss version="2.0">';
echo '<channel>';
echo '<title>'.$BLOGCONF["title"].'</title>';
echo '<link>'.$BLOGCONF["link"].'</link>';
echo '<language>'.$BLOGCONF["language"].'</language>';
echo '<managingEditor>'.$BLOGCONF["email"].'</managingEditor>';
echo '<docs>'.$_SERVER["SCRIPT_FILENAME"].'?feed='.$_REQUEST["feed"].'</docs>';

if ($_REQUEST["limit"]) {
	$limit = (int)$_REQUEST["limit"];
	if ($limit > $BLOGCONF["rssMaxNum"])
		$limit = $BLOGCONF["rssMaxNum"];
} else {
	$limit = $BLOGCONF["rssDefNum"];
}

switch($_REQUEST["feed"]) {
default: // all
	include_once("php/getRecentArticlePath.php");
	$farray = getRecentArticlePath($BLOGCONF["datapath"], $limit);
	foreach ($farray as $fpath)
		showArticleItem($fpath);
	break;
}

echo '</channel>';
echo '</rss>';

?>
