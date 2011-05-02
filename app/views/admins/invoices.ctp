<table summary="Invoices list">
<tr>
	<th>ID</th>
	<th>DT</th>
	<th>AID</th>
	<th>Account name</th>
	<th>Amount</th>
	<th>Processed</th>
	<th>Charge</th>
	<th>Refund</th>
	<th>Transaction ID</th>
	<th>Transaction Date</th>
</tr>

<?php foreach($invoices as $invoice) : ?>
	<tr>
		<td><?php echo $html->link($invoice['Invoice']['id'],'invoice/'.$invoice['Invoice']['id']); ?></td>
		<td><?php echo $invoice['Invoice']['dt']; ?></td>
		<td><?php echo $html->link($invoice['Account']['id'],'account/'.$invoice['Account']['id']); ?></td>
		<td><?php echo $invoice['Account']['name']; ?></td>
		<td><?php echo $pretty->m($invoice['Invoice']['amount']); ?></td>
		<td><?php echo $pretty->yesno($invoice['Invoice']['processed']); ?></td>
		<td><?php echo $pretty->yesno($invoice['Invoice']['charged']); ?></td>
		<td><?php echo $pretty->yesno($invoice['Invoice']['refunded']); ?></td>
		<td><?php echo $invoice['Invoice']['transaction_id']; ?></td>
		<td><?php echo $pretty->dt($invoice['Invoice']['transaction_date']); ?></td>
	</tr>
<?php endforeach; ?>

</table>