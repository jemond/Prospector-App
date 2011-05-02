<script language="javascript">
function updateStatus(id,i) {
	if($(id).getValue() == 'skip')
		$("status"+i).setAttribute('src','/img/x.gif');
	else
		$("status"+i).setAttribute('src','/img/tick.gif');
}
</script>

<div id="Page">

	<div id="Top"><h1>Step 2: Import fields</h1></div>
	
	<div id="Main">
	
		<?php echo $form->create('Upload',array('action'=>'index','type'=>'file')); ?>
		
		<table summary="Field matches" class="uploadtable">
				
		<?php for($i=0;$i<$field_count;$i++) :?>
		<tr>
			<th>Field <?=$i+1;?>: <?php echo $user_columns[$i]; ?></th>
			<td>
				<?php echo $form->select('Upload.field'.$i, $columns, $matches[$i], array('class'=>'largeinput','onchange'=>'updateStatus(this.id,'.$i.')'), false); ?>
				<?php echo $form->error('Upload.field'.$i); ?>
			</td>
			<td>
				<?php if (isset($columns[$matches[$i]])) : ?>
					<?php echo $html->image('tick.gif',array('id'=>'status'.$i)); ?>
				<?php else : ?>
					<?php echo $html->image('x.gif',array('id'=>'status'.$i)); ?>
				<?php endif; ?>
			</td>
			<td><?php echo $sample[$i]; ?></td>
		</tr>
		
		<?php endfor; ?>
		
		</table>
		
		<br />
		
		<?php echo $form->hidden('Upload.step',array('value'=>'3')); ?>

		<?php echo $form->submit("Import $records rows",array('class'=>'largeinput')); ?>
		
		<?php echo $form->end(); ?>
	
	</div>
	
</div>
<div id="Bottom"></div>