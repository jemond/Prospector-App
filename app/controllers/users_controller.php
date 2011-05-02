<?php
class UsersController extends AppController {

	var $name = 'Users';
	var $helpers = array('Pretty','Text','Javascript','Ajax','Merge','Session');
	var $components = array('Utility','Email','Identity','Authentication','Aim','Wrapper');
	var $uses = array('User','Account','Prospect','Plan','Debug','Source','Touchtype','Invoice','Billing','Profile','Campaign','Step');
	
	function index() 
    { 
         
    } 
     
	function beforeFilter() 
	{ 
		$this->__validateLoginStatus();	
		
		//force ssl for login and sign-up
		$ssl_methods = array('login','signup');
		if(in_array($this->action,$ssl_methods))
			$this->Authentication->requireSsl();
		else
			$this->Authentication->requireNonSsl();
	}
	
	function __validateLoginStatus() {
		// these are publicly accessible
		$public_methods = array('login','logout','forgot','reset','activate','signup');
		
		if(!in_array($this->action,$public_methods)) 
		{ 
			if($this->Session->check('User') == false) 
			{
				$this->Session->setFlash('The URL you\'ve followed requires you login.','message-error'); 
				$this->redirect('login',null,true); 
			} 
		} 
    }

	// signup and login call to login
	function __setupLoginSession($user,$account,$plan,$profile) {
		$this->Session->write('User', $user);
		$this->Session->write('Account', $account);
		$this->Session->write('Plan',$plan);
		$this->Session->write('Profile',$profile);
		
		// set new logins
		$this->User->id=$this->Session->read('User.id');
		$this->User->saveField('last_login',$this->Utility->getMySqlNow());
		
		$this->Account->id=$this->Session->read('User.account_id');
		$this->Account->saveField('last_login',$this->Utility->getMySqlNow()); // mostly so we can see whose accounts are active
	}

