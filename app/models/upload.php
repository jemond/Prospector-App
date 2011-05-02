<?php
class Upload extends AppModel
{
	var $name = 'Upload';
	
	var $useTable = false;
	
	var $validate = array(
		'source_id' => array(
			'rule' => 'numeric', // or: array('ruleName', 'param1', 'param2' ...)
			'required' => true,
			'allowEmpty' => false,
			'message' => 'You must specify the prospect source. It\'s wicked important for reporting.'
		),
		'data' => array(
            'rule' => array('isUploadedFile'),
            'message' => 'You must upload a file.'
        )
	);
	
	function isUploadedFile($val){
		$val = $val['data'];
		if ((isset($val['error']) && $val['error'] == 0) || (!empty($val['tmp_name']) && $val['tmp_name'] != 'none'))
			return is_uploaded_file($val['tmp_name']);
		else
			return false;
	}
	
	function matchColumn($column) {
		$Matched = false;
		$column = trim($column);
		
		$columns['firstname'] = array('first name','firstname','first','first_name');
		$columns['lastname'] = array('last name','lastname','last','last_name');
		$columns['address1'] = array('address 1','address1','address');
		$columns['address2'] = array('address 2','address2','second address');
		$columns['city'] = array('city','town');
		$columns['state'] = array('state','region','provence');
		$columns['postalcode'] = array('zip','postal code','postalcode','postal_code','zip code','zipcode','zip_code');
		$columns['country'] = array('country','nation');
		$columns['email'] = array('email','email address');
		$columns['phone'] = array('phone','phone number','phone_number','phonenumber');		
		$columns['education level'] = array('education level','degree','last degree','highest degree');
		$columns['objective'] = array('objective');
		$columns['pois'] = array('pois','programs of interest','programs','interested programs');
		
		foreach($columns as $match=>$options) {
			if(in_array($column,$options)) {
				$Matched = $match;
				break;	
			}
		}
		
		return $Matched;
	}
	
	function getColumnList() {
		$columns['skip'] = 'Do not import';
		$columns['firstname'] = 'First name';
		$columns['lastname'] = 'Last name';
		$columns['address1'] = 'Address 1';
		$columns['address2'] = 'Address 2';
		$columns['city'] = 'City';
		$columns['state'] = 'State';
		$columns['postalcode'] = 'Postal code';
		$columns['country'] = 'Country';
		$columns['email'] = 'Email';
		$columns['phone'] = 'Phone';
		$columns['education level'] = 'Education level';
		$columns['objective'] = 'Objective';
		$columns['pois'] = 'Programs of interest';
		
		return $columns;
	}

}
?>