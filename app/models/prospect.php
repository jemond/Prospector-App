<?php
class Prospect extends AppModel
{
	var $name = 'Prospect';
	var $recursive = 1;

	var $validate = array(
		'source_id' => array(
			'rule' => 'numeric', // or: array('ruleName', 'param1', 'param2' ...)
			'required' => true,
			'allowEmpty' => false,
			'message' => 'You must specify the prospect source. It\'s wicked important for reporting.'
		)
	);
	
	var $belongsTo = array(
		'Campaign',
		'Source'
	);
	
	var $hasMany = array(
        'Comment' => array(
            'className'  => 'Comment',
            'order'      => 'Comment.created DESC'
        ),
		'Touch' => array(
            'className'  => 'Touch'
        )
    );
	
	function getUsageRation($total_prospects, $limit) {
		return ($limit ==0 ) ? 0 : $total_prospects/$limit;
	}
	
	// taken from http://othy.wordpress.com/2006/06/03/unbind-all-associations-except-some/
	function unbindAll($params = array()) {
		foreach($this->__associations as $ass) {
			if(!empty($this->{$ass})) {
				$this->__backAssociation[$ass] = $this->{$ass};
				if(isset($params[$ass]))
				{
					foreach($this->{$ass} as $model => $detail)
					{
						if(!in_array($model,$params[$ass]))
						{
							$this->__backAssociation = array_merge($this->__backAssociation, $this->{$ass});
							unset($this->{$ass}[$model]);
						}
					}
				}
				else
				{
					$this->__backAssociation = array_merge($this->__backAssociation, $this->{$ass});
					$this->{$ass} = array();
				}
			
			}
		}
		return true;
	}
	
	// unlogged closure called for past due accounts
	function closeAll($account_id) {
		return $this->updateAll(array('open'=>0),array('Prospect.account_id'=>$account_id));
	}
	
	// when you drop plans we auto-close prospects that are over the limit; oldest prospects first
	function closeExcess($account_id,$prospect_limit,$user_id=null) {
		// get the total open and determine # to close
		$num_open = $this->openProspectCount($account_id);
		$num_to_close = $num_open - $prospect_limit;
		if($num_to_close > 0) {
			$this->unBindAll();
			$prospects_to_close = $this->find('all',array(
				'fields'=>'Prospect.id,Prospect.open',
				'conditions'=>array('Prospect.account_id'=>$account_id,'Prospect.open'=>1),
				'order'=>'Prospect.created',
				'limit'=>$num_to_close
				)
			);
			
			$prospects_data = array();
			$comments_data = array();
			$counter = 0;
			
			foreach($prospects_to_close as $prospect) {
				$prospects_data[$counter]['open'] = 0;
				$prospects_data[$counter]['id'] = $prospect['Prospect']['id'];
				
				$comments_data[$counter]['note'] = 'Prospector closed this prospect to meet the prospect limit for a plan downgrade.';
				$comments_data[$counter]['prospect_id'] = $prospect['Prospect']['id'];
				$comments_data[$counter]['source'] = 'Comment added';
				$comments_data[$counter]['user_id'] = $user_id;
				
				$counter++;
			}
			
			if
			(
				$this->saveAll($prospects_data,array('fieldList'=>array('open'),'validate'=>false)) 
				&& $this->Comment->saveAll($comments_data,array('validate'=>false))
			)
				return count($prospects_to_close);
			else
				return false;
		}			
		else
			return 0;
	}
	
	function validateProspect($prospect_id,$account_id) {
		$check_account_id = $this->field('account_id','id='.$prospect_id);
		
		if($check_account_id == $account_id)
			return true;
		else
			return false;
	}
	
	function validateTouch($touch_id,$account_id) {
		$prospect_id = $this->Touch->field('prospect_id','id='.$touch_id);
		$check_account_id = $this->field('account_id','id='.$prospect_id);
		
		if($check_account_id == $account_id)
			return true;
		else
			return false;
	}
	
	function getTouchCount($prospect_id) {
		return $this->field('touch_count','id='.$prospect_id);
	}
	
	function appendAccountId($criteria,$account_id) {
		if(!$criteria)
			$criteria = 'Prospect.account_id='.$account_id;
		else
			$criteria['Prospect.account_id']=$account_id;

		return $criteria;
	}
	
