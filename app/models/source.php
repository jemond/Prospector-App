<?php
class Source extends AppModel
{
	var $name = 'Source';
	
	var $validate = array(
		'name'
	);
	
	function validateSource($source_id,$account_id) {
		$check_account_id = $this->field('account_id','id='.$source_id);
		
		return $check_account_id == $account_id ? true : false;
	}
	
	function incrementStat($source_id, $stat = 'admitted', $direction='up') {
		$this->id = $source_id;
		
		if($direction == 'up')
			$count = $this->field($stat)+1;
		else
			$count = $this->field($stat)-1;
			
		return $this->saveField($stat,$count);		
	}
	
	function getSources($account_id,$source_id = false) {
		$criteria['account_id'] = $account_id;
		
		if($source_id)
			$criteria['or'] = array('disabled=0','id='.$source_id);
		else
			$criteria['disabled'] = 0;
			
		$sources = $this->find('list',array('conditions'=>$criteria));
		
		return $sources;
	}
	
	function getStats($account_id) {
	
		$stats = $this->find('all',array(
			'fields'=>'id,name,prospect_count,disabled,applied,admitted,enrolled',
			'conditions'=>array('account_id'=>$account_id),
			'order'=>'disabled,name'
			)
		);
		
		foreach($stats as $key=>$stat) {
			if($stat['Source']['prospect_count'] == 0)
				$stats[$key]['Source']['applicants_to_total'] = 0;
			else
				$stats[$key]['Source']['applicants_to_total'] = $stat['Source']['applied']/$stat['Source']['prospect_count'];
				
			if($stat['Source']['applied'] == 0)
				$stats[$key]['Source']['admits_to_applicants'] = 0;
			else
				$stats[$key]['Source']['admits_to_applicants'] = $stat['Source']['admitted']/$stat['Source']['applied'];
				
			if($stat['Source']['admitted'] == 0)
				$stats[$key]['Source']['enrollees_to_admits'] = 0;
			else
				$stats[$key]['Source']['enrollees_to_admits'] = $stat['Source']['enrolled']/$stat['Source']['admitted'];
		}
		
		return $stats;
	
	}
	
	// returns overall conversion rate, quality and yield
	function getOverallStats($account_id) {
		$stats = $this->getStats($account_id);
		
		$overall_conversion = 0;
		$overall_quality = 0;
		$overall_yield = 0;
		
		$sources_count = count($stats);
		
		foreach($stats as $stat) {
			$overall_conversion += $stat['Source']['applicants_to_total'];
			$overall_quality += $stat['Source']['admits_to_applicants'];
			$overall_yield += $stat['Source']['enrollees_to_admits'];
		}
		
		$overall_stats = array();
		if($sources_count == 0) {
			$overall_stats['conversion'] = 0;
			$overall_stats['quality'] = 0;
			$overall_stats['yield'] = 0;
		}
		else {
			$overall_stats['conversion'] = $overall_conversion/$sources_count;
			$overall_stats['quality'] = $overall_quality/$sources_count;
			$overall_stats['yield'] = $overall_yield/$sources_count;
		}
		
		return $overall_stats;
	}
	
	function setProspectCounts($old_source_id,$new_source_id) {
		if($old_source_id == $new_source_id)
			return true;
		else {
			if($old_source_id) {
				$old_count = $this->field('prospect_count','id='.$old_source_id);
				$old_count--;
				
				$this->id=$old_source_id;
				$this->saveField('prospect_count',$old_count);
			}
			
			if($new_source_id) {			
				$new_count = $this->field('prospect_count','id='.$new_source_id);
				$new_count++;
				
				$this->id=$new_source_id;
				$this->saveField('prospect_count',$new_count);
			}
			
			return true;
		}
	}
}
?>