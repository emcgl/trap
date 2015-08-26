<?php 
include_once dirname(__FILE__)."/includes/database.php";
include_once dirname(__FILE__)."/sitemap.php";
include_once dirname(__FILE__)."/classes/user.class.php";
include_once dirname(__FILE__)."/classes/job.class.php";

$user=null;

session_start();

//Session Data
if(isset($_SESSION['user'])) {
	$user = $_SESSION['user'];
}

//Download?
if(isset($_GET['download']) && isset($_GET['uid']) && isset($_GET['jid']) && $user!=null) {
	
	error_log("1");
	
	$uid=$_GET['uid'];
	$jid=$_GET['jid'];
	
	if($user->hasId($uid) || $user->isAdmin()) {
		
		error_log("2");
		
		if($job = Job::retrieve($jid)) {
		
			error_log("3");
			
			if($job->isOwnedBy($user) || $user->isAdmin()) {

				error_log("4");
				
				$job->retrieveOutput();
			
			}
		}
		
	}
}


include dirname(__FILE__)."/includes/header.php";

//Declarations
$numlevel=SiteMap::$UserLevels[SiteMap::$DefaultLevel];

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
	
	if( (isset($user) && $user->hasAccess($pagedata['level']))  ||
		(!isset($user) && $numlevel >= SiteMap::$UserLevels[$pagedata['level']]) )  {		
			$page = $gpage;
	} else {
		//Go to default!
		header("Location: /index.php?page=$page");
		exit;
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