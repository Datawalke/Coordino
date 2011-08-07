<?php
class UsersController extends AppController {

	var $name = 'Users';
	var $uses = array('User', 'Post', 'History', 'Setting', 'Widget');
	var $components = array('Auth', 'Session', 'Cookie', 'Email', 'Recaptcha');
	var $helpers = array('Time', 'Html', 'Form', 'Javascript', 'Number', 'Thumbnail', 'TrickyFileInput', 'Session', 'Recaptcha');
	
	var $allowedTypes = array(
    	'image/jpeg',
    	'image/gif',
    	'image/png',
    	'image/pjpeg',
    	'image/x-png'
  	);

	public function beforeRender() {
		$this->getWidgets();
		$this->underMaintenance();
	}

	public function beforeFilter() {
		parent::beforeFilter();
        $this->Auth->fields = array(
            'username' => 'email', 
            'password' => 'password'
            );
		$this->getWidgets();
		$this->isAdmin();

		$this->Auth->allow('view', 'register', 'userbar', 'remoteLogin', 'users', 'mini_user_search', 'lost_password',
		'userList', 'miniSearch');

	}

	public function login() {
		if($this->Setting->getValue('remote_auth_only') == 'yes') {
			$this->redirect($this->Setting->getValue('remote_auth_login_url'));
		}
	}
	
	// public function edit($id = null) {
	// 	if ($this->Auth->user('id') == $this->User->findById($id)) {
	// 		$this->User->id = $id;
	// 		if (empty($this->data)) {
	// 			$this->data = $this->User->read();
	// 		} else {
	// 			if ($this->User->save($this->data)) {
	// 				$this->Session->setFlash('Your user information has been updated.');
	// 			}
	// 		}
	// 	}
	// 	$this->redirect(array('action' => 'index'));
	// }
	
	public function lost_password() {
		if(!empty($this->data)) {
			$email_exists = $this->User->find(
				'first', array(
					'conditions' => array(
						'email' => $this->data['User']['email']	
					)
				)
			);
			if(!empty($email_exists)) {
				$pass = rand(8, 12);
				$this->data['User']['password'] = $this->Auth->password($pass);
				$this->data['User']['id'] = $email_exists['User']['id'];
				$this->User->save($this->data);
				$this->set('user', $email_exists);
				$this->set('password', $pass);
				$this->Email->from = 'Engine Juice <sam@bravegamer.com>';
                $this->Email->to = $email_exists['User']['email'];
                $this->Email->subject = 'Engine Juice password recovery.';
                $this->Email->template = 'recovery';
                $this->Email->sendAs = 'both';
                $this->Email->send();
                $this->Session->setFlash('Go check your email!', 'error');	
			}else {
				$this->Session->setFlash('No user has that email address.', 'error');
			}
			$this->redirect('/login');
		}
	}
	
	public function logout(){
		if($this->Setting->getValue('remote_auth_only') == 'yes') {
			$this->Auth->logout();
			$this->redirect($this->Setting->getValue('remote_auth_logout_url'));
		}
		$this->redirect($this->Auth->logout());
	}
	
	public function view($public_key) {
		$user = $this->User->findByPublicKey($public_key);
		$this->pageTitle = $user['User']['username'] . '\'s Profile';
		$this->set('user', $user);
		$this->set('recent', $this->History->retrieve($user['User']['id']));
	}
	
	public function user_settings($public_key) {
		if($this->Auth->user('public_key') != $public_key) {
			$this->Session->setFlash('Those are not your settings to change.', 'error');
			$this->redirect('/');	
		}
		$user = $this->User->find(
			'first', array(
				'conditions' => array(
					'public_key' => $public_key
				)
			)
		);
		
		if(empty($this->data)) {
			$this->set('user_info', $user);
		}else {
			$this->set('user_info', $user);
			if($this->Auth->password($this->data['User']['old_password']) == $user['User']['password']) {
				$this->data['User']['password'] = $this->Auth->password($this->data['User']['new_password']);
				$this->data['User']['id'] = $user['User']['id'];
				$this->User->save($this->data);
				$this->Session->setFlash('Settings updated!', 'error');	
			}elseif(empty($this->data['User']['old_password'])) {
				unset($this->data['old_password']);
				unset($this->data['new_password']);
				$this->data['User']['id'] = $user['User']['id'];
				$this->User->save($this->data);
				$this->Session->setFlash('Settings updated, except password.', 'error');
			}else {
				$this->Session->setFlash('Old Password incorrect.  Settings remain unchanged.', 'error');
				$this->redirect('/users/settings/' . $public_key);
			}
			
		}
		
	}

