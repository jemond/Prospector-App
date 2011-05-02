<?php $session->flash();?>
<div id="SidebarMainContent">

	<div id="Page">
	
		<div id="Top">
			<div class="left">
				<?php echo $form->create('User', array('action' => 'filter')); ?>
					Search: <?php echo $form->input('filter',array('div'=>false,'label'=>false,'size'=>50,'class'=>'largeinput')); ?>
					<?php echo $form->submit('Filter',array('div'=>false,'label'=>false,'class'=>'largeinput')); ?>
					<?php if($q) echo $html->link('Clear','/users/clearfilter/');?>
				<?php echo $form->end(); ?>
				<!-- to do - filter controls -->
			</div>
			<p class="right"><?php echo $html->link($html->image('add.gif',array('title'=>'Add Prospect')),'/prospects/add',array('escape'=>false))?></p>
	
			<br clear="all" />
		</div>
		
		<div id="Main">
	
			<table summary="A filtered list of open prospects">
				<?php echo $this->element('prospects-columns');?>
			
				<?php foreach ($prospects as $prospect): ?>
				<tr>
					<td>
						<?php echo $html->link($pretty->name($prospect['Prospect']['firstname'],$prospect['Prospect']['lastname']), 
							"/prospects/view/{$prospect['Prospect']['id']}"); ?>
					</td>
					<td><?php echo $pretty->citystate($prospect['Prospect']['city'], $prospect['Prospect']['state']); ?></td>
					<td align="center"><?php echo $prospect['Prospect']['touch_count']; ?></td>
					<td><?php echo $pretty->d($prospect['Prospect']['lasttouch']); ?></td>
					<td><div class="cwhstatus state<?php echo $prospect['Prospect']['cwh']; ?>">
						<!-- hot pepers would be cool, 1 blue pepper, 2 orange pepers, 3 red pepers--></div></td>
					<td><?php echo $pretty->d($prospect['Prospect']['created']); ?></td>
					<td><?php echo $pretty->openclose($prospect['Prospect']['open']); ?></td>
					<td>
						<?php echo $html->link($html->image('edit.gif',array('valign'=>'middle')), '/prospects/edit/'.$prospect['Prospect']['id'],array('escape'=>false));?>
						<?php echo $html->link(
								$pretty->openclosetoggle($prospect['Prospect']['open'],"display"), 
								"/prospects/".$pretty->openclosetoggle($prospect['Prospect']['open'])."/{$prospect['Prospect']['id']}", 
								null, 
								'Are you sure?' 
							)?>
					</td>
				</tr>
				<?php endforeach; ?>
			
				<tr>
					<td colspan="7"></td>
					<td><?php echo $html->link($html->image('add.gif',array('title'=>'Add Prospect')),'/prospects/add',array('escape'=>false))?></td>
				</tr>
			
			</table>
			
			<div>
				<span class="callout"><?=count($prospects); ?> 
				<?php if(count($prospects) == 1) : ?>prospect<?php else : ?>prospects<?php endif; ?></span> returned 
				<?php if($q) : ?>for <span class="callout"><?=$q; ?></span><?php endif;?>
			</div>
		
			<?php
				$search_suggestions = array();
				$search_suggestions[] = 'city:"los angeles"';
				$search_suggestions[] = 'lastname:trojan';
				$search_suggestions[] = 'firstname:tommy';
				$search_suggestions[] = 'zip:90036';
				$search_suggestions[] = 'touches:41';
				$search_suggestions[] = 'touches:+5';
				$search_suggestions[] = 'lasttouch:10/1/2008';
				$search_suggestions[] = 'lasttouch:-10/1/2008';
				$search_suggestions[] = 'lasttouch:never';
				$search_suggestions[] = 'lasttouch:lastmonth';
				$search_suggestions[] = 'lasttouch:lastweek';
				$search_suggestions[] = 'cwh:warm';
				
				$random = (rand()%count($search_suggestions));
				$suggestion = $search_suggestions[$random];
			?>
			
			<div class="hr"></div>
			
			<p class="subnote">Searches only include open prospects. To search for closed prospects, try <em>open: no</em></p>
			
			<p class="subnote">You can search specific fields in the search box. Try:<br /><?php echo $suggestion; ?></p>
		
		</div>
		
	</div>
	
	<div id="Bottom"></div>

</div>

<br clear="both" />
