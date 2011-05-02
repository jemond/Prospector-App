<?php
class Debug extends AppModel
{
	var $name = 'Debug';
	
	function log($message,$page = 'No page') {
		$log = is_array($message) ? print_r($message,true) : $message;
		$log = empty($log) ? ' ' : $log;
		return $this->save(array('message'=>$page.'>>\n'.$log));
	}
	
	// to do - make this email me
	function sendSupportEmail($message) {
		$this->Email->delivery = 'smtp';
		$this->Email->template = 'default';
		$this->Email->sendAs = 'text';					
		$this->Email->from    = 'support@prospectorapp.com';
		$this->Email->to      = 'support@prospectorapp.com';
		$this->Email->subject = 'Authorize.net Error';
		//$this->Email->send($message);
		
		return $this->log($message);
	}
}
?>