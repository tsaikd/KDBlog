<?php
function strSafeHtml($text) {
	$text = str_replace("\\\\", "\\", $text);
	$text = str_replace("\\\"", "\"", $text);
	$text = str_replace("\\'", "'", $text);
	$text = htmlentities($text);
	return $text;
}

?>
