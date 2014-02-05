<?php
foreach($questions as $question) { ?>
<div class="list_question wrapper">
	<?php //var_dump($question); die(count($question['Answer'])); ?>
	<div class="wrapper" style="float: left;">
	<div class="list_answers <?= (count($question['Answer']) < 1) ? 'list_answers_unanswered' : 'list_answers_answered';?>">
		<span class="large_text"><?=count($question['Answer']);?></span>
		<span><?php __n('answer','answers',count($question['Answer']))?></span>
	</div>
	<div class="list_views quiet">
		<span class="large_text"><?=$question['Post']['views'];?></span>
		<span><?php __n('view','views',$question['Post']['views']);?></span>
	</div>
	</div>
	
	
	<div class="wrapper list_detail_text">
		<div class="list_title  wrapper">
		<?=$html->link(
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
			<?=$html->link(
					$question['User']['username'],
					'/users/' . $question['User']['public_key'] . '/' . $question['User']['username']
				);
			?> 
			<span style="font-size: 8pt;">&#8226;</span>
			<h4 style="display: inline;"><?=$question['User']['reputation'];?></h4>
					</div> 
			<span class="quiet"><?=$time->timeAgoInWords($question['Post']['timestamp']);?></span>
				</div>
				<div style="clear: both;"></div>
			</div>
		</div>
		<div class="wrapper tags">
		<?php foreach($question['Tag'] as $tag) { ?>
			<div class="tag wrapper">
				<?=$html->link(
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
    <span class="left"><a href="/search/type:<?=$type;?>/page:1/search:<?=$search;?>"><u>1</u>&nbsp;</a></span>
    <span class="left"><a href="/search/type:<?=$type;?>/page:<?=$current-2;?>/search:<?=$search;?>"><u><?=$current-2;?></u>&nbsp;</a></span>
    <span class="left"><a href="/search/type:<?=$type;?>/page:<?=$current-1;?>/search:<?=$search;?>"><u><?=$current-1;?></u>&nbsp;</a></span>
    <span class="left"><?=$current;?>&nbsp;</span>
    <span class="left"><a href="/search/type:<?=$type;?>/page:<?=$current+1;?>/search:<?=$search;?>"><u><?=$current+1;?></u>&nbsp;</a></span>
    <span class="left"><a href="/search/type:<?=$type;?>/page:<?=$current+2;?>/search:<?=$search;?>"><u><?=$current+2;?></u>&nbsp;</a></span>
    <span class="left"><a href="/search/type:<?=$type;?>/page:<?=$end_page;?>/search:<?=$search;?>"><u><?=$end_page;?></u></a></span>
<?php }elseif($current < $end_page) { ?>
    <span class="left">page <?=$current;?> of <a href="/search/type:<?=$type;?>/page:<?=$end_page;?>/search:<?=$search;?>"><?=$end_page;?></a></span>
<?php }else { ?>
    <span class="left">page <?=$current;?> of <?=$end_page;?></span>
<?php }
if(isset($next)) { ?>
    <span class="right"><a href="/search/type:<?=$type;?>/page:<?=$next;?>/search:<?=$search;?>">&nbsp;&nbsp;Next >></a></span>
<?
}
if(isset($previous)) { ?>
    <span class="right"><a href="/search/type:<?=$type;?>/page:<?=$previous;?>/search:<?=$search;?>"><< Previous&nbsp;&nbsp;</a></span>
<?php } ?>

<?php if ( $type == 'recent'): ?>
    <div class="questionFeed">
        <a href="<?php echo $html->url(array('controller' => 'rss','action' => 'feeds', 'ext' =>'rss'), true);?>">
            recent questions feed
        </a>
     </div>
<?php endif; ?>
