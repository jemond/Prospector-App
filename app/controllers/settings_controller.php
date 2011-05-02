<?php
class SettingsController extends AppController {

	var $name = 'Settings';
	var $uses = array('Account','User','Prospect','Invoice','Plan','Accountlog','Systemlog','Billing','Debug','Touch','Campaign','Step','Comment','Touchtype','Source','Profile');
	var $helpers = array('Pretty','Text','Javascript','Ajax','Merge','Session');
	var $components = array('Authentication','Utility','Email','Identity','Aim','Wrapper');

	function beforeFilter() 
    { 
		$this->Authentication->checkLoggedIn();
		$ssl_methods = array('creditcard');
		if(in_array($this->action,$ssl_methods))
			$this->Authentication->requireSsl();
		else
			$this->Authentication->requireNonSsl();
    }

	function index() {
		if(!$this->User->validateAdmin($this->Session->read('User.account_id'),$this->Session->read('User.admin'),false))
			$this->redirect('/dashboard/',null,true);
	
		$account_id = $this->Session->read('User.account_id');

		$this->set('dtNextBill',$this->Invoice->getNextBillDate());
		$this->set('mNextBill',$this->Plan->getCost($this->Session->read('Account.plan_id')));
		$this->set('lastinvoice',$this->Invoice->getLastInvoice($account_id));		
		$this->set('account',$this->Account->getDetails($this->Session->read('User.account_id')));
		$this->set('openusers',$this->User->getUsers($account_id,'open'));
		$this->set('closedusers',$this->User->getUsers($account_id,'closed'));
		$this->set('totalopenprospects',$this->Prospect->openProspectCount($account_id,'open'));
		$this->set('fOverage',$this->Prospect->overage($this->Session->read('User.account_id'),$this->Session->read('Plan.prospect_limit')));
		$this->set('usage_ratio',$this->Prospect->getUsageRation($this->Prospect->openProspectCount($account_id,'open'),$this->Session->read('Plan.prospect_limit')));
		$this->set('user_id',$this->Session->read('User.id'));
		$this->set('fFreePlan',$this->Plan->isFree($this->Session->read('Account.plan_id')));
	}
	
	function inviteuser() {	
		if(!$this->User->validateAdmin($this->Session->read('User.account_id'),$this->Session->read('User.admin'),false))
			$this->redirect('/dashboard/',null,true);

		if(!empty($this->data)) {
			$email = $this->data['Setting']['email'];			
			
			if(!$this->User->isUniqueEmail($email)) {
				if(!$this->User->isAccountOwner($email,$this->Session->read('User.account_id'))) {
					$template = 'invite-different-account'; // to do clean up the names in this email
					$this->set('email_from',$this->Session->read('User.email'));
				}
				else {
					$this->Session->setFlash('That user is already in this account. They can use the forgot password page to login.','message-alert');
					$this->redirect('/settings/',null,true);
				}
			}
			else {
				// create user
				$hash = $this->User->generateLoginHash();
				$this->data['User']['email'] = $email;
				$this->data['User']['account_id'] = $this->Session->read('User.account_id');
				$this->data['User']['password'] = $hash;
				$this->data['User']['invite_pending'] = 1;
			
				if($this->User->save($this->data)) {
					$user_id = $this->User->id;
					
					// setup basic profile
					$this->Profile->setup($user_id);
				
					// invite them				
					$this->User->setHash($user_id,$hash,60*60*24*7); //valid for a week
					
					// send the fucking email
					$template = 'invite';
					$this->set('link','http://prospectorapp.com/users/activate/'.$hash);
					$this->set('email_from',$this->Session->read('User.email'));
				}
			}
			
			$this->Wrapper->sendEmail($this->Identity->EmailFrom(),$email,$this->Identity->Name() . ' - Activate Account',$template);
			
			$this->Session->setFlash('We have invited ' . $email . ' to join. Tell them to check their email!','message-success');
			$this->redirect('/settings/',null,true);
		}
	
	}
	
