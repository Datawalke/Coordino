<div id="admin_header" class="wrapper">
	<h2><?php echo $selected;?></h2>
<ul class="tabs">
	<li <? if($selected == 'Settings') { echo 'class="selected"'; } ?>><?php echo $html->link(__('Settings',true),'/admin');?></li>
	<li <? if($selected == 'Flagged Posts') { echo 'class="selected"'; } ?>><?php echo $html->link(__('Flagged Posts',true),'/admin/flagged');?></li>
	<li <? if($selected == 'Users') { echo 'class="selected"'; } ?>><?php echo $html->link(__('Users',true),'/admin/users');?></li>
	<li <? if($selected == 'Blacklist') { echo 'class="selected"'; } ?>><?php echo $html->link(__('Blacklist',true),'/admin/blacklist');?></li>
	<li <? if($selected == 'Remote Settings') { echo 'class="selected"'; } ?>><?php echo $html->link(__('Remote Settings',true),'/admin/remote_settings');?></li>
</ul>
</div>