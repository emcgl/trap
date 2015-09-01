<?php

include_once dirname(__FILE__)."/../classes/user.class.php";
include_once dirname(__FILE__)."/../classes/job.class.php";
include_once dirname(__FILE__)."/../config.php";
include_once dirname(__FILE__)."/../sitemap.php";
include_once dirname(__FILE__)."/../includes/page.php";

	
	$currentuser=$_SESSION['user'];

	if(isset($_REQUEST)) {
		Job::handle($_REQUEST);
	}		
	
	$edit=false;
	if(isset($_REQUEST['edit']) && $_REQUEST['edit']=='true') {
		$edit=true; 
	}
	
	$ids="";
	if($user->isAdmin())
		$ids=Job::getAllIds($currentuser);
	else 
		$ids=Job::getIds($currentuser);
	
	echo "<div class=\"view\">".PHP_EOL;
	if($ids==false) {
		
		echo "<div class=\"message\">No (more) jobs available for you!</div>".PHP_EOL;
 		
	} else {
		
		echo "<form action=\"/index.php?page=jobadmin\" method=\"POST\">".PHP_EOL;
		echo "<table>".PHP_EOL;
		echo Job::tableHeader($edit=$edit,$admin=$currentuser->isAdmin());
		foreach($ids as $id) {
			$job = Job::retrieve($id);	
			echo $job->tableData($edit=$edit,$admin=$currentuser->isAdmin());
			unset($job);
		}
		echo "</table>".PHP_EOL;
		echo "<input id=\"edit\" name=\"edit\" type=\"hidden\" value=\"".($edit ? "true" : "false")."\">".PHP_EOL; 
		echo "<br/>".PHP_EOL;	
		if($edit) 
			echo "<input type=\"submit\" value=\"Read Only Mode\" onclick=\"return updateValue('edit', 'false');\">".PHP_EOL;
		else
			echo "<input type=\"submit\" value=\"Edit Mode\" onclick=\"return updateValue('edit', 'true');\">".PHP_EOL;
	
		echo "</form>".PHP_EOL;
	}
	
	echo "<br/>".PHP_EOL;
	echo "<div class=\"menu\">".PHP_EOL;
	echo Page::link('main', $currentuser);
	echo Page::link('logout', $currentuser);
	echo "</div>".PHP_EOL;
	echo PHP_EOL;

	#Automatically reload page
	echo "<script type=\"text/javascript\">".PHP_EOL;
	echo "setTimeout(function(){".PHP_EOL;
	echo "  window.location.href=\"/index.php?page=jobadmin".($edit ? "&edit=true" : "")."\"".PHP_EOL;
	echo "}, 10000);".PHP_EOL;
	echo "</script>".PHP_EOL;
	echo "</div>".PHP_EOL;
?>

