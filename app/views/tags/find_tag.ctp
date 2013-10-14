<?php
foreach($questions as $question) { ?>
<div class="list_question wrapper">

	<div class="wrapper" style="float: left;">
	<div class="list_answers <?php echo (count($question['Answer']) < 1) ? 'red' : 'green'; ?>">
		<span class="large_text"><?=count($question['Answer']); ?></span>
		<span>answer<?=(count($question['Answer']) == 1) ? '' : 's'; ?></span>
	</div>
	<div class="list_views quiet">
		<span class="large_text"><?php echo $question['Post']['views']; ?></span>
		<span>view<?=($question['Post']['views'] == 1) ? '' : 's'; ?></span>
	</div>
	</div>


	<div class="wrapper" style="float: left; width: 550px;">
		<div class="list_title  wrapper">
		<?php echo $html->link(
				$question['Post']['title'],
				'/questions/' . $question['Post']['public_key'] . '/' . $question['Post']['url_title']
			);
		?>
		</div>
		<div class="wrapper">
			<div id="list_user_info" style="float: right;">
			<span class="quiet"><?php echo $time->timeAgoInWords($question['Post']['timestamp']); ?></span>
			<?php echo $html->link(
					$question['User']['username'],
					'/users/' . $question['User']['public_key'] . '/' . $question['User']['username']
				);
			?>
			</div>
		</div>
		<?php foreach($question['Tag'] as $tag) { ?>
			<div class="tag">
				<?php echo $html->link(
						$tag['tag'],
						'/tags/' . $tag['tag']
					);
				?>
			</div>
		<?php  } ?>
	</div>

</div>
 <!--   The below pagination could be better done by making tag_search something like $controller_search (inconvenient variable used for
    illustrative purposes).  If desired, it could also be done with the use of $this-params (arrow excluded due to comments) and then set to
    a variable once again called in place of tag_search.  Changing these urls accordingly would allow deletion of this file and use of
    display.ctp in place of both files.
 -->


<?php }
    if((($end_page - $current) > 3) && $current > 3) { ?>
    <span style="float: left;"><a href="/tag_search/tag:<?php echo $tag_name; ?>/page:1"><u>1</u>&nbsp;</a></span>
    <span style="float: left;"><a href="/tag_search/tag:<?php echo $tag_name; ?>/page:<?php echo $current-2; ?>"><u><?php echo $current-2; ?></u>&nbsp;</a></span>
    <span style="float: left;"><a href="/tag_search/tag:<?php echo $tag_name; ?>/page:<?php echo $current-1; ?>"><u><?php echo $current-1; ?></u>&nbsp;</a></span>
    <span style="float: left;"><?php echo $current; ?>&nbsp;</span>
    <span style="float: left;"><a href="/tag_search/tag:<?php echo $tag_name; ?>/page:<?php echo $current+1; ?>"><u><?php echo $current+1; ?></u>&nbsp;</a></span>
    <span style="float: left;"><a href="/tag_search/tag:<?php echo $tag_name; ?>/page:<?php echo $current+2; ?>"><u><?php echo $current+2; ?></u>&nbsp;</a></span>
    <span style="float: left;"><a href="/tag_search/tag:<?php echo $tag_name; ?>/page:<?php echo $end_page; ?>"><u><?php echo $end_page; ?></u></a></span>
<?php }elseif($current < $end_page) { ?>
    <span style="float: left;">page <?php echo $current; ?> of <a href="/tag_search/tag:<?php echo $tag_name; ?>/page:<?php echo $end_page; ?>"><?php echo $end_page; ?></a></span>
<?php }else { ?>
    <span style="float: left;">page <?php echo $current; ?> of <?php echo $end_page; ?></span>
<?php }
if(isset($next)) { ?>
    <span style="float: right;"><a href="/tag_search/tag:<?php echo $tag_name; ?>/page:<?php echo $next; ?>">&nbsp;&nbsp;Next >></a></span>
<?php
}
if(isset($previous)) { ?>
    <span style="float: right;"><a href="/tag_search/tag:<?php echo $tag_name; ?>/page:<?php echo $previous; ?>"><< Previous&nbsp;&nbsp;</a></span>
<?php } ?>