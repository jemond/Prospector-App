This is the break down for the next billing cycle: <?php echo $stats['next_invoice_date']; ?>

Plan Breakdown
==============
<?php $total_income = 0; $total_paid_accounts = 0; ?>
<?php
$max_length = 0;
if(is_array($stats['plans'])) {
	foreach($stats['plans'] as $plan_name=>$plan_details) {
		$max_length = $max_length < strlen($plan_name) ? strlen($plan_name) : $max_length;
	}
	
	foreach($stats['plans'] as $plan_name=>$plan_details) {
		while(strlen($plan_name) < $max_length) {
			$plan_name .= ' ';
		}
		
		echo $plan_name . chr(9) . $plan_details['total_accounts'] . chr(9) . $pretty->m($plan_details['monthly_income']) . chr(10);
		$total_income += $plan_details['monthly_income']; 
	}
}
if($plan_details['monthly_income'] > 0)
	$total_paid_accounts += $plan_details['total_accounts'];
?>
total	<?php echo $total_paid_accounts; ?>	<?php echo $pretty->m($total_income); ?>


Upcoming Billing
================
Billing date		<?php echo $stats['next_invoice_date']; ?>

Total invoices		<?php echo $stats['invoices_total_count']; ?>

Pending charges 		<?php echo $pretty->m($stats['invoices_total_to_charge']); ?>