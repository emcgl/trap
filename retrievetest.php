<?php

	include_once dirname(__FILE__)."/classes/user.class.php";
	include_once dirname(__FILE__)."/config.php";
	include_once dirname(__FILE__)."/sitemap.php";
	
	include dirname(__FILE__)."/includes/header.php";
	
	echo "<h1>Trying to retrieve users</h1><br/>".PHP_EOL; 
	
	if(isset($_REQUEST)) {
		User::handle($_REQUEST);
	}		
	
	$edit=false;
	if(isset($_REQUEST['edit']) && $_REQUEST['edit']=='true') {
		$edit=true; 
	}
	
	$ids = User::getIds();
		
	echo "<form action=\"/retrievetest.php\" method=\"POST\">".PHP_EOL;
	echo "<table>".PHP_EOL;
	echo User::tableHeader($edit);
	foreach($ids as $id) {
		$user = User::retrieve($id);	
		echo $user->tableData($edit);
		unset($user);
	}
	echo "</table>".PHP_EOL;
	echo "<input id=\"edit\" name=\"edit\" type=\"hidden\" value=\"".($edit ? "true" : "false")."\">".PHP_EOL; 
	echo "<br/>".PHP_EOL;	
	if($edit) 
		echo "<input type=\"submit\" value=\"Read Only Mode\" onclick=\"return updateValue('edit', 'false');\">".PHP_EOL;
	else
		echo "<input type=\"submit\" value=\"Edit Mode\" onclick=\"return updateValue('edit', 'true');\">".PHP_EOL;
	
	echo "</form>".PHP_EOL;
	
	include dirname(__FILE__)."/includes/footer.php";
	
?>
	