	function resendinvite($id) {
		if(!$this->User->validateAdmin($this->Session->read('User.account_id'),$this->Session->read('User.admin'),$id))
			$this->redirect('/dashboard/',null,true);
			
		$hash = $this->User->generateLoginHash();
		$this->User->setHash($id,$hash,60*60*24*7); // a week
		$this->User->id=$id;
		$email = $this->User->field('email');
		
		$this->set('link','http://prospectorapp.com/users/activate/'.$hash);
		$this->set('email_from',$this->Session->read('User.email'));
			
		$this->Wrapper->sendEmail($this->Identity->EmailFrom(),$email,$this->Identity->Name() . ' - Activate Account','invite');
		
		$this->Session->setFlash('We reset the email invite to ' . $email . '.','message-success');
		$this->redirect('/settings/',null,true);
	}
	
	function makeadmin($id) {
		if(!$this->User->validateAdmin($this->Session->read('User.account_id'),$this->Session->read('User.admin'),$id))
			$this->redirect('/dashboard/',null,true);
			
		$this->User->id=$id;
		$this->User->saveField('admin',1);
		
		$this->Session->setFlash('User made an admin.','message-success');
		$this->redirect('/settings/',null,true);
	}
	
	function removeadmin($id) {
		if(!$this->User->validateAdmin($this->Session->read('User.account_id'),$this->Session->read('User.admin'),$id))
			$this->redirect('/dashboard/',null,true);
			
		$this->User->id=$id;
		$this->User->saveField('admin',0);
		
		$this->Session->setFlash('User made an regular.','message-success');
		$this->redirect('/settings/',null,true);
	}
	
	function disableuser($id) {
		if(!$this->User->validateAdmin($this->Session->read('User.account_id'),$this->Session->read('User.admin'),$id))
			$this->redirect('/dashboard/',null,true);
			
		// only admin can edit admin
		if(!$this->User->validateAdminAction($this->Session->read('User.admin'),$this->Session->read('User.id'),$id))
			$this->redirect('/settings/',null,true);
			
		$this->User->id=$id; 
		$this->User->saveField('disabled',1);
		$this->Session->setFlash('User disabled.','message-success');
		$this->redirect('/settings/',null,true);
	}
	
	function enableuser($id) {
		if(!$this->User->validateAdmin($this->Session->read('User.account_id'),$this->Session->read('User.admin'),$id))
			$this->redirect('/dashboard/',null,true);
			
		$this->User->id=$id;
		$this->User->saveField('disabled',0);
		$this->Session->setFlash('User enabled.','message-success');
		$this->redirect('/settings/');
	}
	
	function edit() {
		if(!$this->User->validateAdmin($this->Session->read('User.account_id'),$this->Session->read('User.admin')))
			$this->redirect('/dashboard/',null,true);
			
		$this->Account->id=$this->Session->read('User.account_id');
		
		if(empty($this->data)) {
			$this->data = $this->Account->read();
			// rename the data so it works with our renamed form - account ==> setting - lame, I know. but it works!
			$this->data['Setting'] = $this->data['Account'];
			unset($this->data['Account']);
		}
		else {
			$this->data['Account'] = $this->data['Setting'];
			unset($this->data['Setting']);
			if($this->Account->save($this->data)) {
				$this->Session->write('Account.name',$this->data['Account']['name']);
				$this->Session->setFlash('Settings updated.','message-success');
				$this->redirect('/settings/');
			}
		}
	}
	
