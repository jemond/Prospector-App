<?php

// 8.5 is 210, 11 is 276 ot 277
$labelx = 2.625;
$labely = 1;
$cellpadding = .15625;
$marginleft = 0.1875;
$marginright = 0.1875;
$margintop = 0.5;
$marginfooter = 0.5;

App::import('Vendor','xtcpdf'); 
$pdf = new XTCPDF('P','in','Letter',true,'utf-8');

$pdf->setPrintHeader(false); 
$pdf->setPrintFooter(false); 
$pdf->SetAutoPageBreak(false, $marginfooter); 
$pdf->SetMargins($marginleft,0,$marginright); 

$pdf->SetCreator('prospector');
$pdf->SetAuthor($session->read('Account.name') . ' (' . $session->read('User.name') . ')');
$pdf->SetTitle('Letter');

$pdf->SetFont('times', '', 10); //this gets overwritten by inline style as needed

$pdf->AddPage();

$cells = 1;
$y = $margintop;
$x = $marginleft;

foreach($campaigns['Prospect'] as $row=>$prospect) {
	$name = $pretty->name($prospect['firstname'],$prospect['lastname']);
	$address = $pretty->address(
				$prospect['address1'],
				$prospect['address2'],
				$prospect['city'],
				$prospect['state'],
				$prospect['postalcode'],
				$prospect['country'],
				'<br />'
			);
			
	$html = "$name<br />$address";
	
	$pdf->MultiCell($labelx, $labely, $html, 0, 'L', 0, 0, $x ,$y, true,0,true);
	
	$x += $labelx + $cellpadding;
	
	if($cells % 3 == 0) {
		$y = $y + 1;
		$x = $marginleft;
	}
	
	if($cells >= 30) {
		$pdf->AddPage();
		$cells = 0;
		$y = $margintop;
	}
	
	$cells++;
}

echo $pdf->Output('labels.pdf', 'D');
	
?>