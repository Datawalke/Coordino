<?php
if (!@$_REQUEST['list']) {
	header('Location: phpThumb.demo.demo.php');
	exit;
}
echo '<html><body>';
$dh = opendir('.');
while ($file = readdir($dh)) {
	if (is_file($file) && ($file{0} != '.') && ($file != basename(__FILE__))) {
		switch ($file) {
			case 'phpThumb.demo.object.simple.php':
			case 'phpThumb.demo.object.php':
				echo '<tt>'.str_replace(' ', '&nbsp;', str_pad(filesize($file), 10, ' ', STR_PAD_LEFT)).'</tt> '.$file.' (cannot work as a live demo)<br>';
				break;
			default:
				echo '<tt>'.str_replace(' ', '&nbsp;', str_pad(filesize($file), 10, ' ', STR_PAD_LEFT)).'</tt> <a href="'.$file.'">'.$file.'</a><br>';
				break;
		}
	}
}
echo '</body></html>';
?>