<?php
//////////////////////////////////////////////////////////////
///  phpThumb() by James Heinrich <info@silisoftware.com>   //
//        available at http://phpthumb.sourceforge.net     ///
//////////////////////////////////////////////////////////////
///                                                         //
// phpThumb.demo.object.simple.php                          //
// James Heinrich <info@silisoftware.com>                   //
//                                                          //
// Simplified example of how to use phpthumb.class.php as   //
// an object -- please also see phpThumb.demo.object.php    //
//                                                          //
//////////////////////////////////////////////////////////////

// Note: phpThumb.php is where the caching code is located, if
//   you instantiate your own phpThumb() object that code is
//   bypassed and it's up to you to handle the reading and
//   writing of cached files, if appropriate.

require_once('../phpthumb.class.php');

// create phpThumb object
$phpThumb = new phpThumb();

$thumbnail_width = 100;

// set data source -- do this first, any settings must be made AFTER this call
if (is_uploaded_file(@$_FILES['userfile']['tmp_name'])) {
	$phpThumb->setSourceFilename($_FILES['userfile']['tmp_name']);
	$output_filename = './thumbnails/'.basename($_FILES['userfile']['name']).'_'.$thumbnail_width.'.'.$phpThumb->config_output_format;
} else {
	$phpThumb->setSourceData(file_get_contents('..\images\disk.jpg'));
	$output_filename = './thumbnails/disk_small.jpg';
}

// PLEASE NOTE:
// You must set any relevant config settings here. The phpThumb
// object mode does NOT pull any settings from phpThumb.config.php
//$phpThumb->setParameter('config_document_root', '/home/groups/p/ph/phpthumb/htdocs/');
//$phpThumb->setParameter('config_cache_directory', '/tmp/persistent/phpthumb/cache/');

// set parameters (see "URL Parameters" in phpthumb.readme.txt)
$phpThumb->setParameter('w', $thumbnail_width);
//$phpThumb->setParameter('fltr', 'gam|1.2');
//$phpThumb->setParameter('fltr', 'wmi|../watermark.jpg|C|75|20|20');

// generate & output thumbnail
if ($phpThumb->GenerateThumbnail()) { // this line is VERY important, do not remove it!
	if ($phpThumb->RenderToFile($output_filename)) {
		// do something on success
		echo 'Successfully rendered to "'.$output_filename.'"';
	} else {
		// do something with debug/error messages
		echo 'Failed:<pre>'.implode("\n\n", $phpThumb->debugmessages).'</pre>';
	}
} else {
	// do something with debug/error messages
	echo 'Failed:<pre>'.$phpThumb->fatalerror."\n\n".implode("\n\n", $phpThumb->debugmessages).'</pre>';
}

?>