<?php $session->flash();?>

<div id="Page">

	<div id="Top">
		<h1 class="left">Touch Types</h1>
		
		<p class="right"?><?php echo $html->link($html->image('add.gif'),'/touchtypes/add',array('title'=>'Add a touch type','escape'=>false))?></p>
	</div>
	
	<div style="clear:both"></div>

	<div id="Main">
	
		<p class="subnote">Touches are what you apply to groups of prospects. They are any kind of contact you make with a prospect.</p>

		<?php if(count($touchtypes) > 0) :?>

			<table summary="Listing of touch types" class="settingsTable">
				<tr>
					<th>Name</th>
					<th>Type</th>
					<th>Letter</th>
					<th>Control</td>
				</tr>
			
				<!-- Here is where we loop through our $posts array, printing out post info -->
			
				<?php foreach ($touchtypes as $touchtype): ?>
				<tr>
					<td><?php echo $touchtype['Touchtype']['name']; ?></td>
					<td><?php echo $pretty->touchtype($touchtype['Touchtype']['letter'],$touchtype['Touchtype']['labels'],$touchtype['Touchtype']['export']); ?></td>
					<td>
						<?php
						if($touchtype['Touchtype']['letter'] == 1)
							echo $html->link('view','/touchtypes/letter/'.$touchtype['Touchtype']['id'],array('target'=>'_blank'));
						?>
					</td>
					<td>
						<?php echo $html->link($html->image('edit.gif',array('title'=>'Edit')),'/touchtypes/edit/'.$touchtype['Touchtype']['id'],array('escape'=>false))?>
						<?php echo $html->link($html->image('remove.gif',array('title'=>'Disable touchtype')),'/touchtypes/disable/'.$touchtype['Touchtype']['id'],array('escape'=>false))?>
					</td>
				</tr>
				
				<?php endforeach; ?>
				
			</table>
			
		<?php endif; ?>
		
		<?php echo $html->link($html->image('add.gif'),'/touchtypes/add',array('title'=>'Add a touch type','escape'=>false))?>
		
		<?php if(count($closedtouchtypes) > 0) :?>
		
			<div class="hr"></div>
			
			<p class="callout">Disabled touches</p>
			<p class="subnote">These touches won't appear when building campaigns or applying touches to prospects.</p>
			
			<?php foreach ($closedtouchtypes as $touchtype): ?>
			
				<div>
					<span class="deleted"><?=$touchtype["Touchtype"]["name"]; ?></span> 
					<span class="subnote">
						<?php echo $html->link('Enable', "/touchtypes/enable/{$touchtype['Touchtype']['id']}", array('title'=>'Enable touch type') )?>
					</span>
				</div>
			
			<?php endforeach; ?>
		
		<?php endif;?>
		
	</div>
	
</div>
<div id="Bottom"></div>