<?php $session->flash();?>

<div id="Page">

	<div id="Top"><h2>Reset Password</h2></div>
	
	<div id="Main">

		<p>Pick a new password for your account:</p>
		
		<?php echo $form->create('User', array('action' => 'reset/'.$hash,'class'=>'largeform'));?> 
			<div class="input"><label>Email</label><?php echo $email; ?></div>
			<?php echo $form->input('password1',array('size'=>30,'type'=>'password','label'=>'Password'));?>
			<?php echo $form->input('password2',array('size'=>30,'type'=>'password','label'=>'Password Again'));?> 
			<?php echo $form->submit('Reset password');?> 
		<?php echo $form->end(); ?>
			
		<div class="hr"></div>
		
	</div>
	
</div>
<div id="Bottom"></div>