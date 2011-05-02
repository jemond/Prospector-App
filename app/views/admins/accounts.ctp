<?php if($session->read('Backend.filterexplained')) :?>
	Filter: <?php echo $session->read('Backend.filterexplained'); ?>
	<?php echo $html->link('Clear','/admins/clearfilter/'); ?>
<?php endif; ?>

<table summary="Account listing">
<tr>
	<th>Account ID</th>
	<th>Account name</th>
	<th>Plan</th>
	<th>Owner</th>
	<th>Last login</th>
	<th>Created</th>
</tr>

<?php foreach($accounts as $account) : ?>
	<tr style="background-color: <?php echo $account['Account']['pastdue']==1 ? 'red' : 'white';?>">
		<td><?php echo $account['Account']['id']; ?></td>
		<td><?php echo $html->link($pretty->title($account['Account']['name'],'{no name}'),'/admins/account/'.$account['Account']['id']); ?></td>
		<td><?php echo $account['Plan']['name']; ?></td>
		<td><?php echo $account['User']['name']; ?> <a href="mailto:<?php echo $account['User']['email']; ?>"><?php echo $account['User']['email']; ?></a></td>
		<td><?php echo $pretty->d($account['Account']['last_login']); ?></td>
		<td><?php echo $pretty->d($account['Account']['created']); ?></td>
	</tr>
<?php endforeach; ?>
</table>

<p>Returned <?php echo count($accounts); ?> accounts.