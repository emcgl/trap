<?php 

include_once dirname(__FILE__)."/../classes/user.class.php";
include_once dirname(__FILE__)."/../includes/page.php";

$user=$_SESSION['user']; 
?>
<div class="view">
<div class="txt">
<p>The Transcriptomic Age Prediction Tool has been described in:<p> 
<p><i>Peters MJ, Joehanes R, Pilling LC, Conneely K, Powell J et al. 2015: The transcriptional landscape of age in human peripheral blood. Nature Communications. 2015. DOI: . PMID: .</i><p> 
<!--   <a href="" disable>Link to article</a> -->
</div>
<?php 
	echo "<div class=\"menu\">".PHP_EOL;
	echo Page::link('main', $user);
	echo Page::link('logout', $user); 
	echo "</div>".PHP_EOL;
?>
</div>