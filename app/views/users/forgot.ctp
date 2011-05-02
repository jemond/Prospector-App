<?php $session->flash();?>

<div id="Page">

	<div id="Top"><h2>Forgot password</h2></div>
	
	<div id="Main">
		
		<?php echo $form->create('User', array('action' => 'forgot','class'=>'largeform'));?> 
			<?php echo $form->input('email',array('size'=>30));?>
			<br />
			<?php echo $form->submit('Reset password', array('div'=>'inlinesubmit')); ?>&nbsp;
			<?php echo $html->link('or try logging in again','/login'); ?>
		<?php echo $form->end(); ?>

	</div>
	
</div>
<div id="Bottom"></div>