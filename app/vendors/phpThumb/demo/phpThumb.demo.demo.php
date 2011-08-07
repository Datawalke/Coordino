<?php
//////////////////////////////////////////////////////////////
///  phpThumb() by James Heinrich <info@silisoftware.com>   //
//        available at http://phpthumb.sourceforge.net     ///
//////////////////////////////////////////////////////////////
///                                                         //
// phpThumb.demo.demo.php                                   //
// James Heinrich <info@silisoftware.com>                   //
//                                                          //
// Demo showing a wide variety of parameters that can be    //
// passed to phpThumb.php                                   //
// Live demo is at http://phpthumb.sourceforge.net/demo/    //
//                                                          //
//////////////////////////////////////////////////////////////
$ServerInfo['gd_string']  = 'unknown';
$ServerInfo['gd_numeric'] = 0;
//ob_start();
if (!include_once('../phpthumb.functions.php')) {
	//ob_end_flush();
	die('failed to include_once("../phpthumb.functions.php")');
}
if (!include_once('../phpthumb.class.php')) {
	//ob_end_flush();
	die('failed to include_once("../phpthumb.class.php")');
}
//ob_end_clean();
$phpThumb = new phpThumb();
if (include_once('../phpThumb.config.php')) {
	foreach ($PHPTHUMB_CONFIG as $key => $value) {
		$keyname = 'config_'.$key;
		$phpThumb->setParameter($keyname, $value);
	}
}
$ServerInfo['phpthumb_version'] = $phpThumb->phpthumb_version;
$ServerInfo['im_version']       = $phpThumb->ImageMagickVersion();;
$ServerInfo['gd_string']        = phpthumb_functions::gd_version(true);
$ServerInfo['gd_numeric']       = phpthumb_functions::gd_version(false);
unset($phpThumb);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>Demo of phpThumb() - thumbnails created by PHP using GD and/or ImageMagick</title>
	<link rel="stylesheet"    type="text/css" href="/style.css" title="style sheet">
	<link rel="shortcut icon" type="image/x-icon" href="http://phpthumb.sourceforge.net/thumb.ico" />
</head>
<body bgcolor="#C5C5C5">

This is a demo of <a href="http://phpthumb.sourceforge.net"><b>phpThumb()</b></a> (current version: v<?php echo @$ServerInfo['phpthumb_version']; ?>)<br>
<a href="index.php?list=1">Other phpThumb() demos</a> are also available.<br>
<br>
<b>Note:</b> this server is working on GD "<?php
echo $ServerInfo['gd_string'].'"';
if ($ServerInfo['gd_numeric'] >= 2) {
	echo ', so images should be of optimal quality.';
} else {
	echo ', so images (especially watermarks) do not look as good as they would on GD v2.';
}
?><br>

<hr size="1">
<a href="#showpic">phpThumb.demo.showpic.php demo here</a><br>
<a href="#gd1vs2">Difference between GD1 and GD2</a><br>
<hr size="1">
<table border="5" align="center" width="500" cellpadding="5"><tr><td>
	<b>The following images have the textured background behind them to illustrate transparency effects.
	Note that some browsers, notably Internet Explorer, are incapable of displaying alpha-channel PNGs.
	See my page on the <a href="http://www.silisoftware.com/png_alpha_transparency/" target="_blank">PNG transparency problem</a>.
	Other modern browsers such as <a href="http://www.mozilla.org">Mozilla/Firefox</a> display alpha-transparent PNGs with no problems.</b>
</td></tr></table><br>
<script type="text/javascript" defer>
<!--
var agt = navigator.userAgent.toLowerCase();
if ((agt.indexOf("opera") == -1) && (agt.indexOf("msie 7") == -1) && (navigator.product != "Gecko")) {
	alert("You are (probably) using Internet Explorer and PNG transparency is (probably) broken");
}
// -->
</script>


<?php
$phpThumbBase      = '../phpThumb.php';

$img['background'] = 'images/lrock011.jpg';

