<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title>Prospector, Prospect management for higher-ed - <?php echo $title_for_layout?></title>
	<?php echo $html->css('public.css'); ?>
	<?php echo $html->css('common.css'); ?>
	<?php echo $html->charset('utf-8'); ?>
	<?php echo $scripts_for_layout ?>
	<?php echo $javascript->link('prototype'); ?>
	<?php echo $javascript->link('scriptaculous.js?load=effects'); ?>
</head>
<body>
	<div id="Wrapper">
		<div id="Header">
			<?php echo $html->link($html->image('logo-full.png',array('title'=>'Prospector, the dead simple way to turn prospects into applicants.')),'/',array('escape'=>false)); ?>
		</div>
		
		<div id="Navigation">
			<?php echo $this->element('navigation-public'); ?>
		</div>
		
		<div id="Content">
			<?php echo $content_for_layout; ?>
		</div>
	</div>	
</body>
</html>