<?php $session->flash();?>

<div id="Page">

	<div id="Top"><h2>Activate your account</h2></div>
	
	<div id="Main">

		<p>To complete you account activation, pick a new password:</p>
		
		<?php echo $form->create('User', array('action' => 'activate/'.$hash,'class'=>'largeform'));?> 
			<div class="input"><label>Email</label><?php echo $email; ?></div>
			<?php echo $form->input('password1',array('size'=>30,'type'=>'password','label'=>'Password'));?>
			<?php echo $form->input('password2',array('size'=>30,'type'=>'password','label'=>'Password Again'));?> 
			<br />
			<?php echo $form->submit('Activate account');?> 
		<?php echo $form->end(); ?>
			
		<div class="hr"></div>
		
	</div>
	
</div>
<div id="Bottom"></div>