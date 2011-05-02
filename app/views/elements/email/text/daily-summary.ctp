<?php echo $report_title; ?>

===============================================================

Daily Report
=============
New prospects		<?php echo $stats['new_prospects']; ?>

New campaigns		<?php echo $stats['new_prospects']; ?>

New accounts		<?php echo $stats['new_prospects']; ?>

Deletions			<?php echo $stats['deletions']; ?>

Plan changes		<?php echo $stats['plan_changes']; ?>

Total charged today	<?php echo $pretty->m($stats['total_charged']); ?>


New Signups
===========
<?php if(is_array($stats['new_signups'])) : ?>
<?php foreach($stats['new_signups'] as $new_plan=>$new_signup) :?>
<?php echo $new_plan;?>			<?php echo $new_signup; ?>
<?php endforeach; ?>
<?php endif; ?>

Global Totals
=============
Total prospects		<?php echo $stats['total_prospects']; ?>

Total campaigns		<?php echo $stats['total_prospects']; ?>

Total accounts		<?php echo $stats['total_accounts']; ?>

Total past due accounts	<?php echo $stats['past_due_count']; ?>


Plans and Income
================
<?php
$max_length = 0; $total_accounts=0; $total_income=0;
if(is_array($stats['plans'])) {
	foreach($stats['plans'] as $plan_name=>$plan_details) {
		$max_length = $max_length < strlen($plan_name) ? strlen($plan_name) : $max_length;
	}
	
	foreach($stats['plans'] as $plan_name=>$plan_details) {
		while(strlen($plan_name) < $max_length) {
			$plan_name .= ' ';
		}
		
		echo $plan_name . chr(9) . $plan_details['total_accounts'] . chr(9) . '$' . $plan_details['monthly_income'] . chr(10);
		$total_income += $plan_details['monthly_income']; 
		$total_accounts += $plan_details['total_accounts'];
	}
}
?>
total		<?php echo $total_accounts; ?>	$<?php echo $total_income; ?>


===============================================================
Report run on <?php echo date('Y-m-j @ h:i:s a T'); ?> from <?php echo $_SERVER['REMOTE_ADDR']; ?>