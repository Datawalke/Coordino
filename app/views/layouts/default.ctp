<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title><?php echo $title_for_layout;?> | Coordino</title>
	<?php echo $html->css('screen.css');?>
	<?php echo $html->css('prettify.css');?>
	<?php echo $html->script('prettify/prettify.js');?>
	<?php echo $html->css('skin.css');?>
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
			<?php 
				echo $form->create('Post', array('action' => 'display'));
				echo $form->text('needle', array('value' => 'search', 'onclick' => 'this.value=""'));
				echo $form->end();
			?>
			<ul class="tabs">
				 <?php if($session->check('Auth.User.id')) { ?>
					<li>
						<?php echo $html->link(
								$session->read('Auth.User.username'),
								'/users/' . $session->read('Auth.User.public_key') . '/' . $session->read('Auth.User.username')
							);
						?>
					</li>
				<?php } ?>
				<?php if(!$session->check('Auth.User.id')) { ?>
					<li>
					<?php echo $html->link(
							__('login',true),
							array('controller' => 'users', 'action' => 'login')
						);
					?>
					</li>
				<?php } ?>
				<?php if(!$session->check('Auth.User.id') || $session->read('Auth.User.registered') == 0) { ?>
				<li>
					<?php echo $html->link(
							__('register',true),
							array('controller' => 'users', 'action' => 'register')
						);
					?>
				</li>
				<?php } ?>
				<li>
					<?php echo $html->link(
							__('about',true),
							array('controller' => 'pages', 'action' => 'display', 'about')
						);
					?>
				</li>
				<?php if($session->read('Auth.User.id')) { ?>
				<li>
					<?php echo $html->link(
							__('settings',true),
							'/users/settings/' . $session->read('Auth.User.public_key')
						);
					?>
				</li>
				<?php } ?>
				<li>
				    <a href='#'><?php __('change language'); ?></a>
				    <ul>
				        <li><?php echo $html->link(__('english',true),'/lang/eng')?></li>
				        <li><?php echo $html->link(__('french',true),'/lang/fre')?></li>
				        <li><?php echo $html->link(__('chinese',true),'/lang/chi')?></li>
				    </ul>
				</li>
				<?php if($session->check('Auth.User.id') && $session->read('Auth.User.permission') != '') { ?>
				<li>
					<?php echo $html->link(
							__('admin',true),
							array('controller' => 'users', 'action' => 'admin')
						);
					?>
					<ul>
						<li>
							<?php echo $html->link(
									ucfirst(__('settings',true)),
									array('controller' => 'users', 'action' => 'admin')
								);
							?>
						</li>
						<li>
							<?php echo $html->link(
									ucfirst(__('Flagged Posts',true)),
									array('controller' => 'users', 'action' => 'flagged')
								);
							?>
						</li>
						<li>
							<?php echo $html->link(
									ucfirst(__('User Management',true)),
									array('controller' => 'users', 'action' => 'admin_list')
								);
							?>
						</li>
						<li>
							<?php echo $html->link(
									ucfirst(__('Blacklist',true)),
									array('controller' => 'users', 'action' => 'list_blacklist')
							);
							?>
						</li>
						<li>
							<?php echo $html->link(
									ucfirst(__('Remote Settings',true)),
									array('controller' => 'users', 'action' => 'remote_settings')
							);
							?>
						</li>
					</ul>
				</li>
				<?php } ?>
				
				<?php if($session->check('Auth.User.id') && $session->read('Auth.User.registered') == 1) { ?>
				<li>
					<?php echo $html->link(
							__('logout',true),
							array('controller' => 'users', 'action' => 'logout')
						);
					?>
				</li>
				<?php } ?>
			</ul>
		</div>
	</div>

	<div class="wrapper">
        <a href="<?php echo $this->webroot; ?>"><?php echo $html->image('logo.png', array('alt' => 'Logo', 'id' => 'logo')); ?></a>

		  <ul class="tabs">
		    <li>
		    	<?php echo $html->link(__('Questions',true),'/');?>
		    </li>
		    <li><?php echo $html->link(__('Tags',true),'/tags');?></li>
		    <li><?php echo $html->link(__('Unsolved',true),'/questions/unanswered');?></li>
		    <li><?php echo $html->link(__('Users',true),'/users');?></li>
		  </ul>
		  <ul class="tabs" style="float: right;">
			<li>
				<?php echo $html->link(
						__('Ask a question',true),
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
		<?php echo $content_for_layout;?>
    </div>
    <div id="sidebar" class="wrapper">

		<?
			if(!empty($widgets)) {
				foreach($widgets as $widget) {
		?>
		<div class="widget_box wrapper">
			<?php if(!empty($widget['Widget']['title'])) {?>
	      		<h3><?php echo $widget['Widget']['title'];?></h3>
			<?php } ?>
			<?php echo $widget['Widget']['content'];?>
		<?php if(isset($admin) && $admin) { ?>
			<?php echo $html->link(__('edit', true),'/widgets/edit/' . $widget['Widget']['id'], array('title' => __('Edit this Widget', true)));?>	| 
			<?php echo $html->link(__('del', true),'/widgets/delete/' . $widget['Widget']['id'], array('title' => __('Delete Widget', true)));?>	
		<?php } ?>
		  </div>
		<?
		}
	}
        
	    if(isset($admin) && $admin):
    ?>
	    <?php echo $html->link($html->image('icons/plugin_edit.png', array('alt' => __('Edit', true))) . __('add widgets to this page', true),
			'/widgets/add' . $html->url(null, false),
			array('escape' => false)
		); ?>
        <?php endif; ?>

    </div>
  </div>


  <div id="footer" class="wrapper">
	<div class="left">
    <ul class="tabs">
      <li>
      <?php echo $html->link(__('home',true),'/');?></li>
	  <li>
      <?php echo $html->link(__('ask a question',true),'/questions/ask');?></li>

      <li>
      <?php echo $html->link(__('about',true),'/about');?></li>
    </ul>

	</div>
	<?php
		echo $this->element('coordino');
	?>
  </div>


</div>

</body>
</html>
