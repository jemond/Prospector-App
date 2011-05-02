<?php 
	header('Content-Type: application/rtf');
	header('Content-Disposition: attachment; filename="letter.rtf"');

	echo $content_for_layout; 	
?>