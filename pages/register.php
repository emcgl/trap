<?php 

include_once dirname(__FILE__)."/../classes/user.class.php";

$warning=null;

?>
<h1>Transcriptome Age Calculation Tool - Register</h1>

<?php 
if( isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['vpassword'])) {
	
	$name      = $_POST['name'];
	$email     = $_POST['email'];
	$password  = $_POST['password'];
	//$vpass     = $_POST['vpassword'];
	
	$level = "unvalidated";
	
	try {
		$user = User::create($name, $password, $level, $email);	
	} catch(Exception $e) {	
		$warning = $e->getMessage();
	}
	
	if(get_class($user)=="User") {
		
		$simplehash = $user->generateValidationCode();						
		
?>

<h2 class="message">Thanks for providing your personal data!</h2>
<p>A message will be send to the specified email address. Please follow the provided link in order to verify your email and activate your account!<p>

<?php
 		
	$to      = $email;
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
	$headers = 'From: anonymous' . "\r\n";  

	//mail($to, $subject, $message, $headers);
	
	echo "Message:".$message;
	
	exit(0);
	}
	
}


?>


<script>
function validateForm() {

	//Validate Password
	//Good length (min 8 chars)
	var passlength = document.forms["register"]["password"].value.length;
	if(passlength < 8) {
		alert("Please enter password with minimal length of 8 characters!");
		return false;
	}
	//Passwords match?
	var pass = document.forms["register"]["password"].value;
	var pass2 = document.forms["register"]["vpassword"].value;
	if( pass != pass2 ) {
		alert("Passwords don't match!");
		return false;
	}

	return true;
}
</script>

<h2>Please provide your personal data:</h2>
<?php 
	if(isset($warning) ) {
		echo "<div class=\"warning\">".$warning."</div><br/>";
	}
	
	?>
<form name="register" action="/index.php?page=register" onsubmit="return validateForm()" method="POST">
<div class="formlabel">Name:</div>
<input type="text" name="name" required/>
<br/>
<div class="formlabel">E-Mail (needed for validation of account and retrieval of results!):</div>
<input type="text" name="email" size="30" required />
<br/>
<div lass="formlabel">Password:</div>
<input type="password" name="password" required />
<div class="formlabel">Verify Password:</div>
<input type="password" name="vpassword" required />
<br/>
<br/>
<input type="submit" value="Submit">
</form>