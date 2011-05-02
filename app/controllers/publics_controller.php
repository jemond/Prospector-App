<?php
class PublicsController extends AppController {

	var $name = 'Publics';
	var $helpers = array('Pretty','Merge','Javascript','Ajax','Html');
	var $uses = null;
	var $components = array('Authentication');
	
	function beforeFilter() {
		$this->Authentication->requireNonSsl();
	}
	
	function index() {
		$this->set('title', 'Home');
		$this->render('index','public');
	}
	
	function features() {
		$this->set('title', 'Features');
		$this->render('features','public');
	}
	
	function pricing() {
		$this->set('title', 'Pricing');
		$this->render('pricing','public');
	}
	
	function signup() {
		$this->set('title', 'Sign Up');
		$this->render('signup','public');
	}
	
	function support() {
		$this->set('title', 'Support');
		$this->render('support','public');
	}
	
	function help() {
		$this->set('title','Help!');
		$this->render('help','help');
	}
	
	function faq() {
		$this->set('title','FAQ');
		$this->render('faq','help');
	}
	
	function about() {
		$this->set('title','About Us');
		$this->render('about','public');
	}
	
	function privacy() {
		$this->set('title','Privacy');
		$this->render('privacy','public');
	}
}

?>