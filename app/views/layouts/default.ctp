<?php header('Content-type: text/html; charset=UTF-8') ;?><html>
<head>
	<?php echo $html->charset('utf-8'); ?>
	<?php echo $html->css('app.css'); ?>
	<?php echo $html->css('common.css'); ?>
	<title><?php echo $title_for_layout?> - Prospector</title>
	<?php echo $scripts_for_layout ?>
	<?php echo $javascript->link('prototype'); ?>
	<?php echo $javascript->link('scriptaculous.js?load=effects'); ?>
</head>
<body>
	<div id="Header">
		<div id="UserBar">
			<?php echo $pretty->blankProtection($session->read('Account.name'),'name'); ?>:
			<?php echo $html->link($pretty->username(false,$session->read('User.email')),'/users/edit/'.$session->read('User.id')); ?>
			<?php if($session->read('User.admin') == 1) :?><?php echo $html->link('Settings','/settings'); ?><?php endif; ?>
			<?php echo $html->link('Help','/help',array('title'=>'Help documentation','target'=>'blank')); ?>
			<?php echo $html->link('Logout', array('controller' => 'users', 'action' => 'logout')); ?>
		</div>
		
		<div id="Navigation"><?php echo $this->element('navigation-app'); ?></div>
			
	</div>
	
	<div id="Content">
		<?php echo $content_for_layout; ?>
	</div>
	
	<div id="FooterLogo"><?php echo $html->link($html->image('logo-footer-bw.png',array('id'=>'footerlogo')),'/',array(
		'escape'=>false,
		'title'=>'Prospector',
		'target'=>'blank',
		'onmouseover'=>"$('footerlogo').writeAttribute('src','/img/logo-footer.png')",
		'onmouseout'=>"$('footerlogo').writeAttribute('src','/img/logo-footer-bw.png')"
		)
	);?></div>
	
</body>
</html>