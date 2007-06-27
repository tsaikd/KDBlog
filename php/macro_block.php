<?php
function macro_block($node, $type) {
	if ($node["tag"] != "block")
		return "";

	if ($type == "rss") {
		$bstyle  = " style='";
		$bstyle .= " white-space: normal;";
		$bstyle .= " text-indent: 2em;";
		$bstyle .= "'";
	} else { // $type == "html"
		$bstyle = " class='macro_block'";
	}

	$value = $node["value"];
	$value = str_replace("<", "&lt;", $value);
	$value = str_replace(">", "&gt;", $value);
	if (substr($value, 0, 1) == "\n")
		$value = substr($value, 1);
	if (substr($value, -1) == "\n")
		$value = substr($value, 0, -1);
	$value = str_replace("\r\n", "\n", $value);
	$value = str_replace("\n\n", "</p><br /><p>", $value);
	$value = str_replace("\n", "</p><p>", $value);
	$value = "<p>".$value."</p>";

	switch ($node["type"]) {
	case "open":
		$res = "<div$bstyle>$value";
		break;
	case "close":
		$res = "</div>";
		break;
	case "complete":
		$res = "<div$bstyle>$value</div>";
		break;
	default: // "cdata"
		$res = $value;
		break;
	}

	return $res;
}

?>
