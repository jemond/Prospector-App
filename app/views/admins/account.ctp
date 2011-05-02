<h1>Account: <?php echo $account['Account']['name']; ?></h1>

<table summary="Details for this account.">
	<tr>
		<th>Account ID</th>
		<td><?php echo $account['Account']['id']; ?></td>
	</tr>
	<tr>
		<th>Name</th>
		<td><?php echo $account['Account']['name']; ?></td>
	</tr>	
	<tr>
		<th>Authorize.NET Profile ID</th>
		<td><?php echo $account['Account']['authorize_profile_id']; ?></td>
	</tr>
	<tr>
		<th>Authorize.NET Payment ID</th>
		<td><?php echo $account['Account']['authorize_payment_id']; ?></td>
	</tr>
	<tr>
		<th>Owner</th>
		<td>
			<?php echo $account['User']['name']; ?> 
			<a href="mailto:<?php echo $account['User']['email']; ?>"><?php echo $account['User']['email']; ?></a>
			(Last login: <?php echo $pretty->d($account['User']['last_login']); ?>)
		</td>
	</tr>
	<tr>
		<th>Plan name</th>
		<td><?php echo $account['Plan']['name']; ?></td>
	</tr>
	<tr>
		<th>Plan prospect limit</th>
		<td><?php echo $account['Plan']['prospect_limit']; ?></td>
	</tr>
	<tr>
		<th>Plan monthly cost</th>
		<td><?php echo $pretty->m($account['Plan']['monthly_cost']); ?></td>
	</tr>
	<tr>
		<th>Open prospects</th>
		<td><?php echo $openprospects; ?></td>
	</tr>
	<tr>
		<th>Open campaigns</th>
		<td><?php echo $opencampaigns; ?></td>
	</tr>
	<tr>
		<th>Total prospects</th>
		<td><?php echo $totalprospects; ?></td>
	</tr>
	<tr>
		<th>Created</th>
		<td><?php echo $pretty->d($account['Account']['created']); ?></td>
	</tr>
	<tr>
		<th>Last login</th>
		<td><?php echo $pretty->dt($account['Account']['last_login']); ?></td>
	</tr>
	
</table>

<h2>Users</h2>
<table summary="Account users">
<tr>
	<th>User ID</th>
	<th>Name</th>
	<th>Email</th>
	<th>Admin</th>
	<th>Owner</th>
	<th>Created</th>
	<th>Last login</th>
</tr>
<?php foreach($users as $user) :?>
	<tr>
		<td><?php echo $user['User']['id']; ?></td>
		<td><?php echo $user['User']['name']; ?></td>
		<td><?php echo $user['User']['email']; ?></td>
		<td><?php echo $pretty->yesno($user['User']['admin']); ?></td>
		<td><?php echo $pretty->yesno($user['User']['owner']); ?></td>
		<td><?php echo $pretty->d($user['User']['created']); ?></td>
		<td><?php echo $pretty->dt($user['User']['last_login']); ?></td>
	</tr>
<?php endforeach; ?>
</table>

<h2>Invoices</h2>
<table sumary="Invoices">
<tr>
	<th>Invoice #</th>
	<th>Date</th>
	<th>Type</th>
	<th>Amount</th>
	<th>Transaction</th>
	<th>Processed</th>
	<th>Transaction ID</th>
</tr>
<?php foreach($invoices as $invoice) : ?>
	<tr>
		<td><?php echo $invoice['Invoice']['id'];?></td>
		<td><?php echo $html->link($pretty->d($invoice['Invoice']['dt']),'/admins/invoice/'.$invoice['Invoice']['id']);?></td>
		<td>
			<?php if($invoice['Invoice']['charged'] == 1) : ?>
				Charge
			<?php elseif($invoice['Invoice']['refunded'] == 1) : ?>
				Refund
			<?php else : ?>
				Pending
			<?php endif; ?>
		</td>
		<td><?php echo $pretty->m($invoice['Invoice']['amount']);?></td>
		<td><?php echo $pretty->dt($invoice['Invoice']['amount']);?></td>
		<td><?php echo $pretty->yesno($invoice['Invoice']['processed']);?></td>
		<td><?php echo $invoice['Invoice']['transaction_id'];?></td>
	</tr>
<?php endforeach; ?>
</table>

<h2>Account History</h2>

<table summary="History of account changes.">
<tr>
	<th>Date</th>
	<th>Action</th>
	<th>User</th>
	<th>Message</th>
</tr>
<?php foreach($accountlogs as $log) : ?>

	<tr>
		<td><?php echo $pretty->dt($log['Accountlog']['created']); ?></td>
		<td><?php echo $log['Accountlog']['action']; ?></td>
		<td><?php echo $log['Accountlog']['user']; ?></td>
		<td><?php echo $log['Accountlog']['message']; ?></td>
	</tr>

<?php endforeach; ?>

</table>