<?php
class Campaign extends AppModel
{
	var $name = 'Campaign';
		
	function afterFind($results) {
		foreach($results as $key=>$result) {
			if(isset($result['Campaign']['name']) && trim($result['Campaign']['name']) == '')
				$results[$key]['Campaign']['name'] = '{no name}';
		}	
	
		return $results;
	}
	
	function validateCampaign($campaign_id,$account_id) {
		$check_account_id = $this->field('account_id','id='.$campaign_id);
		
		if( $check_account_id == $account_id )
			return true;
		else
			false;			
	}
	
	function close($campaign_id,$user_id) {
		$this->id=$campaign_id;
		$this->saveField('open',0);
		$this->saveField('closed',date('Y-m-d H:i:s'));
		$this->saveField('closed_by',$user_id);
		return true;
	}
	
	// remove blank steps, set any blanks day values to 5
	function cleanSteps($data) {
		foreach($data['Step'] as $key=>$step) {				
			if(!isset($step['touchtype_id']) || !is_numeric($step['touchtype_id']))
				unset($data['Step'][$key]);
			else if(isset($step['days']) && !is_numeric($step['days']))
				$data['Step'][$key]['days'] = 5;
		}
		
		if(sizeof($data["Step"]) == 0)
			unset($data["Step"]);
		
		return $data;	
	}
	
	function getCurrentStepDetails($campaign_id,$fields='Touchtype.name,Step.id,Touchtype.id') {
		
		$stepsLeft = $this->Step->find('count',array(
			'conditions'=>array('campaign_id'=>$campaign_id,'complete'=>0)
			)
		);
		
		if($stepsLeft > 0)
			$stepdetails = $this->Step->find('first',array(
				'conditions'=>array('campaign_id'=>$campaign_id,'complete'=>0),
				'order'=>'position',
				'fields'=>$fields,
				'recursive'=>2
				)
			);
		else
			$stepdetails = $this->Step->find('first',array(
				'conditions'=>array('campaign_id'=>$campaign_id),
				'order'=>'position DESC',
				'fields'=>$fields,
				'recursive'=>2
				)
			);
			
		return $stepdetails;
	}
	
	function getStepsCompleted($campaign_id) {
		return $this->Step->find('count',array(
			'conditions'=>array('Step.campaign_id'=>$campaign_id,'complete'=>1)
			)
		);
	}
	
	function getNextStepDueDate($campaign_id) {
		$campaignover = $this->Step->find('count',array(
			'conditions'=>array('Step.campaign_id'=>$campaign_id,'complete'=>0,)
			)
		);
		
		if($campaignover > 0) {
			$details = $this->Step->find('first',array(
				'conditions'=>array('Step.campaign_id'=>$campaign_id,'complete'=>1,),
				'order'=>'Step.position DESC',
				'fields'=>'DATE_ADD(Step.applied, INTERVAL Step.days DAY) as next_due_date'
				)
			);
			
			$dt = $details[0]['next_due_date'];
		}
		else
			$dt = null;
		
		return $dt;
	}
	
	function getCampaigns($account_id) { 
		return $this->find('list',array(
			'fields'=>'name',
			'order'=>'name',
			'conditions'=>array('open'=>1,'account_id'=>$account_id)
			)
		);
	}

	function getOpenCampaignCount($account_id) {
		return $this->find('count',array(
			'conditions'=>array('Campaign.open=1','account_id'=>$account_id)
			)
		);
	}
	
	function setProspectCounts($old_campaign_id,$new_campaign_id) {
		if($old_campaign_id == $new_campaign_id)
			return true;
		else {
			if($old_campaign_id) {
				$old_count = $this->field('prospect_count','id='.$old_campaign_id);
				$old_count--;
				
				$this->id=$old_campaign_id;
				$this->saveField('prospect_count',$old_count);
			}
			
			if($new_campaign_id) {			
				$new_count = $this->field('prospect_count','id='.$new_campaign_id);
				$new_count++;
				
				$this->id=$new_campaign_id;
				$this->saveField('prospect_count',$new_count);
			}
			
			return true;
		}
	}

}
?>