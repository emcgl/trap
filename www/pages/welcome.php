<div class="view">
<?php 
	include_once dirname(__FILE__)."/../includes/page.php";
	echo "<div class=\"menu\">";
	echo Page::link('login');
	echo Page::link('register');
	echo "</div>".PHP_EOL;
?>
<div class="txt">
<p>
On this website, you can calculate the Transcriptomic Age of your samples, based on gene expression levels measured with gene expression arrays (e.g. Illumina HumanHT-12 Expression BeadChips or Affymetrix Human Exon 1.0 ST GeneChips).
</p>
<p>The Transcriptomic Age Prediction Tool has been described in:<p> 
<p><i>Peters MJ, Joehanes R, Pilling LC, Conneely K, Powell J et al. The transcriptional landscape of age in human peripheral blood. Nature Communications. 2015 Oct 22;6:8570. DOI: 10.1038/ncomms9570.</i><p> 
<a href="http://www.nature.com/ncomms/2015/151022/ncomms9570/full/ncomms9570.html">Link to article</a>
</div>
</div>