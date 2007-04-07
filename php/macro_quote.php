<?php
function macro_quote($node, $type) {
	if ($node["tag"] != "quote")
		return "";

	if ($type == "rss") {
		$style = " style='color: #206; background-color: #ffc; border: dotted 1px #888;'";
	} else { // $type == "html"
		$style = " class='macro_quote'";
	}

	$value = $node["value"];
	$value = str_replace("<", "&lt;", $value);
	$value = str_replace(">", "&gt;", $value);
	if (substr($value, 0, 1) == "\n")
		$value = substr($value, 1);
	if (substr($value, -1) == "\n")
		$value = substr($value, 0, -1);

	$res = "";
	switch ($node["type"]) {
	case "open":
		$res .= "<div$style>".$value;
		break;
	case "close":
		$res .= "</div>";
		break;
	case "complete":
		$res .= "<div$style>".$value."</div>";
		break;
	default: // "cdata"
		$res .= $value;
		break;
	}

	return $res;
}

?>
