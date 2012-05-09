<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title><?=$title_for_layout;?> | Coordino</title>
	<?=$html->css('screen.css');?>
	<?=$html->css('prettify.css');?>
	<?=$html->script('prettify/prettify.js');?>
	<?=$html->css('skin.css');?>
	<!--[if IE]>
	<style type="text/css">
	  .wrapper {
	    zoom: 1;     /* triggers hasLayout */
	    }  /* Only IE can see inside the conditional comment
	    and read this CSS rule. Don't ever use a normal HTML
	    comment inside the CC or it will close prematurely. */
	</style>
	<![endif]-->	

  <!--[if lte IE 6]><link rel="stylesheet" href="stylesheets/lib/ie.css" type="text/css" media="screen" charset="utf-8"><![endif]-->
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
				<? if(!$session->check('Auth.User.id')) { ?>
					<li>
					<?=$html->link(
							'login',
							array('controller' => 'users', 'action' => 'login')
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
				<? if($session->read('Auth.User.id')) { ?>
				<li>
					<?=$html->link(
							'settings',
							'/users/settings/' . $session->read('Auth.User.public_key')
						);
					?>
				</li>
				<? } ?>
				<? if($session->check('Auth.User.id') && $session->read('Auth.User.permission') != '') { ?>
				<li>
					<?=$html->link(
							'admin',
							array('controller' => 'users', 'action' => 'admin')
						);
					?>
					<ul>
						<li>
							<?=$html->link(
									'Settings',
									array('controller' => 'users', 'action' => 'admin')
								);
							?>
						</li>
						<li>
							<?=$html->link(
									'Flagged Posts',
									array('controller' => 'users', 'action' => 'flagged')
								);
							?>
						</li>
						<li>
							<?=$html->link(
									'User Management',
									array('controller' => 'users', 'action' => 'admin_list')
								);
							?>
						</li>
						<li>
							<?=$html->link(
									'Blacklist',
									array('controller' => 'users', 'action' => 'list_blacklist')
							);
							?>
						</li>
						<li>
							<?=$html->link(
									'Remote Settings',
									array('controller' => 'users', 'action' => 'remote_settings')
							);
							?>
						</li>
					</ul>
				</li>
				<? } ?>
				
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
        <a href="<?=$this->webroot; ?>"><?php echo $html->image('logo.png', array('alt' => 'Logo', 'id' => 'logo')); ?></a>

		  <ul class="tabs">
		    <li>
		    	<?=$html->link(_('Questions'),'/');?>
		    </li>
		    <li><?=$html->link(_('Tags'),'/tags');?></li>
		    <li><?=$html->link(_('Unsolved'),'/questions/unanswered');?></li>
		    <li><?=$html->link(_('Users'),'/users');?></li>
		  </ul>
		  <ul class="tabs" style="float: right;">
			<li>
				<?=$html->link(
						_('Ask a Question'),
						array('controller' => 'posts', 'action' => 'ask')
					);
				?>
			</li>
		  </ul>
	</div>

</div>

  <div id="body" class="wrapper">
    <?php echo $session->flash(); ?>
	<div id="content" class="wrapper">
		<?=$content_for_layout;?>
    </div>
    <div id="sidebar" class="wrapper">

		<?
			if(!empty($widgets)) {
				foreach($widgets as $widget) {
		?>
		<div class="widget_box wrapper">
			<? if(!empty($widget['Widget']['title'])) {?>
	      		<h3><?=$widget['Widget']['title'];?></h3>
			<? } ?>
			<?=$widget['Widget']['content'];?>
		<? if(isset($admin) && $admin) { ?>
			<a href="/widgets/edit/<?=$widget['Widget']['id'];?>" title="Edit this Widget">edit</a>	| 
			<a href="/widgets/delete/<?=$widget['Widget']['id'];?>" title="Delete Widget">del</a>	
		<? } ?>
		  </div>
		<?
		}
	}
        
	    if(isset($admin) && $admin):
    ?>
        <a href="/widgets/add<?php echo $_SERVER['REQUEST_URI']; ?>">
            <img src="/img/icons/plugin_edit.png" alt="Edit"/> add widgets to this page.
        </a>
        <? endif; ?>

    </div>
  </div>


  <div id="footer" class="wrapper">
	<div class="left">
    <ul class="tabs">
      <li><a href="/">home</a></li>
	  <li><a href="/questions/ask">ask a question</a></li>

      <li><a href="/about">about</a></li>
    </ul>

	</div>
	<?php
		echo $this->element('coordino');
	?>
  </div>


</div>

</body>
</html>
