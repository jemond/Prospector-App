<?php

if(strlen($lettercontent) == 0)
	die('You haven\'t written a letter to merge. Go to Touch Types, edit this touch and write the letter.');

App::import('Vendor','xtcpdf'); 
$pdf = new XTCPDF('P','in','Letter',true,'utf-8');

$pdf->SetMargins(1,0,1); 
$pdf->setPrintHeader(false); 
$pdf->setPrintFooter(false); 

$pdf->SetCreator('prospector'); // to do - app name
$pdf->SetAutoPageBreak(false, 0); 
$pdf->SetAuthor($session->read('Account.name') . ' (' . $session->read('User.name') . ')');
$pdf->SetTitle('Letter');

$pdf->SetFont('freeserif','',10);

foreach($campaigns['Prospect'] as $row=>$prospect) {

	$html = $merge->letter(
		$lettercontent,
		$prospect['firstname'],
		$prospect['lastname'],
		$prospect['address1'],
		$prospect['address2'],
		$prospect['city'],
		$prospect['state'],
		$prospect['postalcode'],
		$prospect['country'],
		$pretty->address(
				$prospect['address1'],
				$prospect['address2'],
				$prospect['city'],
				$prospect['state'],
				$prospect['postalcode'],
				$prospect['country'],
				'<br />'
			)
		);
		
	$html= $merge->cleanUpTinyHtml($html);
	$pdf->AddPage();
	$pdf->writeHTML($html, false, 0, true, 0);
}

echo $pdf->Output('letter.pdf', 'D');

?>