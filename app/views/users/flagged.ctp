<?=$this->element('admin_header',
	array("selected" => "Flagged Posts")); 
?>
<?
foreach($questions as $question) { ?>
<div class="list_question wrapper">

	<div class="wrapper" style="float: left;">
	<div class="list_answers <?= (count($question['Answer']) < 1) ? 'red' : 'green';?>">
		<span class="large_text"><?=count($question['Answer']);?></span>
		<span>answer<?=(count($question['Answer']) == 1) ? '' : 's';?></span>
	</div>
	<div class="list_views quiet">
		<span class="large_text"><?=$question['Post']['views'];?></span>
		<span>view<?=($question['Post']['views'] == 1) ? '' : 's';?></span>
	</div>
	</div>


	<div class="wrapper" style="float: left; width: 550px;">
		<div class="list_title  wrapper">
        <? if($question['Post']['related_id'] != 0) {
		        echo $html->link(
				    'View this answer (answers have no titles)',
				    '/questions/' . $question['Post']['public_key'] . '/' . $question['Post']['url_title']
			    );
            }else {
                echo $html->link(
                    $question['Post']['title'],
                    '/questions/' . $question['Post']['public_key'] . '/' . $question['Post']['url_title']
                );
            }
        ?>
		</div>
		<div class="wrapper">
			<div id="list_user_info" style="float: right;">
			<span class="quiet"><?=$time->timeAgoInWords($question['Post']['timestamp']);?></span>
			<?=$html->link(
					$question['User']['username'],
					'/users/' . $question['User']['public_key'] . '/' . $question['User']['username']
				);
			?>
			</div>
		</div>
		<div class="wrapper tags">
		<? foreach($question['Tag'] as $tag) { ?>
			<div class="tag wrapper">
				<?=$html->link(
						$tag['tag'],
						'/tags/' . $tag['tag']
					);
				?>
			</div>
            <?  } ?>
		</div>
        <div class="wrapper" style="float: right;">
            <a href="/admin/delete/<?=$question['Post']['public_key'];?>">Delete Post</a> |
            <a href="/admin/restore/<?=$question['Post']['public_key'];?>">Restore Post</a>
        </div>
	</div>
</div>
<? } ?>