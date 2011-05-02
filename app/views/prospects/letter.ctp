<?php
$html = $merge->letter(
	$content["Touchtype"]["lettercontent"],
	$prospect["Prospect"]["firstname"],
	$prospect["Prospect"]["lastname"],
	$prospect["Prospect"]["address1"],
	$prospect["Prospect"]["address2"],
	$prospect["Prospect"]["city"],
	$prospect["Prospect"]["state"],
	$prospect["Prospect"]["postalcode"],
	$prospect["Prospect"]["country"],
	$pretty->address(
			$prospect['Prospect']['address1'],
			$prospect['Prospect']['address2'],
			$prospect['Prospect']['city'],
			$prospect['Prospect']['state'],
			$prospect['Prospect']['postalcode'],
			$prospect['Prospect']['country'],
			'<br />'
		)
	);
	
$html= $merge->cleanUpTinyHtml($html);

App::import('Vendor','xtcpdf'); 
$pdf = new XTCPDF();

$pdf->SetMargins(15,0,15); 
$pdf->setPrintHeader(false); 
$pdf->setPrintFooter(false); 

$pdf->SetCreator('prospector'); // to do app name
$pdf->SetAuthor($session->read('Account.name') . ' (' . $session->read('User.name') . ')');
$pdf->SetTitle('Letter');

$pdf->SetAutoPageBreak(TRUE, 0);
$pdf->AliasNbPages();

$pdf->SetFont('freeserif','',10);

$pdf->AddPage();
$pdf->writeHTML($html, true, 0, true, 0);
$pdf->lastPage();

echo $pdf->Output('letter.pdf', 'D');
?>