<?php 

include_once dirname(__FILE__)."/../classes/user.class.php";
include_once dirname(__FILE__)."/../includes/page.php";

$user=$_SESSION['user']; 
?>
<div class="view">
<div class="txt">
<p>The Transcriptomic Age Prediction Tool has been described in:<p> 
<p><i>Peters MJ, Joehanes R, Pilling LC, Conneely K, Powell J et al. The transcriptional landscape of age in human peripheral blood. Nature Communications. 2015 Oct 22;6:8570. DOI: 10.1038/ncomms9570.</i><p> 
<a href="http://www.nature.com/ncomms/2015/151022/ncomms9570/full/ncomms9570.html">Link to article</a>
</div>
<?php 
	echo "<div class=\"menu\">".PHP_EOL;
	echo Page::link('main', $user);
	echo Page::link('logout', $user); 
	echo "</div>".PHP_EOL;
?>
</div>