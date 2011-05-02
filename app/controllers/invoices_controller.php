<?php
class InvoicesController extends AppController {

	var $name = 'Invoices';
	var $helpers = array('Pretty','Text','Javascript');
	var $uses = array('Invoice','Account','Plan','User');
	var $components = array('Authentication');
	
	function beforeFilter() 
    { 
		$this->Authentication->checkLoggedIn();
		$this->Authentication->requireNonSsl();
		
		// only admins can
		if($this->Session->read('User.owner') != 1)
			$this->redirect('/settings/',null,true);
    }
	
	function index() {
		$this->set('invoices',$this->Invoice->getAll($this->Session->read('User.account_id'),'charged'));
		
		$this->set('dtNextBill',$this->Invoice->getNextBillDate());
		$this->Account->id=$this->Session->read('Account.id');
		$this->set('startdate',$this->Account->field('created'));		
	}
	
	function view($invoice_id) {
		if(!$this->Invoice->validateInvoice($invoice_id,$this->Session->read('User.account_id')))
			$this->redirect('/settings/',null,true);
			
		$invoice = $this->Invoice->get($invoice_id);
		
		$this->set('customeremail',$this->Session->read('User.email'));
		$this->set('customername',$this->Session->read('User.name'));
		$this->set('accountname',$this->Session->read('Account.name'));
		$this->set('accountid',$this->Session->read('User.account_id'));
		
		$this->set('invoice',$invoice);
					
		$this->render('view','invoice');
	}
}

?>