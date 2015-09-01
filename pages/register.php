<?php 

include_once dirname(__FILE__)."/../config.php";
include_once dirname(__FILE__)."/../classes/user.class.php";
include_once dirname(__FILE__)."/../includes/page.php";
include_once "Mail.php";
include_once "Mail/mime.php";

$warning=null;
echo "<div class=\"view\">".PHP_EOL;
if( isset($_POST) && $user=User::handle($_POST)) {
		$user->update($name="", $password="", $level="unvalidated", $email="");	
		$recipients=$_POST['email'];
		$simplehash = $user->generateValidationCode();						
?>
<h2 class="message">Thanks for providing your personal data!</h2>
<p>A message will be send to the specified email address. Please follow the provided link in order to verify your email and activate your account!<p>
<?php
	global $from, $smtp; 			
	
	$headers['From']=$from;
	$headers['Subject']="validate your trap account";
	$headers['Date']=date(DATE_RFC2822);
	
	$mime = new Mail_mime(array('eol' => "\n"));

	$htmlmsg = "Dear Sir / Madam\n".
		   "\n".
		   "This is an automated message!\n".
		   "\n".
		   "Please follow the link below to activate your account:\n".
		   "\n".
		   "<a href=\"https://".$_SERVER['SERVER_NAME']."/index.php?page=validate&email=".$recipients."&code=".$simplehash."\">Click here to verify account!</a>\n";

	$txtmsg =  "Dear Sir / Madam\n".
                   "\n".   
                   "This is an automated message!\n".
                   "\n".   
                   "\n".   
                   "Copy the following link in your browser:\n".
                   "\n".   
                   "https://".$_SERVER['SERVER_NAME']."/index.php?page=validate&email=".$email."&code=".$simplehash."\n";

	$body = $mime->setTXTBody($txtmsg); 
	$body = $mime->setHTMLBody($htmlmsg);

	$body = $mime->get();
	$hdrs = $mime->headers($headers);

	$params['host']=$smtp;
	$params['debug']=FALSE;
	
	$mail_object =& Mail::factory('smtp', $params);
	$mail_object->send($recipients, $hdrs, $body);
	
	exit(1);
		
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
</div>