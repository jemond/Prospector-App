<?php
/* SVN FILE: $Id: default.ctp 7359 2008-07-24 13:34:46Z TommyO $ */
/**
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
 * @subpackage		cake.cake.libs.view.templates.layouts.email.html
 * @since			CakePHP(tm) v 0.10.0.1076
 * @version			$Revision: 7359 $
 * @modifiedby		$LastChangedBy: TommyO $
 * @lastmodified	$Date: 2008-07-24 08:34:46 -0500 (Thu, 24 Jul 2008) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">

<html>
<head>
	<title><?php echo $title_for_layout;?></title>
</head>

<body>
	<?php echo $content_for_layout;?>

	<p>This email was sent using the <a href="http://cakephp.org">CakePHP Framework</a></p>
</body>
</html>
