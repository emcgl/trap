<?php 
include_once dirname(__FILE__)."/../includes/database.php";
include_once dirname(__FILE__)."/../sitemap.php";
include_once dirname(__FILE__)."/../classes/user.class.php";
include_once dirname(__FILE__)."/../includes/page.php";

$user=$_SESSION['user']; 
?> 
<div class="view">
<div class="menu">
<?php 
echo Page::link("useradmin", $user);
echo Page::link("useredit", $user);
echo Page::link("adduser", $user);
echo Page::link("jobadmin", $user);
echo Page::link("definejob", $user);
echo Page::link("tutorial", $user);
echo Page::link("citation", $user);
echo Page::link("logout", $user);
?>
</div>
<div class="txt">
<p>On this website, you can calculate the Transcriptomic Age of your samples, based on gene expression levels measured with gene expression arrays (e.g. Illumina HumanHT-12 Expression BeadChips or Affymetrix Human Exon 1.0 ST GeneChips).</p>
<p>We developed the Transcriptomic Age Predictor using 8,847 whole blood samples (mean age 55.8 years) of eight independent population-based cohort studies with gene expression levels of 11,908 genes. We used a leave-one-out approach, so we trained the predictor in seven of eight cohorts, and tested the predictor in the left out cohort. The correlation between Transcriptomic Age (TA) and Chronological Age (CA) was significant in all cohorts (P<2E-29), and the difference between TA and CA (delta age (DA)) was consistently associated with higher systolic and diastolic blood pressure, total cholesterol, HDL cholesterol, fasting glucose levels, and body mass index (BMI).</p> 
<p>A GENERAL Transcriptomic Age Predictor was calculated using a prediction meta-analysis across all eight cohorts. Cohorts having chronological age available should use this GENERAL predictor: it scales TA using the mean and standard deviation (SD) of CA and the mean and SD of the predictor.</p> 
<p>To make our predictor useful to cohorts that do not have chronological age available, we further transformed the predictor to a SCALED Transcriptomic Age Predictor. This predictor was generated using the mean and SD of chronological age from our eight cohorts in the prediction meta-analysis.</p> 
<p>This website provides information on how to calculate Transcriptomic Age. After uploading your gene expression data, the Transcriptomic Age Predictor will return the estimated Transcriptomic Age for each sample.</p> 
</div>
</div>
<?php 

