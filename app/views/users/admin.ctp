<?php echo $this->element('admin_header',
	array("selected" => "Settings")); 
?>
   
<form action="/admin" method="post" >
<div class="detailed_inputs">
	<div>
		<h3>Vote up: <span class="small"><?php echo $settings['0']['Setting']['description']; ?></span></h3>
		<input type="text" name="data[Setting][0][value]" value="<?php echo $settings['0']['Setting']['value']; ?>"/>
	</div>
	<div>
		<h3>Comment: <span class="small"><?php echo $settings['1']['Setting']['description']; ?></span></h3>
		<input type="text" name="data[Setting][1][value]" value="<?php echo $settings['1']['Setting']['value']; ?>"/>
	</div>
	<div>
		<h3>Vote Down: <span class="small"><?php echo $settings['2']['Setting']['description']; ?></span></h3>
		<input type="text" name="data[Setting][2][value]" value="<?php echo $settings['2']['Setting']['value']; ?>"/>
	</div>
	<div>
		<h3>Advertising: <span class="small"><?php echo $settings['3']['Setting']['description']; ?></span></h3>
		<input type="text" name="data[Setting][3][value]" value="<?php echo $settings['3']['Setting']['value']; ?>"/>
	</div>
	<div>
		<h3>Edit: <span class="small"><?php echo $settings['4']['Setting']['description']; ?></span></h3>
		<input type="text" name="data[Setting][4][value]" value="<?php echo $settings['4']['Setting']['value']; ?>"/>
	</div>
	<div>
		<h3>Flag: <span class="small"><?php echo $settings['5']['Setting']['description']; ?></span></h3>
		<input type="text" name="data[Setting][5][value]" value="<?php echo $settings['5']['Setting']['value']; ?>"/>
	</div>
	<div id="old_password">
		<h3>Display Limit: <span class="small"><?php echo $settings['6']['Setting']['description']; ?></span></h3>
		<input type="text" name="data[Setting][6][value]" value="<?php echo $settings['6']['Setting']['value']; ?>"/>
	</div>
	<div class="submit">
		<input type="submit" value="Update Settings"/>
	</div>
</div>
</form>