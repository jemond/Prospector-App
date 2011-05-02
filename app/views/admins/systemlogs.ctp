<table border="1">
<tr>
	<th>Date</th>
	<th>Action</th>
	<th>Message</th>
</tr>

<?php foreach($logs as $log) : ?>

	<tr>
		<td valign="top"><?php echo $pretty->dt($log['Systemlog']['created']); ?></td>
		<td valign="top"><?php echo $log['Systemlog']['action']; ?></td>
		<td valign="top"><pre><?php echo $log['Systemlog']['log']; ?></pre></td>
	</tr>

<?php endforeach; ?>

</table>