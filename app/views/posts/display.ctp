<?php
foreach($questions as $question) { ?>
<div class="list_question wrapper">
	<?php //var_dump($question); die(count($question['Answer'])); ?>
	<div class="wrapper" style="float: left;">
	<div class="list_answers <?php echo (count($question['Answer']) < 1) ? 'list_answers_unanswered' : 'list_answers_answered';?>">
		<span class="large_text"><?php echo count($question['Answer']);?></span>
		<span><?php __n('answer','answers',count($question['Answer']))?></span>
	</div>
	<div class="list_views quiet">
		<span class="large_text"><?php echo $question['Post']['views'];?></span>
		<span><?php __n('view','views',$question['Post']['views']);?></span>
	</div>
	</div>
	
	
	<div class="wrapper list_detail_text">
		<div class="list_title  wrapper">
		<?php echo $html->link(
				$question['Post']['title'],
				'/questions/' . $question['Post']['public_key'] . '/' . $question['Post']['url_title']
			);
		?>
		</div>
		<div class="wrapper">
			<div style="float: right;">
				<div class="thumb_with_border">
		
				<?php echo $html->link( $thumbnail->get(array(
						        'save_path' => WWW_ROOT . 'img/thumbs',
						        'display_path' => $this->webroot.  'img/thumbs',
						        'error_image_path' => $this->webroot. 'img/answerAvatar.png',
						        'src' => WWW_ROOT .  $question['User']['image'],
						        'w' => 25,
								'h' => 25,
								'q' => 100,
		                        'alt' => $question['User']['username'] . 'picture' )
			),'/users/' .$question['User']['public_key'].'/'.$question['User']['username'], array('escape' => false));?>
				</div>
				<div style="float: left; line-height: .9;">
					<div>
			<?php echo $html->link(
					$question['User']['username'],
					'/users/' . $question['User']['public_key'] . '/' . $question['User']['username']
				);
			?> 
			<span style="font-size: 8pt;">&#8226;</span>
			<h4 style="display: inline;"><?php echo $question['User']['reputation'];?></h4>
					</div> 
			<span class="quiet"><?php echo $time->timeAgoInWords($question['Post']['timestamp']);?></span>
				</div>
				<div style="clear: both;"></div>
			</div>
		</div>
		<div class="wrapper tags">
		<?php foreach($question['Tag'] as $tag) { ?>
			<div class="tag wrapper">
				<?php echo $html->link(
						$tag['tag'],
						'/tags/' . $tag['tag']
					);
				?>
			</div>
		<?php  } ?>
		</div>
	</div>
	
</div>
<?php }
    if((($end_page - $current) > 3) && $current > 3) { ?>
    <span class="left"><a href="/search/type:<?php echo $type;?>/page:1/search:<?php echo $search;?>"><u>1</u>&nbsp;</a></span>
    <span class="left"><a href="/search/type:<?php echo $type;?>/page:<?php echo $current-2;?>/search:<?php echo $search;?>"><u><?php echo $current-2;?></u>&nbsp;</a></span>
    <span class="left"><a href="/search/type:<?php echo $type;?>/page:<?php echo $current-1;?>/search:<?php echo $search;?>"><u><?php echo $current-1;?></u>&nbsp;</a></span>
    <span class="left"><?php echo $current;?>&nbsp;</span>
    <span class="left"><a href="/search/type:<?php echo $type;?>/page:<?php echo $current+1;?>/search:<?php echo $search;?>"><u><?php echo $current+1;?></u>&nbsp;</a></span>
    <span class="left"><a href="/search/type:<?php echo $type;?>/page:<?php echo $current+2;?>/search:<?php echo $search;?>"><u><?php echo $current+2;?></u>&nbsp;</a></span>
    <span class="left"><a href="/search/type:<?php echo $type;?>/page:<?php echo $end_page;?>/search:<?php echo $search;?>"><u><?php echo $end_page;?></u></a></span>
<?php }elseif($current < $end_page) { ?>
    <span class="left">page <?php echo $current;?> of <a href="/search/type:<?php echo $type;?>/page:<?php echo $end_page;?>/search:<?php echo $search;?>"><?php echo $end_page;?></a></span>
<?php }else { ?>
    <span class="left">page <?php echo $current;?> of <?php echo $end_page;?></span>
<?php }
if(isset($next)) { ?>
    <span class="right"><a href="/search/type:<?php echo $type;?>/page:<?php echo $next;?>/search:<?php echo $search;?>">&nbsp;&nbsp;Next >></a></span>
<?
}
if(isset($previous)) { ?>
    <span class="right"><a href="/search/type:<?php echo $type;?>/page:<?php echo $previous;?>/search:<?php echo $search;?>"><< Previous&nbsp;&nbsp;</a></span>
<?php } ?>