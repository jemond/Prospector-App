<?php
class Touch extends AppModel
{
	var $name = 'Touch';
	
	function getLatestTouchId($prospect_id) {
		
		$tid = $this->field('Touch.id','Touch.prospect_id='.$prospect_id,'Touch.id DESC');
		
		if($tid == 0 || !is_numeric($tid))
			return false;
		else
			return $tid;
	}
	
	function getTouchbackCount($prospect_id) {
		$touchbacks = $this->find('first',array(
			'fields'=>'SUM(touchback) as touchbacks',
			'conditions'=>array('prospect_id'=>$prospect_id)
			)
		);
		
		if(isset($touchbacks[0]['touchbacks']))
			return $touchbacks[0]['touchbacks'];
		else
			return 0;
	}
}
?>