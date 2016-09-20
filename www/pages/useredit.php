<?php

	include_once dirname(__FILE__)."/../classes/user.class.php";
	include_once dirname(__FILE__)."/../config.php";
	include_once dirname(__FILE__)."/../sitemap.php";
	include_once dirname(__FILE__)."/../includes/page.php";	
			
	echo "<div class=\"view\">".PHP_EOL;
	
	$currentuser=null;	
	
	if(isset($_REQUEST) && $user=User::handle($_REQUEST)) {
		$currentuser=$user;
	} else {
		$currentuser=$_SESSION['user'];
	}	
	
	echo "<form action=\"/index.php?page=useredit\" onsubmit=\"return validatePassword();\" method=\"POST\">".PHP_EOL;
	echo User::form($level=false,$submitid="edit",$submitvalue="Confirm", $currentuser);	
	echo "</form>".PHP_EOL;
	
	echo "<br/>".PHP_EOL;
	echo "<br/>".PHP_EOL;
	echo "<br/>".PHP_EOL;

	echo "<div class=\"menu\">".PHP_EOL;
	echo Page::link('main', $currentuser);
	echo Page::link('logout', $currentuser);
	echo "</div>".PHP_EOL;
	echo "</div>".PHP_EOL;	
?>

