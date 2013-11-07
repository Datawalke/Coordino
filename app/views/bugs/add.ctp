<p>Thank you for taking the time to submit a bug.  Please provide as much information as possible.</p>

<?php echo  $form->create('Bug'); ?>
	<?php echo  $form->input('content', array('label' => '', 'rows' => '8', 'cols' => '50')); ?><br />
<?php echo  $form->end('submit your bug'); ?>