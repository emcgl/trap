<?php 
include_once dirname(__FILE__)."/includes/database.php";
include_once dirname(__FILE__)."/sitemap.php";
include_once dirname(__FILE__)."/classes/user.class.php";

session_start();

include dirname(__FILE__)."/includes/header.php";

//Declarations
$user=null;
$numlevel=SiteMap::$UserLevels[SiteMap::$DefaultLevel];

//Session Data
if(isset($_SESSION['user'])) {
	$user = $_SESSION['user'];
}

//Default Page
$page = SiteMap::$StartPage;

//White listing page check
if(isset($_GET['page'])) {

	$gpage=$_GET['page'];
	
	$key = array_search( $gpage, array_column(SiteMap::$Pages, 'name') );			

	if(is_bool($key) && $key==false) {
		throw new Exception("Page not available!");				
	}
	
	$pagedata = SiteMap::$Pages[$key];
	
	if( ( isset($user) && $user->hasAccess($pagedata['level']) ) ||
		( !isset($user) && $numlevel >= SiteMap::$UserLevels[$pagedata['level']]) )  {		
			$page = $gpage;
		}
}

//Does page file exist?
if( ! file_exists(dirname(__FILE__)."/pages/".$page.".php") ) {
	throw new Exception("Can't open page file!");
	die();
}
			
include dirname(__FILE__)."/pages/$page.php";
 
include dirname(__FILE__)."/includes/footer.php";
 
?>