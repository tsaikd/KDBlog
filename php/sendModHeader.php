<?php
function sendModHeader($fpath, $exptime=86400) {
	header('Last-Modified: '.date(DATE_RFC2822, filectime($fpath)));
	header('Expires: '.date(DATE_RFC2822, time()+$exptime));
}
?>
