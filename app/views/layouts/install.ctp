<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $title_for_layout; ?> | Coordino</title>
	<?php echo $html->css('install.css'); ?>
    </head>

    <body>
	<?php echo $html->image('coordino_logo.png'); ?>
	<?php echo $session->flash(); ?>
	<?php echo $content_for_layout; ?>
    </body>
</html>
