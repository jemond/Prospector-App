<html>
<head>
	<?php echo $html->charset('utf-8'); ?>
	<link rel="stylesheet" href="/css/app.css">
	<link rel="stylesheet" href="/css/common.css">
	<title><?php echo $title_for_layout?> - Prospector</title>
	<?php echo $scripts_for_layout ?>
	<?php echo $javascript->link('prototype'); ?>
	<?php echo $javascript->link('scriptaculous.js?load=effects'); ?>
</head>
<body>
	<div id="Header">
		<?php echo $html->link($html->image('logo-small.png',array('title'=>'Prospector, the dead simple way to turn prospects into applicants.')),'/',array('escape'=>false)); ?>
	</div>
	
	<div id="Content">
		<?php echo $content_for_layout; ?>
	</div>
	
</body>
</html>