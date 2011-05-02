<?php
class User extends AppModel
{
	var $name = 'User';
	
	var $belongsTo = array(
		'Account' => array(
			'className'    => 'Account',
			'foreignKey'   => 'account_id'
		)
	);
	
	var $hasOne = array('Profile');
	
	var $validate = array(
		'email' => array(
			'email1' => array(
				'rule' => 'isUnique',
				'message'=>'That email is already in use.'
			),
			'email2' => array(
				'rule' => 'notEmpty',
				'message'=>'You must provide an email.'
			)
		)
	);
	
	function getOwner($account_id) {
		return $this->find('first',array(
			'fields'=>'User.name,User.email',
			'conditions'=>array('User.owner'=>1,'User.account_id'=>$account_id)
			)
		);
	}
	
	// for new account sign-ups
	function getDefaultSources($account_id) {
		$sources[0]['name'] = 'Online form';
		$sources[0]['account_id'] = $account_id;
		$sources[1]['name'] = 'Recruiting fair';
		$sources[1]['account_id'] = $account_id;
		$sources[2]['name'] = 'Open house';
		$sources[2]['account_id'] = $account_id;	
	
		return $sources;
	}
	
	function getDefaultTouchTypes($account_id) {
		$touchtypes[0]['name'] = 'Welcome letter';
		$touchtypes[0]['lettercontent'] = '<p>This is your letter for #FirstName# #LastName#.</p>';
		$touchtypes[0]['letter'] = 1;
		$touchtypes[0]['labels'] = 1;
		$touchtypes[0]['account_id'] = $account_id;
		
		$touchtypes[1]['name'] = 'Email';
		$touchtypes[1]['export'] = 1;
		$touchtypes[1]['account_id'] = $account_id;
		
		$touchtypes[2]['name'] = 'Brochure';
		$touchtypes[2]['labels'] = 1;
		$touchtypes[2]['account_id'] = $account_id;
	
		return $touchtypes;
	}
	
	function getDefaultProspects($id, $account_id, $source_id, $campaign_id) {
		$prospects[0]['account_id'] = $account_id;
		$prospects[0]['source_id'] = $source_id;
		$prospects[0]['campaign_id'] = $campaign_id;
		$prospects[0]['firstname'] = 'Tommy';
		$prospects[0]['lastname'] = 'Trojan';
		
		$prospects[1]['account_id'] = $account_id;
		$prospects[1]['source_id'] = $source_id;
		$prospects[1]['campaign_id'] = $campaign_id;
		$prospects[1]['firstname'] = 'Tammy';
		$prospects[1]['lastname'] = 'Trojan';
		
		return $prospects[$id];
	}
	
	function getDefaultCampaigns($account_id) {
		$campaigns[0]['account_id'] = $account_id;
		$campaigns[0]['name'] = 'Fall Applicants';
		
		return $campaigns;
	}
	
	function getDefaultSteps($campaign_id,$touchtype_id) {
		$steps[0]['campaign_id'] = $campaign_id;
		$steps[0]['touchtype_id'] = $touchtype_id;
		$steps[0]['position'] = 0;
		$steps[0]['days'] = 5;
		
		$steps[1]['campaign_id'] = $campaign_id;
		$steps[1]['touchtype_id'] = ($touchtype_id)-1; // to do - make this cleaner - saveAll doesn't return individual keys
		$steps[1]['position'] = 1;
		$steps[1]['days'] = 8;
		
		$steps[2]['campaign_id'] = $campaign_id;
		$steps[2]['touchtype_id'] = ($touchtype_id)-2; // to do - make this cleaner - saveAll doesn't return individual keys
		$steps[2]['position'] = 2;
		$steps[2]['days'] = 3;
		
		return $steps;
	}
	
