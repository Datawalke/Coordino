<?php echo $this->element('admin_header',
	array("selected" => "Blacklist")); 
?>

<h3>Banned words:</h3>
<table>
	<tr>
<?php 
foreach($list as $key => $value) {
?>
		<td>
			"<?php echo $list[$key];?>"&nbsp;&nbsp;<a href="/admin/blacklist/remove/<?php echo $list[$key];?>">remove</a>
		</td>
<?php if($key > 0 && (($key < 6 && ($key % 4 == 0)) || ($key > 6 && (($key - 4) % 5 == 0)))) { ?>
	</tr>
	<tr>
<?php } ?>
<?php } ?>
</table>
<br/>
Or, <a href="/admin/blacklist/add">add</a> a word to the terrible list.