<div id="Banner">
	<h1>What is Prospector?</h1>
	
	<p class="left">
		<span class="callout">Prospector is an easy to use web database that helps you turn more inquirers into applicants.</span>
		Designed specifically for higher education, Prospector organizes your prospects into groups 
		of campaigns, keeping your team coordinated and able to deliver marketing materials on time.
		It includes custom merge letters, labels and exports.
		
		<br /><br />
		
		<?php echo $html->link('Create a free account!','/signup',array('class'=>'button')); ?>
		
		<?php echo $html->link('Features','/features',array('class'=>'button')); ?>
	</p>
	
	<div class="right">
		<?php echo $html->link(
			$html->image('campaigns.png',array(
				'title'=>'Campaigns management screen'
			)),
			'/img/campaigns-full.png',
			array(
				'escape'=>false
			)
		);?>
		
		<br /><?php echo $html->link('More screen shots','/features#screenshots');?>
	</div>	
	
	
	<br clear="all" />
</div>

<div id="Fold">

	<div class="left">

		<h2>The features you need</h2>
		<ul class="checklist">
			<li>Easy to use, <span class="callout">nothing to install</span></li>
			<li>Track groups of prospects in custom <?php echo $html->link('campaigns','/img/campaigns-full.png');?> to organize paralel marketing efforts</li>
			<li>Write <span class="callout">custom merge letters</span> that merge with prospect profile fields</li>
			<li>Track a <span class="callout">contact log</span> for each prospect</li>
			<li>Track <?php echo $html->link('prospect profiles','/img/prospect-profile-full.png');?></li>
			<li>Beautiful <?php echo $html->link('graphs','/img/stats.png');?> providing instant progress on your conversion rates</li>
			<li>Keep your team on schedule with custom campaign processes and action dates</li>
			<li>Search through you entire <?php echo $html->link('prospect list','/img/prospect-list.png');?> instantly</li>
		</ul>
		
		<?php echo $html->link('and loads more...','/features'); ?>
		
	</div>
	
	<div class="right">
	
		<h2>Don't take our word for it</h2>
		
		<p class="quote">Statistics are like a drunk with a lampost: used more for support than illumination.</p>
		<p class="quoteauthor">Winston Churchill, British Prime Minister</p>
		
		<p class="quote">Formula: The value of a perk is inversely related to the expectation of that perk.</p>
		<p class="quoteauthor">Seth Godin</p>
		
		<p class="quote">It is not a field of a few acres of ground, but a cause, that we are defending, and whether 
		we defeat the enemy in one battle, or by degrees, the consequences will be the same.</p>
		<p class="quoteauthor">Thomas Paine, founding father</p>
		
		<h2>Got a question?</h2>
		<p>
			General questions, sales, support: Call us @ (509) 368-7038. We are here to <?php echo $html->link('help','/support'); ?>. <br /><br />
			<?php echo $html->link('About us','/about/',array('title'=>'Info on the creators of Prospector'));?>, 
			<?php echo $html->link('Help Docs','/help/',array('title'=>'Directions on using the system'));?>, 
			<?php echo $html->link('FAQ','/faq/',array('title'=>'Answers to common questions'));?>,
			<?php echo $html->link('Privacy','/privacy/',array('title'=>'The data you upload to our system is yours. We won\'t use it, ever.'));?>
		</p>
		
	</div>
	
	<br clear="both" />
	
</div>