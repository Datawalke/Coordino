<p>Hi <?=$dear;?>,</p>

<p>Your question has been answered.</p>

<p>You asked: <?=$question['Post']['title'];?></p>

<?=$username;?> answered your question:<br/>
<p><?=$answer;?></p>

<p>Give <?=$username;?> some feed back or mark their answer as correct
<a href="http://<?=$_SERVER['SERVER_NAME'];?>/questions/<?=$question['Post']['public_key'];?>/<?=$question['Post']['url_title'];?>">here</a>!</p>

Thank you!