<?php

	include_once dirname(__FILE__)."/../classes/user.class.php";
	include_once dirname(__FILE__)."/../config.php";
	include_once dirname(__FILE__)."/../sitemap.php";
	include_once dirname(__FILE__)."/../includes/page.php";	
	
?>
<h1>Transcriptomic Age Calculation Tool - Add User</h1>
<?php 	
	
	$currentuser=$_SESSION['user'];

	if(isset($_REQUEST)) {
		User::handle($_REQUEST);
	}
	
	echo "<form action=\"/index.php?page=adduser\" onsubmit=\"return validatePassword();\" method=\"POST\">".PHP_EOL;
	echo User::form($level=true,$submitid="add",$submitvalue="Add");	
	echo "</form>".PHP_EOL;
	echo "<br/>".PHP_EOL;
	echo Page::link('main', $currentuser);
	echo Page::link('logout', $currentuser);
	