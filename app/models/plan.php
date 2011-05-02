<?php
class Plan extends AppModel
{
	var $name = 'Plan';
	
	function getCost($plan_id) {
		$this->id = $plan_id;
		return $this->field('monthly_cost');
	}
	
	function getList() {
		return $this->find('list',array(
			'conditions'=>array('show_signup'=>1)
			)
		);
	}
	
	function getFreePlanId() {
		$details = $this->find('first',array(
			'fields'=>'Plan.id',
			'conditions'=>array('monthly_cost'=>0,'show_signup'=>1)
			)
		);
		return $details['Plan']['id'];
	}
	
	// maps public name of plan to the actual ID; returns the free plan as failure
	function getIdFromPublicName($name) {
		$plan_id_mapping['bronze'] = 1;
		$plan_id_mapping['silver'] = 2;
		$plan_id_mapping['gold'] = 3;

		return isset($plan_id_mapping[$name]) ? $plan_id_mapping[$name] : 1;
	}
	
	function getProRate($plan_id) {
		$this->id = $plan_id;
		$monthly = $this->field('monthly_cost');
		$currentday = date('j');
		$daysinmonth = date('t');
		
		// if today is the first, it's 100%
		if($currentday == 1)
			$prorate = $monthly;
		else
			$prorate = ($daysinmonth-$currentday)/$daysinmonth*$monthly;
		
		return round($prorate,2);	
	
	}
	
	// confirm it's a public plan
	function validatePlan($plan_id) {
		$valid = false;
		$valid_plans = $this->find('all',array(
			'fields'=>'Plan.id',
			'conditions'=>array('Plan.show_signup',1)
			)
		);
		
		foreach($valid_plans as $plan) {
			if($plan['Plan']['id'] == $plan_id) {
				$valid = true;
				break;
			}
				
		}
		
		return $valid;
	}
	
	function get($plan_id,$field='name') {
		$this->id = $plan_id;
		return $this->field($field);
	}
	
	function isFree($plan_id) {
		$this->id = $plan_id;
		return $this->field('monthly_cost') == 0 ? true : false;
	}
}
?>