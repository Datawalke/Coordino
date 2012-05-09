<?php
/* SVN FILE: $Id: routes.php 7945 2008-12-19 02:16:01Z gwoo $ */
/**
 * Short description for file.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework (http://www.cakephp.org)
 * Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.app.config
 * @since         CakePHP(tm) v 0.2.9
 * @version       $Revision: 7945 $
 * @modifiedby    $LastChangedBy: gwoo $
 * @lastmodified  $Date: 2008-12-18 18:16:01 -0800 (Thu, 18 Dec 2008) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/views/pages/home.ctp)...
 */

if (file_exists(TMP.'installed.txt')) {
    Router::connect('/lang/*', array('controller' => 'p28n', 'action' => 'change'));
    
	Router::connect('/', array('controller' => 'posts', 'action' => 'display', 'recent'));
	Router::connect('/maintenance', array('controller' => 'posts', 'action' => 'maintenance'));
	
	Router::connect('/questions/unanswered', array('controller' => 'posts', 'action' => 'display', 'unanswered'));
    Router::connect('/questions/hot', array('controller' => 'posts', 'action' => 'display', 'hot'));
    Router::connect('/questions/month', array('controller' => 'posts', 'action' => 'display', 'month'));
    Router::connect('/questions/week', array('controller' => 'posts', 'action' => 'display', 'week'));
    Router::connect('/questions/solved', array('controller' => 'posts', 'action' => 'display', 'solved'));
    Router::connect('/search/*', array('controller' => 'posts', 'action' => 'display'));
	Router::connect('/mini_search', array('controller' => 'posts', 'action' => 'miniSearch'));
	Router::connect('/mini_user_search', array('controller' => 'users', 'action' => 'miniSearch'));
	Router::connect('/about', array('controller' => 'pages', 'action' => 'display', 'about'));
    Router::connect('/help', array('controller' => 'pages', 'action' => 'display', 'help'));
	Router::connect('/bugs', array('controller'=> 'bugs', 'action' => 'add'));
	
    Router::connect('/tags', array('controller' => 'tags', 'action' => 'tag_list'));
    Router::connect('/tags/:page', array('controller' => 'tags', 'action' => 'tag_list'), array('pass' => array('page'), 'page' => '[0-9-]+'));
    Router::connect('/tags/:tag_name', array('controller' => 'tags', 'action' => 'find_tag'), array('pass' => array('tag_name'), 'tag_name' => '[A-z-]+'));
    Router::connect('/tags/:tag_name/:page', array('controller' => 'tags', 'action' => 'find_tag'), array('pass' => array('tag_name', 'page'), 'tag_name' => '[A-z-]+', 'page' => '[0-9-]+'));
    Router::connect('/tag_search/*', array('controller' => 'tags', 'action' => 'find_tag'));

	Router::connect('/login', array('controller' => 'users', 'action' => 'login'));
	Router::connect('/access/remote/:name/:email/:timestamp/:key', array('controller' => 'users', 'action' => 'remoteLogin'), array('pass' => array('name', 'email', 'timestamp', 'key'), 'name' => '[A-z0-9-]+', 'timestamp' => '[0-9]+', 'key' => '[A-z0-9-]+'));
	Router::connect('/lost_password', array('controller' => 'users', 'action' => 'lost_password'));
    Router::connect('/admin', array('controller' => 'users', 'action' => 'admin'));
    Router::connect('/admin/flagged', array('controller' => 'users', 'action' => 'flagged'));
    Router::connect('/admin/delete/:public_key', array('controller' => 'users', 'action' => 'adminDelete'), array('pass' => array('public_key'), 'public_key' => '[A-z0-9-]+'));
    Router::connect('/admin/restore/:public_key', array('controller' => 'users', 'action' => 'adminRestore'), array('pass' => array('public_key'), 'public_key' => '[A-z0-9-]+'));
    Router::connect('/admin/users', array('controller' => 'users', 'action' => 'admin_list'));
    Router::connect('/admin/users/:page', array('controller' => 'users', 'action' => 'admin_list'), array('pass' => array('page'), 'page' => '[0-9-]+'));
    Router::connect('/admin/promote/:public_key', array('controller' => 'users', 'action' => 'adminPromote'), array('pass' => array('public_key'), 'public_key' => '[A-z0-9-]+'));
    Router::connect('/admin/demote/:public_key', array('controller' => 'users', 'action' => 'adminDemote'), array('pass' => array('public_key'), 'public_key' => '[A-z0-9-]+'));
	Router::connect('/admin/blacklist', array('controller' => 'users', 'action' => 'list_blacklist'));
	Router::connect('/admin/blacklist/remove/:word', array('controller' => 'users', 'action' => 'removeWord'), array('pass' => array('word'), 'word' => '[A-z0-9-]+'));
    Router::connect('/admin/blacklist/add', array('controller' => 'users', 'action' => 'add_word'));
    Router::connect('/admin/remote_settings', array('controller' => 'users', 'action' => 'remote_settings'));
	
    Router::connect('/bugs/changeStatus/status/:status', array('controller' => 'bugs', 'action' => 'changeStatus'), array('pass' => array('status'), 'status' => '[A-z0-9-]+'));
    Router::connect('/flag/:public_key', array('controller' => 'posts', 'action' => 'flag'), array('pass' => array('public_key'), 'public_key' => '[A-z0-9-]+'));

/**
 * ...and connect the rest of 'Pages' controller's urls.
 */
	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));
	Router::connect('/register', array('controller' => 'users', 'action' => 'register'));
	Router::connect('/show_off', array('controller' => 'users', 'action' => 'userbarInfo'));

	Router::connect('/questions/ask', array('controller' => 'posts', 'action' => 'ask'));
	Router::connect('/questions/:public_key/:title/edit', array('controller' => 'posts', 'action' => 'edit'), array('pass' => array('public_key'), 'public_key' => '[A-z0-9-]+'));
	Router::connect('/questions/:public_key/:title/answer', array('controller' => 'posts', 'action' => 'answer'), array('pass' => array('public_key'), 'public_key' => '[A-z0-9-]+'));
	Router::connect('/questions/:public_key/comment', array('controller' => 'comments', 'action' => 'comment'), array('pass' => array('public_key'), 'public_key' => '[A-z0-9-]+'));
	Router::connect('/questions/:public_key/correct', array('controller' => 'posts', 'action' => 'markCorrect'), array('pass' => array('public_key'), 'public_key' => '[A-z0-9-]+'));
	Router::connect('/questions/:public_key/:title', array('controller' => 'posts', 'action' => 'view'), array('pass' => array('public_key'), 'public_key' => '[A-z0-9-]+'));
	Router::connect('/answers/:public_key/edit', array('controller' => 'posts', 'action' => 'edit'), array('pass' => array('public_key'), 'public_key' => '[A-z0-9-]+'));

	Router::connect('/tags/:tag', array('controller' => 'tags', 'action' => 'find_tag'), array('pass' => array('tag'), 'tag' => '[A-z0-9-]+'));

    Router::connect('/vote/:public_key/:type', array('controller' => 'votes', 'action' => 'vote'), array('pass' => array('public_key', 'type'), 'public_key' => '[A-z0-9-]+', 'type' => '[A-z]+'));
	
	Router::connect('/users', array('controller' => 'users', 'action' => 'userList'));
    Router::connect('/users/settings/:public_key', array('controller' => 'users', 'action' => 'user_settings'), array('pass' => array('public_key'), 'public_key' => '[A-z0-9-]+'));
    Router::connect('/users/:public_key/upload', array('controller' => 'users', 'action' => 'avatar'), array('pass' => array('public_key'), 'public_key' => '[A-z0-9-]+'));
    Router::connect('/users/:public_key/:title', array('controller' => 'users', 'action' => 'view'), array('pass' => array('public_key'), 'public_key' => '[A-z0-9-]+'));
    Router::connect('/users/:public_key/:title/bar.png', array('controller' => 'users', 'action' => 'userbar'), array('pass' => array('public_key'), 'public_key' => '[A-z0-9-]+'));
    Router::connect('/logout', array('controller' => 'users', 'action' => 'logout'));

	Router::connect('/tags/suggest.js', array('controller' => 'tags', 'action' => 'suggest'));
} else {
	Router::connect('/', array('controller' => 'installer', 'action' => 'start'));
	Router::connect('/install/license', array('controller' => 'installer', 'action' => 'license'));
	Router::connect('/install/database-config', array('controller' => 'installer', 'action' => 'database'));
	Router::connect('/install/run-sql', array('controller' => 'installer', 'action' => 'run_sql'));
	Router::connect('/install/admin-account', array('controller' => 'installer', 'action' => 'admin_account'));
}
?>