	/**
	 * Logs in the user via a remote method.
	 *
	 * @param string $name 
	 * @param string $email 
	 * @param string $hash  md5($name . $email . $api_key)
	 * @return void
	 */
	public function remoteLogin($name, $email, $timestamp, $hash) {
		$serverHash = md5($name . $email . $timestamp . $this->Setting->getValue('remote_auth_key'));
		if($serverHash != $hash) {
			$this->Session->setFlash('Invalid name, email, timestamp, or authentication key. Please check your conditions and try again.', 'error');
			$this->redirect('/');
		}
		if((time() - $timestamp) > 1800) {
			$this->Session->setFlash('The provided timestamp is too old. Please try again.', 'error');
			$this->redirect('/');		
		}
		$account = $this->User->findByEmail($email);
		if(!empty($account)) {
			$this->Auth->login($account);
		} else {
			$data['User']['username'] = $name;
			$data['User']['email'] = $email;
			$data['User']['registered'] = 1;
			$this->Auth->login($this->__userSave($data));
		}
		$this->redirect('/');
	}
	
	public function __userSave($data) {
		$data['User']['public_key'] = uniqid();
		$data['User']['password'] = $this->Auth->password(uniqid('p'));
		$data['User']['joined'] = time();
		$data['User']['ip'] = $_SERVER['REMOTE_ADDR'];
		$data['User']['url_title'] = $this->Post->niceUrl($data['User']['username']);
        
		/**
		 * Set up cookie data incase they leave the site and the session ends and they have not registered yet
		 */
		$this->Cookie->write(array('User' => $data['User']));
		
		/**
		 * Save the data
		 */
		$this->User->save($data);	
		
		$data['User']['id'] = $this->User->id;
		
		return $data;
	}
	
	public function userList() {
		
	}
	
	public function register() {
		$this->pageTitle = 'Register';
		
		if($this->Session->read('Auth.User.registered') == 1) {
			$this->Session->setFlash('You are already registered.');
			$this->redirect('/');
		}
		
		/**
		 * If the user has an unregistered account update the password and set them to registered.
		 */
		if(!empty($this->data)) {

			if($this->Recaptcha->valid($this->params['form']) || $this->Session->read('Auth.User.id')) {
			
			/**
			 * If the user is logged in via Session or Cookie
			 */
			if($this->Auth->user('id')) {
				$user = $this->User->read(null, $this->Auth->user('id'));
				$user['User']['password'] = $this->Auth->password($this->data['User']['secret']);
				$user['User']['registered'] = '1';
				
				/**
				 * Save the user information.
				 */
				if($this->User->save($user)) {
					/**
					 * Push the new registered state to the session.
					 */
					$this->Session->write('Auth.User.registered', 1);
					$this->Session->setFlash('You have been registered! Welcome to the community.');
					$this->redirect('/users/' . $this->Auth->user('public_key') . '/' . $this->Auth->user('url_title'));
				} else {
					$this->Session->setFlash('There was an error with your request.');
				}
			} else {
				/**
				 * Register a new user
				 */
				$this->data['User']['password'] = $this->Auth->password($this->data['User']['secret']);

				$this->data['User']['registered'] = '1';
				$this->data['User']['public_key'] = uniqid();
				$this->data['User']['joined'] = time();
				$this->data['User']['url_title'] = $this->Post->niceUrl($this->data['User']['username']);

				if($this->User->save($this->data)) {
					$this->Auth->login($this->data);
					$this->redirect('/');
				}
			}
			
		} else {
			$this->Session->setFlash('Invalid reCAPTCHA entered.', 'error');
		}
		
		}
	}

