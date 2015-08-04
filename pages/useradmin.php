<?php 
include_once dirname(__FILE__)."/../includes/database.php";
include_once dirname(__FILE__)."/../sitemap.php";
include_once dirname(__FILE__)."/../classes/user.class.php";
include_once dirname(__FILE__)."/../includes/page.php";

?>
<h1>Transcriptomic Age Calculation Tool - User Administration</h1>
<?php

	$sql = "SELECT id, name, nlevel, email FROM users";

	$user_data = array();
	$dbout=null;
	
	try {
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$count = $stmt->rowCount();
		
		//print $count;
		
		if($count>0) {
			
			print "<table class=\"userstable\">".PHP_EOL;
			print "<tr><th>id</th><th>name</th><th>nlevel</th><th>email</th>".PHP_EOL;
			
			while($dbout = $stmt->fetch(PDO::FETCH_ASSOC)) {
				print "<tr><td>".$dbout["id"]."</td><td>".$dbout["name"]."</td><td>".$dbout["nlevel"]."</td><td>".$dbout["email"]."</td></tr>".PHP_EOL;
			}
		
			print "</table>".PHP_EOL;
		}

	} catch(PDOException $e) {
		error_log ("Error: ".$e->getMessage());
		print "Error checking duplicate name or email!";
	}

		
?>

