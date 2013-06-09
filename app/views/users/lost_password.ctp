<p>Enter your email address and a new password will be sent to you shortly.</p>
<?php
	echo $form->create('User', array('action' => 'lost_password'));
    echo $form->input('email', array('class' => 'large_input'));
    echo $form->end('Email Me');
?>