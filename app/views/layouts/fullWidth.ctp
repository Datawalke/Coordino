<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title><?=$title_for_layout;?> | Coordino</title>
	<?=$html->css('screen.css');?>
	<!--[if IE]>
	<style type="text/css">
	  .wrapper {
	    zoom: 1;     /* triggers hasLayout */
	    }  /* Only IE can see inside the conditional comment
	    and read this CSS rule. Don't ever use a normal HTML
	    comment inside the CC or it will close prematurely. */
	</style>
	<![endif]-->
  <link rel="stylesheet" href="stylesheets/print.css" type="text/css" media="print" charset="utf-8">
  <!--[if lte IE 6]><link rel="stylesheet" href="stylesheets/lib/ie.css" type="text/css" media="screen" charset="utf-8"><![endif]-->
	<script type="text/javascript" src="/js/jquery/jquery.js"></script>
	<script type="text/javascript" src="/js/jquery/jquery.tabs.js"></script>
	<script type="text/javascript" src="/js/jquery/jquery.ui-1.7.2.js"></script>
	<script type="text/javascript" src="/js/jquery/ui.core.js"></script>
	
	<script type="text/javascript">
	 $(function() { $("#tabs").tabs(); }); 
	</script>

</head>
<body onload="prettyPrint()">

<div id="page">

<div class="wrapper" id="header">

	<div class="wrapper">
		<div id="top_actions" class="top_actions">
			<? 
				echo $form->create('Post', array('action' => 'display'));
				echo $form->text('needle', array('value' => 'search', 'onclick' => 'this.value=""'));
				echo $form->end();
			?>
			<ul class="tabs">
				 <? if($session->check('Auth.User.id')) { ?>
					<li>
						<?=$html->link(
								$session->read('Auth.User.username'),
								'/users/' . $session->read('Auth.User.public_key') . '/' . $session->read('Auth.User.username')
							);
						?>
					</li>
				<? } ?>
				<? if(!$session->check('Auth.User.id') || $session->read('Auth.User.registered') == 0) { ?>
				<li>
					<?=$html->link(
							'register',
							array('controller' => 'users', 'action' => 'register')
						);
					?>
				</li>
				<? } ?>
				<li>
					<?=$html->link(
							'about',
							array('controller' => 'pages', 'action' => 'display', 'about')
						);
					?>
				</li>
				<li>
					<?=$html->link(
							'help',
							array('controller' => 'pages', 'action' => 'display', 'help')
						);
					?>
				</li>
				<? if($session->check('Auth.User.id') && $session->read('Auth.User.registered') == 1) { ?>
				<li>
					<?=$html->link(
							'logout',
							array('controller' => 'users', 'action' => 'logout')
						);
					?>
				</li>
				<? } ?>
			</ul>
		</div>
	</div>

	<div class="wrapper">
		  <?=$html->link(
			$html->image('logo.png', array('alt' => 'Logo', 'id' => 'logo')),
			'/',
			null, null, false
		);?>

		  <ul class="tabs">
		    <li>
		    	<?=$html->link(
						'Questions',
						'/'
					);
				?>
		    </li>
		    <li><a href="/tags">Tags</a></li>
		    <li><a href="/questions/unanswered">Unsolved</a></li>
		  </ul>
		  <ul class="tabs" style="float: right;">
			<li>
				<?=$html->link(
						'Ask a Question',
						array('controller' => 'posts', 'action' => 'ask')
					);
				?>
			</li>
		  </ul>
	</div>

</div>
  <div id="body" class="wrapper">
	<div id="fullWidth">
		<?=$content_for_layout;?>
    </div>
  </div><!-- end #body -->
  
	<div id="footer">
		<ul class="tabs">
			<li><a href="/">home</a></li>
			<li><a href="/questions/ask">ask a question</a></li>
			<li><a href="/about">about</a></li>
			<li><a href="/help">help</a></li>
		</ul>

		<p class="quiet"><small>&copy; 2009 Meezik Inc.</small></p>
	</div>
</div>

</body>
<?=$this->element('google_analytics');?>
</html>
