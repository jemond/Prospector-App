<?php
class Account extends AppModel
{
	var $name = 'Account';
	
	function getCountByPlan($plan_id) {
		return $this->find('count',array(
			'conditions'=>array('Account.plan_id'=>$plan_id)
			)
		);
	}
	
	function getDetails($account_id) {
		$this->bindModel(array('belongsTo'=>array('Plan'=>array(
			'fields'=>'Plan.prospect_limit,Plan.name,Plan.monthly_cost'		
			)
		)),false);
		
		$this->id=$account_id;
		return $this->read();		
	}
	
	// used by the daily service
	function getPastDue() {
		return $this->find('all',array(
			'fields'=>'Account.id',
			'conditions'=>array('Account.pastdue'=>1)
			)
		);
	}

}
?>