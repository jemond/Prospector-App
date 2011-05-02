<?php $session->flash();?>

<div id="Page">

	<div id="Top"><h1>Edit settings</h1></div>
	
	<div id="Main">
	
		<?php echo $form->create('Setting', array('action' => '/edit')); ?>
		
		<?php echo $form->input('name',array('label'=>'Account name')); ?>
		
		<br />
		
		<?php echo $form->submit('Save', array('div'=>'inlinesubmit')); ?> 
		<?php echo $html->link('or Cancel and return to settings','/settings/'); ?>
		<?php echo $form->end(); ?>
	
	</div>
	
</div>
<div id="Bottom"></div>