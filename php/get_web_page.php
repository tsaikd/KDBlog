<?php
/**
 * Get a web file (HTML, XHTML, XML, image, etc.) from a URL.  Return an
 * array containing the header fields and content.
 * ref: http://nadeausoftware.com/articles/2007/07/php_tip_how_get_web_page_using_fopen_wrappers
 * note: INI configure of "allow_url_fopen" must be "On"
 */
function get_web_page($url) {
	$options = array( 'http' => array(
//		'user_agent'    => 'spider',    // who am i
		'max_redirects' => 10,          // stop after 10 redirects
		'timeout'       => 120,         // timeout on response
	) );
	$context = stream_context_create($options);
	$page = @file_get_contents($url, false, $context);

	$result = array();
	if ($page != false)
		$result['content'] = $page;
	else if (!isset($http_response_header))
		return null;                    // Bad url, timeout

	// Save the header
	$result['header'] = $http_response_header;

	// Get the *last* HTTP status code
	$nLines = count($http_response_header);
	for ($i = $nLines-1 ; $i >= 0 ; $i--) {
		$line = $http_response_header[$i];
		if (strncasecmp("HTTP", $line, 4) == 0) {
			$response = explode( ' ', $line );
			$result['http_code'] = $response[1];
			break;
		}
	}

	return $result;
}
?>
