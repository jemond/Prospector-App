<?php
class Invoice extends AppModel
{
	var $name = 'Invoice';
	
	function validateInvoice($invoice_id,$account_id) {
		if(!is_numeric($invoice_id) || !is_numeric($account_id) )
			return false;
			
		$check_account_id = $this->field('account_id',array('processed'=>1,'id'=>$invoice_id));
		
		if( $check_account_id == $account_id )
			return true;
		else
			return false;			
	}
	
	function getNextBillDate() {
		$month = (date('n')==12) ? 1 : date('n')+1;
		$year = ($month == 1) ? date('Y')+1 : date('Y');
		$month = strlen($month) == 1 ? "0$month" : $month;
			
		$time = strtotime("$month/1/$year");
		return date('Y-m-d',$time);		
	}
	
	function getLastInvoice($account_id) {
		return $this->find('first',array(
			'conditions'=>array('account_id'=>$account_id,'processed'=>1), // so pre-billing isn't captured
			'fields'=>'id,dt',
			'order'=>'id DESC' //only the most recent invoice
			)
		);
	}
	
	function getAll($account_id,$processed='charged') {
		if($processed == 'charged')
			$criteria['processed'] = 1;
		
		$criteria['account_id'] = $account_id;
		
		return $this->find('all',array(
			'fields'=>'id,dt,amount,charged,transaction_date,refunded,processed,transaction_id',
			'conditions'=>$criteria,
			'order'=>'dt DESC,id DESC'
			)
		);
	}
	
	function get($invoice_id,$processed='charged') {
		if($processed == 'charged')
			$criteria['processed'] = 1;
		
		$criteria['id'] = $invoice_id;
		
		return $this->find('first',array(
			'fields'=>'id,dt,amount,transaction_date,description,DATE_FORMAT(dt,"%Y-%m-%d") AS dtfilename,refunded,charged,creditcard',
			'conditions'=>$criteria
			)
		);
	}
	
	// used for refunds, getting the most recent transaction ID
	function getLastTransactionId($account_id) {
		$details = $this->find('first',array(
			'fields'=>'transaction_id',
			'conditions'=>array('account_id'=>$account_id,'processed'=>1),
			'order'=>'transaction_date DESC'
			)
		);
		
		return isset($details['Invoice']['transaction_id']) ? $details['Invoice']['transaction_id'] : false;
	}
	
	function add($account_id,$amount,$creditcard=false,$transaction_id=false,$type='charge',$description=null) {
		$invoice['Invoice']['amount'] = $amount;
		$invoice['Invoice']['transaction_date'] = date('c');
		$invoice['Invoice']['dt'] = date('c');		
		$invoice['Invoice']['processed'] = 1;
		$invoice['Invoice']['account_id'] = $account_id;
		$invoice['Invoice']['creditcard'] = $creditcard;
		$invoice['Invoice']['transaction_id'] = $transaction_id;
		$invoice['Invoice']['description'] = $description;
		
		if($type=='charge')
			$invoice['Invoice']['charged'] = 1;
		else
			$invoice['Invoice']['refunded'] = 1;
			
		return $this->save($invoice);
	}
	
	// called by billing daily routine
	function preLoad($account_id,$amount,$dt,$description) {
		$this->create(); // loop fixey poos
		$invoice['Invoice']['account_id'] = $account_id;
		$invoice['Invoice']['amount'] = $amount;
		$invoice['Invoice']['dt'] = $dt;
		$invoice['Invoice']['description'] = $description;
		$invoice['Invoice']['processed'] = 0;
		
		return $this->save($invoice);
	}
	
	function markProcessed($invoice_id,$transaction_id,$cc) {
		$this->create();
		
		$invoice['Invoice']['processed'] = 1;
		$invoice['Invoice']['charged'] = 1;
		$invoice['Invoice']['transaction_id'] = $transaction_id;
		$invoice['Invoice']['id'] = $invoice_id;
		$invoice['Invoice']['creditcard'] = $cc;
		$invoice['Invoice']['transaction_date'] = date('c');
		
		return $this->save($invoice);
	}
	
	// clears unprocsssed invoices
	function clear($account_id) {
		return $this->deleteAll(array('account_id'=>$account_id,'processed'=>0));
	}
}
?>