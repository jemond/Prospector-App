<div id="Page">

	<div id="Top">
		<h1>Invoices</h1>
	</div>

	<div id="Main">
		<p>
		Next bill date: <?php echo $pretty->d($dtNextBill); ?><br />
		Account created: <?php echo $pretty->d($startdate); ?><br />
		
		<div class="hr"></div>
		
		<?php foreach($invoices as $invoice) : ?>
			<?php echo $html->link($pretty->d($invoice['Invoice']['dt']),'/invoices/view/'.$invoice['Invoice']['id']
				,array('target'=>'_blank'));?>
	
			<span class="subnote">
				Invoice <?php echo $pretty->invoicenumber($invoice['Invoice']['id']); ?>,
				<?php echo $pretty->m($invoice['Invoice']['amount']);?>
			</span>
			<br />
		<?php endforeach; ?>
		</p>
	</div>

</div>
<div id="Bottom"></div>