<?php

include_once dirname(__FILE__)."/classes/user.class.php";
include_once dirname(__FILE__)."/config.php";
include_once dirname(__FILE__)."/sitemap.php";

$admin=null;
$user1=null;
$user2=null;

try {
	$admin = User::create("User One", "abc123", "admin", "userone@erasmusmc.nl");
} catch(Exception $e) {
	echo "Error: ".$e->getMessage();
}

try {
	$user1 = User::create("User Two", "abc246", "normal", "usertwo@erasmusmc.nl");
} catch(Exception $e) {
	echo "Error: ".$e->getMessage();
}

try {
	$user2 = User::create("User Three", "abc567", "normal", "userthree@erasmusmc.nl");
} catch(Exception $e) {
	echo "Error: ".$e->getMessage();
}

$loginuser = User::login("User One", "abc123");
if( get_class($loginuser) == "User") {
	echo "Login worked as expected!<br/>".PHP_EOL;
}

$loginuser2 = User::login("User Two", "abcWRONG");
if($loginuser2==false) {
	echo "Login failed as expected!<br/>".PHP_EOL;
}

echo "Cleaning up!<br/>".PHP_EOL;

$admin->delete();
$user1->delete();
$user2->delete();