$img['square']     = 'images/disk.jpg';
$img['landscape']  = 'images/loco.jpg';
$img['portrait']   = 'images/pineapple.jpg';
$img['unrotated']  = 'images/monkey.jpg';
$img['watermark']  = 'images/watermark.png';
$img['levels1']    = 'images/bunnies.jpg';
$img['levels2']    = 'images/lilies.jpg';
$img['anigif']     = 'images/animaple.gif';
$img['alpha']      = 'images/alpha.png';
//$img['alpha']      = 'images/North15.gif';

$img['mask1']      = 'images/mask04.png';
$img['mask2']      = 'images/mask05.png';
$img['mask3']      = 'images/mask06.png';

$img['frame1']     = 'images/frame1.png';
$img['frame2']     = 'images/frame2.png';

$img['bmp']        = 'images/winnt.bmp';
$img['tiff']       = 'images/1024-none.tiff';
$img['wmf']        = 'images/computer.wmf';

$img['small']      = 'images/small.jpg';
$img['big']        = 'images/big.jpg';

$png_alpha   = 'Note: PNG/ICO output is 32-bit with alpha transparency, subject to <a href="http://www.silisoftware.com/png_alpha_transparency/" target="_blank">PNG transparency problem</a> in Internet Explorer';
$only_gd     = '<br>(only works with GD (any version), this server is '.($ServerInfo['gd_string'] ? 'running GD "<i>'.$ServerInfo['gd_string'].'</i>" so it <b><font color="green">will</font>' : 'not running any recognized version of GD so it <b><font color="red">will not</font>').'</b> work)';
$only_gd2    = '<br>(only works with GD v2.0+, this server is running GD "<i>'.($ServerInfo['gd_string'] ? $ServerInfo['gd_string'] : 'n/a').'</i>" so it <b>'.(($ServerInfo['gd_numeric'] >= 2) ? '<font color="green">will</font>' : '<font color="red">will not</font>').'</b> work)';
$only_php42  = '<br>(only works with PHP v4.2.0+, this server is running PHP v'.phpversion().' so it <b>'.(version_compare(phpversion(), '4.2.0', '>=') ? '<font color="green">will</font>' : '<font color="red">will not</font>').'</b> work)';
$only_php43  = '<br>(only works with PHP v4.3.0+, this server is running PHP v'.phpversion().' so it <b>'.(version_compare(phpversion(), '4.3.0', '>=') ? '<font color="green">will</font>' : '<font color="red">will not</font>').'</b> work)';
$only_php432 = '<br>(only works with PHP v4.3.2+, this server is running PHP v'.phpversion().' so it <b>'.(version_compare(phpversion(), '4.3.2', '>=') ? '<font color="green">will</font>' : '<font color="red">will not</font>').'</b> work (correctly))';
$only_php500 = '<br>(only works with PHP v5.0.0+, this server is running PHP v'.phpversion().' so it <b>'.(version_compare(phpversion(), '5.0.0', '>=') ? '<font color="green">will</font>' : '<font color="red">will not</font>').'</b> work (correctly))';
$php5_or_IM  = '<br>(only works with PHP v5.0.0+ <i>or</i> ImageMagick, this server is running PHP v'.phpversion().' and "<i>'.($ServerInfo['im_version'] ? $ServerInfo['im_version'] : 'n/a').'</i>" so it <b>'.(($ServerInfo['im_version'] && version_compare(phpversion(), '5.0.0', '>=')) ? '<font color="green">will</font>' : '<font color="red">will not</font>').'</b> work (correctly))';
$only_exif   = '<br>(only works when the EXIF extension is loaded, so on this server it <b>'.(extension_loaded('exif') ? '<font color="green">will</font>' : '<font color="red">will not</font>').'</b> work)';
$only_im     = '<br>(requires ImageMagick, this server is running "<i>'.($ServerInfo['im_version'] ? $ServerInfo['im_version'] : 'n/a').'</i>" so it <b>'.($ServerInfo['im_version'] ? '<font color="green">will</font>' : '<font color="red">will not</font>').'</b> work)';

