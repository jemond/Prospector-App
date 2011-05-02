<?php echo $javascript->link('nicEdit'); ?>
<script type="text/javascript">
	bkLib.onDomLoaded(function() {
		new nicEditor({iconsPath : '/img/nicEditorIcons.gif',buttonList : ['bold','italic','underline','strikethrough','subscript','superscript','indent','outdent','ol','li','left','center','right','fontSize','fontFamily','fontFormat']}).panelInstance('TouchtypeLettercontent');
		
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