<h1>Add comment</h1>

<?php echo $form->create('Prospect', array('action' => 'touchback/'.$prospect['Prospect']['id'])); ?>
<?php echo $form->textarea('Touch.touchbacknote',array('cols'=>40,'rows'=>5)); ?>
<?php echo $form->input('Prospect.id', array('type'=>'hidden')); ?>

<br />

<?php echo $ajax->submit('Save', array(
	'loading'=>'Effect.Appear(\'Loading\')',
	'complete'=>'Effect.BlindUp(\'AddTouchback\'),updateContactLog(),Effect.Fade(\'Loading\'),Effect.Highlight(\'contactlog\')',
	'div'=>'inlinesubmit'
	)
); ?> 
<?php echo $html->link('or Cancel and dismiss this bo',"javascript:void(0);",array(
	"onClick"=>'Effect.BlindUp(\'AddTouchback\')'
	)
); ?>

<?php echo $form->end(); ?>
	

