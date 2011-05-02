<?php echo $javascript->link('tiny_mce/tiny_mce.js'); ?>
<script type="text/javascript">
	tinyMCE.init({
		mode : "textareas",
		theme : "advanced",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : false,
		theme_advanced_resizing : true,
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,bullist,numlist,outdent,indent,|,justifyleft,justifycenter,justifyright,justifyfull,|,code",
		theme_advanced_buttons2 : false,
		theme_advanced_buttons3 : false,
		theme_advanced_buttons4 : false,
		content_css : "/css/editor.css",
	});
	
	Event.observe(window, 'load', function() {
		if($('TouchtypeLetter').checked)
			$('Editor').slideDown();
		else
			$('Editor').hide();
	});
		
	function toggleEditor() {
		if($('TouchtypeLetter').checked)
			$('Editor').slideDown();
		else
			$('Editor').slideUp();
	}
</script>