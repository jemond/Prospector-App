<p><?php echo $html->link('Prospector','/',array('title'=>'Home')); ?>, <?php echo $html->link('faq','/faq',array('title'=>'FAQ')); ?></p>

<h1>Help Documentation</h1>

	<p>
		This documentation should provide some help using the system. Of course, you can always just call us. We're around to 
		<?php echo $html->link('help','/support/',array('title'=>'Support')); ?>. You can also get some answers in the 
		<?php echo $html->link('faq','/faq/',array('title'=>'FAQ')); ?>.
	</p>

<h2>Overview</h2>

	<p>At it's heart <strong>URecruiter is a collection of prospects</strong>. A prospect is a potential applicant, an inquirer or someone 
	to whom you want to market your program</p>
	
	<p>Prospects can be grouped into campaigns, which you can use to generate reports and track your marketing progress.
	A campaign is a collection of prospects and a collection of touch types, scheduled into a series of steps.
	Scheduling steps</p>
	
	<p>Prospect sources are important. Each prospect record needs a single source defined. That way, over time we can 
	start to tell you from where your better sources are coming. The more applied, admitted and enrolled dates you can enter, 
	the better.</p>

<h2>Prospects, Campaigns, Sources and Touches</h2>
	<p><span class="callout">Prospects</span> are people, inquirers and potential applicants. You track profile 
	information about prospects (like name, address and programs of interest) and a contact log, which is a chronilogical list of notes. 
	</p>
	
	<p>
	The contact log contains notes from your collegues, a record of every touch applied and an audit record of when the prospect was closed.
	</p>
	
	<p><span class="callout">Campaigns</span> are collections of prospects to which you apply the same marketing process. You can have as many
	campaigns as you want running simultaneously. When you are finished with a campaign, you simply close it.</p>
	
	<p><span class="callout">Sources</span> define exactly how you got the prospect record. Maybe it was from a fair, 
	or an online information request form or through a search service. Defining sources are so important to the system, it's the only
	required field when creating a prospect</p>
	
	<p>Over time, we will automatically start to show you which sources are producing the best prospects, looking at conversion 
	(the rate of turning prospects into applicants), quality (the rate of applicants that get admitted) and enrollees (the rate of admits who enroll).</p>
	
	<p><span class="callout">Touches</span> are any kind of communication or marketing interaction with a prospect. A touch could be a letter
	you send about your programs, an invite to an event, a phone call or an email.</p>
	
	<p>A campaign is made up a specific sequence of touches that guide and define both the type and pace of the marketing effort 
	applied to a group of prospects.</p>

<a name="uploads" />
<h2>Uploads</h2>
	<p>You can upload a bunch of prospects at once to URecruiter. Your file must be CSV (comma serperated files) 
	format and must contain column headers as the first row.</p>
	
	<p>Saving a file as CSV:</p>
	<ol>
		<li>Open the file in Excel</li>
		<li>Go to the "Save as" menu</li>
		<li>In the file format drop down, choose "CSV"</li>
		<li>Done!</li>
	</ol>
	
	<p>Upload tips:</p>
	<ul>
		<li>Remove columns from the file you won't be importing before you start the import</li>
		<li>If you are trying to import more than 250 prospects, try splitting your file into files of 250</li>
		<li>If you aren't sure if the file is a CSV try opening it in Notepad (you should see lots of commas if it's a CSV)
	</ul>

<a name="measuringprogress">
<h2>Measuring Progress</h2>

	<h3>Conversion, Quality and Yield</h3>
	
		<p><span class="callout">If you can't measure your progress you can't get better</span>. It's important to know how well you are doing 
		converting prospects into applicants, admits and enrollees. And it's vital to know what sources are
		producing the best applicants.</p>
		
		<p>Here are the three key statistics you need to worry about when converting prospects:</p>
		
		<ol>
			<li><span class="callout">Conversion rate</span>: The rate of prospects you turn into applicants.</li>
			<li><span class="callout">Quality</span>: The rate of applicants that are admitted.</li>
			<li><span class="callout">Yield</span>: The rate of admits that enroll.</li>
		</ol>
		
		<p>When you first start tracking prospects focus on increasing your conversion rate. Once you increase and stabailize your rate of converting prospects into applicants, begin
		to focus your efforts on the sources that produce better quality applicants. Finally, focus on your yield rate to make the business office happy!</p>
		
		<p>Remember: it's great to turn lots of prospects into applicants, but if they aren't getting accepted, you need to look elsewhere for more qualified candidates. But it's not just
		enough to get them accepted, you need to be converting prospects that actually want to attend your program.</p>
	
	<h3>Marking Records as Applied, Admitted and Enrolled</h3>
		<p>Open the prospect profile and look at the left bar with the prospect details. Near the bottom your will see links named "Mark". Click these to toggle the admitted,
		applied and enrolled status.</p>

<h2>Letters and Labels</h2>

	<a name="mergefields"></a>
	<h3>Letter Merge Fields</h3>
	<p>You can use these merge fields in your letters:</p>
	
	<ul>
		<li><span class="callout">#FirstName#</span> <span class="subnote">Justin</span></li>
		<li><span class="callout">#LastName#</span> <span class="subnote">Emond</span></li>
		<li><span class="callout">#Address1#</span> <span class="subnote">93 S. Jackson Street</span></li>
		<li><span class="callout">#Address2#</span> <span class="subnote">Suite 78735</span></li>
		<li><span class="callout">#City#</span> <span class="subnote">Searttle</span></li>
		<li><span class="callout">#State#</span> <span class="subnote">WA</span></li>
		<li><span class="callout">#PostalCode#</span> <span class="subnote">98104-2818</span></li>
		<li><span class="callout">#Country#</span> <span class="subnote">US</span></li>
		<li><span class="callout">#Address#</span> <span class="subnote">A properly formatted address block contains address1, address2, city, state, postal code and country.</span></li>
	</ul>

	<h3>Label Format</h3>
		<p>Labels are formatted to the Avery 5160 template.</p>
	
	<a name="troubleshootingletters" />
	<h3>Troubleshooting Letter Formating</h3>
		<p>Cutting and pasting a letter from Word to the letter editor can cause formatting issues. If you are having letter format issues, try this:</p>
		
		<ol>
			<li>Edit the touch and cut the entire contents of the letter.</li>
			<li>Open a text program (like Notepad on Windows) and paste the letter into Notepad.</li>
			<li>With the touch letter editor blank, save the touch so the letter is erased.</li>
			<li>Now edit the touch again, and cut the letter from Notepad and paste it into the touch.</li>
			<li>Save, and run the letter again.</li>
			<li>Open the original letter in Word.</li>
		</ol>
		
		<p>If that still doesn't fix the issue just <?php echo $html->link('get in touch','/support');?>. We don't bite.</p>
