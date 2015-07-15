<?php 
include_once dirname(__FILE__)."/../includes/database.php";
include_once dirname(__FILE__)."/../sitemap.php";
include_once dirname(__FILE__)."/../classes/user.class.php";
?>
<h1>Transcriptome Age Calculation Tool - User Administration</h1>
<?php

	$sql = "SELECT id, name, nlevel, email FROM users";

	$user_data = array();
	$dbout=null;
	
	try {
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$count = $stmt->rowCount();
		
		while($dbout = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$user_data = array()
		}

	} catch(PDOException $e) {
		error_log ("Error: ".$e->getMessage());
		print "Error checking duplicate name or email!";
	}

	do {
		$user
		
		
		
	} while($dbout = $stmt->fetch(PDO::FETCH_ASSOC));
		
	
	?>

