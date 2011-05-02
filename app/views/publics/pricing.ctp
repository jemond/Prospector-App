<div id="Banner">

	<h1>Pricing!</h1>
	
	<p>Pay as you go, cancel anytime, <?php echo $html->link('no contracts','/faq#cancel'); ?>. Three plans to choose from to match your needs.</p>
	
</div>

<div id="Fold">

	<table summary="Pricing information." id="PriceTable">
	
		<thead>
			<tr>
				<th></th>
				<th>Bronze</th>
				<th>Silver</th>
				<th>Gold</th>
			</tr>
		</thead>
		
		<tbody>
			<?php echo $this->element('pricing-table-rows'); ?>
			
			<tr>
				<th></th>
				<td class="signup"><?php echo $html->link('Sign up!','/signup/bronze'); ?></td>
				<td><?php echo $html->link('Sign up!','/signup/silver'); ?></td>
				<td><?php echo $html->link('Sign up!','/signup/gold'); ?></td>
			</tr>
		</tbody>
	
	</table>
	
	<?php echo $html->image('ccs.gif',array('title'=>'Visa, Mastercard, American Express and Discover accepted')); ?>
	
	<div style="margin-right:18em;">
	
		<h3>About pricing</h3>
	
		<p>
			<span class="callout">Pricing is based on the number of open prospects.</span><span class="subnote">When you 
			close a prospect, you are finished marketing to that prospect. There is no limit on how many closed prospects 
			your account has, but only open prospects can be used in campaigns and in reports (like merge letters).</span>
		</p>
	
		<p>
			<span class="callout">All plans are month to month, cancel anytime.</span> <span class="subnote"><br />Seriously. No sign-up fees, 
			cancellation fees or long-term contracts. You don't even have to call us. When you cancel, we will refund the unused 
			portion of your monthly fee automatically. <?php echo $html->link('Cancel anytime','/faq#cancel'); ?>.
		</p>
		
		<!-- (c) 2005, 2008. Authorize.Net is a registered trademark of CyberSource Corporation --> <div class="AuthorizeNetSeal" style="float:left; padding-right: 1em"> <script type="text/javascript" language="javascript">var ANS_customer_id="0dd4e9e3-9e9e-47a6-bb74-6d958f86121c";</script> <script type="text/javascript" language="javascript" src="//verify.authorize.net/anetseal/seal.js" ></script> <a href="http://www.authorize.net/" id="AuthorizeNetText" target="_blank">Credit Card Processing</a> </div>
		
		<br />
		
		<?php echo $html->image('rapidssl_ssl_certificate.gif'); ?>
		
		<br clear="all" />
		
	</div>
	
</div>