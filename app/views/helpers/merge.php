<?php
class MergeHelper extends AppHelper {

	function letter($content,$firstname,$lastname,$address1,$address2,$city,$state,$postalcode,$country,$address)
	{
		$content = str_replace("#FirstName#",$firstname,$content);
		$content = str_replace("#LastName#",$lastname,$content);
		
		$content = str_replace("#Address1#",$address1,$content);
		$content = str_replace("#Address2#",$address2,$content);
		$content = str_replace("#City#",$city,$content);
		$content = str_replace("#State#",$state,$content);
		$content = str_replace("#PostalCode#",$postalcode,$content);
		$content = str_replace("#Country#",$country,$content);
		
		$content = str_replace("#Address#",$address,$content);
		
		$content = str_replace("#Today#",date("F j, Y"),$content);
		
		return $content;
	}
	
	function cleanUpTinyHtml($content) {
		// remove line breaks which tcpdf makes into real line breaks
		$content = str_replace(chr(10),'',$content);
		$content = str_replace(chr(13),'',$content);
		
		return $content;
	}

}

?>