	function getExportFields() {
		return 
			'Prospect.id,Prospect.firstname,Prospect.lastname,'
			.'Prospect.address1,Prospect.address2,Prospect.city,Prospect.state,Prospect.postalcode,Prospect.country,Prospect.email,Prospect.phone,'
			.'Source.name,Campaign.name,Prospect.pois,Prospect.educationlevel,Prospect.objective,Prospect.touch_count,Prospect.touchback_count,Prospect.lastcontact,'
			.'Prospect.admitted,Prospect.applied,Prospect.enrolled';
	}
	
	// prepare and flatten data set for csvn file
	function prepareExportData($prospects) {
		// prospects or 1 prospect
		if(!isset($prospects[0])) {
			if(isset($prospects['Source']['name'])) {
				$prospects['Prospect']['source'] = $prospects['Source']['name'];
				unset($prospects['Source']);
			}
			if(isset($prospects['Campaign']['name'])) {
				$prospects['Prospect']['campaign'] = $prospects['Campaign']['name'];
				unset($prospects['Campaign']);
			}
		}
		else {		
			foreach($prospects as $key=>$prospect) {
				if(isset($prospect['Source']['name'])) {
					$prospects[$key]['Prospect']['source'] = $prospect['Source']['name'];
					unset($prospects[$key]['Source']);
				}
				if(isset($prospect['Campaign']['name'])) {
					$prospects[$key]['Prospect']['campaign'] = $prospect['Campaign']['name'];
					unset($prospects[$key]['Campaign']);
				}
			}
		}
		
		return $prospects;
	}
	
	function getSortCriteria($column,$format='db') {
		switch($column) {
			case 'idUp':
				$db = 'Prospect.id';
				$rep = 'idUp';
				break;
				
			case 'idDown':
				$db = 'Prospect.id DESC';
				$rep = 'idDown';
				break;
				
			case 'nameUp':
				$db = 'Prospect.lastname,Prospect.firstname';
				$rep = 'nameUp';
				break;
				
			case 'nameDown':
				$db = 'Prospect.lastname DESC,Prospect.firstname DESC';
				$rep = 'nameDown';
				break;
				
			case 'locationUp':
				$db = 'Prospect.city';
				$rep = 'locationUp';
				break;
				
			case 'locationDown':
				$db = 'Prospect.city DESC';
				$rep = 'locationDown';
				break;
				
			case 'touchesUp':
				$db = 'Prospect.touch_count';
				$rep = 'touchesUp';
				break;
				
			case 'touchesDown':
				$db = 'Prospect.touch_count DESC';
				$rep = 'touchesDown';
				break;
				
			case 'lasttouchUp':
				$db = 'Prospect.lasttouch';
				$rep = 'lasttouchUp';
				break;
				
			case 'lasttouchDown':
				$db = 'Prospect.lasttouch DESC';
				$rep = 'lasttouchDown';
				break;
				
			case 'cwhUp':
				$db = 'Prospect.cwh';
				$rep = 'cwhUp';
				break;
				
			case 'cwhDown':
				$db = 'Prospect.cwh DESC';
				$rep = 'cwhDown';
				break;
				
			case 'createdUp':
				$db = 'Prospect.created';
				$rep = 'createdUp';
				break;
				
			case 'createdDown':
				$db = 'Prospect.created DESC';
				$rep = 'createdDown';
				break;
				
			case 'openUp':
				$db = 'Prospect.open';
				$rep = 'openUp';
				break;
				
			case 'openDown':
				$db = 'Prospect.open DESC';
				$rep = 'openDown';
				break;
				
			default:
				$db = 'Prospect.lastname,Prospect.firstname';
				$rep = 'nameUp';
				break;
		}
		
		if($format == 'db')
			return $db;
		else
			return $rep;
	}
	
	function getProspectsByCampaign($campaign_id) {
		return $this->find('all',array(
			'fields'=>'Prospect.id,Prospect.firstname,Prospect.lastname',
			'conditions'=>array('Prospect.open'=>1,'campaign_id'=>$campaign_id),
			'order'=>'Prospect.lastname,Prospect.firstname'
			)
		);
	}
	
	function openProspectCount($account_id) {
		return $this->find('count',array(
			'conditions'=>array('Prospect.open=1','Prospect.account_id'=>$account_id) 
			)
		);
	}
	
	function totalProspectCount($account_id) {
		return $this->find('count',array(
			'conditions'=>array('Prospect.account_id'=>$account_id) 
			)
		);
	}
	
	function overage($account_id, $prospect_limit) {
		return $this->openProspectCount($account_id) >= $prospect_limit;
	}
	
}
?>