	function getCriteria($filter) {
		$criteria=array(); // what we return for use in find()s
		$q = ''; // our non specific search terms
		$handlers = array('firstname','lastname','city','state','country','zip','touches','lasttouch','open','created','campaign','cwh','source');
		$id_handlers = array('source','campaign'); // if these are passed in with numeric search criteria, we search their id fields
		$datetime_to_date_fields = array('created');
		
		// parse raw text into an array we can use 
		$filter = trim(str_replace(': ',':',$filter));

		// for "my search" we replace space with _
		$inside_quote = false;
		for($i=0;$i<strlen($filter);$i++) {
			if(substr($filter,$i,1) == '"')
				$inside_quote = $inside_quote ? false : true;

			if($inside_quote && substr($filter,$i,1) == ' ')
				$filter = substr($filter,0,$i) . '_' . substr($filter,$i+1,strlen($filter));
		}
		
		$terms = explode(' ',$filter);
		$fIncludeOpen = true; // by default, only show open
		
		foreach($terms as $term) {
			if(!strpos($term,':'))
				$q .= $term.' ';
			else {
				$term = explode(':',$term);
				$field = $term[0];
				$searchq = $term[1];
					
				if(in_array($field,$handlers)) {
					// translate friendly to actual field names
					if($field == 'touches')
						$field = 'touch_count';
					else if($field == 'zip')
						$field = 'postalcode';						
					
					$firstchar = substr($searchq,0,1);
					$operator = 'LIKE';
					
					// handle straight number searches differently than strings
					if(is_numeric($searchq) && $field == 'touch_count') {
						$operator = '=';
					}
					
					// catch campaign by id searchs
					if(in_array($field,$id_handlers)) {
						if(is_numeric($searchq)) {
							$field = $field.'_id';
							$operator = '=';
						} else {
							$field .= '.name';
							
						}
					}
					
					// translate CWH
					if($field == 'cwh') {
						if($searchq == 'cold')
							$searchq = 0;
						else if($searchq == 'warm')
							$searchq = 1;
						else
							$searchq = 2;
							
						$operator = '=';
					}
					
					// catch bit field like open
					if($field == 'open') {
						$operator = '=';
						$fIncludeOpen = false; // turn off forced open:y ciretria in lieu of what the user specified
						
						if(strpos($searchq,'n')>-1)
							$searchq = 0;
						else
							$searchq = 1;						
					}
					
					// look for GTE and LTE
					if($firstchar == '+') {
						$operator = '>=';
						$searchq = substr($searchq,1,strlen($searchq));
					}
					else if($firstchar == '-') {
						$operator = '<=';
						$searchq = substr($searchq,1,strlen($searchq));
					}
					
					// catch date words like never, today, yesterday, last week
					if($field == 'lasttouch' && !strtotime($searchq)) {
						if($searchq == 'never') {
							$operator = 'IS';
							$searchq = 	'NULL';
						}
						else if($searchq == 'lastweek') {
							$operator = '>';
							$searchq = 	date('Y-m-d',time()-7*24*60*60);
						}
						else if($searchq == 'today') {
							$operator = '=';
							$searchq = 	date('Y-m-d',time());
						}
						else if($searchq == 'lastmonth') {
							$operator = '>';
							$searchq = 	date('Y-m-d',time()-30*24*60*60);
						}
					}
					
					if(strtotime($searchq)) {
						$searchq = "'".date('Y-m-d',strtotime($searchq))."'"; // mysql format
						
						if($operator == 'LIKE')
							$operator = '=';
					}
					
					// catch words in quotes for non wild cards
					if(substr($searchq,0,1) == '"' && substr($searchq,strlen($searchq)-1,1) == '"') {
						$operator = '=';
						$searchq = str_replace('_',' ',$searchq);
					} 
					
					// if text searches, add the wildcards
					if($operator == 'LIKE')
						$searchq = "'%$searchq%'";

					// fix datetime fields to date
					if(in_array($field,$datetime_to_date_fields))
						$criteria[] = "date(Prospect.$field) $operator $searchq";
					else
						if(strpos($field,'.'))
							$criteria[] = "$field $operator $searchq";
						else
							$criteria[] = "Prospect.$field $operator $searchq";
				}
				
			}
		}
		
		$q = trim($q); // remove extra spaces
		
		// general search on the rest of the terms
		if(strlen($q) > 0) {
			
			$criteria['or']=array(
				'Prospect.firstname LIKE'=>"%$q%",
				'Prospect.lastname LIKE'=>"%$q%",
				'CONCAT(Prospect.firstname,\' \',Prospect.lastname) LIKE'=>"%$q%",
				'Prospect.city LIKE'=>"%$q%",
				'Prospect.state LIKE'=>"%$q%",
				'Prospect.postalcode LIKE'=>"%$q%",
				);
		}
		
		if($fIncludeOpen)
			$criteria[] = 'Prospect.open = 1';
		
		if(empty($criteria))
			$criteria = false;

		return $criteria;
	}
	
