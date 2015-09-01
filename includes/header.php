<?php 

function gen_html_header($title) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Transcriptomic Age Calculation - <?php echo $title;?></title>
	<link rel="stylesheet" href="/css/tragca.css">
	<script src="/js/tragca.js"></script>
</head>
<body>
<div id="page">
	<div id="top">
		<h1 id="toptitle">Transcriptomic Age Calculation - <?php echo $title;?></h1>
	</div><!-- top -->
	<div id="content">
<?php 
}
?>