	function plan() {
		if(!$this->User->validateOwner($this->Session->read('User.account_id'),$this->Session->read('User.id'),false))
			$this->redirect('/dashboard/',null,true);
			
		$account_id = $this->Session->read('User.account_id');
		
		$plans = $this->Plan->getList();
		$plan_charges = array();
		
		foreach($plans as $plan_id => $plan) {
			$plan_charges[$plan_id]['charge'] =  $this->Plan->getProRate($plan_id) - $this->Plan->getProRate($this->Session->read('Account.plan_id'));
			$plan_charges[$plan_id]['name'] =  $this->Plan->get($plan_id);
		}
		
		$this->set('plan_charges',$plan_charges);
		$this->set('account',$this->Account->getDetails($this->Session->read('User.account_id')));
		$this->set('totalopenprospects',$this->Prospect->openProspectCount($account_id,'open'));
		$this->set('plans',$plans);
		$this->set('prorate',$this->Plan->getProRate($this->Session->read('Account.plan_id')));
		
		// process submit
		if(!empty($this->data)) {
			$new_plan_id = $this->data['Plan']['id'];
			$old_plan_id = $this->Session->read('Account.plan_id');
			$fMovingFree = $this->Plan->isFree($new_plan_id);
			
			if($new_plan_id == $old_plan_id) {
				$this->Session->setFlash('Duh! You picked the same plan you have now.','message-alert');
				$this->redirect('/settings/',null,true);
			}
			
			if(!$this->Plan->validatePlan($new_plan_id))
				$this->redirect('/settings/',null,true);
			
			$new_plan_name = $this->Plan->get($new_plan_id);
			$old_plan_name = $this->Plan->get($old_plan_id);
			
			$charge = $this->Plan->getProRate($new_plan_id) - $this->Plan->getProRate($old_plan_id);
						
			// make sure they have billing information set up
			$this->Account->id = $this->Session->read('User.account_id');
			$authorize_profile_id = $this->Account->field('authorize_profile_id');
			$authorize_payment_id = $this->Account->field('authorize_payment_id');
			$invoice_cc = $this->Account->field('cc');
			$invoice_description = "Prospector Plan change to $new_plan_name from $old_plan_name"; // to do - lang for refund/charge
			
			if(!is_numeric($authorize_profile_id)) {
				$this->Session->setFlash('You need to add a credit card to your account first. Click on "Edit credit card" below.','message-error');
				$this->redirect('/settings/',null,true);
			}
			
			if($charge < 0) { // refund
				$transaction_id = $this->Invoice->getLastTransactionId($this->Session->read('User.account_id'));
				$create_transaction_request = $this->Aim->refund($transaction_id,$charge,$invoice_cc);
			}
			else { // charge
				$create_transaction_request = $this->Aim->charge(
					$authorize_profile_id,
					$authorize_payment_id,
					$charge,
					$invoice_description
				);
			}
			
			if(!$create_transaction_request['success']) {
				if($create_transaction_request['error_type'] == 'system')
					$this->Wrapper->sendSupportEmail('Plan change AID'.$this->Session->read('User.account_id'),$create_transaction_request);	
				
				$this->Session->setFlash($create_transaction_request['user_message'],'message-error');
				$this->redirect('/settings/',null,true);
			}
			
			// remove billing details if moving to a free plan
			if($fMovingFree) {
				$this->Account->id = $this->Session->read('User.account_id');
				$authorize_profile_id = $this->Account->field('authorize_profile_id');
				
				$delete_profile_response = $this->Aim->deleteProfile($authorize_profile_id);
				
				if(!$delete_profile_response['success']) {
					$this->Wrapper->sendSupportEmail('Plan change remove AIM profile failed AID'.$this->Session->read('User.account_id'),$delete_profile_response);
					$this->Session->setFlash('We are having a problem removing your billing information. Please give us a call so we can take care of this for you.','message-error');
					$this->redirect('/settings/',null,true);
				}
				else {
					$this->Account->saveField('cc',null);
					$this->Account->saveField('authorize_profile_id',null);
					$this->Account->saveField('authorize_payment_id',null);
				}
			}
			
			// create charge/refund invoice
			$invoice_type = $charge > 0 ? 'charge' : 'refund';
			$transaction_id = isset($create_transaction_request['transaction_id']) ? $create_transaction_request['transaction_id'] : false;	
				
			$this->Invoice->add($account_id,$charge,$invoice_cc,$transaction_id,$invoice_type,$invoice_description);
			
			// remove any unprocessed invoices as the daily routine will add them back in correctly
			$this->Invoice->clear($this->Session->read('User.account_id'));			
			
			// change plan in db
			$this->Account->id=$account_id;
			$this->Account->saveField('plan_id',$new_plan_id);
			
			// close prospects as needed
			$prospects_closed_count = $this->Prospect->closeExcess($account_id,$this->Plan->get($new_plan_id,'prospect_limit'),$this->Session->read('User.id'));
			
			// log to account history
			$log_message = "Plan changed from $old_plan_name ($old_plan_id) to $new_plan_name ($new_plan_id). Authorize.net response:";
			$this->Accountlog->insertLog($account_id,$log_message,'Plan change',$this->Session->read('User.email'));
			
			// update session
			$this->Session->write('Account.plan_id',$new_plan_id);
			$this->Session->write('Plan.name',$this->Plan->get($new_plan_id,'name'));
			$this->Session->write('Plan.prospect_limit',$this->Plan->get($new_plan_id,'prospect_limit'));
			
			// move em out
			$flash_message = "We successfully changed your plan to $new_plan_name and charged your credit card for $$charge.";
			if($prospects_closed_count > 0)
				$flash_message .= " We also closed $prospects_closed_count prospects to make your account fit in the plan.";
			
			$this->Session->setFlash($flash_message,'message-success');
			$this->redirect('/settings/',null,true);
		}
	}
	