    public function admin() {
		$this->pageTitle = 'Settings';
        if(!$this->Auth->user('id')) {
            $this->Session->setFlash('You must be logged in to do that.', 'error');
            $this->redirect('/login');
        }
        if($this->User->adminCheck($this->Auth->user('id'), 'update')) {
            $this->Session->setFlash('You are not allowed to do that.', 'error');
            $this->redirect('/');
        }
        $settings = $this->Setting->find(
        	'all', array(
        		'conditions' => array(
        			'OR' => array(
        				'name' => array('rep_vote_up', 'rep_comment', 'rep_vote_down',
        								'rep_advertising', 'rep_edit', 'rep_flag',
        								'flag_display_limit')
        			)
        		)
        	)
        );
        $this->set('settings', $settings);

        if($this->data) {
            foreach($this->data['Setting'] as $key => $value) {
                $data = array(
                    'id' => $key + 1,
                    'value' => $this->data['Setting'][$key]['value']
                );
                $this->Setting->save($data);
                $count = count($this->data);
            }
                $this->Session->setFlash('Settings updated.', 'error');
                $this->redirect('/admin');
        }
    }

    public function flagged() {
		$this->pageTitle = 'Flagged Posts';
        if(!$this->Auth->user('id')) {
            $this->Session->setFlash('You must be logged in to do that.', 'error');
            $this->redirect('/login');
        }elseif($this->User->adminCheck($this->Auth->user('id'), 'update')) {
            $this->Session->setFlash('You are not allowed to do that.', 'error');
            $this->redirect('/');
        }
        $setting = $this->Setting->find(
            'first', array(
                'conditions' => array(
                    'Setting.name' => 'flag_display_limit'
                )
            )
        );
        $posts = $this->Post->find(
            'all', array(
                'conditions' => array(
                    'Post.flags >=' => $setting['Setting']['value']
                )
            )
        );
        $posts = array_reverse($posts);
        $this->set('questions', $posts);
    }

    public function adminDelete($public_key) {
        if(!$this->Auth->user('id')) {
            $this->Session->setFlash('You must be logged in.', 'error');
            $this->redirect('/login');
        }elseif($this->User->adminCheck($this->Auth->user('id'), 'delete')) {
            $this->Session->setFlash('You are not allowed to do that.', 'error');
            $this->redirect('/');
        }
        $post_id = $this->Post->find(
            'first', array('conditions' => array('Post.public_key' => $public_key),
                           'fields' => array('Post.id'))
        );
        $this->Post->del($post_id['Post']['id']);
        $this->Session->setFlash('Post deleted successfully!', 'error');
        $this->redirect('/admin/flagged');
    }

    public function adminRestore($public_key) {
        if(!$this->Auth->user('id')) {
            $this->Session->setFlash('You must be logged in to do that.', 'error');
            $this->redirect('/login');
        }elseif($this->User->adminCheck($this->Auth->user('id'), 'update')) {
            $this->Session->setFlash('You are not allowed to do that.', 'error');
            $this->redirect('/');
        }
        $post = $this->Post->find(
            'first', array('conditions' => array('Post.public_key' => $public_key),
                           'fields' => array('Post.id'))
        );
        $restored_post = array(
            'id' => $post['Post']['id'],
            'flags' => 0
        );
        $this->Post->save($restored_post);
        $this->Session->setFlash('Post restored successfully!', 'error');
        $this->redirect('/admin/flagged');
    }

	public function miniSearch($page=null) {
		Configure::write('debug', 0);
		$this->autoLayout = false;
		$users = $this->User->find('all', array(
			'conditions' => array(
				"User.username LIKE" => '%' . $_GET['query'] . '%'),
			'fields' => array('User.username', 'User.public_key', 'User.reputation', 'User.image'),
			'order' => 'User.reputation DESC',
			'limit' => 42	
				));
		$this->set('users', $users);
	}


