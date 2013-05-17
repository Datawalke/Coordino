<h3>
	<?php echo $html->link("Open", array('action'=>'openBugs')); ?>  ---
	<?php echo $html->link("Closed", array('action'=>'closedBugs')); ?> ---
	Invalid
</h3>

<table>
	
	<tr>
		<th></th>
		<th>bug filed</th>
	<tr>

<form id="changeStatusForm" method="post">

<? foreach ($invalidBugs as $bug => $value) : ?>
	<tr>
		<td>
			<input type="checkbox" name="data[Bugs][<?php echo $invalidBugs[$bug]['Bug']['id'];?>]" />
		</td>
		<td><?php echo $invalidBugs[$bug]['Bug']['content']; ?></td>
	</tr>
<? endforeach; ?>
</table>

<div class="submit">
	<input type="submit" onclick="document.getElementById('changeStatusForm').action='/bugs/changeStatus/status/closed'" value="mark as closed" />
	<input type="submit" onclick="document.getElementById('changeStatusForm').action='/bugs/changeStatus/status/open'" value="mark as open" />
</div>

</form>
