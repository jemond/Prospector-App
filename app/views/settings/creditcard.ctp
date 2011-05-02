<?php $session->flash();?>
<div id="Page">

	<div id="Top">
		<h1>Credit Card Information</h1>
	</div>
	
	<div id="Main">
		<?php if(strlen($cc) > 0) :?>
			<p>
				Your current credit card on file: ************<?php echo $cc; ?>.<br /> 
				<span class="subnote">
				If you want to remove your billing information from our system, 
				simply <?php echo $html->link('downgrade','/settings/plan'); ?> to the free plan and we will remove it automatically.
				</span>
			</p>
			
			<div class="hr"></div>
		<?php endif; ?>
		
		<p class="callout">Enter in a new credit card to use for billing:</p>
		
		<?php echo $form->create('Settings',array('action'=>'creditcard')); ?>
		
			<?php echo $form->input('Billing.creditcard',array('label'=>'Credit card number')); ?>
			
			<?php echo $form->input('Billing.expiration_month',array('label'=>'Expriation month','size'=>2)); ?>
			<?php echo $form->input('Billing.expiration_year',array('label'=>'Expriation year','size'=>4)); ?>
	
			<p class="subnote">You credit card will not be charged until the next billing date (the first of the month).</p>
			
			<?php echo $form->submit('Update', array('div'=>'inlinesubmit')); ?> 
			<?php echo $html->link('Cancel','/settings',array('class'=>'cancellink')); ?>
		
		<?php echo $form->end(); ?>
	</div>

</div>
<div id="Bottom"></div>