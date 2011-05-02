<h1>Invoice #<?php echo $pretty->invoicenumber($invoice['Invoice']['id']); ?>: <?php echo $pretty->d($invoice['Invoice']['dt']); ?></h1>

<h2>Prospector</h2>

<p>
	<strong>62 Cents Software</strong><br />
	ECM# 78735<br />
	93 S. Jackson Street<br />
	Seattle, Washington 98104-2818<br />
	United States of America<br />
	<br />
	Phone (509) 368-7038<br />
	Fax (512) 857-6904<br />
	Email support@62cents.com<br />
	<br />
	http://62cents.com<br />
	http://prospectorapp.com <!-- to do correct address information -->

	<br /><br />
	
	<strong>Customer</strong><br />
	Account name: <?php echo $accountname; ?><br />
	Account ID: <?php echo $accountid; ?><br />
	Name: <?php echo $customername; ?><br />
	Email: <?php echo $customeremail; ?>
	
	<br /><br />
	
	<strong>Invoice Details</strong><br />
	Invoice number: <?php echo $pretty->invoicenumber($invoice['Invoice']['id']); ?><br />
	Invoice date: <?php echo $pretty->d($invoice['Invoice']['dt']); ?><br />
	Payment date:<?php echo $pretty->dt($invoice['Invoice']['transaction_date']); ?><br />
	Product: <?php echo $invoice['Invoice']['description']; ?><br />
	
	<?php if($invoice['Invoice']['charged'] == 1 ) :?>
		Paid: <?php echo $pretty->m(abs($invoice['Invoice']['amount'])); ?> USD<br />
		Credit card: ************<?php echo $invoice['Invoice']['creditcard']; ?><br />
	<?php elseif($invoice['Invoice']['refunded'] == 1 ) : ?>
		Refund: <?php echo $pretty->m(abs($invoice['Invoice']['amount'])); ?> USD<br />
	<?php else : ?>
		PENDING<br />
	<?php endif; ?>		
	
	Printed: <?php echo $pretty->dt(time()); ?>
	
	<br /><br />
	
	<strong>Please Note</strong>

</p>
<ul>
	<li>The company charge on your credit card will read "62 Cents Software", our parent company</li>
	<li>Your first invoice will be a pro-rate charge for the remainder of the month</li>
	<li>Please give us a call if you have any questions: http://prospectorapp.com/support</li>
</ul>