<?php
/* SVN FILE: $Id: account_fixture.php 7393 2008-07-31 15:38:23Z gwoo $ */
/**
 * Short description for file.
 *
 * Long description for file
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) Tests <https://trac.cakephp.org/wiki/Developement/TestSuite>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 *  Licensed under The Open Group Test Suite License
 *  Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Cake Software Foundation, Inc.
 * @link				https://trac.cakephp.org/wiki/Developement/TestSuite CakePHP(tm) Tests
 * @package			cake.tests
 * @subpackage		cake.tests.fixtures
 * @since			CakePHP(tm) v 1.2.0.4667
 * @version			$Revision: 7393 $
 * @modifiedby		$LastChangedBy: gwoo $
 * @lastmodified	$Date: 2008-07-31 10:38:23 -0500 (Thu, 31 Jul 2008) $
 * @license			http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */
/**
 * Short description for class.
 *
 * @package		cake.tests
 * @subpackage	cake.tests.fixtures
 */
class AccountFixture extends CakeTestFixture {
/**
 * name property
 * 
 * @var string 'Aco'
 * @access public
 */
	var $name = 'Account';
	var $table = 'Accounts';
/**
 * fields property
 * 
 * @var array
 * @access public
 */
	var $fields = array(
		'iAccountId'		=> array('type' => 'integer', 'key' => 'primary'),
		'cDescription'	=> array('type' => 'string', 'length' => 10, 'null' => true)
	);
/**
 * records property
 * 
 * @var array
 * @access public
 */
	var $records = array(
		array('cDescription' => 'gwoo'),
		array('cDescription' => 'phpnut'),
		array('cDescription' => 'schreck'),
		array('cDescription' => 'dude')
	);
}

?>