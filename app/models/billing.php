<?php
class Billing extends AppModel
{
	var $name = 'Billing';
	var $useTable = false;
	
	var $validate = array(
		'creditcard' => array(
			'rule' => array('cc', 'all', false, null),
			'message' => 'The credit card number you supplied was invalid.'
		),
		'expiration_month' => array(
			'rule' => array('inList', array(1,2,3,4,5,6,7,8,9,10,11,12)),
			'message'=>'Please enter your credit card expidation month. For example: 01'
		),
		'expiration_year' => array(
			'rule' => array('inList', array(2008,2009,2010,2011,2012,2013,2014,2015,2016,2017,2018)),
			'message'=>'Please enter your credit card expidation year in four digits. For example: 2010'
		)
	);
	

}
?>