<h2>Show Off</h2>
<p>So you want to show off your reputation and EngineJuice status on forums or a profile somewhere?</p>
<p>Below is the code for your own EngineJuice Bar</p>
<?php $user = $session->read('Auth.User');?>
<?=$html->image('/users/' . $user['public_key'] . '/' . $user['username'] . '/bar.png', array('alt' => 'Oh no'));?><br/>
<strong>Copy and Paste the code below to your profile:<br/>
<input type="text" class="big_input" style="width: 500px" onclick="this.select();" value='<a href="http://enginejuice.com" title="EngineJuice"><img src="http://enginejuice.com/users/<?=$user['public_key'];?>/<?=$user['username'];?>/bar.png" alt="EngineJuice"/></a>'/><br/>
<strong>Copy and Paste the code below to your forum signature (BBCode):<br/>
<input type="text" class="big_input" style="width: 500px" onclick="this.select();" value='[url=http://enginejuice.com][img]http://enginejuice.com/users/<?=$user['public_key'];?>/<?=$user['username'];?>/bar.png[/img][/url]'/><br/>
