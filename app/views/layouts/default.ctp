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
							__('login',true),
							array('controller' => 'users', 'action' => 'login')
						);
					?>
					</li>
				<? } ?>
				<? if(!$session->check('Auth.User.id') || $session->read('Auth.User.registered') == 0) { ?>
				<li>
					<?=$html->link(
							__('register',true),
							array('controller' => 'users', 'action' => 'register')
						);
					?>
				</li>
				<? } ?>
				<li>
					<?=$html->link(
							__('about',true),
							array('controller' => 'pages', 'action' => 'display', 'about')
						);
					?>
				</li>
				<? if($session->read('Auth.User.id')) { ?>
				<li>
					<?=$html->link(
							__('settings',true),
							'/users/settings/' . $session->read('Auth.User.public_key')
						);
					?>
				</li>
				<? } ?>
				<li>
				    <a href='#'><?php __('change language'); ?></a>
				    <ul>
				        <li><?=$html->link(__('english',true),'/lang/eng')?></li>
				        <li><?=$html->link(__('french',true),'/lang/fre')?></li>
				        <li><?=$html->link(__('chinese',true),'/lang/chi')?></li>
				    </ul>
				</li>
				<? if($session->check('Auth.User.id') && $session->read('Auth.User.permission') != '') { ?>
				<li>
					<?=$html->link(
							__('admin',true),
							array('controller' => 'users', 'action' => 'admin')
						);
					?>
					<ul>
						<li>
							<?=$html->link(
									ucfirst(__('settings',true)),
									array('controller' => 'users', 'action' => 'admin')
								);
							?>
						</li>
						<li>
							<?=$html->link(
									ucfirst(__('Flagged Posts',true)),
									array('controller' => 'users', 'action' => 'flagged')
								);
							?>
						</li>
						<li>
							<?=$html->link(
									ucfirst(__('User Management',true)),
									array('controller' => 'users', 'action' => 'admin_list')
								);
							?>
						</li>
						<li>
							<?=$html->link(
									ucfirst(__('Blacklist',true)),
									array('controller' => 'users', 'action' => 'list_blacklist')
							);
							?>
						</li>
						<li>
							<?=$html->link(
									ucfirst(__('Remote Settings',true)),
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
							__('logout',true),
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
		    	<?=$html->link(__('Questions',true),'/');?>
		    </li>
		    <li><?=$html->link(__('Tags',true),'/tags');?></li>
		    <li><?=$html->link(__('Unsolved',true),'/questions/unanswered');?></li>
		    <li><?=$html->link(__('Users',true),'/users');?></li>
		  </ul>
		  <ul class="tabs" style="float: right;">
			<li>
				<?=$html->link(
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
			<?=$html->link(__('edit', true),'/widgets/edit/' . $widget['Widget']['id'], array('title' => __('Edit this Widget', true)));?>	| 
			<?=$html->link(__('del', true),'/widgets/delete/' . $widget['Widget']['id'], array('title' => __('Delete Widget', true)));?>	
		<? } ?>
		  </div>
		<?
		}
	}
        
	    if(isset($admin) && $admin):
    ?>
	    <?=$html->link($html->image('icons/plugin_edit.png', array('alt' => __('Edit', true))) . __('add widgets to this page', true),
			'/widgets/add' . $html->url(null, false),
			array('escape' => false)
		); ?>
        <? endif; ?>

    </div>
  </div>


  <div id="footer" class="wrapper">
	<div class="left">
    <ul class="tabs">
      <li>
      <?=$html->link(__('home',true),'/');?></li>
	  <li>
      <?=$html->link(__('ask a question',true),'/questions/ask');?></li>

      <li>
      <?=$html->link(__('about',true),'/about');?></li>
    </ul>

	</div>
	<?php
		echo $this->element('coordino');
	?>
  </div>


</div>

</body>
</html>
