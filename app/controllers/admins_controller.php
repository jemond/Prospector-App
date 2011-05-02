<?php
class AdminsController extends AppController {

	var $name = 'Admins';
	var $uses = array('Account','User','Plan','Prospect','Campaign','Invoice','Accountlog','Debug','Systemlog');
	var $helpers = array('javascript','pretty');
	
	function beforeFilter() 
    { 
		// require from our IPs
		$user_ip = $_SERVER['REMOTE_ADDR'];
		
		$allowed_ips = array(
			'127.0.0.1',
			'71.178.193.10',
		);
		
		if(!in_array($user_ip,$allowed_ips))
			$this->redirect('/',null,true);
		
		$this->__validateLoginStatus(); 
	
    }
	
	function __validateLoginStatus() {
		// these are publicly accessible
		$public_methods = array('login');
		
		if(!in_array($this->action,$public_methods)) 
		{ 
			if($this->Session->check('Backend') == false) 
			{
				$this->redirect('login',null,true); 
			} 
		} 
    }
	
	function index() {
		$this->redirect('accounts',null,true); 
	}
	
	function clearfilter() {
		$this->Session->write('Backend.filterexplained',false);
		$this->Session->write('Backend.filter',false);
		$this->redirect('accounts',null,true);
	}
	
	function setfilter($filter=false,$q=false) {
		$criteria = $this->Session->read('Backend.filter');
		
		if($filter && $q) {
			if($filter == 'plan' && is_numeric($q)) {
				$criteria['Account.plan_id'] = $q;
				$this->Plan->id=$q;
				
				$this->Session->write('Backend.filterexplained','Showing plan ' . $this->Plan->field('name'));
				$this->Session->write('Backend.filter',$criteria);
			}
				
		}
		$this->redirect('accounts',null,true);
	}
	
	function debuglogs() {
		$this->set('logs',$this->Debug->find('all',array(
			'order'=>'created DESC'
			)
		));
		
		$this->render('debuglogs','admin');
	}
	
	function systemlogs() {
		$this->set('logs',$this->Systemlog->find('all',array(
			'order'=>'created DESC'
			)
		));
		
		$this->render('systemlogs','admin');
	}
	
	function accounts() {
		$criteria = $this->Session->read('Backend.filter');
		
		// grab owner information
		$this->Account->bindModel(
			array('hasOne' => array(
				'User'=>array(
					'conditions' => array('User.owner' => 1),
					)
				),
				'belongsTo' => array('Plan')
			)
		);	
		$this->set('accounts',$this->Account->find('all',array(
			'fields'=>'Account.id,Account.name,Account.created,Account.last_login,Account.plan_id,Account.authorize_profile_id,'.
				'Account.authorize_payment_id,User.name,User.email,Plan.name,Account.pastdue',
			'order'=>'Account.created DESC',
			'conditions'=>$criteria
			)
		));
		
		$this->render('accounts','admin');
	}
	
	function invoices() {
		$this->Invoice->bindModel(array('belongsTo'=>array('Account')));
		$invoices = $this->Invoice->find('all',array(
			'fields'=>'Invoice.id,Invoice.dt,Invoice.transaction_date,Invoice.amount,Invoice.charged,Invoice.refunded,Invoice.transaction_id,'
			.'Invoice.creditcard,Invoice.processed,Account.id,Account.name',
			'order'=>'Invoice.dt DESC'
			)
		);
		
		$this->set('invoices',$invoices);
	
		$this->render('invoices','admin');
	}
	
	function plans() {
	
		$plans = $this->Plan->find('all',array(
			'fields'=>'Plan.id,Plan.name,Plan.monthly_cost,Plan.prospect_limit,Plan.show_signup',
			'order'=>'Plan.show_signup DESC,Plan.prospect_limit'
			)
		);
		
		foreach($plans as $key=>$plan) {
			$plans[$key]['Plan']['account_total'] = $this->Account->getCountByPlan($plan['Plan']['id']);
		}
		
		$this->set('plans',$plans);
	
		$this->render('plans','admin');	
	}
	
	function account($account_id) {
		$this->set('users',$this->User->getUsers($account_id));
		$this->set('invoices',$this->Invoice->getAll($account_id,'all'));
		$this->set('accountlogs',$this->Accountlog->get($account_id));
		$this->set('totalprospects',$this->Prospect->totalProspectCount($account_id));
		$this->set('openprospects',$this->Prospect->openProspectCount($account_id));
		$this->set('opencampaigns',$this->Campaign->getOpenCampaignCount($account_id));
		
		$this->Account->bindModel(array('belongsTo'=>array('Plan'=>array(
			'fields'=>'Plan.prospect_limit,Plan.name,Plan.monthly_cost'		
			)
		)),false);
		$this->Account->bindModel(
			array('hasOne' => array(
				'User'=>array('conditions' => array('User.owner' => 1))
				)
			)
		,false);
		$this->set('account',$this->Account->find('first',array(
			'fields'=>	
				'Account.id,Account.name,Account.created,Account.last_login,Account.plan_id,Account.authorize_profile_id,Account.authorize_payment_id,'
				.'Plan.name,Plan.monthly_cost,Plan.prospect_limit,'
				.'User.email,User.last_login,User.name'
				,
			'conditions'=>array('Account.id'=>$account_id)
			)
		));
		
		$this->render('account','admin');
	}
	
	function invoice($invoice_id) {
		$invoice = $this->Invoice->get($invoice_id,'any');
		
		$this->Invoice->id = $invoice_id;
		$account_id = $this->Invoice->field('account_id');
		
		$this->Account->id = $account_id;
		$accountname = $this->Account->field('name');
		$owner = $this->User->getOwner($account_id);
		
		$this->set('customeremail',$owner['User']['email']);
		$this->set('customername',$owner['User']['name']);
		$this->set('accountname',$accountname);
		$this->set('accountid',$account_id);
		
		$this->set('invoice',$invoice);
		
		$this->render('invoice','invoice');
	}
	
	/*
	should we even have this?
	function refund($account_id) {
		
		// to do trigger auth.net refund
		
		// to do remove from auth.net cim
		
		// to do demote plan to free
		
		// to do close prospects to limit
		
		// to do send refund recript email
		
		// to do insert refund invoice		
		
		// log message
		$log_message = 'Admin processed refund for $. Plan changed from to . Authorize.net response:';
		$this->Accountlog->insertLog($account_id,$log_message,'Refund','system');
		
		$this->redirect('/admins/account/'.$account_id,null,true);	
	}*/ 
	
	function login() {
		if(!empty($this->data)) {
			if($this->data['Admins']['username'] == 'prsbilling' && $this->data['Admins']['password'] == '234njjklkj97666') {
				$this->Session->write('Backend',array('logged_in'=>1));
				$this->Session->write('Backend.filter',array());
				$this->Session->write('Backend.filterexplained',false);
				$this->redirect('/admins/',null,true);
			}
		}
		
		$this->render('login','admin');
	}
	
	function logout() {
		$this->Session->destroy();
		$this->redirect('/',null,true); 
	}
}

?>