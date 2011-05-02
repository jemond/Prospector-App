<div id="Page">

	<div id="Top">
		<h1>Change your plan</h1>
	</div>
	
	<div id="Main">
	
		<p class="callout">Your current plan: <?php echo $account['Plan']['name']; ?></p>
		<ul class="subnote">
			<li>Prospect limit: <?php echo $account['Plan']['prospect_limit']; ?></li>
			<li>Monthly cost: <?php echo $pretty->m($account['Plan']['monthly_cost']); ?></li>
			<li>Unlimited campaigns</li>
			<li>Unlimited custom letters</li>
			<li>Unlimited users</li>
		</ul>
		
		<p class="callout">Plans</p>
		
		<table summary="Plans" id="PriceTable">
			<thead>
				<tr>
					<th></th>
					<th>Bronze</th>
					<th>Silver</th>
					<th>Gold</th>
				</tr>
			</thead>
			<tbody>
				<?php echo $this->element('pricing-table-rows'); ?>
			</tbody>
		</table>
		
		<p class="callout">Choose your new plan</p>
		<?php echo $form->create('Settings',array('action'=>'plan')); ?>
			<p>
			New plan:<br /> <?php echo $form->select('Plan.id',$plans,null,null,false); ?><br /><br />
			
			Payment method:<br />
			<?php if(strlen($account['Account']['cc']) == 0) : ?>
				You need to <?php echo $html->link('add a credit card','/settings/creditcard'); ?> to your account before you can change to a paid plan.
			<?php else : ?>
				************<?php echo $account['Account']['cc']; ?> <?php echo $html->link('Change','/settings/creditcard'); ?>
			<?php endif; ?><br /><br />
			
			Charges or refunds:
			</p>

			<ul>
			<?php foreach($plan_charges as $plan_id =>$charge) : ?>
				<?php if($account['Account']['plan_id'] != $plan_id) : ?>
					<li><?php echo $charge['name']; ?>: <?php echo $pretty->prorate($charge['charge']); ?></li>
				<?php endif; ?>
			<?php endforeach; ?>
			</ul>
			
			<p>Important notes:</p>
			
			<ul class="subnote">
				<li>When you change your plan we will charge (or refund) you a pro-rate of the monthly fee for your new plan (based on the number
				of days until the first of the month).</li>
				<li>If you downgrade to a plan that puts you over the open prospect limit, we will automatically close enough
				prospects to bring you to the prospect limit.</li>
			</ul>
		
			<?php echo $form->submit('Change plan', array('div'=>'inlinesubmit')); ?> 
			<?php echo $html->link('Cancel','/settings/'); ?>	
		<?php echo $form->end(); ?>
	
	</div>

</div>
<div id="Bottom">

</div>