<div id="Page">

	<div id="Top">
		<h1>Edit Prospect</h1>
	</div>
	
	<div id="Main">

		<?php echo $form->create('Prospect', array('action' => 'edit')); ?>
		
		<fieldset>
		
			<legend>Summary</legend>
		
			<?php echo $form->input('firstname'); ?>
			<?php echo $form->input('lastname'); ?>
			<?php echo $form->input('source_id'); ?>
			<?php echo $form->input('cwh', array(
				'options'=>array(0=>'cold',1=>'warm',2=>'hot'),
				'type'=>'select',
				'legend'=>false,
				'label'=>'Cold/Warm/Hot'
				)
			); ?>
			<?php echo $form->input('campaign_id',array(
				'empty'=>true
				)
			); ?>
		
		</fieldset>
		
		<fieldset>
		
			<legend>Address</legend>
		
			<?php echo $form->input('address1'); ?>
			<?php echo $form->input('address2'); ?>
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
		<?php echo $form->submit('Save', array('div'=>'inlinesubmit')); ?> 
		<?php echo $html->link('or Cancel and return to the prospect','/prospects/view/'.$prospect['Prospect']['id']); ?>
		<?php echo $form->end(); ?>
		
	</div>
	
</div>
<div id="Bottom"></div>