	function signup($page,$plan_name = 'bronze') {
	
		$this->pageTitle = 'Sign up';
		$plan_id = $this->Plan->getIdFromPublicName($plan_name);
		
		if($plan_name == 'silver') {
			$this->set('message','Plan: Silver, 250 prospects, unlimited campaigns<br />Cost: $' . $this->Plan->get($plan_id,'monthly_cost') . ' per month');
			$this->set('plan_name','silver');
			$this->set('fPaid',true);
			$this->set('charge',$this->Plan->getProRate($plan_id));
			$fPaidPlan = true; // tells us later on if this is free or paid; for payment processing
		}
		else if($plan_name == 'gold') {
			$this->set('message','Plan: Gold, 3000 prospects, unlimited campaigns<br />Cost: $' . $this->Plan->get($plan_id,'monthly_cost') . ' per month');
			$this->set('plan_name','gold');
			$this->set('fPaid',true);
			$this->set('charge',$this->Plan->getProRate($plan_id));
			$this->set('monthly_fee',$this->Plan->get($plan_id,'monthly_cost'));
			$fPaidPlan = true;
		}
		else { // default is the free plan
			$this->set('message','Plan: Bronze, a free plan with 15 prospects');
			$this->set('plan_name','bronze');
			$this->set('fPaid',false);
			$fPaidPlan = false;
		} 

		if(!empty($this->data)) {
			// VALIDATE the incoming fields
			$this->data['Account']['plan_id'] = $plan_id;
			$this->Account->set($this->data);
			$this->User->set($this->data);
			
			// validate CC info for paid plans only
			if($fPaidPlan) {
				if(strlen($this->data['Billing']['expiration_month']) == 1)
					$this->data['Billing']['expiration_month'] = '0' . $this->data['Billing']['expiration_month'];
				$this->Billing->set($this->data);
				$fBilling = $this->Billing->validates();
			}
			else
				$fBilling = true;
			
			$fPassword = false;
			$p1 = $this->data['User']['password1'];
			$p2 = $this->data['User']['password2'];				
			$fPassword = $this->User->checkPassword($p1) && $p1 == $p2;
			
			if(!$fPassword)
				$this->User->validationErrors['password1'] = 'You\'re passwords didn\'t match';
			
			$fUser = $this->User->validates();
			$fAccount = $this->Account->validates();
			
			// run the CC, setup CIM
			$fPayment = true;
			$fBilled = false; // tells us to do post billing stuff, like build the auth ids into the account
			
			if($fBilling && $fUser && $fAccount && $fPassword && $fPaidPlan) {
				
				// STEP 1, setup the A.net profile
				$create_profile_request = $this->Aim->setupProfile($this->data['User']['email']);
				
				if(!$create_profile_request['success']) {
					$fPayment = false;
					$sPaymentError = $create_profile_request['user_message'];
					if($create_payment_profile_request['error_type'] == 'system')
						$this->Wrapper->sendSupportEmail('Sign up of AIM profile failed',$create_profile_request);
				} 
				
				// STEP 2, setup the payment profile
				if($fPayment) {					
					$customer_profile_id = $create_profile_request['profile_id'];
					$create_payment_profile_request = $this->Aim->setupPaymentProfile(
						$customer_profile_id,
						$this->data['Billing']['creditcard'],
						$this->data['Billing']['expiration_year'],
						$this->data['Billing']['expiration_month'],
						$this->data['User']['email']
					);
					$customer_payment_profile_id = $create_payment_profile_request['payment_id'];
					
					if(!$create_payment_profile_request['success']) {	
						$fPayment = false;			
						$sPaymentError = $customer_payment_profile_id['user_message'];
						if($create_payment_profile_request['error_type'] == 'system')
							$this->Wrapper->sendSupportEmail('Sign up of payment profile failed',$create_payment_profile_request);						
					} 
				}
					
				// STEP 3, charge them the pro rate
				if($fPayment) {
					$mBilled = $this->Plan->getProRate($plan_id);
					$create_transaction_request = $this->Aim->charge(
						$customer_profile_id,
						$customer_payment_profile_id,
						$mBilled,
						"ProspectorApp Plan: $plan_name"
					);
					
					if(!$create_transaction_request['success']) {
						$fPayment = false;
						$sPaymentError = $create_transaction_request['user_message'];
						if($create_transaction_request['error_type'] == 'system')
							$this->Wrapper->sendSupportEmail('Sign up charge failed',$create_transaction_request);						
					} else {
						$fBilled = true; // causes the auth id to be saved
					}
					
					// ALL STEPS COMPLETE
					// if $fPayment is true, then it went through; 
					// fBilled will also be true to tell the script later on to do extra billing stuff
				}

			}
			
			if(!$fPayment)
				$this->Session->setFlash($sPaymentError,'message-error');				
			
			// EVERYTHING validates, paid-up, etc, so let's create 'em!
			if($fBilling && $fUser && $fAccount && $fPassword && $fPayment) {
				// save their auth.net ids for profile and payment; invoice gets created later
				if($fBilled) {
					$this->data['Account']['authorize_profile_id'] = $customer_profile_id;
					$this->data['Account']['authorize_payment_id'] = $customer_payment_profile_id;
					$this->data['Account']['cc'] = substr($this->data['Billing']['creditcard'],strlen($this->data['Billing']['creditcard'])-4,4);
				}
					
				$this->Account->save($this->data);
				
				$this->data['User']['owner'] = 1;
				$this->data['User']['admin'] = 1;
				$this->data['User']['last_login'] = $this->Utility->getMySqlNow();
				$this->data['User']['account_id'] = $this->Account->id;		
				$this->data['User']['password'] = md5($p1);
				
				$this->User->save($this->data);
				
				// setup basic profile
				$this->Profile->setup($this->User->id);
				
				// send welcome email
				$this->Wrapper->sendEmail('support@prospectorapp.com',$this->data['User']['email'],'Welcome to Prospector!','sign-up-welcome');
				
				// create invoice
				if($fBilled)
					$this->Invoice->add($this->Account->id,$mBilled,$this->data['Account']['cc'],$create_transaction_request['transaction_id'],'charge',"Plan: $plan_name");
				
				// get ready to setup initial login info
				$userdetails = $this->User->read();
				$accountdetails = $this->Account->read();
				$this->Plan->id = $this->Account->field('plan_id');
				$plandetails = $this->Plan->read();
				
				$this->__setupLoginSession($userdetails['User'],$accountdetails['Account'],$plandetails['Plan'],$userdetails['Profile']);
				
				// setup detaults for new accounts
				$this->Source->saveAll($this->User->getDefaultSources($this->Session->read('User.account_id')));
				$new_source_id = $this->Source->id;
				
				// setup default touch types
				$this->Touchtype->saveAll($this->User->getDefaultTouchTypes($this->Session->read('User.account_id')));
				
				// setup default campaigns
				$this->Campaign->saveAll($this->User->getDefaultCampaigns($this->Session->read('User.account_id')));
				$this->Step->saveAll($this->User->getDefaultSteps($this->Campaign->id,$this->Touchtype->id));
				$this->Campaign->saveField('step_count',2);
				
				// setup detault prospects
				$this->Prospect->create();
				$this->Prospect->save($this->User->getDefaultProspects(0,$this->Session->read('User.account_id'),$new_source_id,$this->Campaign->id));
				$this->Source->setProspectCounts(false,$new_source_id);
				$this->Campaign->setProspectCounts(false,$this->Campaign->id);

				$this->Prospect->create();
				$this->Prospect->save($this->User->getDefaultProspects(1,$this->Session->read('User.account_id'),$new_source_id,$this->Campaign->id));
				$this->Source->setProspectCounts(false,$new_source_id);
				$this->Campaign->setProspectCounts(false,$this->Campaign->id);
				
				// alert me to the sign-up
				$this->set('new_user_email',$this->data['User']['email']);				
				$this->Wrapper->sendEmail($this->Identity->EmailFrom(),'justin@62cents.com','New sign up! -- ' . $this->data['User']['email'],'new-signup');
				
				$this->Session->setFlash('Your account is created!','message-success');
				$this->redirect('/dashboard',null,true);				
			}
		}
		
		$this->render('signup','login');
	}
	
