<div id="Page">

	<div id="Top"><h1>Add source</h1></div>
	
	<div id="Main">

		<?php echo $form->create('Source'); ?>
		
		<?php echo $form->input('name'); ?>
		
		<?php echo $form->submit('Save', array('div'=>'inlinesubmit')); ?>&nbsp;
		<?php echo $html->link('or Cancel and return to the source list','/sources/'); ?>
		
		<?php echo $form->end(); ?>
		
	</div>
	
</div>
<div id="Bottom"></div>