$Examples[] = array('getstrings' => array($phpThumbBase), 'description' => 'phpThumb version');
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['square'].'&w=200'), 'description' => 'width=200px');
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['square'].'&w=200&q=10&sia=custom-filename'), 'description' => 'width=200px, JPEGquality=10%, SaveImageAs=custom-filename');
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['watermark'].'&w=400&aoe=1&bg=ffffff'), 'description' => 'width=400px, AllowOutputEnlargement enabled');
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['square'].'&w=250&sx=600&sy=5&sw=100&sh=100&aoe=1'), 'description' => 'section from (600x5 - 700x105) cropped and enlarged by 250%, AllowOutputEnlargement enabled');
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.urlencode('http://silisoftware.com/images/SiliSoft.gif').'&w=100'), 'description' => 'HTTP source image'.$only_gd);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['square'].'&w=200&fltr[]=wmi|'.$img['watermark'].'|BL'), 'description' => 'width=200px, watermark (bottom-left, 75% opacity)'.$only_gd);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['square'].'&w=200&fltr[]=wmi|'.$img['watermark'].'|*|25'), 'description' => 'width=200px, watermark (tiled, 25% opacity)'.$only_gd);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['square'].'&w=200&fltr[]=wmi|'.$img['watermark'].'|75x50|80|75|75|45'), 'description' => 'width=200px, watermark (absolute position (75x50), rotation (45), scaling (75x75)))'.$only_gd);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['watermark'].'&bg=00FFFF&f=png', $phpThumbBase.'?src='.$img['watermark'].'&bg=00FFFF&f=gif', $phpThumbBase.'?src='.$img['watermark'].'&bg=00FFFF&f=jpeg'), 'description' => 'source image (GIF) transpancy with transparent output (PNG, GIF) vs. specified background color (JPEG)');
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['anigif'], $phpThumbBase.'?src='.$img['anigif'].'&w=25&f=gif', $phpThumbBase.'?src='.$img['anigif'].'&w=25&f=png', $phpThumbBase.'?src='.$img['anigif'].'&w=25&f=ico', $phpThumbBase.'?src='.$img['anigif'].'&w=25&f=bmp', $phpThumbBase.'?src='.$img['anigif'].'&w=25&f=jpeg'), 'description' => 'resize animated GIF. Notice how output format affects the result: GIF is animated and transparent; PNG and ICO are tranparent but not animated (first frame is rendered as a still image); JPEG and BMP are neither transparent nor animated. Any filters will disable animated resizing (may be fixed in a future version).<br>'.$only_im);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['anigif'], $phpThumbBase.'?src='.$img['anigif'].'&sfn=0&f=png', $phpThumbBase.'?src='.$img['anigif'].'&sfn=2&f=png'), 'description' => 'Specifying still-image source frame in multi-frame source images<br>'.$only_im);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['alpha'].'&f=png', $phpThumbBase.'?src='.$img['alpha'].'&f=ico', $phpThumbBase.'?src='.$img['alpha'].'&f=gif', $phpThumbBase.'?src='.$img['alpha'].'&f=jpeg'), 'description' => 'PNG alpha transparency test, using test image from the <a href="http://trific.ath.cx/web/png/">PNG transparency test page</a>'.$only_php432);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['square'].'&w=200&fltr[]=stc|FFFFFF|5|10&f=png'), 'description' => 'Create transparency from source image color'.$only_gd2);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['landscape'].'&w=200', $phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=usm|80|0.5|3'), 'description' => 'normal vs. unsharp masking at default settings'.$only_gd2);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['landscape'].'&w=200', $phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=blur|1', $phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=blur|5'), 'description' => 'normal vs. blur at default (1) and heavy (5)'.$only_gd2);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['landscape'].'&w=200', $phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=gblr', $phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=sblr'), 'description' => 'normal vs. gaussian blur vs. selective blur'.$only_php500.$only_gd2);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['portrait'].'&w=100&h=100&far=L&bg=0000FF&f=png&fltr[]=bord|1', $phpThumbBase.'?src='.$img['landscape'].'&w=100&h=100&far=T&bg=FF0000&f=png&fltr[]=bord|1', $phpThumbBase.'?src='.$img['portrait'].'&w=100&h=100&far=C&bg=0000FF&f=png&fltr[]=bord|1', $phpThumbBase.'?src='.$img['landscape'].'&w=100&h=100&far=B&bg=FF0000&f=png&fltr[]=bord|1', $phpThumbBase.'?src='.$img['portrait'].'&w=100&h=100&far=R&bg=0000FF&f=png&fltr[]=bord|1'), 'description' => 'Forced Aspect Ratio, colored background, PNG output'.$only_gd);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['portrait'].'&w=150&ar=L', $phpThumbBase.'?src='.$img['landscape'].'&w=150&ar=L'), 'description' => 'auto-rotate counter-clockwise to landscape from portrait &amp; lanscape'.$only_php42.$only_gd);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['portrait'].'&hp=100&wl=200', $phpThumbBase.'?src='.$img['landscape'].'&hp=100&wl=200'), 'description' => 'auto-selection of W and H based on source image orientation');
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['unrotated'].'&w=150&h=150', $phpThumbBase.'?src='.$img['unrotated'].'&w=150&h=150&ar=x'), 'description' => 'original image vs. auto-rotated based on EXIF data'.$only_php42.$only_exif.$only_gd);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['landscape'].'&w=200&ra=30&bg=0000FF', $phpThumbBase.'?src='.$img['landscape'].'&w=200&ra=30&f=png', $phpThumbBase.'?src='.$img['alpha'].'&ra=30&f=png', $phpThumbBase.'?src='.$img['alpha'].'&ra=30&f=gif'), 'description' => 'Rotated 30° (counter-clockwise), width=200px, blue background vs. transparent background vs. rotated image with pre-existing alpha'.$only_php42.$only_gd);
//$Examples[] = array('getstrings' => array($phpThumbBase.'?src=images/1-bit.gif&ra=30&f=png', $phpThumbBase.'?src=images/4-bit.gif&ra=30&f=png', $phpThumbBase.'?src=images/8-bit.gif&ra=30&f=png'), 'description' => '2-color, 16-color, 256-color transparent GIFs'.$only_php42.$only_gd);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['landscape'].'&w=200&h=300&far=1&bg=CCCCCC', $phpThumbBase.'?src='.$img['landscape'].'&w=200&h=300&iar=1'), 'description' => 'Normal resize behavior (left) vs. Forced non-proportional resize (right)'.$only_gd);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['landscape'].'&w=100&h=50&zc=1', $phpThumbBase.'?src='.$img['landscape'].'&w=100&zc=1', $phpThumbBase.'?src='.$img['landscape'].'&h=50&zc=1', $phpThumbBase.'?src='.$img['portrait'].'&w=100&h=50&zc=1', $phpThumbBase.'?src='.$img['portrait'].'&w=100&zc=1', $phpThumbBase.'?src='.$img['portrait'].'&h=50&zc=1'), 'description' => 'Zoom-Crop');
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=crop|50', $phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=crop|0|0|0|0.25'), 'description' => 'crop filter');
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=bord|2|20|10|009900&f=png'), 'description' => '2px border, curved border corners (20px horizontal radius, 10px vertical radius)'.$only_gd);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=ric|50|20&f=png'), 'description' => 'curved border corners (20px vertical radius, 50px horizontal radius)<br>'.$png_alpha.$only_gd2.$only_php432);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=sat|75', $phpThumbBase.'?src='.$img['landscape'].'&w=200', $phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=sat|-100'), 'description' => 'saturation -75% vs. normal vs. -100%'.$only_gd2);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=ds|75', $phpThumbBase.'?src='.$img['landscape'].'&w=200', $phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=ds|-100'), 'description' => 'desaturated 75% vs. normal vs. -100%'.$only_gd2);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=clr|25|00FF00'), 'description' => 'colorized 25% to green (#00FF00)'.$only_gd2);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=gray', $phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=sep'), 'description' => 'grayscale vs. sepia'.$only_gd2);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=mask|'.$img['mask3'].'&f=png', $phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=mask|'.$img['mask1'].'&f=png', $phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=mask|'.$img['mask2'].'&f=jpeg&bg=9900CC&q=100'), 'description' => 'Assorted alpha masks (seen below) applied<br>'.$png_alpha.$only_php432.'<br>JPEG/GIF output is flattened to "bg" background color'.$only_gd2.'<br><img src="../'.$img['mask3'].'" alt=""> <img src="../'.$img['mask1'].'" alt=""> <img src="../'.$img['mask2'].'" alt="">');
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=drop|5|10|000000|225&f=png', $phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=mask|'.$img['mask3'].'&fltr[]=drop|5|10|000000|225&f=png', $phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=drop|5|10|000000|225&fltr[]=elip&f=png', $phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=elip&fltr[]=drop|5|10|000000|225&f=png'), 'description' => 'Drop shadow. Note how the order in which filters are applied matters.'.$only_php432.$only_gd);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=elip&f=png', $phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=elip&f=jpeg&bg=00FFFF'), 'description' => 'Elipse<br>'.$png_alpha.$only_php432.'<br>JPEG/GIF output is flattened to "bg" background color'.$only_gd2);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=flip|x', $phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=flip|y', $phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=flip|xy'), 'description' => 'flipped on X, Y and X+Y axes'.$only_gd);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=bvl|10|FFFFFF|000000', $phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=bvl|10|000000|FFFFFF'), 'description' => '10px bevel edge filter'.$only_php432.$only_gd);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=fram|3|2|CCCCCC|FFFFFF|000000', $phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=fram|3|2|CC9966|333333|CCCCCC'), 'description' => '3+2px frame filter'.$only_gd);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=neg'), 'description' => 'Negative filter (inverted color)'.$only_gd);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=th|105', $phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=mask|'.$img['mask1'].'&fltr[]=th|105&f=png'), 'description' => 'Threshold filter; showing preserved alpha channel'.$only_php432.$only_gd);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['portrait'].'&w=150', $phpThumbBase.'?src='.$img['portrait'].'&w=150&fltr[]=rcd|16|1', $phpThumbBase.'?src='.$img['portrait'].'&w=150&fltr[]=rcd|16|0', $phpThumbBase.'?src='.$img['portrait'].'&w=150&fltr[]=gray&fltr[]=rcd|8|1'), 'description' => 'ReduceColorDepth filter; original vs. 16-color dither vs. 16-color nodither vs. 4-gray dither'.$only_gd2);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['levels1'].'&w=150&fltr[]=hist|rgb||||BR|100&fltr[]=hist|*||||BL|100', $phpThumbBase.'?src='.$img['levels1'].'&w=150&fltr[]=lvl|*|0&fltr[]=hist|rgb||||BR|100&fltr[]=hist|*||||BL|100', $phpThumbBase.'?src='.$img['levels1'].'&w=150&fltr[]=lvl|*|1&fltr[]=hist|rgb||||BR|100&fltr[]=hist|*||||BL|100', $phpThumbBase.'?src='.$img['levels1'].'&w=150&fltr[]=lvl|*|2&fltr[]=hist|rgb||||BR|100&fltr[]=hist|*||||BL|100', $phpThumbBase.'?src='.$img['levels1'].'&w=150&fltr[]=lvl|*|3&fltr[]=hist|rgb||||BR|100&fltr[]=hist|*||||BL|100', "\n", $phpThumbBase.'?src='.$img['levels2'].'&w=150&fltr[]=hist|rgb||||BR|100&fltr[]=hist|*||||BL|100', $phpThumbBase.'?src='.$img['levels2'].'&w=150&fltr[]=lvl|*|0&fltr[]=hist|rgb||||BR|100&fltr[]=hist|*||||BL|100', $phpThumbBase.'?src='.$img['levels2'].'&w=150&fltr[]=lvl|*|1&fltr[]=hist|rgb||||BR|100&fltr[]=hist|*||||BL|100', $phpThumbBase.'?src='.$img['levels2'].'&w=150&fltr[]=lvl|*|2&fltr[]=hist|rgb||||BR|100&fltr[]=hist|*||||BL|100', $phpThumbBase.'?src='.$img['levels2'].'&w=150&fltr[]=lvl|*|3&fltr[]=hist|rgb||||BR|100&fltr[]=hist|*||||BL|100'), 'description' => 'original vs. Levels filter methods (0=Internal RGB; 1=Internal Grayscale; 2=ImageMagick Contrast-Stretch; 3=ImageMagick Normalize)'.$only_gd);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['portrait'].'&w=200', $phpThumbBase.'?src='.$img['portrait'].'&w=200&fltr[]=lvl', $phpThumbBase.'?src='.$img['portrait'].'&w=200&fltr[]=wb'), 'description' => 'original vs. Levels vs. White Balance'.$only_gd);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['landscape'].'&w=300&fltr[]=hist|rgb', $phpThumbBase.'?src='.$img['levels1'].'&w=200&fltr[]=hist|*'), 'description' => 'histograms of RGB vs. grayscale'.$only_gd);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['landscape'].'&w=300&fltr[]=edge'), 'description' => 'Edge Detect filter'.$php5_or_IM.$only_gd2);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['landscape'].'&w=300&fltr[]=emb'), 'description' => 'Emboss filter'.$php5_or_IM.$only_gd2);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['landscape'].'&w=300&fltr[]=mean'), 'description' => 'Mean Removal filter'.$only_php500.$only_gd2);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['landscape'].'&w=300&fltr[]=smth'), 'description' => 'Smooth filter'.$only_php500.$only_gd2);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=gam|0.6', $phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=gam|1.6'), 'description' => 'Gamma corrected to 0.6 vs. 1.6'.$only_gd);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['landscape'].'&w=200', $phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=brit|50', $phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=brit|-50'), 'description' => 'Brightness filter (original vs. +50 vs. -50)'.$only_gd2);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['landscape'].'&w=200', $phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=cont|50', $phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=cont|-50'), 'description' => 'Contrast filter (original vs. +50 vs. -50)'.$only_gd2);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['portrait'].'&w=200&fltr[]=over|'.$img['frame1'].'|0', $phpThumbBase.'?src='.$img['portrait'].'&w=200&fltr[]=over|'.$img['frame2'].'|1'), 'description' => 'Overlay vs. Underlay<br><br>Original over/under images:<br><table border="0"><tr><td style="padding: 20px; background-image: url(../'.$img['background'].');"><img src="../'.$img['frame1'].'" alt=""> <img src="../'.$img['frame2'].'" alt=""></td></tr></table>'.$only_gd);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=wmt|phpThumb|18|C|FF0000|loki.ttf|100|5|20&f=png', $phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=wmt|'.rawurlencode('&#9786;&#9835;&#0470;&#1694;').'|40|L|FF0000|arial.ttf|100&f=png', $phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=wmt|copyright+'.date('Y').'|3|BR|00FF00||50&f=png', $phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=wmt|copyright+'.date('Y').'%0AphpThumb()|3|L|00FFFF&f=png'), 'description' => 'Text overlay, TTF and built-in fonts, unicode characters (rawurlencoded HTMLentities), multiple lines, metacharacters (height, width)'.$only_gd);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=wmt|thumbnail+=+^Xx^Y|3|BR|00FFFF||50&f=png', $phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=wmt|click%0Ahere%0A^FkkB|10|L|FF00FF|arial.ttf|100|0||333399|50|y&f=png', $phpThumbBase.'?src='.$img['landscape'].'&w=200&fltr[]=wmt|resized:+^Xx^Y+to+^xx^y|10|B|FFFFFF|arial.ttf|100|0||000000|100|x&f=png'), 'description' => 'metacharacters (height, width), background color, background extend'.$only_gd);
$Examples[] = array('getstrings' => array($phpThumbBase.'?new=FF0000&w=100&h=50&fltr[]=bvl|10&fltr[]=wmt|hello|14|C|00FFFF|arial.ttf&f=png', $phpThumbBase.'?new=FF0000|25&w=150&h=50&fltr[]=bvl|10&fltr[]=wmt|25%+opaque|14|C|0066FF|arial.ttf&f=png'), 'description' => 'Image created with "new", red background, bevel, TTF text'.$only_gd);

