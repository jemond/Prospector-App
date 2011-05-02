<?php $session->flash();?>

<div id="Page">

	<div id="Top"><h2>Login</h2></div>
	
	<div id="Main">
		
		<?php echo $form->create('User', array('action' => 'login','class'=>'largeform'));?> 
			<?php echo $form->input('email',array('size'=>30));?> 
			<?php echo $form->input('password',array('size'=>30));?> 
			<?php echo $form->submit('Login');?> 
		<?php echo $form->end(); ?>

		<div class="hr"></div>
		
		<?php echo $html->link('Forget your password?','/users/forgot'); ?> |
		<?php echo $html->link('Need to sign up?','/signup'); ?> |
		<?php echo $html->link('Support','/support/'); ?> | 
		<?php echo $html->link('Home','/'); ?>
		
	</div>
	
</div>
<div id="Bottom"></div>