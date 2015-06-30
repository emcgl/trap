<h1>Transcriptome Age Calculation Tool - Login</h1>
<?php 

include_once dirname(__FILE__)."/../classes/user.class.php";

//Initialize attempts
if(!isset($_SESSION['loginattempts'])) {
	$_SESSION['loginattempts']=0;
}

//Handle login
if( isset($_POST['name']) && isset($_POST['password'])) {
		
	$_SESSION['loginattempts']++;	
	if($_SESSION['loginattempts'] > 3) {
		echo "<div class=\"error\">Number of attempts exceeded!</div><br/>".PHP_EOL;
		return;
	}
	
	$name=$_POST['name'];
	$password=$_POST['password'];
	
	$user = User::login($name, $password);
	
	if($user==false) {
		echo "<div class=\"error\">Wrong user or password!</div><br/>".PHP_EOL;	
	} else {
		if(get_class($user)=="User") {
			$_SESSION['user']=$user;
			unset($_SESSION['loginattempts']);
			echo "<div class=\"message\">Welcome!</div><br/>".PHP_EOL;
			echo "<a href=\"/index.php?page=main\">Continue to Main Page</a><br/>".PHP_EOL; 
			return;
		} else {
			throw new Exception("Unexpected user class name!");
		}
	}
} 



?>

<h2>Please provide your credentials:</h2>
<form action="/index.php?page=login" method="POST">
<div class="formlabel">Name:</div>
<input type="text" name="name" />
<br/>
<div class="formlabel">Password:</div>
<input type="password" name="password" />
<br/>
<br/>
<input type="submit" value="Submit">
</form>