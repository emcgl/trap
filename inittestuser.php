<?php

include_once dirname(__FILE__)."/classes/user.class.php";
include_once dirname(__FILE__)."/config.php";
include_once dirname(__FILE__)."/sitemap.php";

$admin=null;
$user=null;

try {
	$admin = User::create("Admin User", "test123", "admin", "testuser@erasmusmc.nl");
} catch(Exception $e) {
	echo "Error: ".$e->getMessage();
}

try {
	$user = User::create("Normal User", "test123", "normal", "normaluser@erasmusmc.nl");
} catch(Exception $e) {
	echo "Error: ".$e->getMessage();
}
