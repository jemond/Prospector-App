<?php
/* SVN FILE: $Id: binary_test_fixture.php 7588 2008-09-09 18:51:28Z phpnut $ */
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
 * @since			CakePHP(tm) v 1.2.0.6700
 * @version			$Revision: 7588 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2008-09-09 13:51:28 -0500 (Tue, 09 Sep 2008) $
 * @license			http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */
/**
 * Short description for class.
 *
 * @package		cake.tests
 * @subpackage	cake.tests.fixtures
 */
class BinaryTestFixture extends CakeTestFixture {
/**
 * name property
 * 
 * @var string 'BinaryTest'
 * @access public
 */
	var $name = 'BinaryTest';
/**
 * fields property
 * 
 * @var array
 * @access public
 */
	var $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'data' => 'binary'
	);
/**
 * records property
 * 
 * @var array
 * @access public
 */
	var $records = array();
}

?>
