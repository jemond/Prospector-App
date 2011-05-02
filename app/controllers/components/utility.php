<?php
class UtilityComponent extends Object {

	var $controller;
	var $components = 'Email';
	
	function getMySqlNow() {
		return date('Y-m-d H:i:s');
	}
}

?>