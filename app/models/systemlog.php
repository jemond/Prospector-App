<?php
class Systemlog extends AppModel
{
	var $name = 'Systemlog';
	
	function insertLog($account_owner_email,$log,$action,$ip) {
		$systemlog['Systemlog']['account_owner_email'] = $account_owner_email;
		$systemlog['Systemlog']['log'] = $log;
		$systemlog['Systemlog']['action'] = $action;
		$systemlog['Systemlog']['ip'] = $ip;
		
		return $this->save($systemlog) ? true : false;
	}
}
?>