$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['bmp'].'&w=200'), 'description' => 'BMP source, width=200px');
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['tiff'], $phpThumbBase.'?src='.$img['tiff'].'&w=200&aoe=1'), 'description' => 'TIFF source, width=200px'.$only_im);
$Examples[] = array('getstrings' => array($phpThumbBase.'?src='.$img['wmf'].'&w=200'), 'description' => 'WMF source, width=200px'.$only_im);
//$Examples[] = array('getstrings' => array(''), 'description' => '');

foreach ($Examples as $key => $ExamplesArray) {
	echo '<a href="#" name="x'.$key.'" title="click to get URL link for example #'.$key.'" onClick="prompt(\'Here is the link to example #'.$key.'\', \'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'#x'.$key.'\'); return false;">#'.$key.'</a>';
	echo '<table border="0"><tr><td style="padding: 20px; background-image: url(../'.$img['background'].');">';
	$text = '';
	foreach ($ExamplesArray['getstrings'] as $dummy => $GETstring) {
		if ($GETstring == "\n") {
			echo '<br>';
			$text .= "\n";
		} else {
			echo '<a href="'.htmlentities($GETstring.'&down=phpThumb.demo.'.$key.'.'.$dummy).'.jpg" title="'.htmlentities(str_replace($phpThumbBase, '', $GETstring)).'">';
			echo '<img border="0" src="'.htmlentities($GETstring).'" alt="">';
			echo '</a> ';
			$text .= '<img src="'.$GETstring.'" alt="">'."\n";
		}
	}
	echo '</td></tr></table>';
	echo '<pre>'.htmlentities($text).'</pre>';
	echo $ExamplesArray['description'].'<br>';
	echo '<br><br><hr size="1">';
}