	function creditcard() {
		if(!$this->User->validateOwner($this->Session->read('User.account_id'),$this->Session->read('User.id'),false))
			$this->redirect('/settings/',null,true);
			
		$this->Account->id = $this->Session->read('User.account_id');
		$this->set('cc',$this->Account->field('cc'));
		
		if(!empty($this->data)) {
			$fPaymentSetupWorked = true; // set to false when the billing fails
			$sPaymentError = false; // contains user friendly error message
			$fBilling = false; // set to true when the billing details validate
			$RedactedCreditCard = substr($this->data['Billing']['creditcard'],strlen($this->data['Billing']['creditcard'])-4,4);
			
			// correct mal-format exp month for user assist
			if(strlen($this->data['Billing']['expiration_month']) == 1)
				$this->data['Billing']['expiration_month'] = '0' . $this->data['Billing']['expiration_month'];
			$this->Billing->set($this->data);
			$fBilling = $this->Billing->validates();
			
			// if billing worked, try to setup the auth.net stuff
			if($fBilling) {			
				// if they have a.net profile, don't set it up
				$AuthorizeProfileId = $this->Account->field('authorize_profile_id');
				$AuthorizePaymentId = $this->Account->field('authorize_payment_id');
				
				// no profile id, so set them up
				if(!is_numeric($AuthorizeProfileId)) {
					$create_profile_request = $this->Aim->setupProfile($this->Session->read('User.email'));
					
					if(!$create_profile_request['success']) {
						$fPaymentSetupWorked = false;
						$sPaymentError = $create_profile_request['user_message'];
						$this->Wrapper->sendSupportEmail('Create AIM profile failed AID'.$this->Session->read('User.account_id'),$create_profile_request); // email report
					}
					else
						$AuthorizeProfileId = $create_profile_request['profile_id'];
				}
				
				// add the payment profile if it isn't there
				if(!is_numeric($AuthorizePaymentId)) {
					$create_payment_profile_request = $this->Aim->setupPaymentProfile(
						$AuthorizeProfileId,
						$this->data['Billing']['creditcard'],
						$this->data['Billing']['expiration_year'],
						$this->data['Billing']['expiration_month'],
						$this->Session->read('User.email')
					);
					if(!$create_payment_profile_request['success']) {
						$fPaymentSetupWorked = false;
						$sPaymentError = $create_payment_profile_request['user_message'];
						$this->Wrapper->sendSupportEmail('Create payment profile failed AID'.$this->Session->read('User.account_id'),$create_payment_profile_request); // email report
					}
					else
						$AuthorizePaymentId = $create_payment_profile_request['payment_id'];
				}
				// update payment profile if it is there
				else {
					$update_payment_profile_request = $this->Aim->updatePaymentProfile(
						$AuthorizeProfileId,
						$this->data['Billing']['creditcard'],
						$this->data['Billing']['expiration_year'],
						$this->data['Billing']['expiration_month'],
						$this->Session->read('User.email'),
						$AuthorizePaymentId
					);
					if(!$update_payment_profile_request['success']) {
						$fPaymentSetupWorked = false;
						$sPaymentError = $update_payment_profile_request['user_message'];
						$this->Wrapper->sendSupportEmail('Update payment profile failed AID'.$this->Session->read('User.account_id'),$update_payment_profile_request); // email report
					}
					else
						$AuthorizePaymentId = $update_payment_profile_request['payment_id'];
				}
					
			}
			
			// if it works, save it; otehrwise, fail!
			if($fPaymentSetupWorked && $fBilling) {
				$this->Account->saveField('authorize_profile_id',$AuthorizeProfileId);
				$this->Account->saveField('authorize_payment_id',$AuthorizePaymentId);
				$this->Account->saveField('cc',$RedactedCreditCard);
				$this->Accountlog->insertLog($this->Session->read('User.account_id'),'Credit card updated to ' . $RedactedCreditCard,'CC update', $this->Session->read('User.email'));
				
				$this->Session->setFlash('Credit card updated successfully. It will be charged on the first of the month.','message-success');
				$this->redirect('/settings/',null,true);
			}
			else if($fBilling) {
				$this->Session->setFlash($sPaymentError,'message-error');
				$this->redirect('/settings/creditcard/',null,true);
			}
		}
	}
	
