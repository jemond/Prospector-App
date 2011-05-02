<h1>Add comment</h1>

<?php echo $form->create('Prospect', array('action' => 'comment/'.$prospect['Prospect']['id'])); ?>
<?php echo $form->textarea('Comment.note',array('rows'=>5,'cols'=>40)); ?>
<?php echo $form->input('Prospect.id', array('type'=>'hidden')); ?>

<br />

<?php echo $ajax->submit('Save', array(
	'loading'=>'Effect.Appear(\'Loading\')',
	'complete'=>'Effect.BlindUp(\'AddComment\'),updateContactLog(),Effect.Fade(\'Loading\'),Effect.Highlight(\'contactlog\')',
	'div'=>'inlinesubmit'
	)
); ?> 
<?php echo $html->link('or Cancel and dismiss this box','javascript:void(0);',array(
	'onClick'=>'Effect.BlindUp(\'AddComment\')'
	)
); ?>

<?php echo $form->end(); ?>
	