	function edit($user_id) {
		// admins can edit anyone in their account, and people can edit themselves
		if( $this->Session->read('User.id') == $user_id || $this->User->validateAdmin($this->Session->read('User.account_id'),$this->Session->read('User.admin'),$user_id) )
			$stay = true;
		else
			$this->redirect('/settings/',null,true);
	
		$this->User->id=$user_id;
		$this->set('user_id',$user_id);
		
		if(empty($this->data)) {			
			$this->data = $this->User->read();
		}
		else {
			if($this->User->save($this->data)) {
				if($user_id == $this->Session->read('User.id')) {
					$this->Session->write('User.name',$this->data['User']['name']);
					$this->Session->write('User.email',$this->data['User']['email']);
				}
				
				$this->Session->setFlash('User settings updated.','message-success');
				if($this->Session->read('User.admin') == 1)
					$this->redirect('/settings/',null,true);
				else
					$this->redirect('/dashboard/',null,true);
			}
		}
	}
     
	function activate($hash) {
		$user = $this->User->lookupUserByHash($hash);
		
		if(!$user)
			$this->redirect('/users/login',null,true);
			
		$this->set('email',$user['User']['email']);
		$this->set('hash',$hash);
		
		$this->render('invite','login');
		
		if(!empty($this->data)) {
			$p1 = $this->data['User']['password1'];
			$p2 = $this->data['User']['password2'];
			$user_id = $user['User']['id'];
			
			if($this->User->checkPassword($p1) && $p1 == $p2) {
				$this->User->resetUser($user_id,$p1);
				$this->User->id=$user_id;
				$this->User->saveField('invite_pending',null);
				
				$this->Session->setFlash('Your account is now active. Log in now right now!','message-success'); 
				$this->redirect('/users/login/',null,true);
			}
			else {
				$this->Session->setFlash('Passwords didn\'t match, or they aren\'t more than 5 characters.','message-error'); 
				$this->redirect('/users/reset/'.$hash,null,true);
			}
						
		}
	}
	 
