<?php $session->flash();?>

<div id="Page">

	<div id="Top">
		<h1 class="left">Campaign: <?php echo $campaign['Campaign']['name']; ?></h1>
		<p class="right">
			<?php echo $html->link($html->image("edit.gif"), '/campaigns/edit/'.$campaign['Campaign']['id'], array('escape' => false,'title'=>'Edit this campaign'));?>
			<!-- to do : close icon -->	
		</p>
	</div>
		
	<div id="Left">
		<table summary="Details on the campaign, listing of prospects" id="PaneDetails">
			<thead>
				<tr><th colspan="2" style="padding:0;">Summary</th></tr>
			</thead>
			<tbody>
				<tr><th>Name</th><td><?php echo $campaign['Campaign']['name']; ?></td></tr>
				<tr><th>Prospect count</th><td><?php echo $html->link($campaign['Campaign']['prospect_count'],
					'/prospects/setfilter/campaign/'.$campaign['Campaign']['id'].'/open/yes',array('title'=>'Fitler the prospect list for this campaign.')); ?></td></tr>
				<tr><th>Progress</th><td><?php echo $pretty->progress($campaign['Campaign']['step_count'],$campaign['Campaign']['step_completed_count']);?></td></tr>
			</tbody>
			
			<thead>
				<tr><th colspan="2" style="padding:0;">Audit</th></tr>
			</thead>
			<tbody>
				<tr><th>Created</th><td><?php echo $pretty->d($campaign['Campaign']['created']); ?></td></tr>
				<tr><th>Modified</th><td><?php echo $pretty->d($campaign['Campaign']['modified']); ?></td></tr>
			</tbody>
			
			<thead>
				<tr><th colspan="2" style="padding:0;">Prospects</th></tr>
			</thead>
			<tbody>				
				<tr><td colspan="2">
				<?php foreach ($campaign['Prospect'] as $position=>$prospect): ?>
					<?php echo $html->link($pretty->name($prospect['firstname'],$prospect['lastname']), 
						"/prospects/view/{$prospect['id']}"); ?><?php if(count($campaign['Prospect']) > $position+1) : ?>,<?php endif; ?>					
				<?php endforeach; ?>
				</td></tr>
				<?php if(count($campaign["Prospect"]) == 0) : ?>
					<tr><td colspan="3" class="subnote">
						You don't have any open prospects for this campaign, but you might have some
						<?php echo $html->link('closed prospects','/prospects/setfilter/campaign/'.$campaign['Campaign']['id'].'/open/no');?>.
						<br /><br />
						To assign <?php echo $html->link('prospects','/prospects'); ?> to this campaign, edit a prospect and choose 
						<?php echo $campaign['Campaign']['name']; ?>
						from the drop down list. By adding prospects to this campaign, your 
						touches can be applied to multiple prospects at once.
						<br /><br />
						Please note: Only open prospects will appear in a campaign.
					</td></tr>
				<?php endif; ?>
				
			</tbody>
			
		</table>
	</div>
	
	<div id="Right">
	
		<span class="callout">Marketing steps</span>
		
		<br /><br />
	
		<table id="CampaignProgress">
		
			<?php foreach($campaign["Step"] as $key=>$step) : ?>
				<?php
				
				$fSteps = true;
				$fOneComplete = false;
				$outputReportLinks = ""; // used to show the report links
				$reportsClass = ""; // css class for reports message
				$fOver = false; // if the entire campaign is over - affects output of reports display
				
				if($step['complete'] == 1) {
					$result = 'Applied ' . $pretty->dt($step['applied']) . ' by ' . $session->read('User.name');
					$class = 'complete';
					$action = '';
					if($key == count($campaign["Step"])-1) {
						$fOneComplete = true;
						$fOver = true;
					}
				}
				else if($key==0) {
					$result = '';
					$action=$html->link('Apply touch','/campaigns/touch/'.$campaign['Campaign']['id'],null,'Are you sure?');
					$class = 'next';
				}
				else if (isset($class) && $class == 'complete') {
					// the previous touch defines how many days from that touch they want the this one applied - kinda wacky!
					$result = 'Due ' . $pretty->d($campaign['Campaign']['next_step_due']);
					$action=$html->link('Apply touch','/campaigns/touch/'.$campaign['Campaign']['id'],null,'Are you sure?');
					$class = 'next';
					$fOneComplete = true;
				}
				else {
					if(isset($campaign["Step"][$key-1]["days"]) && is_numeric($campaign["Step"][$key-1]["days"]) )
						$result='Due ' . $campaign["Step"][$key-1]["days"] . ' days after the previous step';
					else
						$result='';
					
					$class = 'pending';
					$action = '';
				}
				?>
				
				<?php 
					if($fOneComplete) {	
						if($fOver)
							$reportstep = $key;
						else
							$reportstep = $key-1;
							
						if( isset($campaign["Step"][$reportstep]["Touchtype"]["letter"]) && $campaign["Step"][$reportstep]["Touchtype"]["letter"] == 1)
							$reportlinks[] = $html->link("Letters",'/campaigns/letter/'.$campaign['Campaign']['id']);
					
						if( isset($campaign["Step"][$reportstep]["Touchtype"]["labels"]) && $campaign["Step"][$reportstep]["Touchtype"]["labels"] == 1)
							$reportlinks[] = $html->link("Labels",'/campaigns/labels/'.$campaign['Campaign']['id']);
				
						if( isset($campaign["Step"][$reportstep]["Touchtype"]["export"]) && $campaign["Step"][$reportstep]["Touchtype"]["export"] == 1)
							$reportlinks[] = $html->link("Export",'/campaigns/export/'.$campaign['Campaign']['id']);			
						
						if(isset($reportlinks) && count($reportlinks) > 0)
						{
							$reportsClass = "reports";
							$outputReportLinks = 'Reports: ';
							foreach($reportlinks as $link) {
								$outputReportLinks .= $link . ', ';
							}
							$outputReportLinks = substr($outputReportLinks,0,strlen($outputReportLinks)-2);
						}
						else
						{
							$reportsClass = "reports";
							$outputReportLinks = 'No reports for step ' . $key;
						}
						
						unset($reportlinks);				
					}
				?>
				
				<?php if($outputReportLinks && !$fOver) : ?>
				
				<tr><td></td><td colspan="4" class="<?=$reportsClass;?>"><p><?=$outputReportLinks;?></p></td></tr>
				
				<?php endif; ?>
				
				<tr class="<?=$class;?>">
					<td class="step"><?= $step['position']+1;?></td>
					<?php if($action) : ?>
						<td colspan="3">
							<?=$action;?>:
							<?= $step['Touchtype']['name'];?>
							<span class="subnote"><?=$result;?></span>
						</td>
					<?php else : ?>
						<td><?= $step['Touchtype']['name'];?></td>
						<td></td>
						<td><?=$result;?></td>
					<?php endif; ?>
					
				</tr>
				
				<?php if($outputReportLinks && $fOver) : ?>
				
				<tr><td></td><td colspan="4" class="<?=$reportsClass;?>"><p><?=$outputReportLinks;?></p></td></tr>
				
				<?php endif; ?>
				
			<?php endforeach ?>
		</table>
		
		<?php if(isset($fOver) && $fOver && $campaign['Campaign']['open'] == 1) : ?>
			<div id="ClosedCampaignBox">
				<p class="callout">Your campaign is over. Time to wrap it up.</p>
				<ul>
					<li><?php echo $html->link('Close this campaign and all prospects in this campaign','/campaigns/wrapup/'.$campaign['Campaign']['id'],
						array('confirm'=>'Are you sure?','title'=>'We will also attach a note to the contact log of each prospect'));?></li>
					<li><?php echo $html->link('Just close the campaign','/campaigns/close/'.$campaign['Campaign']['id'],
						array('confirm'=>'Are you sure?'));?></li>
				</ul>
				
				<span class="subnote">Tips when you finish a campaign:</span>
				<ul class="subnote">
					<li>Come back later and mark the admitted, applied and enrolled status of these prospects. This 
						<?php echo $html->link('data','/dashboard/');?> is 
						<?php echo $html->link('invaluable','/help/#measuringprogress',array('target'=>'blank'));?> when seeing which
						sources offer the best prospects.</li>
					<li>Don't be afraid to close the campaign and the prospects, they can always be re-opened later.</li>
					<li>Spend 2 minutes and perform a quick post-mortem on how the campaign went. Email your answers to these three questions to your team for later discussion:
						<ol>
							<li>What step worked best?</li>
							<li>What didn't work in this campaign?</li>
							<li>What are three simple ideas we can try next time to convert even more prospects?</li>
						</ol>
					</li>
				</ul>
			</div>
		<?php endif; ?>
		
		<?php if(!isset($fSteps)): ?>
			You need to setup a series of marketing steps for this campaign. <?php echo $html->link('Edit this campaign','/campaigns/edit/'.$campaign['Campaign']['id']);?> to do this.
		<?php endif ?>
	</div>

	<br clear="all" />
			
</div>
<div id="Bottom"></div>