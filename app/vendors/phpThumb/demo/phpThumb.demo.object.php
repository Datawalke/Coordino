<?php
//////////////////////////////////////////////////////////////
///  phpThumb() by James Heinrich <info@silisoftware.com>   //
//        available at http://phpthumb.sourceforge.net     ///
//////////////////////////////////////////////////////////////
///                                                         //
// phpThumb.demo.object.php                                 //
// James Heinrich <info@silisoftware.com>                   //
//                                                          //
// Example of how to use phpthumb.class.php as an object    //
//                                                          //
//////////////////////////////////////////////////////////////

// Note: phpThumb.php is where the caching code is located, if
//   you instantiate your own phpThumb() object that code is
//   bypassed and it's up to you to handle the reading and
//   writing of cached files, if appropriate.


require_once('../phpthumb.class.php');

// create phpThumb object
$phpThumb = new phpThumb();

// create 3 sizes of thumbnail
$thumbnail_widths = array(160, 320, 640);
$capture_raw_data = false; // set to true to insert to database rather than render to screen or file (see below)
foreach ($thumbnail_widths as $thumbnail_width) {
	// this is very important when using a single object to process multiple images
	$phpThumb->resetObject();

	// set data source -- do this first, any settings must be made AFTER this call
	$phpThumb->setSourceFilename('images/loco.jpg');  // for static demo only
	//$phpThumb->setSourceFilename($_FILES['userfile']['tmp_name']);
	// or $phpThumb->setSourceData($binary_image_data);
	// or $phpThumb->setSourceImageResource($gd_image_resource);

	// PLEASE NOTE:
	// You must set any relevant config settings here. The phpThumb
	// object mode does NOT pull any settings from phpThumb.config.php
	//$phpThumb->setParameter('config_document_root', '/home/groups/p/ph/phpthumb/htdocs/');
	//$phpThumb->setParameter('config_cache_directory', '/tmp/persistent/phpthumb/cache/');

	// set parameters (see "URL Parameters" in phpthumb.readme.txt)
	$phpThumb->setParameter('w', $thumbnail_width);
	//$phpThumb->setParameter('h', 100);
	//$phpThumb->setParameter('fltr', 'gam|1.2');
	//$phpThumb->setParameter('fltr', 'wmi|../watermark.jpg|C|75|20|20');

	// set options (see phpThumb.config.php)
	// here you must preface each option with "config_"
	$phpThumb->setParameter('config_output_format', 'jpeg');
	$phpThumb->setParameter('config_imagemagick_path', '/usr/local/bin/convert');
	//$phpThumb->setParameter('config_allow_src_above_docroot', true); // needed if you're working outside DOCUMENT_ROOT, in a temp dir for example

	// generate & output thumbnail
	$output_filename = './thumbnails/'.basename($_FILES['userfile']['name']).'_'.$thumbnail_width.'.'.$phpThumb->config_output_format;
	if ($phpThumb->GenerateThumbnail()) { // this line is VERY important, do not remove it!
		$output_size_x = ImageSX($phpThumb->gdimg_output);
		$output_size_y = ImageSY($phpThumb->gdimg_output);
		if ($output_filename || $capture_raw_data) {
			if ($capture_raw_data && $phpThumb->RenderOutput()) {
				// RenderOutput renders the thumbnail data to $phpThumb->outputImageData, not to a file or the browser
				mysql_query("INSERT INTO `table` (`thumbnail`) VALUES ('".mysql_escape_string($phpThumb->outputImageData)."') WHERE (`id` = '".$id."')");
			} elseif ($phpThumb->RenderToFile($output_filename)) {
				// do something on success
				echo 'Successfully rendered:<br><img src="'.$output_filename.'">';
			} else {
				// do something with debug/error messages
				echo 'Failed (size='.$thumbnail_width.'):<pre>'.implode("\n\n", $phpThumb->debugmessages).'</pre>';
			}
		} else {
			$phpThumb->OutputThumbnail();
		}
	} else {
		// do something with debug/error messages
		echo 'Failed (size='.$thumbnail_width.').<br>';
		echo '<div style="background-color:#FFEEDD; font-weight: bold; padding: 10px;">'.$phpThumb->fatalerror.'</div>';
		echo '<form><textarea rows="10" cols="60" wrap="off">'.htmlentities(implode("\n* ", $phpThumb->debugmessages)).'</textarea></form><hr>';
	}

}

?>