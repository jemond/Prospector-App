<?php
class Accountlog extends AppModel
{
	var $name = 'Accountlog';
	
	function get($account_id) {
		return $this->find('all',array(
			'fields'=>'Accountlog.message,Accountlog.user,Accountlog.created,Accountlog.action',
			'conditions'=>array('Accountlog.account_id'=>$account_id),
			'order'=>'created DESC'
			)
		);
	}
	
	function insertLog($account_id,$message,$action,$user) {
		$this->create();
		$accountlog['Accountlog']['message'] = $message;
		$accountlog['Accountlog']['account_id'] = $account_id;
		$accountlog['Accountlog']['action'] =$action;
		$accountlog['Accountlog']['user'] = $user;
		
		return $this->save($accountlog) ? true : false;
	}
	
	// return dump of all messages for display in system log
	function getRaw($account_id) {
		$raw = '';
		$tab = chr(9);
		$nl = chr(10);
		
		$logs = $this->find('all',array(
			'fields'=>'Accountlog.message,Accountlog.action,Accountlog.user,Accountlog.created',
			'conditions'=>array('account_id'=>$account_id),
			'order'=>'created DESC'
			)
		);
			
		foreach($logs as $log) {
			
			$raw .= $log['Accountlog']['created'] . $tab . $log['Accountlog']['user'] . $tab . $log['Accountlog']['action'] . $tab . $log['Accountlog']['message'] . $nl;
		}
		
		$raw = 'Account ID: ' . $account_id . $tab . $raw;
		
		return $raw;
	}
}
?>