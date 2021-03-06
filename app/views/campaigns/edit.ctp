<div id="Page">

	<div id="Top">
		<h1>Edit campaign</h1>
	</div>
	
	<div id="Main">

		<?php echo $form->create('Campaign', array('action' => 'edit')); ?>
		<?php echo $form->input('name'); ?>
		<?php echo $form->input('id', array('type'=>'hidden')); ?>
		
		<div>
			<table summary="Setup your marketing steps order">
			<tr>
				<th>Order</th>
				<th>Touch</th>
				<th>Time to next step</th>
				<th></th>
			</tr>
			
			<?php for($i=0;$i<12;$i++) { ?>
			
				<?php
						
					if(isset($this->data["Step"][$i]["complete"]) && $this->data["Step"][$i]["complete"] == 1)
						$disabled = true;
					else
						$disabled = false;
				?>
			
				<tr id="step<?=$i;?>">
					<th>Step <?=$i+1;?></th>
					<td>
						<?=$form->select("Step.$i.touchtype_id",$touchtypes,null,array(
							'label'=>false,
							'disabled'=>$disabled
							)
						);?>
						<?= $form->input("Step.$i.position",array(
							'type'=>'hidden',
							'value'=>$i
							)
						); ?>
					</td>
					<td>
						<?php if($i!=11) { ?>
							<?=$form->input("Step.$i.days",array(
								'div'=>false,
								'label'=>false,
								'maxLength'=>'3',
								'size'=>'3',
								'disabled'=>$disabled
								)
							);?>
							<span class="subnote">days until step <?=$i+2;?></span>
						<?php } ?>
					</td>
					<td><span class="attentionnote"><?php if(strlen($disabled)>0) echo "Since this step has been applied, it's locked."; ?></span></td>
				</tr>
			
			<?php } ?>
			
			</table>
			
		</div>
		
		<?php echo $form->submit('Save', array('div'=>'inlinesubmit')); ?> 
		<?php echo $html->link('or Cancel and return to the campaign list','/campaigns/'); ?>
		
		<?php echo $form->end(); ?>
	
	</div>
	
</div>
<div id="Bottom"></div>