    function login() 
    {
		$this->pageTitle = 'Login';
		
		$this->render('login','login');
		if(!empty($this->data)) 
		{			
			if(($user = $this->User->validateLogin($this->data['User'])) == true) 
			{ 
				$plan = $this->Plan->find('first',array(
					'conditions'=>array('Plan.id'=>$user['Account']['plan_id']),
					'fields'=>'Plan.name,Plan.prospect_limit'
					)
				);
				
				$this->__setupLoginSession($user['User'],$user['Account'],$plan['Plan'],$user['Profile']);

				$this->redirect('/dashboard',null,true);
			} 
			else 
			{ 
				$this->Session->setFlash('Sorry, that login info didn\'t work. Bummer.','message-error');
				$this->redirect('/login',null,true);
			} 
		} 
    }
     
    function logout() 
    { 
		$this->Session->destroy();
		$this->Session->setFlash('You\'ve successfully logged out.','message-alert'); 
		$this->redirect('/login',null,true); 
    }
	
	function reset($hash) {
		$user = $this->User->lookupUserByHash($hash);
		
		if(!$user)
			$this->redirect('/login',null,true);
			
		$this->set('email',$user['User']['email']);
		$this->set('hash',$hash);
		
		$this->render('reset','login');
		
		if(!empty($this->data)) {
			$p1 = $this->data['User']['password1'];
			$p2 = $this->data['User']['password2'];
			$user_id = $user['User']['id'];
			
			if($this->User->checkPassword($p1) && $p1 == $p2) {
				$this->User->resetUser($user_id,$p1);
				
				$this->Session->setFlash('You\'re password is reset! Log in right now.','message-success'); 
				$this->redirect('/login/',null,true);
			}
			else {
				$this->Session->setFlash('Passwords didn\'t match, or they aren\'t more than 5 characters.','message-error'); 
				$this->redirect('/users/reset/'.$hash,null,true);
			}
						
		}

	}
	
	function forgot() {
		$this->pageTitle = 'Reset your password';
		$this->render('forgot','login');
			
		if(!empty($this->data)) {
			$email_address = $this->data['User']['email'];
			$user_id = $this->User->field('id',array('email'=>$email_address));
			
			if(is_numeric($user_id)) {
				$hash = $this->User->generateLoginHash();
				$this->User->setHash($user_id,$hash);
				
				$this->set('hash',$hash);
				
				$this->Wrapper->sendEmail($this->Identity->EmailFrom(),$email_address,$this->Identity->Name() . ' - Forgot password','forgot-password');
			}
			
			// we always say success, so that people can't use the tool to identify email addresses
			$this->Session->setFlash('I\'ve just sent you an wicked awesome email to login. Dude, go check your email.','message-success'); 
			$this->redirect('/login',null,true); 
		}
	}
	
	function filter($saved = false) {
		if (!empty($this->data)) {
			// if keying a prospect ID directly, get em out of here
			if(is_numeric($this->data['User']['filter']) && $this->Prospect->validateProspect($this->data['User']['filter'],$this->Session->read('User.account_id')))
				$this->redirect('/prospects/view/'.$this->data['User']['filter']);
			else {
				$this->User->id=$this->Session->read('User.id');
				$this->Session->write('User.filter',$this->data['User']['filter']);
				$this->User->saveField('filter',$this->data['User']['filter']);			
			}
		}
		// built in filters go here
		$this->redirect('/prospects/',null,true);
	}
	
	function dismiss($page = false) {
		if($page == 'welcome') {
			$this->Profile->id=$this->Session->read('Profile.id');
			$this->Profile->saveField('show_welcome',0);
			$this->Session->write('Profile.show_welcome',0);
			$this->redirect('/dashboard',null,true);
		}
		else
			$this->redirect('/dashboard',null,true); 
	}
	
	function clearfilter() {
		$this->User->id=$this->Session->read('User.id');
		$this->Session->write('User.filter',null);
		$this->User->saveField('filter',null);
		$this->redirect('/prospects/',null,true);
	}
}

?>