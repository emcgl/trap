<?php

	include_once dirname(__FILE__)."/classes/user.class.php";
	include_once dirname(__FILE__)."/config.php";
	include_once dirname(__FILE__)."/sitemap.php";
	
	include dirname(__FILE__)."/includes/header.php";
	
	echo "<h1>Trying to retrieve user with ID 67</h1><br/>".PHP_EOL; 
	
	$user = User::retrieve(67);
	
	echo "<table>".PHP_EOL;
	echo $user->trth(true);
	echo $user->trtd(true);
	echo "</table>".PHP_EOL;
	
	include dirname(__FILE__)."/includes/footer.php";
	
?>
	