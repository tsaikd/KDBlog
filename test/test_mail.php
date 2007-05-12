<?php
if (!isset($_SERVER["SHELL"]))
	die("Please run this script at shell\n");
if (!file_exists("config.php"))
	die("Please chdir to KDBlog root dir, and run 'php test/test_mail.php'\n");

include_once("config.php");

$body  = "this is a test mail for KDBlog\n";
$body .= "time: ".date(DATE_RFC2822, time())."\n";

echo("Sending a test mail to your email ('".$BLOGCONF["email"]."')\n");
if (mail($BLOGCONF["email"], "KDBlog test_mail", $body))
	echo("Sent mail successfully\nYou can check your email account now\n");
else
	echo("Sent mail failed\n");

?>
