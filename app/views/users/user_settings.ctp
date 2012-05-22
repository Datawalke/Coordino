<div>
		<h3><?php __('Profile Image'); ?></h3>
		<?=$thumbnail->show(array(
						        'save_path' => WWW_ROOT . 'img/thumbs',
						        'display_path' => $this->webroot.  'img/thumbs',
						        'error_image_path' => $this->webroot. 'img/answerAvatar.png',
						        'src' => WWW_ROOT .  $user_info['User']['image'],
						        'w' => 75,
								'h' => 75,
								'q' => 100,
		                        'alt' => $user_info['User']['username'] . 'picture' )
			);
		?>
	<?=$trickyFileInput->draw('picker', array(
								'form' => array(
									'id' => 'User' . $user_info['User']['public_key'] . 'ImageChangeForm',
									'name' => 'User' . $user_info['User']['public_key'] . 'ImageChangeForm',
									'action' => $this->webroot.'users/' . $user_info['User']['public_key'] . '/upload'),
								'input' => array(
									'name' => 'data[Upload][file]',
									'submitOnChange' => true),
								'image' => $this->webroot.'img/buttons/choose_image.gif'));
	?>
	</div>
<form action="?" method="post" >
<div class="detailed_inputs">
	<div>
		<h3>Email Address <span class="small">An email address we can contact you at.</span></h3>
		<input type="text" name="data[User][email]" value="<?=$user_info['User']['email'];?>"/>
	</div>
	<div>
		<h3>Age <span class="small">An optional age to show.</span></h3>
		<input type="text" maxlength="3" name="data[User][age]" value="<?=$user_info['User']['age'];?>"/>
	</div>
	<div>
		<h3>Location <span class="small">Where are you located?</span></h3>
		<input type="text" name="data[User][location]" value="<?=$user_info['User']['location'];?>"/>
	</div>
	<div>
		<h3>Website <span class="small">Have a website or social profile?</span></h3>
		<input type="text" name="data[User][website]" value="<?=$user_info['User']['website'];?>"/>
	</div>
	<div>
		<h3>Summary <span class="small">Tell us a little about yourself.</span></h3>
		<textarea name="data[User][info]"><?=$user_info['User']['info'];?></textarea>
	</div>
	<div>
		<h3>Password Change <span class="small">Set a new password. Leave blank if you do not wish to change your password.</span></h3>
		<input type="password" name="data[User][new_password]"/>
	</div>
	<div id="old_password">
		<h3>Old Password <span class="small">If setting a new password please enter your current password.</span></h3>
		<input type="password" name="data[User][old_password]"/>
	</div>
	<div class="submit">
		<input type="submit" value="Update Your Account"/>
	</div>
</div>
</form>