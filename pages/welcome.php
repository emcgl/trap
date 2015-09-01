<?php 
	include_once dirname(__FILE__)."/../includes/page.php";
	echo "<div class=\"menu\">";
	echo Page::link('login');
	echo Page::link('register');
	echo "</div>".PHP_EOL;
?>
