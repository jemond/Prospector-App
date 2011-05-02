<table border="1">
<tr>
	<th>Date</th>
	<th>Message</th>
</tr>

<?php foreach($logs as $log) : ?>

	<tr>
		<td><?php echo $pretty->dt($log['Debug']['created']); ?></td>
		<td><pre><?php echo $log['Debug']['message']; ?></pre></td>
	</tr>

<?php endforeach; ?>

</table>