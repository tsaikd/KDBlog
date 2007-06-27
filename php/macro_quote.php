<?php
function macro_quote($node, $type) {
	if ($node["tag"] != "quote")
		return "";

	if ($type == "rss") {
		$bstyle  = " style='";
		$bstyle .= " color: #104;";
		$bstyle .= " background-color: #eef;";
		$bstyle .= " border: dotted 1px #888;";
		$bstyle .= "'";

		if ($node["attributes"]["header"]) {
			$hval  = "<div style='color: white;";
			$hval .= " background-color: #64d;";
			$hval .= " font-weight: bold;'>";
			$hval .= $node["attributes"]["header"];
			$hval .= "</div>";
		} else {
			$hval = "";
		}

		$cstyle = "";
	} else { // $type == "html"
		$bstyle = " class='macro_quote'";

		if ($node["attributes"]["header"]) {
			$hval  = "<div class='macro_quote_header'";
			$hval .= " onclick='javascript:toggleObj(this.nextSibling, \"block\")'>";
			$hval .= $node["attributes"]["header"];
			$hval .= "</div>";
		} else {
			$hval = "";
		}

		$cstyle = " class='macro_quote_contents'";
	}

	$value = $node["value"];
	$value = str_replace("<", "&lt;", $value);
	$value = str_replace(">", "&gt;", $value);
	if (substr($value, 0, 1) == "\n")
		$value = substr($value, 1);
	if (substr($value, -1) == "\n")
		$value = substr($value, 0, -1);

	switch ($node["type"]) {
	case "open":
		$res = "<div$bstyle>$hval<div$cstyle>$value";
		break;
	case "close":
		$res = "</div></div>";
		break;
	case "complete":
		$res = "<div$bstyle>$hval<div$cstyle>$value</div></div>";
		break;
	default: // "cdata"
		$res = $value;
		break;
	}

	return $res;
}

?>
