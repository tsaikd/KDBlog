<?php
/*
return: (array)
	$res["index"]
	$res["vals"]
*/
function parseXml($fpath) {
	if (!file_exists($fpath))
		return array();

	$xml = xml_parser_create("UTF-8");
	xml_parser_set_option($xml, XML_OPTION_CASE_FOLDING, 0);
	xml_parse_into_struct($xml, file_get_contents($fpath), $vals, $index);
	xml_parser_free($xml);

	return array(
		  0 => $index
		, 1 => $vals
		, "index" => $index
		, "vals" => $vals
	);
}

?>