    public function admin_list($page=null) {
		$this->pageTitle = 'Appoint An Admin';
        if($page < 1 || !is_numeric($page) || !isset($page)) {
            $page = 1;
        }

        $users = $this->User->find('all', array('order' => 'username ASC'));
        $user_count = count($users);
        if(($user_count - ($page * 100)) > 0) {
            $this->set('next', $page + 1);
        }
        if($page >= 2) {
            $this->set('previous', $page - 1);
        }
        if(($user_count % 100) == 0) {
            $end_page = $user_count / 100;
        }else {
            $end_page = floor($user_count / 100) + 1;
        }
        $loop_fuel = (($page * 100) - 100) - 1;
        $this->set('end_page', $end_page);
        $this->set('current', $page);
        $this->set('users', $users);
        $this->set('loop_fuel', $loop_fuel);
    }

    public function adminPromote($public_key) {
		$this->pageTitle = 'Promote a user to Administrator';
        if(!$this->Auth->user('id')) {
            $this->Session->setFlash('You must be logged in to do that!', 'error');
            $this->redirect('/login');
        }elseif($this->User->adminCheck($this->Auth->user('id'), 'create')) {
            $this->Session->setFlash('You are not allowed to do that.', 'error');
            $this->redirect('/');
        }
        $user = $this->User->find(
            'first', array('conditions' => array('public_key' => $public_key))
        );
        $permission = serialize(array('create', 'read', 'update', 'delete', 'admin'));
        $new_admin = array('id' => $user['User']['id'], 'permission' => $permission);
        $this->User->save($new_admin);

        $this->Session->setFlash('' . $user['User']['username'] . ' is now an administrator.', 'error');
        $this->redirect('/admin/users');

    }
    public function adminDemote($public_key) {
        if(!$this->Auth->user('id')) {
            $this->Session->setFlash('You must be logged in to do that!', 'error');
            $this->redirect('/login');
        }elseif($this->User->adminCheck($this->Auth->user('id'), 'create')) {
            $this->Session->setFlash('You are not allowed to do that.', 'error');
            $this->redirect('/');
        }
        $user = $this->User->find(
            'first', array('conditions' => array('public_key' => $public_key))
        );
        $new_admin = array('id' => $user['User']['id'], 'permission' => '');
        $this->User->save($new_admin);

        $this->Session->setFlash('' . $user['User']['username'] . ' is no longer an administrator.', 'error');
        $this->redirect('/admin/users');

    }
    
    public function list_blacklist() {
		$this->pageTitle = 'Spam Filter Words';
    	if($this->User->adminCheck($this->Auth->user('id'), 'update')) {
    		$this->Session->setFlash('You can\'t do that', 'error');
    		$this->redirect('/');
    	}
    	$this->set('list', $this->Setting->getBlacklist());
    }
    
    public function removeWord($word) {
    	if($this->User->adminCheck($this->Auth->user('id'), 'update')) {
    		$this->Session->setFlash('You can\'t do that', 'error');
    		$this->redirect('/');
    	}
    	$blacklist = $this->Setting->getBlacklist();
    	if(in_array($word, $blacklist)) {
    		$unset_this = array_keys($blacklist, $word);
    		unset($blacklist[$unset_this['0']]);
    		$this->Setting->updateBlacklist(array_values($blacklist));
    		$this->Session->setFlash('Word removed from the blacklist.', 'error');
    		$this->redirect('/admin/blacklist');
    	}else {
    		$this->Session->setFlash('That word isn\'t on the list', 'error');
    		$this->redirect('/');	
    	}
    }
    
    public function add_word() {
		$this->pageTitle = 'Add Spam Words';
    	if($this->User->adminCheck($this->Auth->user('id'), 'update')) {
    		$this->Session->setFlash('You can\'t do that', 'error');
    		$this->redirect('/');
    	}
    	$blacklist = $this->Setting->getBlacklist();
    	if(!empty($this->data)) {
    		$blacklist[] = $this->data['Setting']['word'];
    		$this->Setting->updateBlacklist($blacklist);
    		$this->Session->setFlash('Your word is now on the blacklist!', 'error');
    		$this->redirect('/admin/blacklist');
    	}
    }
    
