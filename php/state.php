<?php
function is_state_old($name) {
	global $CONF;
	$path = $CONF["state"][$name];

	if (!file_exists($path))
		return true;

	$stime = (int)file_get_contents($path);
	$ftime = filectime($path);
	if ($ftime > $stime)
		return true;
	else
		return false;
}

function touch_state_file($name, $offset=2) {
	global $CONF;
	$path = $CONF["state"][$name];

	$t = time() + $offset;
	$fp = fopen($path, "w");
	fwrite($fp, $t);
	fclose($fp);
	touch($path, $t);
}

function set_state_old($name) {
	global $CONF;
	$path = $CONF["state"][$name];
	touch($path);
}

function lock_if_state_old($name) {
	if (!is_state_old($name))
		return false;

	global $CONF;
	$path = $CONF["state"][$name];
	$lockpath = $path.".lock";
	$max_wait_time = time() + 30;

	while (time() <= $max_wait_time) {
		if (file_exists($lockpath)) {
			usleep(500000); // sleep 0.5 second
		} else {
			touch($lockpath);
			return true;
		}
	}

	die("lock_if_state_old($name) timeout");
}

function unlock_state_and_touch($name) {
	global $CONF;
	$path = $CONF["state"][$name];
	$lockpath = $path.".lock";
	if (file_exists($lockpath)) {
		unlink($lockpath);
		touch_state_file($name);
		return true;
	} else {
		return false;
	}
}
?>
