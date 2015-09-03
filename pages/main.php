<?php 
include_once dirname(__FILE__)."/../includes/database.php";
include_once dirname(__FILE__)."/../sitemap.php";
include_once dirname(__FILE__)."/../classes/user.class.php";
include_once dirname(__FILE__)."/../includes/page.php";

$user=$_SESSION['user']; 
?> 
<div class="view">
<div class="menu">
<?php 
echo Page::link("useradmin", $user);
echo Page::link("adduser", $user);
echo Page::link("jobadmin", $user);
echo Page::link("definejob", $user);
echo Page::link("tutorial", $user);
echo Page::link("citation", $user);
echo Page::link("logout", $user);
?>
</div>
</div>
<?php 

