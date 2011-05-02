<?php $session->flash();?>

<div id="Page">

	<div id="Top"><h1>Invite User</h1></div>
	
	<div id="Main">
	
		<p>We will send the user an email with login information:</p>
	
		<?php echo $form->create('Setting', array('action' => '/inviteuser')); ?>
		
		<?php echo $form->input('email',array('size'=>30)); ?>
		
		<br />
		
		<?php echo $form->submit('Invite', array('div'=>'inlinesubmit')); ?> 
		<?php echo $html->link('or Cancel and return to settings','/settings/'); ?>
		<?php echo $form->end(); ?>
	
	</div>
	
</div>
<div id="Bottom"></div>