<script type="text/javascript">
function updateContactLog() {
	<?php echo $ajax->remoteFunction( 
		array( 
			'url' => array( 'controller' => 'prospects', 'action' => 'contactlog', $prospect['Prospect']['id'] ), 
			'update' => 'contactlog' 
		) 
	); ?>
}

updateContactLog();
</script>

<?php $session->flash();?>

<div id="Page">

	<div id="Top">
		<h1 class="left"><?php echo $pretty->prospecttitle($prospect['Prospect']['id'], 
		$prospect['Prospect']['open'], $prospect['Prospect']['firstname'], $prospect['Prospect']['lastname']); ?></h1>
		
		<p class="right">
			<?php echo $html->link($html->image('edit.gif',array('valign'=>'middle')), '/prospects/edit/'.$prospect['Prospect']['id'],array('escape'=>false));?>
			<?php echo $html->link(
				$pretty->openclosetoggle($prospect['Prospect']['open'],"display"), 
				"/prospects/".$pretty->openclosetoggle($prospect['Prospect']['open'])."/{$prospect['Prospect']['id']}", 
				null, 
				'Are you sure?' 
			)?>
		</p>
	</div>
	<div style="clear:all"></div>
	<div id="Left">
		<table id="PaneDetails" summary="Prospect details">
			<thead><tr><th colspan="3" style="padding:0;"><span>Summary</span></th></tr></thead>
			<tbody>
				<tr><th>Name</th><td><?php echo $pretty->name($prospect['Prospect']['firstname'], $prospect['Prospect']['lastname']); ?></td></tr>
				<tr><th>Campaign</th><td><?php echo $html->link($prospect['Campaign']['name'], '/campaigns/view/'.$prospect['Campaign']['id']);?></td></tr>
				<tr><th>Last contact</th><td><?php echo $pretty->d($prospect['Prospect']['lasttouch']); ?></td></tr>
				<tr><th>Source</th><td><?php echo $pretty->d($prospect['Source']['name']); ?></td></tr>
				<tr><th>Touches</th><td><?php echo $prospect['Prospect']['touch_count']; ?></td></tr>
				<tr><th>Touchbacks</th><td><?php echo $prospect['Prospect']['touchback_count']; ?></td></tr>
				<tr><th>CWH</th><td><div class="cwhstatus state<?php echo $prospect['Prospect']['cwh']; ?>"></div></td></tr>
			</tbody>
			
			<thead><tr><th colspan="3"><span>Contact</span></th></tr></thead>
			<tbody>		
				<tr><th>Address</th><td>
					<?php echo $pretty->address(
						$prospect['Prospect']['address1'],
						$prospect['Prospect']['address2'],
						$prospect['Prospect']['city'],
						$prospect['Prospect']['state'],
						$prospect['Prospect']['postalcode'],
						$prospect['Prospect']['country']
					);
					?>
				</td></tr>
				<tr><th>Phone</th><td><?php echo $prospect['Prospect']['phone']; ?></td></tr>
				<tr><th>Email</th><td><?php echo $text->autoLinkEmails($prospect['Prospect']['email']); ?></td></tr>
			</tbody>
			
			<thead><tr><th colspan="3"><span>Academic</span></th></tr></thead>
			<tbody>
				<tr><th>Education Level</th><td><?php echo $prospect['Prospect']['educationlevel']; ?></td></tr>
				<tr><th>Objective</th><td><?php echo $prospect['Prospect']['objective']; ?></td></tr>
				<tr><th title="Programs of interest">POIs</th><td><?php echo $prospect['Prospect']['pois']; ?></td></tr>
			</tbody>
			
			<thead><tr><th colspan="3"><span>Conversion</span></th></tr></thead>
			<tbody>
				<tr>
					<th>Applied</th>
					<td>
						<?php echo $pretty->yesno($prospect['Prospect']['applied']); ?>
						<span class="subnote"><?php echo $html->link('Mark',"/prospects/applied/{$prospect['Prospect']['id']}",array('title'=>'Toggle applied')); ?></span>
					</td>
				</tr>
				<tr>
					<th>Admitted</th>
					<td>
						<?php echo $pretty->yesno($prospect['Prospect']['admitted']); ?>
						<span class="subnote"><?php echo $html->link('Mark',"/prospects/admitted/{$prospect['Prospect']['id']}",array('title'=>'Toggle admitted')); ?></span>
					</td>
				</tr>
				<tr>
					<th>Enrolled</th>
					<td>
						<?php echo $pretty->yesno($prospect['Prospect']['enrolled']); ?>
						<span class="subnote"><?php echo $html->link('Mark',"/prospects/enrolled/{$prospect['Prospect']['id']}",array('title'=>'Toggle enrolled')); ?></span>
					</td>
				</tr>
			</tbody>
			
			<thead><tr><th colspan="3"><span>Audit</span></th></tr></thead>
			<tbody>
				<tr><th>Prospect created</th><td><?php echo $pretty->dt($prospect['Prospect']['created']); ?></td></tr>
				<tr><th>Prospect last modified</th><td><?php echo $pretty->dt($prospect['Prospect']['modified']); ?></td></tr>
				<tr><th></th><td>
					
				</td></tr>
			</tbody>
			
		</table>
	</div>
	
	<div id="Right">
		
		<div id="tools">
			<h2>Contact Log</h2>
			
			<?php if($prospect['Prospect']['open'] ==1) : ?>
			
				<?php echo $ajax->link('Comment', '/prospects/comment/'.$prospect['Prospect']['id'],array(
					'update'=>'AddComment',
					'loading'=>'Effect.Appear(\'Loading\')',
					'complete'=>'Effect.BlindDown(\'AddComment\'),Effect.Fade(\'Loading\')'
					)
				);?>
				<?php echo $ajax->link('Touch', '/prospects/touch/'.$prospect['Prospect']['id'],array(
					'update'=>'AddTouch',
					'loading'=>'Effect.Appear(\'Loading\')',
					'complete'=>'Effect.BlindDown(\'AddTouch\'),Effect.Fade(\'Loading\')'
					)
				);?>
				<?php if($touchid) : ?>
				<?php echo $ajax->link('Touchback', '/prospects/touchback/'.$prospect['Prospect']['id'],array(
					'update'=>'AddTouchback',
					'loading'=>'Effect.Appear(\'Loading\')',
					'complete'=>'Effect.BlindDown(\'AddTouchback\'),Effect.Fade(\'Loading\')'
					)
				);?>
				<?php endif; ?>
				
				<span id="Loading" style="display:none; float: left;"><?php echo $html->image('loader.gif',array('alt'=>'loading...','title'=>'loading...')); ?></span>
				
			<?php endif; ?>
		</div>
		
		<br clear="both" /><br />
		
		<div id="AddTouch" class="addbox" style="display:none;"></div>
		<div id="AddComment" class="addbox" style="display:none;"></div>
		<div id="AddTouchback" class="addbox" style="display:none;"></div>
		
		<div id="contactlog">
			
		</div>
	
	</div>

	<br clear="both" ?>
	
</div>
<div id="Bottom"></div>