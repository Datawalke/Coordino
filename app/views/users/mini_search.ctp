<?php foreach($users as $user) { 
	$display = 0;
	if($user['User']['reputation'] >= 1000) {
		$display = round(($user['User']['reputation'] / 1000), 2) . 'k';
	} else {
		$display = $user['User']['reputation'];
	}
?>
	<div style="float: left; width: 200px;">
		<a style="float: left; margin-right: 10px;" href="/users/<?php echo $user['User']['public_key']; ?>/<?php echo $user['User']['username']; ?>" title="<?php echo $user['User']['username']; ?>">
		<?php echo $thumbnail->show(array(
						        'save_path' => $_SERVER['DOCUMENT_ROOT'] . '/app/webroot/img/thumbs',
						        'display_path' => '/img/thumbs',
						        'error_image_path' => $this->webroot. 'img/answerAvatar.png',
						        'src' => '/app/webroot' . $user['User']['image'],
						        'w' => 25,
								'h' => 25,
								'q' => 100)
			);
		?>
		</a>
		<a href="/users/<?php echo $user['User']['public_key']; ?>/<?php echo $user['User']['username']; ?>" title="<?php echo $user['User']['username']; ?>" style="float: left;"><?php echo $user['User']['username']; ?></a>
		<strong style="float: left;">&nbsp;<?php echo $display; ?></strong>
		<div style="clear: both;"></div>
	</div>
<?php } ?>