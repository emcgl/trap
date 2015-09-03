<?php 

include_once dirname(__FILE__)."/../classes/user.class.php";
include_once dirname(__FILE__)."/../includes/page.php";

$user=$_SESSION['user']; 
?>
<div class="view">
<div class="txt">
<b>Requirements</b>
<ul>
<li>To run the Transcriptomic Age Prediction, your gene expression data need to contain either Illumina IDs (e.g., ILMN_1726589, ILMN_1773650, etc.) or GENE symbols (e.g., CD248, LRRN3, etc.). </li>
<li>Your gene expression data needs to be normalized and standardized: we prefer quantile-normalization to the median distribution; log2 transformation; probe centering, and sample Z-transformation.</li>
<li>Your gene expression data should be adjusted for a number of covariates (if available):
	<ul>
		<li>Sex (0=male, 1=female)</li>
		<li>Fasting (0=fasting, 1 = non-fasting)</li>
		<li>Smoking (0= non-smoking, 1 = smoking)</li>
		<li>RNA quality score (RQS or RIN)</li>
		<li>Batch effects (e.g., plate ID) </li>
		<li>Cell counts (#granulocytes, #lymphocytes, #monocytes, #erythrocytes, #platelets)</li>
	</ul>
	The easiest way to do so is to calculate the residuals. These can be used as gene expression input files for the Transcriptomic Age Prediction. 
</li>
<li>Both input files (gene expression and chronological age) need to be tab separated.</li>
<li>Your Sample IDs need to be non-numeric (i.e. Sample1, Sample2, etc.)</li>
<li>Your gene expression file should have genes as rows and samples as columns. </li>
<li>If you have missing gene expression values, please set them to 0.</li>
<li>Your age file should have samples as rows and age as column. </li>
<li>If you have missing age values, please take these individuals out of the chronological age file.</li>
</ul>
<b>Tutorial PDF</b>
<p>We tried to make this website as intuitive as possible. Just in case, you can download an extensive tutorial with extra information on this page.</p>
<a href="/downloads/TUTORIAL-TRAP.pdf">Click here to download the tutorial</a><br/>
<br/>
<b>Example files</b>
<p>The following zip file contains test input files as an example.</p>
<a href="/downloads/TESTFILES.zip">Click here to download some Test Files</a><br/>
<br/>
<b>Genes used</b>
<p>The following genes are used in the TRAP analysis, repectively for GENE symbols and Illumina IDs.</p>
<a href="/downloads/GENEID.txt">Click here for GENE symbols</a><br/>
<a href="/downloads/ILMNID.txt">Click here for Illumina IDs</a><br/>
</div>
<?php 
	echo "<div class=\"menu\">".PHP_EOL;
	echo Page::link('main', $user);
	echo Page::link('logout', $user); 
	echo "</div>".PHP_EOL;
?>
</div>