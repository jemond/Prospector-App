<div id="Page">

	<div id="Top">
		<h1>Create a new prospect</h1>
	</div>
	
	<div id="Main">

		<?php echo $form->create('Prospect'); ?>
		
		<fieldset>
		
			<legend>Summary</legend>
		
			<?php echo $form->input('firstname'); ?>
			<?php echo $form->input('lastname'); ?>
			<div class="input">
				<label id="ProspectSourceId">Source</label><?php echo $form->select('source_id', $sources, null, null); ?>
				<?php echo $form->error('source_id'); ?>
			</div>
			<div class="formnote"><p>Where did the record come from?</p></div>
			
			<div class="input"><label id="ProspectCampaignId">Campaign</label><?php echo $form->select('campaign_id', $campaigns); ?></div>
			<div class="formnote"><p>Track and generate materials for the prospect in a group of prospects.</p></div>
		
		</fieldset>
		
		<fieldset>
		
			<legend>Address</legend>
		
			<?php echo $form->input('address1',array('label'=>'Address 1')); ?>
			<?php echo $form->input('address2',array('label'=>'Address 2')); ?>
			<?php echo $form->input('city'); ?>
			<?php echo $form->input('state',array('label'=>'State/province')); ?>
			<?php echo $form->input('postalcode',array('label'=>'Zip code')); ?>
			<?php echo $form->input('country'); ?>
			<?php echo $form->input('phone'); ?>
			<?php echo $form->input('email'); ?>
		
		</fieldset>
		
		<fieldset>
		
			<legend>Academic Information</legend>
		
			<?php echo $form->input('educationlevel',array('label'=>'Education level')); ?>
			<?php echo $form->input('objective'); ?>
			<?php echo $form->input('pois',array('label'=>'POIs')); ?>
		
		</fieldset>
		
		<?php echo $form->input('id', array('type'=>'hidden'));  ?>
		<?php echo $form->end('Save Prospect'); ?>

	</div>
	
</div>
<div id="Bottom"></div>