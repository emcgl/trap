<?php

include_once dirname(__FILE__)."/classes/user.class.php";
include_once dirname(__FILE__)."/config.php";
include_once dirname(__FILE__)."/sitemap.php";

$admin=null;
$user=null;

echo "Creating 'Admin User'<br/>";
try {
	$admin = User::create("Admin User", "test123", "admin", "testuser@erasmusmc.nl");
} catch(Exception $e) {
	echo "Error: ".$e->getMessage()."<br/>".PHP_EOL;
}

echo "Creating 'Normal Users'<br/>";

for($i=0;$i<10;$i++) {

	try {
		$user = User::create("Normal User $i", "test123", "user", "normaluser$i@erasmusmc.nl");
	} catch(Exception $e) {
		echo "Error: ".$e->getMessage()."<br/>".PHP_EOL;
	}
}
