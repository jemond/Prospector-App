<?php 
$labels = "";
$row = "";
foreach($prospect["Prospect"] as $label=>$value) { 
	if($label != "id") {
		$labels.=",".$label;
		$row.=',"'.$value.'"';
	}
}
$labels = substr($labels,1,strlen($labels))."\n";
$row = substr($row,1,strlen($row))."\n";

echo $labels.$row;

?>