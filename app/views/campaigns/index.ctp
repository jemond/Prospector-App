<?php $session->flash();?>

<div id="Page">

	<div id="Top">
		<h1 class="left">Campaigns</h1>
		<p class="right"><?php echo $html->link($html->image('add.gif'),'/campaigns/add',array('title'=>'Add a campaign','escape'=>false)); ?></p>
	</div>
	
	<div id="Main">	
	
		<table summary="Campaign status" id="CampaignDetail">
	
		<?php foreach ($opencampaigns as $campaign): ?>

			<tr>
			<td class="icons">
				<?php if($campaign['Campaign']['step_count'] == 0) :?>
					<?php echo $html->image('x.gif',array('title'=>'No marketing steps')); ?>
				<?php elseif($campaign['Campaign']['step_completed_count'] == $campaign['Campaign']['step_count']) :?>
					<?php echo $html->image('tick.gif',array('title'=>'Marketing complete')); ?> 
				<?php elseif($campaign[0]['past_due'] > 0) :?>
					<?php echo $html->image('pastdue.png'); ?>
				<?php endif; ?>
			</td>
			<td class="title">
				<h2><?php echo $html->link($campaign['Campaign']['name'],'/campaigns/view/'.$campaign['Campaign']['id']); ?></h2>
				<span class="callout">
					<?php if($campaign['Campaign']['step_count'] == 0) :?>
						No marketing steps
					<?php elseif($campaign['Campaign']['step_completed_count'] == $campaign['Campaign']['step_count']) :?>
						Campaign complete 
					<?php elseif($campaign['Campaign']['step_completed_count'] == 0): ?>
						Pending start
					<?php elseif($campaign[0]['past_due'] > 0) :?>
						<?php echo $pretty->days($campaign[0]['past_due']); ?> past due
					<?php else :?>
						Next due date: <?php echo $pretty->d($campaign['Campaign']['next_step_due']); ?>
					<?php endif; ?>
				</span><br /> 
			</td>
			<td><?php echo $pretty->progressbar($campaign['Campaign']['step_count'],$campaign['Campaign']['step_completed_count']);?></td>
			</tr>
			
		<?php endforeach; ?>
		
		</table>
		
		<br />
		<?php echo $html->link($html->image('add.gif'),'/campaigns/add',array('title'=>'Add campaign','escape' => false)); ?>
		
		<?php if(count($closedcampaigns) > 0) : ?>
		
			<p class="callout">Closed campaigns</p>
			<p class="subnote">You won't be able to assign any prospects to these campaigns, bit propects with this campaign will still be assigned to the campaign.</p>
			<?php foreach ($closedcampaigns as $campaign): ?>
			
				<div>
					<span class="deleted"><?=$campaign["Campaign"]["name"]; ?></span> 
					<span class="subnote">
						Closed <?=$pretty->dt($campaign["Campaign"]["closed"]); ?> by <?=$campaign["User"]["name"]; ?>
						<?php echo $html->link('Open', "/campaigns/open/{$campaign['Campaign']['id']}", array('title'=>'Open campaign') )?>
					</span>
				</div>
			
			<?php endforeach; ?>
		
		<?php endif; ?>
		
	</div>
	
</div>
<div id="Bottom"></div>