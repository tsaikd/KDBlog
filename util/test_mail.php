<?php
if (!isset($_SERVER["SHELL"]))
	die("Please run this script at shell\n");
chdir(dirname(dirname(__FILE__)));
if (!file_exists("config.php"))
	die("Please put test directory to KDBlog root dir.\n");

include_once("config.php");

$body  = "this is a test mail for KDBlog\n";
$body .= "time: ".date(DATE_RFC2822, time())."\n";

echo("Sending a test mail to your email ('".$CONF["email"]."')\n");
if (mail($CONF["email"], "KDBlog test_mail", $body))
	echo("Sent mail successfully\nYou can check your email account now\n");
else
	echo("Sent mail failed\n");

?>
