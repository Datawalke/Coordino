<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title><?php echo $title_for_layout; ?> | Coordino</title>
	<?php echo $html->css('install.css'); ?>
</head>
<body>
	<?php echo $html->image('coordino_logo.png'); ?>
	<?php echo $session->flash(); ?>
	<?php echo $content_for_layout; ?>
<body>
</html>
