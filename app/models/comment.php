<?php
class Comment extends AppModel
{
	var $name = "Comment";
	var $belongsTo = array("User","Prospect","Touch");
	
	function formatNote($note) {
		if(strlen($note) == 0)
			return false;
		else
			return 'Note: ' . $note;
		
	}
}
?>