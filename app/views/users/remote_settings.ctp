<?=$this->element('admin_header');?>
<div class="detailed_inputs">
	<div>
		<h3>Remote Authentication Key:</h3>
		<?=$settings['0']['Setting']['value'];?>
	</div>
	<br/>
<form action="?" method="post">
	<div>
		<h3>Remote Authentication Only:</h3>
		<select name="data[1][Setting][value]">
			<option <?php echo ($settings['1']['Setting']['value'] == 'no') ? 'selected="yes"' : ''; ?>>no</option>
			<option <?php echo ($settings['1']['Setting']['value'] == 'yes') ? 'selected="yes"' : ''; ?>>yes</option>
		</select>
	</div>
	<br/>
	<div>
		<h3>Remote Authentication Login URL:</h3>
		<input type="text" name="data[2][Setting][value]" value="<?=$settings['2']['Setting']['value'];?>" />
	</div>
	<br/>
	<div>
		<h3>Remote Authentication Logout URL:</h3>
		<input type="text" name="data[3][Setting][value]" value="<?=$settings['3']['Setting']['value'];?>" />
	</div>
	<br/>
	<div class="submit">
		<input type="submit" value="Change Settings"/>
	</div>	
</div>
</form>