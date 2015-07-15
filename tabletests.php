<?php
include_once dirname(__FILE__)."/classes/user.class.php";
include_once dirname(__FILE__)."/config.php";
include_once dirname(__FILE__)."/sitemap.php";

$admin=null;


echo "Creating temp user!<br/>".PHP_EOL;

try {
	$admin = User::create("TableUser One", "abc123", "admin", "tableuserone@erasmusmc.nl");
} catch(Exception $e) {
	echo "Error: ".$e->getMessage();
}

echo "Generating table:<br/>".PHP_EOL;

echo User::userTable($admin);


echo "Cleaning up!<br/>".PHP_EOL;

$admin->delete();



