<h1>Add a new touch</h1>

<?php echo $form->create('Prospect', array('action' => 'touch/'.$prospect['Prospect']['id'])); ?>
<?php echo $form->input('Touch.touchtype_id',array('label'=>'Touch type')); ?>
<?php echo $form->input('Touch.note',array('size'=>40)); ?>
<?php echo $form->input('Prospect.id', array('type'=>'hidden')); ?>

<br />

<?php echo $ajax->submit('Save touch', array(
	'loading'=>'Effect.Appear(\'Loading\')',
	'complete'=>'Effect.BlindUp(\'AddTouch\'),updateContactLog(),Effect.Fade(\'Loading\')',
	'div'=>'inlinesubmit'
	)
); ?> 
<?php echo $html->link('or Cancel and dismiss this box','javascript:void(0)',array(
	'onClick'=>'Effect.BlindUp(\'AddTouch\')'
	)
); ?>

<?php echo $form->end(); ?>
	

