<?php
//////////////////////////////////////////////////////////////
///  phpThumb() by James Heinrich <info@silisoftware.com>   //
//        available at http://phpthumb.sourceforge.net     ///
//////////////////////////////////////////////////////////////
//                                                          //
// phpThumb.demo.showpic.php                                //
// James Heinrich <info@silisoftware.com>                   //
// 23 Feb 2004                                              //
//                                                          //
// This code is useful for popup pictures (e.g. thumbnails  //
// you want to show larger, such as a larger version of a   //
// product photo for example) but you don't know the image  //
// dimensions before popping up. This script displays the   //
// image with no window border, and resizes the window to   //
// the size it needs to be (usually better to spawn it      //
// large (600x400 for example) and let it auto-resize it    //
// smaller), and if the image is larger than 90% of the     //
// current screen area the window respawns itself with      //
// scrollbars.                                              //
//                                                          //
// Usage:                                                   //
// window.open('showpic.php?src=big.jpg&title=Big+picture', //
//   'popupwindowname',                                     //
//   'width=600,height=400,menubar=no,toolbar=no')          //
//                                                          //
// See demo linked from http://phpthumb.sourceforge.net    ///
//////////////////////////////////////////////////////////////

$phpThumbLocation = '../phpThumb.php?';

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo '<html><head>';
if (isset($_GET['title'])) {
	echo '<title>'.htmlentities($_GET['title']).'</title>';
	unset($_GET['title']);
} else {
	echo '<title>'.htmlentities('phpThumb :: popup window resizing demo').'</title>';
}
?>

	<script type="text/javascript">
	<!--
	// http://www.xs4all.nl/~ppk/js/winprop.html
	function CrossBrowserResizeInnerWindowTo(newWidth, newHeight) {
		if (self.innerWidth) {
			frameWidth  = self.innerWidth;
			frameHeight = self.innerHeight;
		} else if (document.documentElement && document.documentElement.clientWidth) {
			frameWidth  = document.documentElement.clientWidth;
			frameHeight = document.documentElement.clientHeight;
		} else if (document.body) {
			frameWidth  = document.body.clientWidth;
			frameHeight = document.body.clientHeight;
		} else {
			return false;
		}
		if (document.layers) {
			newWidth  -= (parent.outerWidth - parent.innerWidth);
			newHeight -= (parent.outerHeight - parent.innerHeight);
		}

		// original code:
		//parent.window.resizeTo(newWidth, newHeight);
		// fixed code: James Heinrich, 20 Feb 2004
		parent.window.resizeBy(newWidth - frameWidth, newHeight - frameHeight);

		return true;
	}
	// -->
	</script>

	<script type="text/javascript" src="javascript_api.js"></script>

<?php
	function SafeStripSlashes($string) {
		return (get_magic_quotes_gpc() ? stripslashes($string) : $string);
	}

	$additionalparameters = array();
	foreach ($_GET as $key => $value) {
		if (is_array($value)) {
			foreach ($value as $key2 => $value2) {
				$additionalparameters[] = $key.'[]='.SafeStripSlashes($value2);
			}
		} else {
			$additionalparameters[] = $key.'='.SafeStripSlashes($value);
		}
	}
	$imagesrc = $phpThumbLocation.implode('&', $additionalparameters);

	echo '<script type="text/javascript">';
	echo 'var ns4;';
	echo 'var op5;';
	echo 'function setBrowserWindowSizeToImage() {';
	echo 	'if (!document.getElementById("imageimg")) { return false; }';
	echo	'sniffBrowsers();';
	echo 	'var imageW = getImageWidth("imageimg");';
	echo 	'var imageH = getImageHeight("imageimg");';
			// check for maximum dimensions to allow no-scrollbar window
	echo 	'if (((screen.width * 1.1) > imageW) || ((screen.height * 1.1) > imageH)) {'."\n";
				// screen is large enough to fit whole picture on screen with 10% margin
	echo 		'CrossBrowserResizeInnerWindowTo(imageW, imageH);'."\n";
	echo 	'} else {'."\n";
				// image is too large for screen: add scrollbars by putting the image inside an IFRAME
	echo 		'document.getElementById("showpicspan").innerHTML = "<iframe width=\"100%\" height=\"100%\" marginheight=\"0\" marginwidth=\"0\" frameborder=\"0\" scrolling=\"on\" src=\"'.$imagesrc.'\">Your browser does not support the IFRAME tag. Please use one that does (IE, Firefox, etc).<br><img src=\"'.$imagesrc.'\"><\/iframe>";';
	echo 	'}'."\n";
	echo '}';
	echo '</script>';
?>

</head>

<body style="margin: 0px;" onLoad="setBrowserWindowSizeToImage();"><div id="showpicspan"><?php

if (@$_GET['src']) {

	echo '<script type="text/javascript">';
	echo 'document.writeln(\'<img src="'.$imagesrc.'" border="0" id="imageimg" hspace="0" hspace="0" style="padding: 0px; margin: 0px;">\');';
	echo '</script>';

} else {

	echo '<pre>';
	echo 'Usage:<br><br><b>'.$_SERVER['PHP_SELF'].'?src=<i>filename</i>&title=<i>Picture+Title</i></b>';
	echo '</pre>';

}

?></div></body>
</html>