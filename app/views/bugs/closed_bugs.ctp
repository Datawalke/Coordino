<h3>
	<?php echo $html->link("Open", array('action'=>'openBugs')); ?>  ---
	Closed ---
	<?php echo $html->link("Invalid", array('action'=>'invalidBugs')); ?>
</h3>

<table>
	
	<tr>
		<th></th>
		<th>bug filed</th>
	<tr>
<form id="changeStatusForm" method="post">	
	
	<?php foreach ($closedBugs as $bug => $value) : ?>
		<tr>
			<td>
				<input type="checkbox" id="checkeroo" name="data[Bugs][<?php echo $closedBugs[$bug]['Bug']['id'];?>]" />
			</td>
			<td><?php echo $closedBugs[$bug]['Bug']['content']; ?></td>
		</tr>
	<?php endforeach; ?>
	</table>
</table>

<div class="submit">
	<input type="submit" onclick="document.getElementById('changeStatusForm').action='/bugs/changeStatus/status/open'" value="revert to open" />
	<input type="submit" onclick="document.getElementById('changeStatusForm').action='/bugs/changeStatus/status/invalid'" value="mark as invalid" />
</div>
</form>