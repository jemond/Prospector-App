<?php 
	$current = $session->read('User.sort');
	$output = '';
	
	$columns = array('Name','Location','Touches','Last Touch','CWH','Created','Open');
	
	foreach($columns as $column) {
		$column = str_replace(' ','',$column);
		if(strtolower($column).'Up' == $current)
			$link = $html->link($column . ' ' . $html->image('arrowup.gif'), '/prospects/sort/'.strtolower($column).'Down',array('escape'=>false,'title'=>'Sort descending'));
		else if(strtolower($column).'Down' == $current)
			$link = $html->link($column . ' ' . $html->image('arrowdown.gif'), '/prospects/sort/'.strtolower($column).'Up',array('escape'=>false,'title'=>'Sort ascending'));
		else
			$link = $html->link($column, '/prospects/sort/'.strtolower($column).'Up');
	
		$output .= '<th>' . $link . '</th>';
	}
	
?>
<tr><?=$output;?></tr>
