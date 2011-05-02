<?php
pr($comment["Comment"]["touch_id"]);
if(isset($comment["Comment"]["touch_id"]) && !empty($comment["Comment"]["touch_id"]))
{
	echo $html->link('Touch export', "/prospects/touchexport/{$comment["Comment"]["touch_id"]}");
}
?>