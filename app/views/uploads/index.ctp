<?php $session->flash();?>

<div id="Page">

	<div id="Top"><h1>Uploads</h1></div>
	
	<div id="Main">
	
		<p>Use this form to upload new prospects into the system from an CSV file.</p>
	
		<?php echo $form->create('Upload',array('action'=>'index','type'=>'file')); ?>
		
		<fieldset>
				
			<legend>Required stuff</legend>
				
			<div class="input">
				<label>CSV file</label>
				<?php echo $form->file('Upload.data'); ?>
				<?php echo $form->error('Upload.data'); ?>
			</div>
			
			<div class="formnote"><p>CSV files are files you can save in Excel. Choose File > Save As and select "CSV" as the file format.</p></div>

			<div class="input">
				<label>Source</label><?php echo $form->select('Upload.source_id', $sources, null, null); ?>
				<?php echo $form->error('Upload.source_id'); ?>
			</div>
			
			<div class="formnote"><p>Every prospect needs a source, otherwise, how will you know where to spend your money?</p></div>

		</fieldset>
		
		<fieldset>
		
			<legend>Optional: Drop 'em in a campaign</legend>
			
			<div class="input">
				<label>Campaign</label><?php echo $form->select('campaign_id', $campaigns, null, null); ?>
				<?php echo $form->error('campaign_id'); ?>
			</div>
			
			<div class="input">
				<label></label>Or create a new campaign:<br />
				<label></label><?php echo $form->input('Campaign.name',array('label'=>false,'div'=>false)); ?>
				<?php echo $form->error('Campaign.name'); ?>
			</div>
			
		</fieldset>
	
		<?php echo $form->hidden('Upload.step',array('value'=>'2')); ?>
		<?php echo $form->end('Next step'); ?>
	
	</div>
	
</div>
<div id="Bottom"></div>