<?php
//////////////////////////////////////////////////////////////
///  phpThumb() by James Heinrich <info@silisoftware.com>   //
//        available at http://phpthumb.sourceforge.net     ///
//////////////////////////////////////////////////////////////
///                                                         //
// phpThumb.demo.random.php                                 //
// James Heinrich <info@silisoftware.com>                   //
//                                                          //
// Display a random image from a specified directory.       //
// Run with no parameters for usage instructions.           //
//                                                          //
//////////////////////////////////////////////////////////////

function SelectRandomImage($dirname='.', $portrait=true, $landscape=true, $square=true) {
	// return a random image filename from $dirname
	// the last 3 parameters determine what aspect ratio of images
	// may be returned
	$possibleimages = array();
	if ($dh = opendir($dirname)) {
		while ($file = readdir($dh)) {
			if (is_file($dirname.'/'.$file) && eregi('\.(jpg|jpeg|gif|png|tiff|bmp)$', $file)) {
				if ($gis = @GetImageSize($dirname.'/'.$file)) {
					if ($portrait && ($gis[0] < $gis[1])) {
						// portrait
						$possibleimages[] = $file;
					} elseif ($landscape && ($gis[0] > $gis[1])) {
						// landscape
						$possibleimages[] = $file;
					} elseif ($square) {
						// square
						$possibleimages[] = $file;
					}
				}
			}
		}
		closedir($dh);
	}
	if (empty($possibleimages)) {
		return false;
	}
	if (phpversion() < '4.2.0') {
		mt_srand(time());
	}
	$randkey = mt_rand(0, count($possibleimages) - 1);
	return realpath($dirname.'/'.$possibleimages[$randkey]);
}

if (@$_REQUEST['dir']) {
	if (is_dir($_REQUEST['dir'])) {

		if (!@$_REQUEST['o']) {
			$_REQUEST['o'] = 'PLS';
		}
		$_REQUEST['o'] = strtoupper($_REQUEST['o']);
		$portrait  = (strpos(@$_REQUEST['o'], 'P') !== false);
		$landscape = (strpos(@$_REQUEST['o'], 'L') !== false);
		$square    = (strpos(@$_REQUEST['o'], 'S') !== false);
		$randomSRC = SelectRandomImage($_REQUEST['dir'], $portrait, $landscape, $square);
		if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
			$randomSRC = str_replace('\\', '/', eregi_replace('^'.realpath(@$_SERVER['DOCUMENT_ROOT']), '', realpath($randomSRC)));
		} else {
			$randomSRC = str_replace(realpath(@$_SERVER['DOCUMENT_ROOT']), '', realpath($randomSRC));
		}

		$otherParams = array();
		foreach ($_GET as $key => $value) {
			if (($key == 'dir') || ($key == 'o')) {
				continue;
			}
			if (is_array($value)) {
				foreach ($value as $vkey => $vvalue) {
					$otherParams[] = urlencode($key).'['.urlencode($vkey).']='.urlencode($vvalue);
				}
			} else {
				$otherParams[] = urlencode($key).'='.urlencode($value);
			}
		}
		header('Location: ../phpThumb.php?src='.urlencode($randomSRC).'&'.implode('&', $otherParams));
		exit;

	} else {
		die($_REQUEST['dir'].' is not a directory');
	}

} else {

	echo '<html><body>Usage: <b>'.basename($_SERVER['PHP_SELF']).'?dir=<i>&lt;directory&gt;</i>&amp;<i>&lt;phpThumb parameters&gt;</i></b>&amp;o=<i>(P|L|S)</i><br><br>Examples:<ul>';
	echo '<li>'.basename($_SERVER['PHP_SELF']).'?./images/&o=L <i>(landscape images only)</i></li>';
	echo '<li>'.basename($_SERVER['PHP_SELF']).'?./images/&o=PS <i>(portrait or square images only)</i></li>';
	echo '</ul></body></html>';

}

?>
