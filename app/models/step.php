<?php
class Step extends AppModel
{
	var $name = 'Step';
	
	var $validate = array(
		'days' => 'numeric'
	);
	
	var $belongsTo = array(
        'Touchtype','Campaign'
    );

}
?>