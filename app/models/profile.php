<?php
class Profile extends AppModel
{
	var $name = 'Profile';
	
	var $hasOne = array('User');
	
	function setup($user_id) {
		$data['Profile']['user_id'] = $user_id;
		return $this->save($data);
	}
}	
?>