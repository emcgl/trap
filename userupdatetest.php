<?php

include_once dirname(__FILE__)."/classes/user.class.php";
include_once dirname(__FILE__)."/config.php";
include_once dirname(__FILE__)."/sitemap.php";

$user1=null;

try {
	$user1 = User::create("User One", "abcd1234", "admin", "userone@erasmusmc.nl");
} catch(Exception $e) {
	echo "Error: ".$e->getMessage();
}

$loginuser = User::login("User One", "abc123");
if( get_class($loginuser) == "User") {
	echo "Login worked as expected!<br/>".PHP_EOL;
}

echo "Updating password!<br/>";
$user1->update("", "1234dcba", "", "");

echo "Checking login with new password<br/>";
$loginuser = User::login("User One", "1234dcba");
if( get_class($loginuser) == "User") {
	echo "Login with new password worked as expected!<br/>".PHP_EOL;
}
echo "Updating everything else<br/>";
$user1->update("Name Change", "", "user", "changeuser@erasmusmc.nl");


