<p>Hi <?php echo $dear;?>,</p>

<p>Your question has been answered.</p>

<p>You asked: <?php echo $question['Post']['title'];?></p>

<?php echo $username;?> answered your question:<br/>
<p><?php echo $answer;?></p>

<p>Give <?php echo $username;?> some feed back or mark their answer as correct
<a href="http://<?php echo $_SERVER['SERVER_NAME'];?>/questions/<?php echo $question['Post']['public_key'];?>/<?php echo $question['Post']['url_title'];?>">here</a>!</p>

Thank you!