$PATH_INFO_examples = array(
	'fltr[]=sep;200x200;'.$img['portrait'],
	'f=png;fltr[]=wmt|hello;fltr[]=flip|y;fltr[]=wmt|hello;200x100;new=FF00FF',
);

echo '<a href="#" name="pathinfo" title="click to get URL link for PATH_INFO example" onClick="prompt(\'Here is the link to the PATH_INFO example\', \'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'#pathinfo\'); return false;">#pathinfo</a>';
echo '<table border="0"><tr><td style="padding: 20px; background-image: url(../'.$img['background'].');">';
foreach ($PATH_INFO_examples as $key => $value) {
	echo ' <img src="'.$phpThumbBase.'/'.$value.'" alt=""> ';
}
echo '</td></tr></table>';
echo '<pre>';
foreach ($PATH_INFO_examples as $key => $value) {
	echo htmlentities(' <img src="'.$phpThumbBase.'/'.$value.'" alt=""> ')."\n";
}
echo '</pre>';
echo 'PATH_INFO example<br>';
echo '<br><br><hr size="1">';

?>


<a name="gd1vs2"></a><br>
<table border="5" cellspacing="0" cellpadding="3" width="500">
	<tr>
		<td colspan="4">
			<b>Illustration of potential difference between GD1.x and GD2.x</b><br>
			In most cases the thumbnails produced by phpThumb() on GD v1.x are perfectly
			acceptable, but in some cases it may look ugly. Diagonal lines and reducing a
			very large source image increase chance for bad results (the house/sky picture
			has both problems). Here are three static examples:
		</td>
	</tr>
	<tr>
		<td><b>GD v2.0.15</b></td>
		<td><img src="../images/PHP-GD2-kayak.jpg"  width="200" height="133" border="0" alt="kayak.jpg generated with phpThumb() on GD v2.0.15"></td>
		<td><img src="../images/PHP-GD2-bottle.jpg" width="100" height="152" border="0" alt="bottle.jpg generated with phpThumb() on GD v2.0.15"></td>
		<td><img src="../images/PHP-GD2-sky.jpg"    width="200" height="150" border="0" alt="sky.jpg generated with phpThumb() on GD v2.0.15"></td>
	</tr>
	<tr>
		<td><b>GD v1.6.2</b></td>
		<td><img src="../images/PHP-GD1-kayak.jpg"  width="200" height="133" border="0" alt="kayak.jpg generated with phpThumb() on GD v1.6.2"></td>
		<td><img src="../images/PHP-GD1-bottle.jpg" width="100" height="152" border="0" alt="bottle.jpg generated with phpThumb() on GD v1.6.2"></td>
		<td><img src="../images/PHP-GD1-sky.jpg"    width="200" height="150" border="0" alt="sky.jpg generated with phpThumb() on GD v1.6.2"></td>
	</tr>
