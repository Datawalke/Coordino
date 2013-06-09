<?php if(!empty($questions)) { ?>
<h3>Related Questions</h3>
<?php foreach($questions as $question) { ?>
	<div class="list_question wrapper">

		<div class="wrapper" style="float: left;">
		<div class="list_answers_mini <?= (count($question['Answer']) < 1) ? 'red' : 'green';?>">
			<span class="large_text"><?=count($question['Answer']);?></span>
		</div>
		</div>


		<div class="wrapper" style="float: left; width: 400px;">
			<div class="list_title_mini  wrapper">
			<?=$html->link(
					$question['Post']['title'],
					'/questions/' . $question['Post']['public_key'] . '/' . $question['Post']['url_title']
				);
			?>
			</div>
		</div>
	</div>
<?php
	}
}
?>