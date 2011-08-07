<p>Thank you for taking the time to submit a bug.  Please provide as much information as possible.</p>

<?= $form->create('Bug'); ?>
	<?= $form->input('content', array('label' => '', 'rows' => '8', 'cols' => '50')); ?><br />
<?= $form->end('submit your bug'); ?>