<?php 

include_once dirname(__FILE__)."/../config.php";
include_once dirname(__FILE__)."/../classes/user.class.php";
include_once dirname(__FILE__)."/../includes/page.php";

$warning=null;

?>
<h1>Transcriptomic Age Calculation Tool - Register</h1>

<?php 
if( isset($_POST) && $user=User::handle($_POST)) {

		$user->update($name="", $password="", $level="unvalidated", $email="");	

		$email=$_POST['email'];
		
		$simplehash = $user->generateValidationCode();						
		
?>

<h2 class="message">Thanks for providing your personal data!</h2>
<p>A message will be send to the specified email address. Please follow the provided link in order to verify your email and activate your account!<p>

<?php
 			

	$subject = 'validate your account!';
	$message = "Dear Sir / Madam\n".
			   "\n".
			   "This is an automated message!\n".
			   "\n".
			   "Please follow the link below to activate your account:\n".
			   "\n".
			   "<a href=\"".(isset($_SERVER['HTTPS']) ? "https://" : "http://").$_SERVER['SERVER_NAME']."/index.php?page=validate&email=".$email."&code=".$simplehash."\">Click here to verify account!</a>\n".
			   "\n".
			   "Or copy the following link in your browser:\n".
			   "\n".
			   (isset($_SERVER['HTTPS']) ? "https://" : "http://").$_SERVER['SERVER_NAME']."/index.php?page=validate&email=".$email."&code=".$simplehash."\n".
			   "\n";			   
	$headers = 'From: $from' . "\r\n";  

	//mail($to, $subject, $message, $headers);
	
	echo "Message:".$message;
	
	exit(0);
		
}


?>

<h2>Please provide your personal data:</h2>
<?php 
	if(isset($warning) ) {
		echo "<div class=\"warning\">".$warning."</div><br/>";
	}
	
	?>
<form name="register" action="/index.php?page=register" onsubmit="return validatePassword()" method="POST">
<?php 
echo User::form($level=false,$submitid="register",$submitvalue="Register");
?>
</form>