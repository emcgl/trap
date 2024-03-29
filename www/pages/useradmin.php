<?php

	include_once dirname(__FILE__)."/../classes/user.class.php";
	include_once dirname(__FILE__)."/../config.php";
	include_once dirname(__FILE__)."/../sitemap.php";
	include_once dirname(__FILE__)."/../includes/page.php";	
			
	echo "<div class=\"view\">".PHP_EOL;
	
	$currentuser=$_SESSION['user'];	
	
	if(isset($_REQUEST)) {
		User::handle($_REQUEST);
	}		
	
	$edit=false;
	if(isset($_REQUEST['edit']) && $_REQUEST['edit']=='true') {
		$edit=true; 
	}
	
	$ids = User::getIds();

	echo "<form action=\"/index.php?page=useradmin\" method=\"POST\">".PHP_EOL;
	echo "<table class=\"inftbl\">".PHP_EOL;
	echo User::tableHeader($edit=$edit);
	foreach($ids as $id) {
		$user = User::retrieve($id);	
		echo $user->tableData($edit=$edit);
		unset($user);
	}
	echo "</table>".PHP_EOL;
	echo "<input id=\"edit\" name=\"edit\" type=\"hidden\" value=\"".($edit ? "true" : "false")."\">".PHP_EOL; 
	echo "<br/>".PHP_EOL;	
	if($edit) 
		echo "<input class=\"btn\" type=\"submit\" value=\"Read Only Mode\" onclick=\"return updateValue('edit', 'false');\">".PHP_EOL;
	else
		echo "<input class=\"btn\" type=\"submit\" value=\"Edit Mode\" onclick=\"return updateValue('edit', 'true');\">".PHP_EOL;
	
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

