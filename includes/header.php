<?php 

function gen_html_header($title) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Transcriptomic Age Prediction - <?php echo $title;?></title>
	<link rel="stylesheet" href="/css/trap.css">
	<script src="/js/trap.js"></script>
</head>
<body>
<?php include_once("analyticstracking.php") ?>
<div id="page">
	<h1 id="toptitle"><span style="color: #92b6cf;">Tr</span>anscriptomic <span style="color: #92b6cf;">A</span>ge <span style="color: #92b6cf;">P</span>rediction - <?php echo $title;?></h1>
	<div id="top"></div><!-- top -->
	<div id="content">
<?php 
}
?>