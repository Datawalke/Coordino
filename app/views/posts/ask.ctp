<?
	echo $html->css('wmd.css');
	echo $html->script('wmd/showdown.js');
	echo $html->script('wmd/wmd.js');
	
	echo $html->script('jquery/jquery.js');
	echo $html->script('jquery/jquery.bgiframe.min.js');
	echo $html->script('jquery/jquery.ajaxQueue.js');
	echo $html->script('jquery/thickbox-compressed.js');
	echo $html->script('jquery/jquery.autocomplete.js');
	echo $html->script('/tags/suggest');
	
	echo $html->css('thickbox.css');
	echo $html->css('jquery.autocomplete.css');
?>


  <script>
  $(document).ready(function(){
	$("#resultsContainer").show("blind");
	
	$("#tag_input").autocomplete(tags, {
		minChars: 0,
		multiple: true,
		width: 350,
		matchContains: true,
		autoFill: false,
		formatItem: function(row, i, max) {
			return row.name + " (<strong>" + row.count + "</strong>)";
		},
		formatMatch: function(row, i, max) {
			return row.name + " " + row.count;
		},
		formatResult: function(row) {
			return row.name;
		}
	});
	
	$("#PostTitle").blur(function(){
		if($("#PostTitle").val().length >= 10) {
			$("#title_status").toggle();
			getResults();
		} else {
			$("#title_status").show();
		}
	});

	function getResults()
	{
	
		$.get("/mini_search",{query: $("#PostTitle").val(), type: "results"}, function(data){
		
			$("#resultsContainer").html(data);
			$("#resultsContainer").show("blind");
		});
	}	
	
	$("#PostTitle").keyup(function(event){
		if($("#PostTitle").val().length < 10) {
			$("#title_status").html('<span class="red">Titles must be at least 10 characters long.</span>');
		} else {
			$("#title_status").html('What is your question about?');
		}
	});
	
  });
  </script>
<h2>Ask a Question</h2>
<? if ($session->read('errors')) {
		foreach($session->read('errors.errors') as $error) {
			echo '<div class="error">' . $error . '</div>';
		}
	}
?>
<?=$form->create('Post', array('action' => 'ask'));?>
<?=$form->label('title');?><br/>

<?=$form->text('title', array('class' => 'wmd-panel big_input', 'value' => $session->read('errors.data.Post.title')));?><br/>
<span id="title_status"class="quiet">What is your question about?</span>
<div id="resultsContainer"></div>

<div id="wmd-button-bar" class="wmd-panel"></div>
<?=$form->textarea('content', array(
	'id' => 'wmd-input', 'class' => 'wmd-panel', 'value' => $session->read('errors.data.Post.content')
	));
 ?>

<div id="wmd-preview" class="wmd-panel"></div>

<?=$form->label('tags');?><br/>
<?=$form->text('tags', array('id' => 'tag_input', 'class' => 'wmd-panel big_input'));?><br/>
<span id="tag_status" class="quiet">Combine multiple words into single-words.</span>

<? if(!$session->check('Auth.User.id')) { ?>
<h2>Who Are You?</h2>
<span class="quiet">Have an account already? <a href="#">Login before answering!</a></span><br/>
	<?=$form->label('name');?><br/>
	<?=$form->text('User.username', array(
		'class' => 'big_input medium_input', 
		'value' => $session->read('errors.data.User.username')
		));
	?><br/>
	<?=$form->label('email');?><br/>
	<?=$form->text('User.email', array(
		'class' => 'big_input medium_input',
		'value' => $session->read('errors.data.User.email')
		));
	?><br/>		
<? } ?>
<br/><br/>
<?=$form->checkbox('Post.notify', array('checked' => true));?>
<span style="margin-left: 5px;">Notify me when my question is answered.</span>

<?$recaptcha->display_form('echo');?>

<?=$form->end('Ask Your Question');?>

