<?php $session->flash();?>

<?php 
	$graphs = array(); 
	foreach($stats as $stat) {
		$graphs[] = array(
			str_replace("'","\'",$stat['Source']['name']),
			$pretty->percentage($stat['Source']['applicants_to_total']),
			$pretty->percentage($stat['Source']['admits_to_applicants']),
			$pretty->percentage($stat['Source']['enrollees_to_admits'])
			); 
	}	
?>
<!--[if IE]><?php echo $javascript->link('excanvas'); ?><![endif]-->
<?php echo $javascript->link('flotr'); ?>

<script type="text/javascript">
document.observe('dom:loaded', function(){
	<?php 
		$position = 0;
		$d1 = '';
		$d2 = '';
		$d3 = '';
		$xlabels = '';
		foreach($graphs as $graph) {
			$d1 .= '['.$position++.','.$graph[1].'],';
			$xlabels .= '['.$position.',\''.$graph[0].'\'],';
			$d2 .= '['.$position++.','.$graph[2].'],';
			$d3 .= '['.$position++.','.$graph[3].'],';
		}
		
		$d1 = substr($d1,0,strlen($d1)-1);
		$d2 = substr($d2,0,strlen($d2)-1);
		$d3 = substr($d3,0,strlen($d3)-1);
		
		$xlabels = substr($xlabels,0,strlen($xlabels)-1);
	?>
	var d1=[<?php echo $d1; ?>];
	var d2=[<?php echo $d2; ?>];
	var d3=[<?php echo $d3; ?>];
	var xlabels = [<?php echo $xlabels; ?>];
	var ylabels = [[0,'0%'],[20,'20%'],[40,'40%'],[60,'60%'],[80,'80%'],[100,'100%']];
	
	var f = Flotr.draw($('statsgraph'), [
			{data:d1, label:'Conversion',bars: {show:true,barWidth:0.5}},
			{data:d2, label:'Quality',bars: {show:true,barWidth:0.5}},
			{data:d3, label:'Yield',bars: {show:true,barWidth:0.5}}
		],{
			legend: {position: 'ne',backgroundColor:'#e5e5e5'},
			xaxis: {ticks: xlabels},
			yaxis: {min:0,max:100,ticks:ylabels}
		}
	);

});			
</script>

<div id="Page">

	<div id="Top"><h1>Dashboard</h1></div>
	
	<div id="Main">
		<?php if($fOverage) :?>
			<div class="alert-box subnote">
				<?php echo $html->image('alert.gif',array('title'=>'Alert! I need your attention!')); ?>
				Your account open prospect limit has been reached. Either close some prospects or 
				<?php echo $html->link('upgrade','/settings/'); ?>.
			</div>
			<div class="hr"></div>
		<?php endif; ?>
	
		<?php if($session->read('Profile.show_welcome') == 1) : ?>
		
			<div class="DashboardMessage">
	
				<h2><?php echo $session->read('User.name');?>, welcome to Prospector!</h2>
				<p>
					A <?php echo $html->link('prospect','/prospects/',array('class'=>'callout'));?> is a person you are trying to get to apply or enroll in your program.
					A <?php echo $html->link('campaign','/campaigns/',array('class'=>'callout'));?> is a collection of prospects you market to as a group.
					A <span class="callout">touch</span> is any kind of communication to a prospect, like a letter, a phone call or an email.
				</p>
				
				<p>
					A <span class="callout">campaign</span> is wicked because for each campaign you define
					a series of touches -- really a marketing process and schedule -- that will make it super easy to generate the material
					you need for your prospects <span class="callout">and</span> keep your team on schedule.
				</p>
				
				<p>
					I took the liberty of adding some <?php echo $html->link('sources','/sources/');?> and <?php echo $html->link('touch types','/touchtypes/');?> to
					your account. You can keep these, remove them or rename them.
				</p>
				
				<p>
					I really want you to enjoy using this system as much as we enjoyed creating it. And we are very confident that this application will improve your 
					<?php echo $html->link('prospect conversion rate, quality and yield','/help#measuringprogress',array('target'=>'_blank','title'=>'The rate admits enroll.')); ?>.
				</p>
				
				<p>Remember: We are just a <?php echo $html->link('jingle','/support/',array('target'=>'_blank'));?> away. Give us a buzz, we don't bite.</p>
				
				<p>Cheers,<br />Justin Emond<br />Founder, Prospector</p>
				
				<p>PS: You can click that red X below to dismiss this message. Don't worry, you won't hurt my feelings.</p>
				
				<?php echo $html->link($html->image('x.gif'),'/users/dismiss/welcome/',array('title'=>'Dismiss message','escape'=>false)); ?>
				
			</div>
				
			<div class="hr"></div>
		
		<?php endif; ?>
		
		<h2>Prospect Statistics</h2>
		<ul>
			<li>You have <?php echo $nProspects;?> open <?php echo $html->link('prospects','/prospects/');?></li>
			<li>There are <?php echo $nCampaigns;?> open <?php echo $html->link('campaigns','/campaigns/');?></li>
			<li>Your overall <span class="callout">prospect conversion rate</span>
				(<?php echo $html->link('?','/help#measuringprogress',array('target'=>'_blank','title'=>'The rate you turn prospects into applicants.')); ?>) 
				for all sources is <?php echo $pretty->percentage($overallstats['conversion']);?>%</li>
			<li>Your overall <span class="callout">prospect quality</span>
				(<?php echo $html->link('?','/help#measuringprogress',array('target'=>'_blank','title'=>'The rate of applicants that are admitted.')); ?>)
				rate for all sources is <?php echo $pretty->percentage($overallstats['quality']);?>%</li>
			<li>Your overall <span class="callout">prospect yield</span>
				(<?php echo $html->link('?','/help#measuringprogress',array('target'=>'_blank','title'=>'The rate admits enroll.')); ?>)
				for all sources is <?php echo $pretty->percentage($overallstats['yield']);?>%</li>
		</ul>
		
		<h3>Conversion, Quality and Yield Rates by Source</h3>
		
		<p><span class="callout">About the graph</span> <span class="subnote">As you mark prospects applied, admitted and enrolled, we will show you 
		your rates of conversion, quality and rate for each source.
		<?php echo $html->link('More information','/help#measuringprogress',array('target'=>'blank','title'=>'More information on tracking progress.')); ?></span></p>
		<div id="statsgraph" style="width:600px;height:300px;"></div>
		
		<p><?php echo $html->link('Download full stats','/stats/');?> <span class="subnote">(CSV format)</span></p>
		
	</div>
	
</div>
<div id="Bottom"></div>