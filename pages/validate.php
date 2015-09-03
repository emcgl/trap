<?php 
include_once dirname(__FILE__)."/../classes/user.class.php";
include_once dirname(__FILE__)."/../includes/page.php";
?>
<div class="view">
<?php
if(isset($_GET['email']) && isset($_GET['code'])) {
		
	$email=$_GET['email'];
	$code=$_GET['code'];
	
	try {
		
		User::confirmValidationCode($email, $code);
?>
	<div class="message">Thank you for validating your email address. Your account is enabled. </div><br/>
<?php 	
		echo "<div class=\"menu\">".PHP_EOL;
		echo Page::link('login');
		echo "<\div>".PHP_EOL;
		
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
</div>