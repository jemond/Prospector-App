<?php
/* SVN FILE: $Id: multi_cache.ctp 7591 2008-09-10 16:48:43Z AD7six $ */
/**
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *			1785 E. Sahara Avenue, Suite 490-204
 *			Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package			cake
 * @subpackage		cake.cake.libs.view.templates.layouts
 * @since			CakePHP(tm) v 0.10.0.1076
 * @version			$Revision: 7591 $
 * @modifiedby		$LastChangedBy: AD7six $
 * @lastmodified	$Date: 2008-09-10 11:48:43 -0500 (Wed, 10 Sep 2008) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
?>
<p>This is regular text</p>
<cake:nocache>
	<p>A. Layout Before Content</p>
	<?php $this->log('1. layout before content') ?>
</cake:nocache>
<cake:nocache><?php echo $this->element('nocache/plain'); ?></cake:nocache>
<cake:nocache>
	<p>C. Layout After Test Element But Before Content</p>
	<?php $this->log('3. layout after test element but before content') ?>
</cake:nocache>
<?php echo $content_for_layout; ?>
<cake:nocache>
	<p>E. Layout After Content</p>
	<?php $this->log('5. layout after content') ?>
</cake:nocache>
<p>Additional regular text.</p>
<?php //echo $this->element('nocache/contains_nocache'); stub?>
<cake:nocache>
	<p>G. Layout After Content And After Element With No Cache Tags</p>
	<?php $this->log('7. layout after content and after element with no cache tags') ?>
</cake:nocache>
