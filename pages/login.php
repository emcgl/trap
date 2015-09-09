<?php 

include_once dirname(__FILE__)."/../classes/user.class.php";
include_once dirname(__FILE__)."/../includes/page.php";

//Initialize attempts
if(!isset($_SESSION['loginattempts'])) {
	$_SESSION['loginattempts']=0;
}

//Handle login
if( isset($_POST['name']) && isset($_POST['password'])) {
	
	echo "<div class=\"view\">".PHP_EOL;
		
	$_SESSION['loginattempts']++;	
	if($_SESSION['loginattempts'] > 3) {
		echo "<div class=\"error\">Number of attempts exceeded!</div><br/>".PHP_EOL;
		echo "</div>".PHP_EOL;
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
			unset($_SESSION['loginattempts']); //2do <- new IP or session resets password guessing!
			echo "<h2 class=\"message\">Welcome!</h2><br/>".PHP_EOL;
			echo "<div class=\"menu\">".PHP_EOL;
			echo Page::link('main', $user);
			echo Page::link('logout', $user); 
			echo "</div>".PHP_EOL;
			echo "</div><!-- view -->".PHP_EOL;
			return;
		} else {
			throw new Exception("Unexpected user class name!");
		}
	}	
	echo "</div>".PHP_EOL;	
} 

?>
<div class="view">
	<h2>Please provide your credentials:</h2>
	<form action="/index.php?page=login" method="POST">
	<table class="frmtbl">
	<tr><th>Username:</th><td><input type="text" name="name" /></td></tr>
	<tr><th>Password:</th><td><input type="password" name="password" /></td></tr>
	</table>
	<br/>
	<br/>
	<input class="btn" type="submit" value="Submit">
	</form>
</div>