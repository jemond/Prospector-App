<?php 
$labels = "";
$rowdisplay = "";
$fFirstRow = true;

foreach($prospects as $prospect) {
	$row = "";
	foreach($prospect["Prospect"] as $label=>$value) { 
		if($fFirstRow)
			$labels.=$label.',';
		$row.=',"'.$value.'"';
	}
	$fFirstRow = false;
	$labels = substr($labels,0,strlen($labels)-1)."\n";
	$rowdisplay .= substr($row,1,strlen($row))."\n";
}

echo $labels.$rowdisplay;
?>