<?php
class AuthenticationComponent extends Object {

	var $controller;
	var $components = array('Session','RequestHandler');
	
	function initialize(&$controller)
    {
		$this->controller =& $controller;
    }
	
    function checkLoggedIn() {
		if(!$this->Session->check('User')) {
			$this->Session->setFlash('The URL you\'ve followed requires you login.','message-error');
			$this->controller->redirect('/login');
		} 
	}
	
	// moves them to ssl if they aren't already there
	function requireSsl() {
		if(!isset($_SERVER['HTTPS'])) {
			$fWww = is_numeric(strpos($_SERVER['HTTP_HOST'],'www.'));
			$url = 'https://';
			if(!$fWww)
				$url .= 'www.';
			$url .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$this->controller->redirect($url,null,true);
		}
		
		return true;
	}
	
	// force out of ssl, cpus aren't cheap
	function requireNonSsl() {
		if(isset($_SERVER['HTTPS'])) {
			$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$this->controller->redirect($url,null,true);
		}
		
		return true;
	}
}

?>