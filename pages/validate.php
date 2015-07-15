<h1>Transcriptome Age Calculation Tool - Validate Email Adress</h1>
<?php 

if(isset($_GET['email']) && isset($_GET['code'])) {
		
	$email=$_GET['email'];
	$code=$_GET['code'];
	
	try {
		
		User::confirmValidationCode($email, $code);
?>
	<div class="message">Thank you for validating your email address. Your account is enabled. </div><br/>
	<a class="menu" href="/index.php?page=login">Login</a><br/>
<?php 
		
	} catch(Exception $e ) {
?>
	<div class="error"><?php echo $e->getMessage();?></div>
<?php 		
	}
		
}

?>