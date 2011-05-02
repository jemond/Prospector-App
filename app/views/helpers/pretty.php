<?php
class PrettyHelper extends AppHelper {
    function name($first, $last) {
		$return = "";
		if($first)
			$return = $first." ";
		if($last)
			$return .= $last;
		
		if($return == "")
			$return = "{no name}";
		
        return $this->output($return);
    }
	
	function blankProtection($title,$default='name') {
		return strlen($title) == 0 ? '{no '.$default.'}' : $title;
	}
	
	function d($dt) {
		if(strtotime($dt))
			$return=date("n/j/Y",strtotime($dt));
		else
			$return=$dt;
			
		return $this->output($return);
	}
	
	function dt($dt=false) {
		if(!$dt)
			$return=false;
		else if(is_numeric($dt)) // for seconds sinec epoch thingy
			$return=date("n/j/Y g:i A",$dt);
		else if(strtotime($dt))
			$return=date("n/j/Y g:i A",strtotime($dt));
		else
			$return=$dt;
			
		return $this->output($return);
	}

	function address($address1, $address2, $city, $state, $postal_code, $country, $break="<br />") {
		$return = $address1;
	
		if($address2)
			$return = "$return$break$address2";
			
		if(!empty($city) || !empty($state) || !empty($post_code) )
			$return = "$return$break$city, $state $postal_code$break$country";
			
		return $this->output($return);
	
	}
	
	function citystate($city,$state) {
		$return = "$city";
		
		if($state)
			$return = "$return, $state";
	
		return $this->output($return);
	}
	
	function comment($comment) {
		$return = str_replace(chr(13),"<br />",$comment);
		$return = str_replace(chr(10),"<br />",$return);
		return $this->output($return);
	}
	
	function prospecttitle($id, $open, $firstname, $lastname) {
		$return = "Prospect: " . $this->name($firstname,$lastname);
		
		$return = $open == 0 ? "$return (CLOSED)" : $return;
			
		return $this->output($return);
	}
	
	function openclosetoggle($open,$style="command") {
		if($open == 0)
			$return = "open";
		else
			$return = "close";
		
		if($style == "display")
			$return = strtoupper(substr($return,0,1)).substr($return,1,strlen($return));
		
		return $this->output($return);
	}
	
	function openclose($open) {
	if($open == 1)
			$return = "Y";
		else
			$return = "N";
		return $this->output($return);
	}
	
	function touchtype($fLetter, $fLabels, $fExport)
	{
		$return="";
		if($fLetter == 1)
			$return="letter, ";
		if($fLabels == 1)
			$return.="lables, ";
		if($fExport==1)
			$return.="export, ";
			
		if(substr($return,strlen($return)-2,2) == ", ")
			$return = substr($return,0,strlen($return)-2);
			
		return $this->output($return);
	}
	
	function touchstatus($count,$d) {
		$return = $count;
		
		if($d)
			$return = "$return (".$this->d($d).")";
		
		return $this->output($return);
	}
	
	function progress($total,$completed) {
		if($total > 0) {
			$progress = round($completed/$total*100);
		}
		else
			$progress = "0";
			
		return $this->output($progress . '% complete');
	}
	
	function progressbar($total,$completed) {
		if($total > 0) {
			$progress = round($completed/$total*100);
		}
		else
			$progress = "0";

		$width = $progress*3;
		
		return $this->output("<span id='progresswrapper'><span id='progressbar'><img src=\"/img/orange-block.png\" width=\"$width\" height=\"30\" /></span> <span class='subnote'>$progress% complete</span></span>");
	}
	
	function username($name = false,$email = false) {
		if(strlen($name) == 0)
			$name = false;
		
		if(strlen($email) == 0)
			$email = false;
		
		if($name && $email)
			$display = "$name ($email)";
		else if ($name)
			$display = $name;
		else if (!$name && !$email)
			$display = '{no name}';
		else
			$display = $email;
			
		return $display;
	}
	
	function yesno($f) {
		return $f==1 ? 'Yes' : 'No';
	}
	
	function days($days) {
		return ($days == 1) ? '1 day' : $days . ' days';
	}
	
	function percentage($n) {
		return round(100*$n);
	}
	
	function prorate($m) {
		$word = $m < 0 ? 'Refund' : 'Charge';
		return "$word " . $this->m(abs($m));
	}
	
	// incase I need to pretty it up
	function invoicenumber($id) {
		return $id;
	}
	
	function title($title,$default) {
		return $title == '' ? $default : $title;
	}
	
	// moeny formatter
	// taken from: http://us.php.net/manual/en/function.number-format.php#87381
	// formats money to a whole number or with 2 decimals; includes a dollar sign in front
	function m($number, $cents = 1) { // cents: 0=never, 1=if needed, 2=always
		if (is_numeric($number)) { // a number
			if (!$number) { // zero
				$money = ($cents == 2 ? '0.00' : '0'); // output zero
			} else { // value
				if (floor($number) == $number) { // whole number
					$money = number_format($number, ($cents == 2 ? 2 : 0)); // format
				} else { // cents
				$money = number_format(round($number, 2), ($cents == 0 ? 0 : 2)); // format
				} // integer or decimal
			} // value
			return '$'.$money;
		} // numeric
	} // formatMoney
}

?>