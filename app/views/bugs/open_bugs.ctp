<h3>
	Open ---
	<?= $html->link("Closed", array('action'=>'closedBugs')); ?> ---
	<?= $html->link("Invalid", array('action'=>'invalidBugs')); ?>
</h3>

<table>
	
	<tr>
		<th></th>
		<th>bug filed</th>
	<tr>

<form id="changeStatusForm" method="post">

<?php foreach ($openBugs as $bug => $value) : ?>
	<tr>
		<td>
			<input type="checkbox" name="data[Bugs][<?=$openBugs[$bug]['Bug']['id'];?>]" />
		</td>
		<td><?= $openBugs[$bug]['Bug']['content']; ?></td>
	</tr>
<?php endforeach; ?>
</table>

<div class="submit">
	<input type="submit" onclick="document.getElementById('changeStatusForm').action='/bugs/changeStatus/status/closed'" value="mark as closed" />
	<input type="submit" onclick="document.getElementById('changeStatusForm').action='/bugs/changeStatus/status/invalid'" value="mark as invalid" />
</div>

</form>
