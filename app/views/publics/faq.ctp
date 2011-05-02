<p><?php echo $html->link('Prospector','/',array('title'=>'Home')); ?>, <?php echo $html->link('help','/help',array('title'=>'Home')); ?></p>

<h1>Frequently Asked Questions</h1>

<a name="cancel" />
<strong>Can I really cancel anytime?</strong>
	<p>Yep. No contracts, no cancellation fees and no hassle. We hate difficult-to-cancel companies as much as you do. You don't even have to call us
	to cancel, just login and go to "Settings". When you cancel, we will instantly refund the unused portion of your monthly fee 
	(for example: if you're half way through the month, we will refund 50% of your monhtly subscribtion).</p>

<a name="privacy" />
<strong>Who owns the data on the prospects I upload? Will you share my prospects with other account holders, or sell or disribute it?</strong>
	<p>You own all of the data you upload into our system, including your prospects, campaigns, letters and marketing steps. We will not sell, distribute or share your 
	prospect information with anyone, period. Kindly see our <?= $html->link('privacy statement','/privacy',array('title'=>'Our privacy statement')); ?> for full details.</p>

<strong>Can you choose another label format?</strong>
	<p>No, the system only prints labels in <?php echo $html->link('Avery 5160 format','/files/avery-5160-template.doc',array('title'=>'Download the print template for Word'));?>. If you would like to request an additional format, 
	<?php echo $html->link('let us know','/support',array('title'=>'Support')); ?>. However, it's easy to just download an excel of your prospects to feed into
	your own label.</p>

<strong>My letters look weird when I print them. What is going on?</strong>
	<p>Sometimes cutting and pasting from Word causes issues. Try <?php echo $html->link('this','/help#troubleshootingletters'); ?>.</p>

<strong>What is a closed prospect?</strong>
	<p>A closed prospect is a prospect to whom you no longer are marketing. There are several reasons you might stop: 
	the prospect applied to your program, you determined the prospect wasn't worth the effort to recruit or the prospect
	indicated to you he or she wasn't interested.</p>

<strong>I assigned some prospects to a campaign but they don't appear on the campaign screen. What gives?</strong>
	<p>Closed prospects don't appear in campaigns, only open prospects. Simply open the prospects to make them appear in the campaign.</p>
	<p>You can also search for closed prospects for a certain campaign in the prospect list with a search like this: <em>campaign:22 open:no</em>.</p>

<strong>My upload isn't working! Argh!</strong>
	<p>Deep breath. Make sure your file is a CSV (open it in notepad and make sure you see lots of commas) and make sure the first row contains 
	the column names of your data. If you have a lot of names in your file, try splitting it up into chunks of 150 prospects at time.</p>

	<p>Also, we have a section on <?php echo $html->link('uploads','/help#uploads'); ?> in the help documentation that you might find helpful.</p>
	
	<p>Still not working? No problem, just give us a <?php echo $html->link('jingle','/support'); ?>.</p>
	
<strong>When do you bill?</strong>
	<p>We bill monthly, on the first of the month. When you sign-up we charge you a pro-rate of the plan's monthly fee to get you to the first of the next month.</p>
	
<strong>Do you have non-profit/education pricing?</strong>
	<p>We work hard to provide a rock-bottom price.</p>
	
<strong>What merge fields can I use the letter editor?</strong>
	<p>See the <?php echo $html->link('list','/help#mergefields',array('title'=>'Support')); ?> in the help docs.</p>