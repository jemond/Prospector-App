<div id="Page">

	<div id="Top"><h1>Edit </h1></div>

	<div id="Main">
	
		<?php echo $form->create('Source'); ?>
		
		<?php echo $form->input('name'); ?>
		<div class="input">
			<label for="SourceDisabled">Disabled</label>
			<?php echo $form->input('disabled',array(
				'label'=>false,
				'div'=>false
				)
			); ?>

		</div>
		
		<br /><br />
		
		<?php echo $form->input('id', array('type'=>'hidden')); ?>
		<?php echo $form->submit('Save', array('div'=>'inlinesubmit')); ?>&nbsp;
		<?php echo $html->link('or Cancel and return to the source list','/sources/'); ?>
		
		<?php echo $form->end(); ?>

	</div>
	
</div>
<div id="Bottom"></div>