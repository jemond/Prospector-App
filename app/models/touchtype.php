<?php
class Touchtype extends AppModel
{
	var $name = 'Touchtype';
	
	var $hasMany = array('Touch');
	
	function validateTouchtype($touchtype_id,$account_id) {
		$check_account_id = $this->field('account_id','id='.$touchtype_id);
		
		if( $check_account_id == $account_id )
			return true;
		else
			false;			
	}
	
	function getList($account_id) {
		return $this->find('list',array('conditions'=>array('disabled'=>0,'account_id'=>$account_id)));
	}

}
?>