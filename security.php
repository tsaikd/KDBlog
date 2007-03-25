<?php
function genCheckImage($str=null) {
	ob_start();
	session_start();
	if ($str == null) {
		$str = "abcdefghijklmnopqrstuvwxyz";
		$str .= strtoupper($str)."0123456789";
	}
	header("Content-type: image/gif");
	header("Pragma: no-cache");
	header("Expires: 0");
	$im = imagecreate(45, 16) or die("Cannot Initialize new GD image stream");
	imagecolorallocate($im, 240, 240, 240);
	$loc = 2;
	$color1 = imagecolorallocate($im, 0, 0, 0);
	for ($i=0 ; $i<4 ; $i++) {
		$rd = rand(0, strlen($str)-1);
		$rands[$i] = $str[$rd];
		$color = imagecolorallocate($im, rand(0,200), rand(0,200), rand(0,200));
		imagestring($im, 5, ($loc+1), 0, $rands[$i], $color1);
		imagestring($im, 5, $loc, 0, $rands[$i], $color);
		$loc += 11;
	}

	$_SESSION['reg_num_check'] = implode("",$rands);

	$width = 60;
	$height = 24;

	$nim = imagecreate($width, $height);
	imagecopyresized($nim, $im, 0, 0, 0, 0, $width, $height, 45, 16);
	Imagegif($nim);
	imagedestroy($nim);
	imagedestroy($im);
	ob_end_flush();
}

genCheckImage("0123456789");

?>