</table><br>
<hr size="1">
<br>
<a name="showpic"></a>
<b>Demo of <i>phpThumb.demo.showpic.php</i></b><br>
<br>
<?php
echo 'Small picture (400x300), window opened at wrong size (640x480):<br>';
echo '<i>(mouse-over to see calling parameters)</i><br>';
echo '<img src="'.$phpThumbBase.'?src='.$img['small'].'&amp;w=100" border="2" alt=""><br>';
$SmallParams = array(
	'unmodified'     => '',
	'text watermark' => '&fltr[]=wmt|Watermark|20|C|FF0000|arial.ttf|100',
);
foreach ($SmallParams as $description => $moreparams) {
	echo '<a title="phpThumb.demo.showpic.php?src='.htmlentities($img['small'].$moreparams).'" href="#" onClick="window.open(\'phpThumb.demo.showpic.php?src='.htmlentities($img['small'].$moreparams.'&title=This+is+a+small+picture').'\', \'showpic1\', \'width=640,height=480,resizable=no,status=no,menubar=no,toolbar=no,scrollbars=no\'); return false;">'.htmlentities($description).'</a> ';
}
?>
<br>
<br>
<?php
echo 'Big picture (2272x1704), window opened at wrong size (640x480):<br>';
echo '<i>(mouse-over to see calling parameters)</i><br>';
echo '<img src="'.$phpThumbBase.'?src='.$img['big'].'&amp;w=100" border="2" alt=""><br>';
$BigParams = array(
	'unmodified'           => '',
	'width=800'            => '&w=800',
	'width=200, grayscale' => '&w=200&fltr[]=gray',
);
foreach ($BigParams as $description => $moreparams) {
	echo '<a title="phpThumb.demo.showpic.php?src='.htmlentities($img['big'].$moreparams).'" href="#" onClick="window.open(\'phpThumb.demo.showpic.php?src='.htmlentities($img['big'].$moreparams.'&title=This+is+a+big+picture').'\', \'showpic2\', \'width=640,height=480,resizable=yes,status=no,menubar=no,toolbar=no,scrollbars=no\'); return false;">'.htmlentities($description).'</a> ';
}
?>
<br>
<hr size="1">
<?php
echo 'The source images, without manipulation:<ul>';
foreach ($img as $key => $value) {
	echo '<li><a href="../'.$value.'">'.basename($value).'</a></li>';
}
echo '</ul><hr>';
?>
</body>
</html>