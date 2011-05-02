<?php $session->flash();?>

<div id="Page">

	<div id="Top"><h2>Sign Up for Prospector</h2></div>
	
	<div id="Main">
		
		<p class="callout"><?php echo $message; ?></p>
	
		<?php echo $form->create('User', array('action' => 'signup/'.$plan_name,'class'=>'largeform'));?> 
			
			<fieldset style="width:35em;">
		
				<legend>Your account</legend>
			
				<?php echo $form->input('User.email',array('size'=>30));?> 
				<?php echo $form->input('password1',array('size'=>30,'label'=>'Password','type'=>'password'));?>
				<?php echo $form->input('password2',array('size'=>30,'label'=>'Password again','type'=>'password'));?> 
				
			</fieldset>
			
			<fieldset style="width:35em;">
		
				<legend>You and your department</legend>
			
				<?php echo $form->input('Account.name',array('size'=>30,'label'=>'Company, unit or department name'));?>
				<?php echo $form->input('User.name',array('size'=>30,'label'=>'Your name'));?>
			
			</fieldset>
			
			<?php if($fPaid) : ?>
			
				<fieldset style="width:35em;">
			
					<legend>Billing</legend>
					
					<p>We bill on the first of each month. When you sign up we will charge 
					this credit card <?php echo $pretty->m($charge); ?>, the portion left in the current billing cycle
					(the time until the first of the month).</p>

					<?php echo $form->input('Billing.creditcard',array('size'=>30,'label'=>'Credit card number'));?>
					<?php echo $form->input('Billing.expiration_month',array('size'=>2,'label'=>'Expiration month'));?>
					<?php echo $form->input('Billing.expiration_year',array('size'=>4,'label'=>'Expiration year'));?>
					
				</fieldset>
			
			<?php endif; ?>
			
			<br />
			
			<?php echo $form->submit('Sign Up!'); ?>
			<?php echo $form->hidden('plan_name',array('value'=>$plan_name)); ?>
			
		<?php echo $form->end(); ?>
		
		<div class="hr"></div>
		
		<?php echo $html->link('Pricing and plans','/pricing'); ?> | 
		<?php echo $html->link('Login','/login'); ?> |
		<?php echo $html->link('Support','/support/'); ?> |
		<?php echo $html->link('Home','/'); ?>
		
	</div>
	
</div>
<div id="Bottom"></div>