    public function remote_settings() {
		$this->pageTitle = 'Remote Auth Settings';
    	if($this->User->adminCheck($this->Auth->user('id'), 'update')) {
    		$this->Session->setFlash('You can\'t do that!', 'error');
    		$this->redirect('/');
    	}
    	$this->set('selected', 'Remote Settings');
    	$find = $this->Setting->find('all', array('conditions' => array('name LIKE' => 'remote%')));
    	$this->set('settings', $find);
    	if(!empty($this->data)) {
    		$this->Setting->save(array('id' => $find['1']['Setting']['id'], 'value' => $this->data['1']['Setting']['value']));
    		$this->Setting->save(array('id' => $find['2']['Setting']['id'], 'value' => $this->data['2']['Setting']['value']));
    		$this->Setting->save(array('id' => $find['0']['Setting']['id'], 'value' => $this->data['0']['Setting']['value']));
    		$this->Session->setFlash('Settings updated.', 'error');
    	}
    }
    
    public function avatar() {
		if(!empty($this->data['Upload']['file'])) {
			/* check all image parameters */
			$this->__checkImgParams();
						
			$user = $this->User->findById($this->Auth->user('id'));
			$uploadPath = WWW_ROOT . 'img/uploads/users/';
			$uploadFile = $uploadPath . $this->Auth->user('public_key') . '-' . $this->data['Upload']['file']['name'];
			
			$directory = dir($uploadPath); 
			if(!empty($user['User']['image'])) {
				unlink(WWW_ROOT . $user['User']['image']);
			}
			$directory->close();

			if(move_uploaded_file($this->data['Upload']['file']['tmp_name'], $uploadFile)) {
				$user['User']['image'] = '/img/uploads/users/' . $this->Auth->user('public_key') . '-' . $this->data['Upload']['file']['name'];
				$this->User->id = $user['User']['id'];
				$this->User->save($user);
				
				$this->Session->setFlash('Your profile picture has been set!', 'error');
				$this->redirect(Controller::referer('/'));
			}
			else {
				$this->Session->setFlash('Something went wrong uploading your avatar...', 'error');
				$this->redirect(Controller::referer('/'));
			}
		} else {
			$this->Session->setFlash('We didn\'t catch that avatar, please try again...', 'error');
			$this->redirect(Controller::referer('/'));
		}
	}
	
	function __checkImgParams() {
		/* check file type */
		$this->__checkType($this->data['Upload']['file']['type']);
		
		/* check file size */
		$this->__checkSize($this->data['Upload']['file']['size']);
		
		/* check image dimensions */
		$this->__checkDimensions($this->data['Upload']['file']['tmp_name']);
		
	}
	
	function __checkType($type = null) {
		$valid = false;
    	foreach($this->allowedTypes as $allowedType) {
      		if(strtolower($type) == strtolower($allowedType)){
        		$valid = true;
      		}
    	}
		if(!$valid) {
			$this->Session->setFlash('You tried to upload an invalid type!  Please upload your pictures in jpeg, gif, or png format!', 'error');
			$this->redirect(Controller::referer('/'));
		}
	}
	
	function __checkSize($size = null) {
	    if($size > 1024 * 1024 * 2) {
			$this->Session->setFlash('You tried to upload an image that was too large!  Images must be under 2MB.', 'error');
			$this->redirect(Controller::referer('/'));
		}
	}
	
	function __checkDimensions($filePath) {
		$size = getimagesize($filePath);
		
		if(!$size) {
			$this->Session->setFlash('We could not check that image\'s size, so we can\'t upload it.', 'error');
			$this->redirect(Controller::referer('/'));
		}
		
		$error = '';
		if($size[0] > 800 || $size[1] > 800) {
			$this->Session->setFlash('Images cannot be any larger than 800 by 800 pixels.', 'error');
			$this->redirect(Controller::referer('/'));
		}
	}

}
?>