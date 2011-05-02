<table summary="All plans in the application">
<tr>
	<th>Name</th>
	<th>Monthly cost</th>
	<th>Prospect limit</th>
	<th>Total accounts</th>
	<th>Total income</th>
</tr>

<?php $income_total = 0; $account_total = 0; ?>

<?php foreach($plans as $plan) : ?>
	<?php $income_total += ($plan['Plan']['monthly_cost'] * $plan['Plan']['account_total']); ?>
	<?php $account_total += $plan['Plan']['account_total']; ?>
	<tr>
		<td><?php echo $plan['Plan']['name']; ?></td>
		<td><?php echo $pretty->m($plan['Plan']['monthly_cost']); ?></td>
		<td><?php echo $plan['Plan']['prospect_limit']; ?></td>
		<td><?php echo $html->link($plan['Plan']['account_total'],'/admins/setfilter/plan/'.$plan['Plan']['id']); ?></td>
		<td><?php echo $pretty->m($plan['Plan']['monthly_cost'] * $plan['Plan']['account_total']); ?></td>
	</tr>
<?php endforeach; ?>
<tr>
	<td></td>
	<td></td>
	<td>Total:</td>
	<td><?php echo $account_total; ?></td>
	<td><?php echo $pretty->m($income_total); ?></td>
</tr>
</table>