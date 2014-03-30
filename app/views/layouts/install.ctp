<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title><?=$title_for_layout;?> | Coordino</title>
	<?=$html->css('install.css');?>
</head>
<body>
	<?=$html->image('coordino_logo.png');?>
	<?= $session->flash(); ?>
	<?=$content_for_layout;?>
<body>
</html>
