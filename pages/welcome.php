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
</div>
</div>