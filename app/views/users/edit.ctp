<?php $session->flash();?>

<div id="Page">

	<div id="Top"><h1>Edit user</h1></div>
	
	<div id="Main">
	
		<?php echo $form->create('User', array('action' => 'edit')); ?>
		
		<?php echo $form->input('name',array('size'=>30)); ?>
		<?php echo $form->input('email',array('size'=>30)); ?>
		
		<br />
		
		<?php echo $form->submit('Save', array('div'=>'inlinesubmit')); ?>
		<?php if($session->read('User.admin') == 1) :?>
			<?php echo $html->link('or Cancel and return to settings','/settings/'); ?>
		<?php else :?>
			<?php echo $html->link('or Cancel and return to the dashboard','/dashboard/'); ?>
		<?php endif; ?>
		
		<?php echo $form->input('id',array('type'=>'hidden','value'=>$user_id)); ?>
		
		<?php echo $form->end(); ?>
	
	</div>
	
</div>
<div id="Bottom"></div>