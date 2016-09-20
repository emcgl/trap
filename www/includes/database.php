<?php
include_once dirname(__FILE__).'/../config.php';

try {
	$db = new PDO("$dbdriver:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpasswd);
} catch (PDOException $e) {
	error_log ("Error: ".$e->getMessage());
	print "Error creating database connection!";
	die();
}

?>