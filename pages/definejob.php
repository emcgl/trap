<?php

include_once dirname(__FILE__)."/../classes/user.class.php";
include_once dirname(__FILE__)."/../classes/job.class.php";
include_once dirname(__FILE__)."/../includes/page.php";

?>
<h1>Transcriptomic Age Calculation Tool - Define Job</h1>
<?php 

$currentuser=$_SESSION['user'];

if(isset($_REQUEST)) {
	Job::handle($_REQUEST);	
}


?>
<form name="definejob" action="/index.php?page=definejob" onsubmit="" method="POST" enctype="multipart/form-data">
<?php 
echo Job::form();
echo "<br/>".PHP_EOL;
echo Page::link('main', $currentuser);
echo Page::link('logout', $currentuser);
?>
</form>