	function delete() {
		if(!$this->User->validateOwner($this->Session->read('User.account_id'),$this->Session->read('User.id'),false))
			$this->redirect('/settings/',null,true);
			
		$account_id = $this->Session->read('User.account_id');
		$owner_email = $this->Session->read('User.email');
		
		// we can only delete a free plan, so they must downgrade first
		if(!$this->Plan->isFree($this->Session->read('Account.plan_id'))) {
			$this->Session->setFlash('You must first downgrade to a free plan befre you can delete.','message-error');
			$this->redirect('/settings/',null,true);
		}
		
		// dump all account log to systemlog
		$log = $this->Accountlog->getRaw($account_id);
		$action = 'Account deleted';
		$ip = $_SERVER['REMOTE_ADDR'];		
		
		$this->Systemlog->insertLog($owner_email,$log,$action,$ip);
		
		// delete order: touches, steps, comments, touchtypes, campaigns, prospects, sources, invoices, users, accounts, accountlogs
		
		// account_ids let us delete most, but we need prospect_ids and campaign_ids
		$prospect_ids = '';
		$prospect_counter = 1;
		$prospects = $this->Prospect->find('list',array(
			'conditions'=>array('account_id'=>$account_id)
			)
		);		
		foreach($prospects as $prospect) {
			$prospect_ids .= count($prospects) == $prospect_counter ? "prospect_id=$prospect " : "prospect_id=$prospect OR ";
			$prospect_counter++;
		}
	
		$campaign_ids = '';
		$campaign_counter = 1;
		$campaigns = $this->Campaign->find('all',array(
			'conditions'=>array('account_id'=>$account_id),
			'fields'=>'id'
			)
		);
		foreach($campaigns as $campaign) {
			$campaign_ids .= count($campaigns) == $campaign_counter ? 'campaign_id='.$campaign['Campaign']['id'] . ' ' : 'campaign_id='.$campaign['Campaign']['id'] . ' OR ';
			$campaign_counter++;
		}
		
		$account_criteria = 'account_id='.$account_id;
		
		// unbind comment
		$this->Comment->unbindModel(array('belongsTo'=>array('Prospect','Touch')),false);
		$this->Prospect->unbindModel(array('belongsTo'=>array('Source','Campaign')),false);
		
		// run the deletes
		$this->Touch->deleteAll($prospect_ids);
		$this->Step->deleteAll($campaign_ids);
		$this->Comment->deleteAll($prospect_ids);
		$this->Touchtype->deleteAll($account_criteria);
		$this->Campaign->deleteAll($account_criteria);
		$this->Prospect->deleteAll($account_criteria);
		$this->Source->deleteAll($account_criteria);
		$this->Invoice->deleteAll($account_criteria);
		$this->User->deleteAll($account_criteria);
		$this->Accountlog->deleteAll($account_criteria);
		$this->Account->del($account_id);
		
		// to do send email confirmation to owner
		$this->Wrapper->sendEmail($this->Identity->EmailFrom(),$owner_email,$this->Identity->Name() . ' - Account Deleted','account-delete');
		
		// wipe session and move to homepage
		$this->Session->destroy();
		$this->redirect('/',null,true); 
	}
	
}

?>