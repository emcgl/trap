<?php 
include_once dirname(__FILE__)."/../includes/database.php";
include_once dirname(__FILE__)."/../sitemap.php";
include_once dirname(__FILE__)."/../classes/user.class.php";
include_once dirname(__FILE__)."/../includes/page.php";

$user=$_SESSION['user']; 
?> 

<h1>Transcriptomic Age Calculation Tool - Main</h1>
<?php 
echo Page::link("useradmin", $user);
echo Page::link("logout", $user); 
?>

<?php 

