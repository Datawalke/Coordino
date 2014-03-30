<p>Welcome to the Coordino setup. Before we can get the instillation process started we need the following items:</p>
<ul>
	<li>Database Name</li>
	<li>Database Username</li>
	<li>Database Password</li>
	<li>Database Host</li>
</ul>

<p><strong>If for any reason you cannot use this installer or something goes wrong you can access the following file:<br/>
			<span class="highlight">/app/config/database_same.php</span> with your favorite text editor and edit these settings manually. Once you are done editing save the file as: <span class="highlight">database.php</span>.
</strong></p>

<p><strong>Note:</strong> To use the automatic instillation process you must have write permissions to the <span class="highlight">/app/config/database.php</span>  writable. If you do not wish to make this directory writable and wish to edit the <span class="highlight">/app/config/database.php</span> file manually ignore any errors related to the <span class="highlight">/app/config</span> directory below.</p>

<p>Directory permission tests:</p>
<ul>
	<li>/app/config <?php if($writeChecks['config']) { echo '<span class="success">is writable.</span>'; }
		else { echo '<span class="error">is not writable.</span>';}?>
	</li>
	<li>/app/config/database.php <?php if($writeChecks['database']) { echo '<span class="success">is writable.</span>'; }
		else { echo '<span class="error">is not writable.</span>';}?>
	</li>
	<li>/app/tmp <?php if($writeChecks['tmp']) { echo '<span class="success">is writable.</span>'; }
		else { echo '<span class="error">is not writable.</span>';}?>
	</li>
    <li>/app/tmp/cache <?php if($writeChecks['tmp_cache']) { echo '<span class="success">is writable.</span>'; }
    else { echo '<span class="error">is not writable.</span>';}?>
    </li>
	<li>/app/webroot/img/thumbs <?php if($writeChecks['thumbs']) { echo '<span class="success">is writable.</span>'; }
		else { echo '<span class="error">is not writable.</span>';}?>
	</li>	
	<li>/app/webroot/img/uploads/users <?php if($writeChecks['uploads']) { echo '<span class="success">is writable.</span>'; }
		else { echo '<span class="error">is not writable.</span>';}?>
	</li>	
</ul>

<?php	if(!in_array(false, $writeChecks)) { ?>
	<p>Looks like you are good to go. <a href="install/database-config">Lets Continue!</a></p>
<?php } else { ?>
	<p><span class="error">Look above for errors you must fix.</span></p>
<?php } ?>
