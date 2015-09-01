<?php

include_once dirname(__FILE__)."/../classes/user.class.php";
include_once dirname(__FILE__)."/../classes/job.class.php";
include_once dirname(__FILE__)."/../includes/page.php";

$currentuser=$_SESSION['user'];

echo "<div class=\"view\">".PHP_EOL;

if(isset($_REQUEST)) {
	try {
		Job::handle($_REQUEST);
	} catch(Exception $e) {
		echo "<div class=\"error\">Error processing job data: ".$e->getMessage()."</div><br/>".PHP_EOL;
	}
}

?>
<form name="definejob" action="/index.php?page=definejob" onsubmit="" method="POST" enctype="multipart/form-data">
<?php 
echo Job::form();
echo "<br/>".PHP_EOL;
?>
</form>
<div class="menu">
<?php
echo Page::link('main', $currentuser);
echo Page::link('jobadmin', $currentuser);
echo Page::link('logout', $currentuser);
?>
</div>
</div>