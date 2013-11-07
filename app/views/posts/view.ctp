<?php
	echo $html->css('wmd.css');
	echo $javascript->link('wmd/showdown.js');
	echo $javascript->link('wmd/wmd.js');
	echo $javascript->link('jquery/jquery.js');
?>
<script> //<![CDATA[    
  // When the page is ready
  $(document).ready(function(){
    $(".comment_area").hide();
    
    $(".comment_actions a").click(function(event){
      $(this).parents("div").prev(".comment_area").toggle();
	  $(this).toggle();
      
      // Stop the link click from doing its normal thing
      event.preventDefault();
    });

  });
//]]></script>


<div id="question" class="question">
	<div class="content_container wrapper">
		<div class="content_actions" style="float: left; width: 55px; margin-right: 10px;">
			<?php
                echo $html->image('arrow_up.png', array('alt' => 'Vote Up', 'url' => '/vote/' . $question['Post']['public_key'] . '/up'));
			?>
			<span class="large_text quiet" style="display: block; padding: 0px; margin: 0px;"><strong><?php echo $question['Post']['votes'];?></strong></span>
			<?php
                echo $html->image('arrow_down.png', array('alt' => 'Vote Down', 'url' => '/vote/' . $question['Post']['public_key'] . '/down'));
	        ?>

		</div>
		<div class="question_content" style="float: left; width: 600px;">
			<h2><?php echo $question['Post']['title'];?></h2>
			<?php echo $question['Post']['content'];?>
		</div>
	</div>

	<div class="post_actions wrapper">

		<div style="width: 100px; float: left;">
        <?php if($question['Post']['user_id'] != $session->read('Auth.User.id')) { ?>
        <?php echo $html->link(
				__('flag',true),
				'/flag/' . $question['Post']['public_key']
			 );
        ?>
        <?php } 
        if($question['Post']['user_id'] == $session->read('Auth.User.id') || isset($rep_rights) || $admin) { ?>
		| 
		<?php echo $html->link(
				__('edit',true),
				'/questions/' . $question['Post']['public_key'] . '/' . $question['Post']['url_title'] . '/edit');
		}
		?>

        <?php if($admin): ?>
               | <?php echo $html->link(
                       __('del',true),
               '/posts/delete/'.$question['Post']['id']); ?></a>
        <?php endif; ?>

		</div>

		<?php if(!empty($question['Post']['last_edited_timestamp'])) { ?>
			<div style="width: 275px; float: left; text-align: center;">
				edited <strong><?php echo $time->timeAgoInWords($question['Post']['last_edited_timestamp']);?></strong>
			</div>
		<?php } ?>

		<div class="user_info wrapper">
			<div style="float: left;">
				<div class="thumb_with_border">
				<?php echo $html->link( $thumbnail->get(array(
						        'save_path' => WWW_ROOT . 'img/thumbs',
						        'display_path' => $this->webroot.  'img/thumbs',
						        'error_image_path' => $this->webroot. 'img/answerAvatar.png',
						        'src' => WWW_ROOT .  $question['User']['image'],
						        'w' => 25,
								'h' => 25,
								'q' => 100,
		                        'alt' => $question['User']['username'] . ' picture' )
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
			<span class="quiet">asked <?php echo $time->timeAgoInWords($question['Post']['timestamp']);?></span>
				</div>
				<div style="clear: both;"></div>
			</div>
		</div>

		<div id="tags" style="clear: left;">
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

	<?php if(!empty($question['Comment'])) { ?>
		<div id="question_comments">
			<?php foreach($question['Comment'] as $comment) { ?>
			<div class="comment">
				<?php echo $comment['content']?> &ndash;

				<?php echo $html->link(
						$comment['User']['username'],
						'/users/' . $comment['User']['public_key'] . '/' . $comment['User']['username']
					);
				?>
				<span class="quiet"><?php echo $time->timeAgoInWords($comment['timestamp']); ?></span>
			</div>
			<?php } ?>
		</div>

	<?php } ?>

	<div id="comment_<?php echo $question['Post']['public_key'];?>" class="comment_area">
		<?php echo $form->create(null, array(
				'url' => '/questions/' . $question['Post']['public_key'] . '/comment')
			);
		?>
		<?php echo $form->text('Comment.content', array('class' => 'comment_input'));?>
		<?php echo $form->end('Comment');?>
	</div>
	<div class="comment_actions">
	<?php echo $html->link(
			__('add comment',true),
			'#');
	?>
	</div>

</div>

<div id="answers">
	<h2><?php __n('answer','answers',count($answers));?></h2>
	<hr/>
	<?php foreach($answers as $answer) { ?>
	<div class="<?php echo ($answer['Answer']['status'] == 'correct') ? 'answered' : 'answer';?>" id="a_<?php echo $answer['Answer']['public_key']?>">

		<div class="content_container wrapper">
			<div class="content_actions" style="float: left; width: 55px; margin-right: 10px;">
				<?php echo $html->image('arrow_up.png', array(
                                        'alt' => 'Vote Up',
                                        'url' => '/vote/' . $answer['Answer']['public_key'] . '/up'
                                    )); ?>
				<span class="large_text quiet" style="display: block; padding: 0px; margin: 0px;"><strong><?php echo $answer['Answer']['votes'];?></strong></span>
				<?php echo $html->image('arrow_down.png', array(
                                        'alt' => 'Vote Down',
                                        'url' => '/vote/' . $answer['Answer']['public_key'] . '/down'
                                    )); ?>
                                
				<?php if($question['Post']['user_id'] == $session->read('Auth.User.id') && $answer['Answer']['status'] != 'correct' && $question['Post']['status'] != 'closed') {?>
				<div class="checkmark">
					<?php echo $html->link(
							'',
							'/questions/' .  $answer['Answer']['public_key'] . '/' . 'correct'
						);
					?>
				</div>
				<?php } if($answer['Answer']['status'] == 'correct') {
					echo $html->image('checkmark_green.png');
				} ?>
				
			</div>
			<div class="answer_content" style="float: left; width: 600px;">
				<?php echo $answer['Answer']['content'];?>
			</div>
		</div>

		<div class="post_actions wrapper">
			<div class="user_info wrapper">
				<div style="float: left;">
				<div class="thumb_with_border">
				<?php echo $html->link( $thumbnail->get(array(
						        'save_path' => WWW_ROOT . 'img/thumbs',
						        'display_path' => $this->webroot.  'img/thumbs',
						        'error_image_path' => $this->webroot. 'img/answerAvatar.png',
						        'src' => WWW_ROOT .  $answer['User']['image'],
						        'w' => 25,
								'h' => 25,
								'q' => 100,
		                        'alt' => $answer['User']['username'] . 'picture' )
			),'/users/' .$answer['User']['public_key'].'/'.$answer['User']['username'], array('escape' => false));?>
				</div>
				<div style="float: left; line-height: .9;">
					<div>
			<?php echo $html->link(
					$answer['User']['username'],
					'/users/' . $answer['User']['public_key'] . '/' . $answer['User']['username']
				);
			?> 
			<span style="font-size: 8pt;">&#8226;</span>
			<h4 style="display: inline;"><?php echo $answer['User']['reputation'];?></h4>
					</div> 
			<span class="quiet">answered <?php echo $time->timeAgoInWords($answer['Answer']['timestamp']);?></span>
				</div>
				<div style="clear: both;"></div>
			</div>
			</div>
	
			<?php echo $html->link(
					'flag',
					'/flag/' . $answer['Answer']['public_key']
				);
			?>
			<span class="quiet">|</span> 
			<?php echo $html->link(
					'link',
					'/questions/'
					. $question['Post']['public_key'] . '/' 
					. $question['Post']['url_title'] 
					. '#a_' . $answer['Answer']['public_key']
				);
			?>
			<?php if($answer['Answer']['user_id'] == $session->read('Auth.User.id') || isset($rep_rights)) { ?>
			<span class="quiet">|</span>
			<?php echo $html->link(
					__('edit',true),
					'/answers/' . $answer['Answer']['public_key'] . '/edit');
			}
			?>
	
		</div>

		<?php if(!empty($answer['Comment'])) { ?>
			<div id="comments">
				<?php foreach($answer['Comment'] as $comment) { ?>
				<div class="comment">
					<?php echo $comment['content']?> &ndash; 
				
					<?php echo $html->link(
							$comment['User']['username'],
							array('controller' => 'users', 'action' => 'view', $comment['User']['public_key'], $comment['User']['username'])
						);
					?>
					<span class="quiet"><?php echo $time->timeAgoInWords($comment['timestamp']); ?></span>
				</div>
				<?php } ?>
			</div>
			
		<?php } ?>
	
		<div id="comment_<?php echo $answer['Answer']['public_key'];?>" class="comment_area">
			<?php echo $form->create(null, array(
					'url' => '/questions/' . $answer['Answer']['public_key'] . '/comment')
				); 
			?>
			<?php echo $form->text('Comment.content', array('class' => 'comment_input'));?> 
			<?php echo $form->end('Comment');?>
		</div>
		<div class="comment_actions">
		<?php echo $html->link(
				'add comment',
				'#');
		?>
		</div>	
	
	</div>
	<?php } ?>
</div>

<div id="user_answer">
	<?php if ($session->read('errors')) {
			foreach($session->read('errors.errors') as $error) {
				echo '<div class="error">' . $error . '</div>';
			}
		}
	?>
	<h3><?php __('your answer'); ?></h3>
	<?php echo $form->create(null, array(
			'url' => '/questions/' . $question['Post']['public_key'] . '/' . $question['Post']['url_title'] . '/answer')
		); ?>
	<div id="wmd-button-bar" class="wmd-panel"></div>
	<?php echo $form->textarea('content', array(
		'id' => 'wmd-input', 'class' => 'wmd-panel', 'value' => $session->read('errors.data.Post.content')
		));
	 ?>

	<div id="wmd-preview" class="wmd-panel"></div>

	<?php if(!$session->check('Auth.User.id')) { ?>
	<h2>Who Are You?</h2>
	<span class="quiet">Have an account already? <a href="/login">Login before answering!</a></span><br/>
		<?php echo $form->label('name');?><br/>
		<?php echo $form->text('User.username', array('class' => 'big_input medium_input '));?><br/>
		<?php echo $form->label('email');?><br/>
		<?php echo $form->text('User.email', array('class' => 'big_input medium_input '));?><br/>		
	<?php } ?>
	
	<?php $recaptcha->display_form('echo');?>
	
	<br/>
	<?php echo $form->end(__d('verb','Answer',true));?>
</div>
