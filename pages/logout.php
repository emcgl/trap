<?php
if(isset($_SESSION['user'])) 
	unset($_SESSION['user']);
?>
<div class="view">
<h2 class="message">Goodbye!</h2>
</div>