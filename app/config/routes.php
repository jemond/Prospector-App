<?php
/* SVN FILE: $Id: routes.php 7567 2008-09-07 14:36:06Z nate $ */
/**
 * Short description for file.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package			cake
 * @subpackage		cake.app.config
 * @since			CakePHP(tm) v 0.2.9
 * @version			$Revision: 7567 $
 * @modifiedby		$LastChangedBy: nate $
 * @lastmodified	$Date: 2008-09-07 09:36:06 -0500 (Sun, 07 Sep 2008) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/views/pages/home.ctp)...
 */
	Router::connect ('/', array('controller'=>'publics', 'action'=>'index', 'index'));
	
	Router::connect ('/features', array('controller'=>'publics', 'action'=>'features', 'features'));
	Router::connect ('/privacy', array('controller'=>'publics', 'action'=>'privacy', 'privacy'));
	Router::connect ('/pricing', array('controller'=>'publics', 'action'=>'pricing', 'pricing'));
	Router::connect ('/signup/*', array('controller'=>'users', 'action'=>'signup', 'signup'));
	Router::connect ('/users/signup/*', array('controller'=>'users', 'action'=>'signup', 'signup'));
	Router::connect ('/support', array('controller'=>'publics', 'action'=>'support', 'support'));
	Router::connect ('/about', array('controller'=>'publics', 'action'=>'about', 'about'));
	Router::connect ('/help', array('controller'=>'publics', 'action'=>'help', 'help'));
	Router::connect ('/faq', array('controller'=>'publics', 'action'=>'faq', 'faq'));
	
	Router::connect ('/dashboard', array('controller'=>'dashboards', 'action'=>'index', 'index'));
	Router::connect ('/stats', array('controller'=>'dashboards', 'action'=>'stats', 'stats'));
	Router::connect ('/help', array('controller'=>'helps', 'action'=>'index', 'index'));
	
	Router::connect ('/login', array('controller'=>'users', 'action'=>'login', 'login'));
?>