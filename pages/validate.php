<?php 
include_once dirname(__FILE__)."/../classes/user.class.php";
include_once dirname(__FILE__)."/../includes/page.php";
?>
<h1>Transcriptomic Age Calculation Tool - Validate Email Adress</h1>
<?php 

if(isset($_GET['email']) && isset($_GET['code'])) {
		
	$email=$_GET['email'];
	$code=$_GET['code'];
	
	try {
		
		User::confirmValidationCode($email, $code);
?>
	<div class="message">Thank you for validating your email address. Your account is enabled. </div><br/>
<?php 	

		echo Page::link('login');
		
	} catch(Exception $e ) {
?>
	<div class="error"><?php echo $e->getMessage();?></div>
<?php 		
	}
		
} else {
?>	
	<div class="error">Error validating your account!</div><br/>
<?php
}

?>