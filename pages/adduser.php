<?php

	include_once dirname(__FILE__)."/../classes/user.class.php";
	include_once dirname(__FILE__)."/../config.php";
	include_once dirname(__FILE__)."/../sitemap.php";
	include_once dirname(__FILE__)."/../includes/page.php";	

	$currentuser=$_SESSION['user'];

	echo "<div class=\"view\">".PHP_EOL;
	
	if(isset($_REQUEST)) {
		User::handle($_REQUEST);
	}
	
	echo "<form action=\"/index.php?page=adduser\" onsubmit=\"return validatePassword();\" method=\"POST\">".PHP_EOL;
	echo User::form($level=true,$submitid="add",$submitvalue="Add");	
	echo "</form>".PHP_EOL;
	echo "<br/>".PHP_EOL;
	echo "<div class=\"menu\">".PHP_EOL;
	echo Page::link('main', $currentuser);
	echo Page::link('logout', $currentuser);
	echo "</div>".PHP_EOL;
	echo "</div>".PHP_EOL;