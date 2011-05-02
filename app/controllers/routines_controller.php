<?php
class RoutinesController extends AppController {

	var $name = 'Routines';
	var $uses = array('Invoice','Account','Plan','User','Prospect','Campaign','Accountlog','Systemlog','Plan');
	var $components = array('Authentication','Identity','Email','Aim','Wrapper');
	var $helpers = array('Pretty');
	
	function beforeFilter() 
    { 
		// only from our IP and the server itself
		$allowed_ips = array('127.0.0.1','209.20.73.159');
		if(!in_array($_SERVER['REMOTE_ADDR'],$allowed_ips))
			die();
    }
	
	function daily($key) {	
		/* 
		SETUP and INTIALIZE
		*/
		if($key != 564851)
			die();
	
		$report_title = 'Prospector daily report: ' . date('n/j/Y');
		$this->set('report_title',$report_title);
		
		$send_report_to = 'justin@62cents.com';
		
		// bindings we will use later
		$this->Account->bindModel(array('belongsTo'=>array('Plan')),false);
		
		// set next invoice/billing date
		$next_invoice_date = $this->Invoice->getNextBillDate(); //in mysql format
		$today_invoice_date = date('Y-n-j');
		
		
		
		/*
		DAILY REPORT
		send a daily report to me of new accounts, past dues, plan changes, deletions, total prospects, total campaigns
		*/
		$stats = array();
		
		$stats['total_prospects'] = $this->Prospect->find('count');
		$stats['total_campaigns'] = $this->Campaign->find('count');
		$stats['total_accounts'] = $this->Account->find('count');
		
		$stats['new_prospects'] = $this->Prospect->find('count',array(
			'conditions'=>'Prospect.created > DATE_SUB(NOW(), INTERVAL 24 HOUR)'
			)
		);
		$stats['new_campaigns'] = $this->Campaign->find('count',array(
			'conditions'=>'Campaign.created > DATE_SUB(NOW(), INTERVAL 24 HOUR)'
			)
		);
		$stats['new_accounts'] = $this->Account->find('count',array(
			'conditions'=>'Account.created > DATE_SUB(NOW(), INTERVAL 24 HOUR)'
			)
		);
		$stats['plan_changes'] = $this->Accountlog->find('count',array(
			'conditions'=>'action=\'Plan change\' AND Accountlog.created > DATE_SUB(NOW(), INTERVAL 24 HOUR)'
			)
		);
		$stats['deletions'] = $this->Systemlog->find('count',array(
			'conditions'=>'action=\'Account deleted\' AND Systemlog.created > DATE_SUB(NOW(), INTERVAL 24 HOUR)'
			)
		);
		
		// get total we charged today
		$total_charged = $this->Invoice->find('all',array(
			'fields'=>'SUM(Invoice.amount)',
			'conditions'=>'DATEDIFF(Invoice.transaction_date,Now()) = 0'
			)
		);
		$stats['total_charged'] = $total_charged[0][0]['SUM(`Invoice`.`amount`)'] == '' ? 0 : $total_charged[0][0]['SUM(`Invoice`.`amount`)'];
		
		// income and totals from plans
		$plans = $this->Account->find('all',array(
			'fields'=>'count(Account.id) AS total_accounts, Plan.name, Plan.monthly_cost, (count(Account.id)*Plan.monthly_cost) AS monthly_income',
			'group'=>'Account.plan_id'
			)
		);
		foreach($plans as $plan) {
			$stats['plans'][$plan['Plan']['name']]['total_accounts'] = $plan[0]['total_accounts'];
			$stats['plans'][$plan['Plan']['name']]['monthly_income'] = $plan[0]['monthly_income'];
		}
		
		$stats['new_signups'] = array();
		$new_signups = $this->Account->find('all',array(
			'fields'=>'count(Account.id) AS total_accounts, Plan.name',
			'group'=>'Account.plan_id',
			'conditions'=>'Account.created > DATE_SUB(NOW(), INTERVAL 24 HOUR)'
			)
		);
		foreach($new_signups as $new_signup) {
			$stats['new_signups'][$new_signup['Plan']['name']] = $new_signup[0]['total_accounts'];
		}
		
		// past dues
		$stats['past_due_count'] = count($this->Account->getPastDue());
		
		$this->set('stats',$stats);
		
		// send report
		$this->Wrapper->sendEmail($this->Identity->EmailFrom(),$send_report_to,$report_title,'daily-summary');
		
		
		
		
		/*
		DAILY INVOICE PREP
		prepare next month invoices for all clients
		*/
		
		$accounts = $this->Account->find('all',array(
			'fields'=>'Account.id,Plan.monthly_cost,Account.cc,Plan.name',
			'conditions'=>'Plan.monthly_cost > 0'
			)
		);
		
		// if they don't already have the next invoice, create it
		foreach($accounts as $account) {
			$invoice = $this->Invoice->find('count',array(
				'conditions'=>array('account_id'=>$account['Account']['id'],'dt'=>$next_invoice_date)
				)
			);
			// if no invoice, prep it
			if($invoice == 0)
				$this->Invoice->preLoad(
					$account['Account']['id'],
					$account['Plan']['monthly_cost'],
					$next_invoice_date,
					'Prospector Plan ' . $account['Plan']['name'] . ' monthly subscription' // what appears on invoice
				);
		}		
		
		
		
		/*
		DAILY PAST DUE RE-RUNS
		try to run unprocessed older past due invoices with current billing, which is hopefully updated
		*/
		if(date('j') > 1 && date('j') < 10) { // on from the 2nd to the 9th, @ the 10th we close so this doesn't run; on the 1st we bill
			$past_due_accounts = $this->Account->getPastDue();
			
			foreach($past_due_accounts as $past_due_account) {
				$this->Account->id = $past_due_account['Account']['id'];
				$authorize_profile_id = $this->Account->field('authorize_profile_id');
				$authorize_payment_id = $this->Account->field('authorize_payment_id');
				$cc = $this->Account->field('cc');
				
				$invoice_account = $this->Invoice->find('first',array(
					'fields'=>'Invoice.amount,Invoice.id',
					'conditions'=>array('Invoice.processed'=>0,'Invoice.dt'<$today_invoice_date)
					)
				);
				
				$create_transaction_request = $this->Aim->charge(
					$authorize_profile_id,
					$authorize_payment_id,
					$invoice_account['Invoice']['amount'],
					'Prospector monthly subscription.'
				);
				
				// if it works, update invoice
				if($create_transaction_request['success']) {
					$this->Invoice->markProcessed($invoice_account['Invoice']['id'],$create_transaction_request['transaction_id'],$cc);
				}
				else { // if it fails, mark past due and email them
					$owner_details = $this->User->getOwner($past_due_account['Account']['id']);
					$this->Account->saveField('pastdue',1);
					
					$this->Wrapper->sendEmail($this->Identity->EmailFrom(),$owner_details['User']['email'],'Your Prospector account is past due','past-due-alert');
				}
			}		
		}
		
		
		/*
		MONTHLY CLOSE PAST DUE
		10 days after past due revert to bronze plan
		*/		
		if(date('j') == 10) { // on the 10th, we close them
			$accounts_to_close = $this->Account->getPastDue();
			$downgrade_plan_id = $this->Plan->getFreePlanId();
			
			foreach($accounts_to_close as $account_to_close) {
				// close extra prospects
				$this->Prospect->closeAll($account_to_close['Account']['id']);
				
				// downgrade them and clear the past due flag
				$this->Account->id = $account_to_close['Account']['id'];
				$this->Account->saveField('plan_id',$downgrade_plan_id);
				$this->Account->saveField('pastdue',0);
				$owner_past_due_account = $this->User->getOwner($account_to_close['Account']['id']);
				
				// remove any unprocessed invoices
				$this->Invoice->deleteAll(array('account_id'=>$account_to_close['Account']['id'],'processed'=>0));
				
				// log to account log
				$this->Accountlog->insertLog(
					$account_to_close['Account']['id'],
					'Account past due and expired: downgraded.',
					'Past due downgrade',
					'Daily routine'
					);
				
				// alert them
				$this->Wrapper->sendEmail($this->Identity->EmailFrom(),$owner_past_due_account['User']['email'],'Prospector Account Downgrade','past-due-downgrade');
			}
			
			// alert me to the past dues
			$this->Wrapper->sendEmail($this->Identity->EmailFrom(),'justin@62cents.com','Prospector Past Due Closes Ran','default','Past due closes ran');
		} // do this only after 10 days
		
		
		
		/*
		MONTHLY BILLING PREP
		10 days before the 1st, send billing summary to me
		*/
		$invoices_total_to_charge = $this->Invoice->find('all',array(
			'fields'=>'SUM(Invoice.amount),COUNT(*)',
			'conditions'=>array('dt'=>$next_invoice_date,'Invoice.processed'=>0)
			)
		);
		
		$stats['invoices_total_to_charge'] = $invoices_total_to_charge[0][0]['SUM(`Invoice`.`amount`)'];
		$stats['invoices_total_count'] = $invoices_total_to_charge[0][0]['COUNT(*)'];
		$stats['next_invoice_date'] = $next_invoice_date;
		
		$this->set('stats',$stats);
		
		// send report only on the last days of the month, as a run-up to billing
		if(date('j') > 23)
			$this->Wrapper->sendEmail($this->Identity->EmailFrom(),$send_report_to,'Prospector Pre-Billing Report for ' . $next_invoice_date,'prebilling-summary');
		
		
		
		/*
		MONTHLY PROCESS BILLING
		run invoices, generate receipts, mark and process declines/past dues
		*/
		if(date('j') == 1) { // only bill on the first
			$total_billed_successfully = 0;
			$total_billed_unsuccessfully = 0;
			$this->Invoice->bindModel(array('belongsTo'=>array('Account')));
			$invoices_to_bill = $this->Invoice->find('all',array(
				'fields'=>'Invoice.amount,Invoice.id,Account.id,Account.authorize_profile_id,Account.authorize_payment_id,Account.cc',
				'conditions'=>array('Invoice.processed'=>0,'Invoice.dt'=>$next_invoice_date)
				)
			);
			
			foreach($invoices_to_bill as $invoice_to_bill) {
				$create_transaction_request = $this->Aim->charge(
					$invoice_to_bill['Account']['authorize_profile_id'],
					$invoice_to_bill['Account']['authorize_payment_id'],
					$invoice_to_bill['Invoice']['amount'],
					'Prospector monthly subscription.'
				);
				
				// if it works, update invoice
				if($create_transaction_request['success']) {
					$total_billed_successfully += $invoice_to_bill['Invoice']['amount'];
					$this->Invoice->markProcessed($invoice_to_bill['Invoice']['id'],$create_transaction_request['transaction_id'],$invoice_to_bill['Account']['cc']);
				}
				else { // if it fails, mark past due and email them
					// to do - how do re-use DRY code in the later section here as well?
					$total_billed_unsuccessfully += $invoice_to_bill['Invoice']['amount'];
					$owner_details = $this->User->getOwner($invoice_to_bill['Account']['id']);
					
					$this->Account->id = $invoice_to_bill['Account']['id'];
					$this->Account->saveField('pastdue',1);
					
					$this->Wrapper->sendEmail($this->Identity->EmailFrom(),$owner_details['User']['email'],'Your Prospector account is past due','past-due-alert');
				}
			}
			
			$this->set('billed',$total_billed_successfully);
			$this->set('notbilled',$total_billed_unsuccessfully);
			$this->set('past_due_count',count($this->Account->getPastDue()));
			
			// send me billing summary
			$this->Wrapper->sendEmail($this->Identity->EmailFrom(),'justin@62cents.com','Billing complete summary','billing-summary');
		} // only bill on the first
	
		/*
		FINISH UP!!!
		*/		
		$this->render('daily','blank');
	}
}

?>