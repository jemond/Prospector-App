<?php $session->flash();?>

<div id="Page">

	<div id="Top">
	
		<h1 class="left">Sources</h1>
		
		<p class="right"?><?php echo $html->link($html->image('add.gif'),'/sources/add',array('title'=>'Add a source','escape'=>false))?></p>
	
	</div>
	
	<div id="Main">
	
		<p class="subnote">A source describes where a prospect was found.</p>
		
		<?php if(count($sources) > 0) :?>
		
			<table summary="List of prospect sources, or where they came from." class="settingsTable">
			<tr>
				<th>Source name</th>
				<th>Prospects</th>
				<th></th>
			</tr>
			
			<?php foreach ($sources as $source): ?>
			
				<tr>
					<td><?php echo $source['Source']['name']; ?></td>
					<td><?php echo $html->link($source['Source']['prospect_count'],
						'/prospects/setfilter/source/'.$source['Source']['id'],array('title'=>'Filter the prospect list for this source')); ?></td>
					<td>
						<?php echo $html->link($html->image('edit.gif',array('title'=>'Edit')),'/sources/edit/'.$source['Source']['id'],array('escape'=>false))?>
						<?php echo $html->link($html->image('remove.gif',array('title'=>'Disable source')),'/sources/disable/'.$source['Source']['id'],array('escape'=>false))?>
					</td>
				</tr>
			
			<?php endforeach; ?>
			
			</table>
		
		<?php endif; ?>
			
		<?php echo $html->link($html->image('add.gif'),'/sources/add',array('title'=>'Add a source','escape'=>false))?>
		
		<?php if(count($closedsources) > 0) : ?>
		
			<div class="hr"></div>
			
			<p class="callout">Disabled sources</p>
			<p class="subnote">These sources won't appear when creating or editing prospects. Prospects with this source already will keep it.</p>
			
			<?php foreach ($closedsources as $source): ?>
			
				<div>
					<span class="deleted"><?=$source["Source"]["name"]; ?></span> 
					<span class="subnote">
						<?php echo $html->link('Enable', "/sources/enable/{$source['Source']['id']}", array('title'=>'Enable source') )?>
					</span>
				</div>
			
			<?php endforeach; ?>
		
		<?php endif; ?>
	
	</div>

</div>
<div id="Bottom"></div>