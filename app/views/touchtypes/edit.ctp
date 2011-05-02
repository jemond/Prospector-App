<?php $session->flash();?>

<?php echo $this->element('tinymce-editor'); ?>

<div id="Page">

	<div id="Top"><h1>Edit touch</h1></div>

	<div id="Main">

		<?php 
			$lettercontent = $this->data["Touchtype"]["lettercontent"];
			$touchtype_id = $this->data["Touchtype"]["id"];	
		?>
		
		<?php echo $form->create('Touchtype',array('enctype' => 'multipart/form-data')); ?>
		
		<?php echo $form->input('name'); ?>
		
		<div class="input">
			<label for="TouchtypeLetter">Include letter</label>
			<?php echo $form->input('letter',array(
				'label'=>false,
				'onclick'=>'toggleEditor()'
				)
			); ?>
		</div>
		
		<div id="Editor">
			<?php echo $form->input('lettercontent',array('label'=>false,'cols'=>80,'rows'=>30,'before'=>'<p class="subnote">You can use ' .$html->link('merge fields','/help#mergefields',array('target'=>'_blank')). ' to customize letters.</p>')); ?>
		</div>
		
		<div class="input">
			<label for="TouchtypeLabels">Include labels</label>
			<?php echo $form->input('labels',array(
				'label'=>false
				)
			); ?>
		</div>
		
		<div class="input">
			<label for="TouchtypeExport">Include export file</label>
			<?php echo $form->input('export',array(
				"label"=>false
				)
			); ?>
		</div>
		
		<?php echo $form->input('id',array("type"=>"hidden")); ?>
		
		<br />
		
		<?php echo $form->submit('Save', array('div'=>'inlinesubmit')); ?>&nbsp;
		<?php echo $html->link('or Cancel and return to the touch types list','/touchtypes/'); ?>
		
		<?php echo $form->end(); ?>

	</div>
	
</div>
<div id="Bottom"></div>