
<?php foreach ($comments as $comment): ?>
	<div id="contactitem<?php echo $comment["Comment"]['id'];?>" class="contactblock">
		<div class="contactdetails"><?php echo $comment["User"]["name"]; ?> @ <?php echo $pretty->d($comment["Comment"]["created"]); ?></div>
		<div class="contactaction"><?php echo $comment["Comment"]["source"]; ?></div>
		<div class="contactnote">
			<?php echo $pretty->comment($comment["Comment"]["note"]); ?>
			<?php
				if( isset($comment["Touch"]["Touchtype"]["letter"]) && $comment["Touch"]["Touchtype"]["letter"] == 1)
					if($comment['Prospect']['open'] == 1)
						echo $html->link('Download letter','/prospects/letter/'.$comment["Comment"]['touch_id']);
					else
						echo '<span class="deleted">Download letter</span>';
			?>
			<?php
				if( isset($comment["Touch"]["Touchtype"]["labels"]) && $comment["Touch"]["Touchtype"]["labels"] == 1)
					if($comment['Prospect']['open'] == 1)
						echo $html->link("Download labels",'/prospects/labels/'.$comment["Comment"]['touch_id']);
					else
						echo '<span class="deleted">Download labels</span>';
			?>
			<?php	
				if( isset($comment["Touch"]["Touchtype"]["export"]) && $comment["Touch"]["Touchtype"]["export"] == 1)
					if($comment['Prospect']['open'] == 1)
						echo $html->link("Download export",'/prospects/export/'.$comment["Comment"]['touch_id']);	
					else
						echo '<span class="deleted">Download export</span>';
			?>
		</div>
	</div>
	
<?php endforeach; ?>