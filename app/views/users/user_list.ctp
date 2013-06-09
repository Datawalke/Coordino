<?php
echo $javascript->link('jquery/jquery.js');
echo $javascript->link('jquery/jquery.bgiframe.min.js');
echo $javascript->link('jquery/jquery.ajaxQueue.js');
echo $javascript->link('jquery/thickbox-compressed.js');
?>
<script>
$(document).ready(function(){
	$("#results").show("blind");
	
	function getResults()
	{
	
		$.get("/mini_user_search",{query: $("#UserUsername").val(), type: "results"}, function(data){
		
			$("#results").html(data);
			$("#results").show("blind");
		});
	}	
	
	$("#UserUsername").keyup(function(event){
		getResults();
	});
	
	getResults();

});
</script>
<?=$form->create('User', array('action' => '?'));?>
<?=$form->label('username');?><br/>
 <?=$form->text('username', array('class' => 'big_input', 'autocomplete' => 'off', 'value' => $session->read('errors.data.Post.username')));?><br/>
<span id="title_status"class="quiet">Who are you looking for?</span>
<div id="results" style="overflow: auto;"></div>