<?php
include_once dirname(__FILE__)."/../includes/page.php";

if(isset($_SESSION['user'])) 
	unset($_SESSION['user']);
?>
<div class="view">
<h2 class="message">Goodbye!</h2>
<br/>
<?php 

	echo "<div class=\"menu\">";
	echo Page::link('login');
	echo "</div>".PHP_EOL;
?>
</div>