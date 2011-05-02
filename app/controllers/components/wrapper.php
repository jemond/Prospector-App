<?php
class WrapperComponent extends Object {

	var $controller;
	var $components = array('Email');
	
	function initialize(&$controller)
	{
		$this->controller =& $controller;
	}
	
	function sendEmail($from,$to,$subject,$template='default',$body='') {
		$this->Email->delivery = 'smtp';
		$this->Email->template = $template;
		$this->Email->sendAs = 'text';		
		$this->Email->from    = $from;
		$this->Email->to      = $to;
		$this->Email->subject = $subject;
		return $this->Email->send($body);
	}
	
	function sendSupportEmail($source,$body) {
		$log = is_array($body) ? print_r($body,true) : $body;
		return $this->sendEmail('noreply@prospectorapp.com','support@prospectorapp.com','Website error report: ' . $source, null, $log);
	}
	
}

?>