	function beforeSave() {
		if(isset($this->data['User']['filter']) && $this->data['User']['filter'] == '')
			$this->data['User']['filter'] = false;

		return $this->data;
	}
	
	function getUsers($account_id,$type='open') {
		if($type == 'open')
			$conditions['disabled'] = 0;
		else if ($type == 'opennonadmins') {
			$conditions['disabled'] = 0;
			$conditions['admin'] = 0;
		}
		else if ($type == 'admins'){
			$conditions['disabled'] = 0;
			$conditions['admin'] = 1;
		}		
		else
			$conditions['disabled'] = 1;
		
		$conditions['account_id'] = $account_id;
		
		return $this->find('all',array(
			'conditions'=>$conditions,
			'order'=>'owner DESC, admin DESC, email'
			)
		);
		
	}
	
	function validateLogin($data) 
	{
		$this->bindModel(array('belongsTo'=>array('Account'=>array(
			'fields'=>'Account.name,Account.plan_id'		
			)
		)),false);
		$this->Account->bindModel(array('belongsTo'=>array('Plan'=>array(
			'fields'=>'Plan.prospect_limit,Plan.name'		
			)
		)),false);
		$user = $this->find('first',array(
			'conditions'=>array('email' => $data['email'], 'password' => md5($data['password']), 'disabled' => 0),
			'fields'=> 'User.id,User.email,User.name,User.account_id,User.sort,User.filter,Account.name,Account.id,Account.plan_id,User.admin,User.owner,'
			.'Profile.show_welcome,Profile.id'
			)
		);
		
		if(empty($user) == false) 
			return $user; 
		return false; 
	}
	
	function validateUsers($user_id,$account_id) {
		$check_account_id = $this->field('account_id','id='.$user_id);
		$check_owner = $this->field('owner','id='.$user_id);
		
		// owner's can't be edited, unless it's the owner, and then this model doens't matter
		return ($account_id == $check_account_id && $check_owner != 1) ? true : false;
	}
	
	function validateAdminAction($admin,$my_user_id,$action_user_id=false,$role='admins') {
		if($admin == 1)
			return true;
		else if($role == 'self' && $my_user_id == $action_user_id)
			return true;
		else
			return false;
	}
	
	function validateOwner($account_id,$user_id) {
		$fOwner = $this->find('count',array(
			'conditions'=>array('User.account_id'=>$account_id,'User.id'=>$user_id,'User.owner'=>1)
			)
		);
		
		return ($fOwner == 1) ? true : false;
	}
	
	function validateAdmin($account_id,$admin,$user_id=false) {		
		$access = false;
		
		if($admin == 1 && !$user_id)
			$access = true;
		else if($admin == 1 && $this->validateUsers($user_id,$account_id))
			$access = true;
		
		return $access;			
	}
	
	function generateLoginHash() {
		$hash = "";
		for($i=0;$i<35;$i++) {
			if( (rand(0,100)%2) == 0 )
				$hash .= chr(rand(97,122));
			else
				$hash .= chr(rand(48,57));
		}
		
		return $hash;
	}
	
	function checkPassword($password) {
		$good = true;
		
		if(strlen($password) < 5)
			$good = false;
			
		return $good;
	}

	function setHash($user_id,$hash,$valid = 360) {
		$time = time()+$valid;
		$this->id = $user_id;
		$this->saveField('hash',$hash);
		$this->saveField('hash_valid',date('Y-m-d H:i:s',$time)); //1 hour
	
		return true;
	}
	
	function resetUser($user_id,$password) {
		$this->id=$user_id;
		$this->saveField('password',md5($password));
		$this->saveField('hash',null);
		$this->saveField('hash_valid',null);	
		
		return true;
	}
	
	function lookupUserByHash($hash) {
		$user = $this->find('first',array(
			'conditions'=>array('hash'=>$hash,'hash_valid >'=>date('Y-m-d H:i:s'))
			)
		);
		
		if( !isset($user['User']['id']) || !is_numeric($user['User']['id']) )
			return false;
		else
			return $user;
	}
	
	function isUniqueEmail($email) {
		$count = $this->find('count',array(
			'conditions'=>array('email'=>$email)
			)
		);
		
		if($count == 0)
			return true;
		else
			return false;
	}
	
	function isAccountOwner($email,$account_id) {
		$account_id_check= $this->field('account_id',array('email'=>$email));

		if($account_id_check == $account_id)
			return true;
		else
			return false;
	}
	
}
?>