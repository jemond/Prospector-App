<?php header('Content-type: text/html; charset=UTF-8') ;?><html>
<head>
	<?php echo $html->charset('utf-8'); ?>
	<title>Prospector Admin</title>
	<?php echo $scripts_for_layout ?>
</head>
<body>

<div id="Navigation">Administration: <?php echo $this->element('navigation-admin'); ?></div>

<hr />
	
<div id="Body">
<?php echo $content_for_layout; ?>
</div>
	
</body>
</html>