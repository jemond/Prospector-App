<?php $session->flash();?>
<div id="Page">

	<div id="Top"><h1>Settings</h1></div>
	
	<div id="Main">
		
		<?php if($usage_ratio>.9) :?>
		<div class="alert-box subnote">
			<?php echo $html->image('alert.gif',array('title'=>'Alert! I need your attention!')); ?>
			<?php if($usage_ratio>=1) :?>
				You are at the open prospect limit for this account. You must close some prospects or upgrade.
			<?php else : ?>
				You are currently near your account limit.
			<?php endif; ?>
		</div>			
		<?php endif; ?>

		<div class="hr">
		
			<h2>Account Details</h2>

			<p>
				<span class="callout">Account name: <?php echo $account['Account']['name']; ?></span>
				<?php if($session->read('User.admin') == 1) :?>
					<span class="subnote"><?php echo $html->link('Edit name','/settings/edit')?></span>
				<?php endif; ?>
				<br/ >
				<span class="subnote">
					Created: <?php echo $pretty->d($account['Account']['created']); ?><br />
					Last user login: <?php echo $pretty->d($account['Account']['last_login']); ?>
				</span>
			</p>

			<?php if($session->read('User.owner') == 1) :?>
			
				<p>
					<span class="callout">Your plan: <?php echo $account['Plan']['name']; ?></span>
					<span class="subnote"><?php echo $html->link('Change plan','/settings/plan')?></span>
					
					<br />
					<span class="subnote">
						Cost: <?php echo $pretty->m($account['Plan']['monthly_cost']); ?>/month<br />
						Prospect limit: <?php echo $account['Plan']['prospect_limit']; ?><br />
						Total open prospects: <?php echo $totalopenprospects; ?><br />
						Usage: <?php echo $pretty->percentage($usage_ratio); ?>%
					</span>
				</p>

				<p>
					<span class="callout">Billing</span>
					<span class="subnote"><?php echo $html->link('Edit credit card','/settings/creditcard'); ?></span><!-- to do billing edit page -->
					<br />
					
					<span class="subnote">
						<?php if($mNextBill > 0) : ?>
							Next bill date: <?php echo $pretty->d($dtNextBill);?><br />
							Next bill amount: <?php echo $pretty->m($mNextBill);?><br />
						<?php endif; ?>
						
						<?php if($account['Account']['cc'] != '') : ?>
						Credit card on file: ************<?php echo $account['Account']['cc'];?>
						<?php else : ?>
						No credit card on file
						<?php endif; ?>
						<br />
						
						<?php if($lastinvoice['Invoice']['dt'] != '') :?>
						Last invoice: 
							<?php echo $html->link($pretty->d($lastinvoice['Invoice']['dt']),
								'/invoices/view/'.$lastinvoice['Invoice']['id'],array('target'=>'_blank')); ?>
							
						<br />
						<?php endif; ?>
						
						<?php echo $html->link('Older invoices','/invoices/'); ?>
					</span>
					
				</p>
			
			<?php endif; ?>
			
		</div>

		<h2>Users</h2>
		<ul class="subnote">
			<li>Admins can invite, edit, disable and enable user accounts;</li>
			<li>The account owner will get invoices by email;</li>
			<li>The account owner can't be disabled; and,</li>
			<li>Only the owner can edit billing information and delete the account.</li>
		</ul>
		
		<?php foreach($openusers as $user) : ?>
			<!-- to do - show current user logged in -->
			<p>
				<?php echo $pretty->username($user['User']['name'],$user['User']['email']); ?>
				
				<span class="subnote">
					<?php if($user['User']['owner'] == 1) : ?>
						account owner, admin
					<?php elseif($user['User']['admin'] == 1) :?>
						admin user
					<?php endif; ?>
				</span>
				
				<br />
				
				<span class="subnote">
				
				<?php if ($user['User']['invite_pending'] == 1 && $session->read('User.admin') == 1) : ?>
					User invited, but they haven't signed-up.
					<?php echo $html->link('Resend invite','/settings/resendinvite/'.$user['User']['id'],array('title'=>'Resend the invite email.')); ?>
					<br />
				<?php else :?>
					Last login: <?php echo $pretty->dt($user['User']['last_login']); ?><br />
				<?php endif; ?>				
				
				<?php if($user_id == $user['User']['id']) :?>					
					This is you.<br />					
				<?php endif; ?>
				
				<?php if($user['User']['invite_pending'] != 1) :?>
					<?php echo $html->link('Edit','/users/edit/'.$user['User']['id'],array('title'=>'Edit your name and email.')); ?>
					
					<?php if($user['User']['owner'] == 0) : ?>
					
						<?php echo $html->link('Disable','/settings/disableuser/'.$user['User']['id'],array('title'=>'Disable the user; they won\'t be able to login'),
							'Are you sure you want to disable this user?'); ?>
					
						<?php if($user['User']['admin'] == 0) : ?>
							<?php echo $html->link('Make admin','/settings/makeadmin/'.$user['User']['id'],array('title'=>'Make the user an admin'),
								'Are you sure you want to make this user an admin?'); ?>
						<?php else : ?>
							<?php echo $html->link('Remove admin','/settings/removeadmin/'.$user['User']['id'],array('title'=>'Make this user a regular user'),
								'Are you sure you want make this user a regular user?'); ?>
						<?php endif; ?>
						
					<?php endif; ?>
					
				<?php endif; ?>
				
				</span>

			</p>
		
		<?php endforeach; ?>
		
		<p>
			<?php echo $html->link($html->image('add.gif',array('title'=>'Invite user')),'/settings/inviteuser',array('escape'=>false))?>
			Invite a new user
		</p>
		
		<div class="hr"></div>
		
		<?php if(count($closedusers) > 0) : ?>
		
			<p class="callout">Disabled users</p>
			<p class="subnote">
				These users can't login. You can't delete them entirely 
				because they are assocaited to prospect comment log entries. No worries, they can't login.
			</p>
			
			<?php foreach($closedusers as $user) : ?>
				<div>
					<span class="deleted"><?php echo $user['User']['name']; ?> (<?php echo $user['User']['email']; ?>)</span>
					
					<span class="subnote"><?php echo $html->link('Enable','/settings/enableuser/'.$user['User']['id'],array('title'=>'Enable the user so they can login')); ?></span>
				</div>		
			<?php endforeach; ?>
			
		<?php endif; ?>
		
		<?php if($session->read('User.owner') == 1) :?>
		
			<h2>Delete My Account</h2>
			<?php if(!$fFreePlan) : ?>
				<p>
					In order to delete your account you must first <?php echo $html->link('downgrade','/settings/plan')?> to the free plan.
				</p>
			<?php else : ?>
				<p>
					Yes, delete my account.
					<?php echo $form->checkbox('delete',array('onclick'=>'Effect.BlindDown(\'DeleteConfirm\')')); ?>
				</p>
				
				<div id="DeleteConfirm" style="display: none; " class="WarningBox">
					<p>This will erase all of your account data (prospects, campaigns, letters, conversion data, etc) and cannot be undone.</p>
					<p>
						<?php echo $html->link('Delete everything','/settings/delete',array('confirm'=>'Totally, absolutely sure?')); ?>
						
					</p>
				</div>
				
			<?php endif; ?>
			
		<?php endif; ?>
	
	</div>
	
</div>
<div id="Bottom"></div>