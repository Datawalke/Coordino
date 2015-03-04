<?php
	echo $html->css('wmd.css');
	echo $javascript->link('wmd/showdown.js');
	echo $javascript->link('wmd/wmd.js');
	
	echo $javascript->link('jquery/jquery.js');
	echo $javascript->link('jquery/jquery.bgiframe.min.js');
	echo $javascript->link('jquery/jquery.ajaxQueue.js');
	echo $javascript->link('jquery/thickbox-compressed.js');
	echo $javascript->link('jquery/jquery.autocomplete.js');
	echo $javascript->link('/tags/suggest');
	
	echo $html->css('thickbox.css');
	echo $html->css('jquery.autocomplete.css');
?>
<form action="?" method="post" >
<div class="detailed_inputs">
	<div>
		<h3>Title <span class="small">The large text that appears on the top of a widget.</span></h3>
		<input type="text" name="data[Widget][title]" value="<?php echo $widget['Widget']['title'];?>"/>
	</div>
	<div>
		<h3>Content <span class="small">What would you like to say?.</span></h3>
		<div id="wmd-button-bar" class="wmd-panel"></div>
		<textarea name="data[Widget][content]" id="wmd-input" class="wmd-panel"><?php echo $widget['Widget']['content'];?></textarea><br/>
		<input type="checkbox" name="data[Widget][global]" style="width:15px;" <?php if($widget['Widget']['global'] == 1) { echo 'checked' ;}?>/> Show this Widget on all pages
	</div>
	<div id="wmd-preview" class="wmd-panel"></div>
	<div class="submit">
		<input type="submit" value="Update This Widget"/>
	</div>
</div>
<input type="hidden" name="data[referer]" value="<?php echo $referer;?>"/>
</form>