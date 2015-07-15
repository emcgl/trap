<?php 
include_once dirname(__FILE__)."/../includes/database.php";
include_once dirname(__FILE__)."/../sitemap.php";
include_once dirname(__FILE__)."/../classes/user.class.php";

$user=$_SESSION['user']; 
?> 

<h1>Transcriptome Age Calculation Tool - Main</h1>
<?php 
echo $user->linkPage("useradmin");
echo $user->linkPage("logout"); 
?>

<?php 

