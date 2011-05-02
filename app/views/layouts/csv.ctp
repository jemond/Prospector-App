<?php 
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=export.csv");

	echo $content_for_layout; 	
?>