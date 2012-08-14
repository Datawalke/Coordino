<?php
class PostsController extends AppController {

	var $name = 'Posts';
	var $uses = array('Post', 'User', 'Answer', 'History', 'Setting', 'Tag', 'PostTag', 'Vote', 'Widget');
	var $components = array('Auth', 'Session', 'Markdownify', 'Markdown', 'Cookie', 'Email', 'Recaptcha', 'Htmlfilter');
	var $helpers = array('Javascript', 'Time', 'Cache', 'Thumbnail', 'Recaptcha', 'Session');
	//var $cacheAction = "1 hour";
	
	public function beforeRender() {
		$this->getWidgets();
		$this->underMaintenance();
	}
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('ask', 'view', 'answer', 'display', 'miniSearch', 'maintenance');
		$this->isAdmin($this->Auth->user('id'));
		
		$this->Cookie->name = 'user_cookie';
		$this->Cookie->time =  604800;  // or '1 hour'
		$this->Cookie->path = '/'; 
		$this->Cookie->domain = $_SERVER['SERVER_NAME'];   
		$this->Cookie->key = 'MZca3*f113vZ^%v ';

		/**
		 * If a user leaves the site and the session ends they will be relogged in with their cookie information if available.
		 */
		if($this->Cookie->read('User')) {
			$this->Auth->login($this->Cookie->read('User'));
		}
	}
	
	public function afterFilter() {
		$this->Session->delete('errors');
	}

	public function delete($id) {
        if(!$this->isAdmin($this->Auth->user('id'))) {
			$this->Session->setFlash(__('You do not have permission to access that..',true), 'error');
			$this->redirect('/');
        }
		$this->Post->delete($id);
		$this->Session->setFlash(__('Post deleted',true), 'error');
		$this->redirect('/');
	}
	
	public function ask() {
		$this->set('title_for_layout', __('Ask a question',true));
		
		if(!empty($this->data)) {
			
			/**
			 * reCAPTCHA Check
			 */
				$this->data['reCAPTCHA'] = $this->params['form'];
				$this->__validatePost($this->data, '/questions/ask', true);
			
				/**
				 * If the user is not logged in create an account for them.
				 */
				if(!empty($this->data['User'])) {
					$user = $this->__userSave(array('User' => $this->data['User']));
					$this->Auth->login($user);
				}
			
				/**
				 * Add in required Post data
				 */
				if(!empty($user)) {
					$userId = $user['User']['id'];
				} else {
					$userId = $this->Auth->user('id');
				}
				$post = $this->__postSave('question', $userId, $this->data);
			
				$this->redirect('/questions/' . $post['public_key'] . '/' . $post['url_title']);
		}
		
	}
	
	public function answer($public_key) {
		$question = $this->Post->findByPublicKey($public_key);
		
		if(!empty($this->data)) {
				$this->data['reCAPTCHA'] = $this->params['form'];
				$this->__validatePost($this->data, '/questions/' . $question['Post']['public_key'] . '/' . $question['Post']['url_title'] . '#user_answer', true);
				
				if(!empty($this->data['User'])) {
					$user = $this->__userSave(array('User' => $this->data['User']));
					$this->Auth->login($user);
				}
		
				if(!empty($user)) {
					$userId = $user['User']['id'];
                    $username = $user['User']['username'];
				} else {
					$userId = $this->Auth->user('id');
                    $username = $this->Auth->user('username');
				}

                $flag_limit = $this->Setting->find(
                	'first', array(
                		'conditions' => array('name' => 'flag_display_limit')
                	)
                );
                
                $post = $this->__postSave('answer', $userId, $this->data, $question['Post']['id']);
               	
                if(($question['Post']['notify'] == 1) && ($post['flags'] < $flag_limit['Setting']['value'])) {
                    $user = $this->User->find(
                        'first', array(
                            'conditions' => array(
                                'User.id' => $question['Post']['user_id']
                            ),
                            'fields' => array('User.email', 'User.username')
                        )
                    );

                $this->set('question', $question);
                $this->set('username', $username);
                $this->set('dear', $user['User']['username']);
                $this->set('answer', $this->data['Post']['content']);
                $this->Email->from = 'Answerman <answers@' . $_SERVER['SERVER_NAME'] . '>';
                $this->Email->to = $user['User']['email'];
                $this->Email->subject = __('Your question has been answered!',true);
                $this->Email->template = 'notification';
                $this->Email->sendAs = 'both';
                $this->Email->send();
                }
				
				$this->redirect('/questions/' . $question['Post']['public_key'] . '/' . $question['Post']['url_title']);
		}




	}

	/**
	 * Validates the Post data.
	 * Since Posts need to validate for both logged in and non logged in accounts a separate validation technique was needed.
	 *
	 * @param string $data
	 * @param string $redirectUrl
	 * @return void
	 */
	public function __validatePost($data, $redirectUrl, $reCaptcha = false) {
		$this->Post->set($data);
		$this->User->set($data);
		$errors = array();
		$recaptchaErrors = array();
		
		if($reCaptcha == true) {
			if(!$this->Recaptcha->valid($data['reCAPTCHA'])) {
				$data['Post']['content'] = $this->Markdownify->parseString($data['Post']['content']);
				$recaptchaErrors = array('recaptcha' => __('Invalid reCAPTCHA entered.',true));
				$errors = array(
					'errors' => $recaptchaErrors,
					'data' => $data
					);
				$this->Session->write(array('errors' => $errors));
				$this->redirect($redirectUrl);				
			}
		}
		
		if(!$this->Post->validates() || !$this->User->validates()) {
			$data['Post']['content'] = $this->Markdownify->parseString($data['Post']['content']);
			$validationErrors = array_merge($this->Post->invalidFields(), $this->User->invalidFields(), $recaptchaErrors);
			$errors = array(
				'errors' => $validationErrors,
				'data' => $data
				);
			$this->Session->write(array('errors' => $errors));
			$this->redirect($redirectUrl);
		}
	}

	/**
	 * Saves the Post data for a user
	 *
	 * @param string $type  Either question or answer
	 * @param string $userId the ID of the user posting
	 * @param string $data $this->data
	 * @return array $post The saved Post data.
	 */
	
	public function __postSave($type, $userId, $data, $relatedId = null) {
		/**
		 * Add in required Post data
		 */
		$this->data['Post']['type'] = $type;
		$this->data['Post']['user_id'] = $userId;
		$this->data['Post']['timestamp'] = time();

		if($type == 'question') {
			$this->data['Post']['url_title'] = $this->Post->niceUrl($this->data['Post']['title']);
		}
		if($type == 'answer') {
			$this->data['Post']['related_id'] = $relatedId;
		}

		$this->data['Post']['public_key'] = uniqid();

		if(!empty($this->data['Post']['tags'])) {
			$this->Post->Behaviors->attach('Tag', array('table_label' => 'tags', 'tags_label' => 'tag', 'separator' => ', '));
		}


		/**
		 * Filter out any nasty XSS
		 */
		Configure::write('debug', 0);
		$this->data['Post']['content'] = str_replace('<code>', '<code class="prettyprint">', $this->data['Post']['content']);
		$this->data['Post']['content'] = @$this->Htmlfilter->filter($this->data['Post']['content']);
		
		/**
		 * Spam Protection
		 */ 
		$flags = 0;
		$content = strip_tags($this->data['Post']['content']);
		// Get links in the content
		$links = preg_match_all("#(^|[\n ])(?:(?:http|ftp|irc)s?:\/\/|www.)(?:[-A-Za-z0-9]+\.)+[A-Za-z]{2,4}(?:[-a-zA-Z0-9._\/&=+%?;\#]+)#is", $content, $matches);
		$links = $matches[0];
		
		$totalLinks = count($links);
		$length = strlen($content);

		// How many links are in the body
		// +2 if less than 2, -1 per link if over 2
		if ($totalLinks > 2) {
			$flags = $flags + $totalLinks;
		} else {
			$flags = $flags - 1;
		}
		
		// How long is the body
		// +2 if more then 20 chars and no links, -1 if less then 20
		if ($length >= 20 && $totalLinks <= 0) {
			$flags = $flags - 1;
		} else if ($length >= 20 && $totalLinks == 1) {
			++$flags;
		} else if ($length < 20) {
			$flags = $flags + 2;
		}
		
		// Keyword search
		$blacklistKeywords = $this->Setting->find('first', array('conditions' => array('name' => 'blacklist')));
		$blacklistKeywords = unserialize($blacklistKeywords['Setting']['description']);
		foreach ($blacklistKeywords as $keyword) {
			if (stripos($content, $keyword) !== false) {
				$flags = $flags + substr_count(strtolower($content), $keyword);
			}
		}

		$blacklistWords = array('.html', '.info', '?', '&', '.de', '.pl', '.cn');
		foreach ($links as $link) {
			foreach ($blacklistWords as $word) {
				if (stripos($link, $word) !== false) {
					++$flags;
				}
			}
			
			foreach ($blacklistKeywords as $keyword) {
				if (stripos($link, $keyword) !== false) {
					++$flags;
				}
			}
			
			if (strlen($link) >= 30) {
				++$flags;
			}
		}
		
		// Body starts with...
		// -10 flags
		$firstWord = substr($content, 0, stripos($content, ' '));
		$firstDisallow = array_merge($blacklistKeywords, array('interesting', 'cool', 'sorry'));
		
		if (in_array(strtolower($firstWord), $firstDisallow)) {
			$flags = $flags + 10;
		}
		
		$manyTimes = $this->Post->find('count', array(
			'conditions' => array('Post.content' => $this->data['Post']['content'])
			));
			
		// Random character match
		// -1 point per 5 consecutive consonants
		$consonants = preg_match_all('/[^aAeEiIoOuU\s]{5,}+/i', $content, $matches);
		$totalConsonants = count($matches[0]);
		
		if ($totalConsonants > 0) {
			$flags = $flags + $totalConsonants;
		}
		
		$flags = $flags + $manyTimes;
		
		$this->data['Post']['flags'] = $flags;
		if($flags >= $this->Setting->getValue('flag_display_limit')) {
			$this->data['Post']['tags'] = '';
		}
		/** 
		 * Save the Data
		 */
		if($this->Post->save($this->data)) { 
            if($type == 'question') {
                $this->History->record('asked', $this->Post->id, $this->Auth->user('id'));
            }elseif($type == 'answer') {
                $this->History->record('answered', $this->Post->id, $this->Auth->user('id'));
            }
            $user_info = $this->User->find('first', array('conditions' => array('id' => $userId)));
            if($type == 'question') {
                $this->User->save(array('id' => $userId, 'question_count' => $user_info['User']['question_count'] + 1));
            }elseif($type == 'answer') {
                $this->User->save(array('id' => $userId, 'answer_count' => $user_info['User']['answer_count'] + 1));
            }

            $post = $this->data['Post'];

			/**
			 * Hack to normalize data.
			 * Note this should be added to the Tag Behavior at some point.
			 * This was but in because the behavior would delete the relations as soon as they put them in.
			 */
			$this->Post->Behaviors->detach('Tag');
			$tags = array(
					'id' => $this->Post->id,
					'tags' => ''
				);

			$this->Post->save($tags);

			return $post;
		} else {
			return false;
		}
	}

	/**
	 * Saves the user data and creates a new user account for them.
	 *
	 * @param string $data 
	 * @return void
	 * @todo this should be moved to the model at some point
	 */
	public function __userSave($data) {
		$data['User']['public_key'] = uniqid();
		$data['User']['password'] = $this->Auth->password(uniqid('p'));
		$data['User']['joined'] = time();
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

	/**
	 * Allowes the user to view a question.
	 * If a user tries to view a Post that is a answer type they will be redirected.
	 *
	 * @param string $public_key 
	 * @return void
	 */
	public function view($public_key) {

                /**
		 * Set the Post model to recursive 2 so we can pull back the User's information with each comment.
		 */
		$this->Post->recursive = 2;
		/**
		 * Change to contains. Limit to just question and comment data, no answers.
		 */
	    $this->Post->unbindModel(
		 	array('hasMany' => array('Answer'))
		);
		$question = $this->Post->findByPublicKey($public_key);
		
		
        /*
        *  Look up the flag limit.
        */
        $flag_check = $this->Setting->find(
            'first', array(
                'conditions' => array(
                    'name' => 'flag_display_limit'
                ),
                'fields' => array('value'),
                'recursive' => -1
            )
        );
		/**
		 * Check to see if the post is spam or not. 
		 * If so redirect.
		 */
		if($question['Post']['flags'] >= $flag_check['Setting']['value'] && $this->Setting->repCheck($this->Auth->user('id'), 'rep_edit')) {
			$this->Session->setFlash(__('The question you are trying to view no longer exists.',true), 'error');
			$this->redirect('/');
		}
		
		/**
		 * Even though Post can return an array of answers through associations
		 * we cannot order or sort this data as we need to
		 */
		$this->Answer->recursive = 3;
		$answers = $this->Answer->find('all', array(
				'conditions' => array(
                    'Answer.related_id' => $question['Post']['id'],
                    'Answer.flags <' => $flag_check['Setting']['value']
                ),
				'order' => 'Answer.status DESC'
			));

		if(!empty($question)) {
			$views = array(
					'id' => $question['Post']['id'],
					'views' => $question['Post']['views'] + 1
				);
			$this->Post->save($views);
		}
        if($this->Auth->user('id') && !$this->Setting->repCheck($this->Auth->user('id'), 'rep_edit')) {
            $this->set('rep_rights', 'yeah!');
        }
		$this->set('title_for_layout', $question['Post']['title']);
		$this->set('question', $question);
		$this->set('answers', $answers);
	}

	public function edit($public_key) {
		$question = $this->Post->findByPublicKey($public_key);
        $this->set('title_for_layout', $question['Post']['title']);
        $redirect = $question;
        if(empty($redirect['Post']['title'])) {
            $redirect = $this->Post->findById($redirect['Post']['related_id']);
        }
		if($question['Post']['user_id'] != $this->Auth->user('id') && !$this->isAdmin($this->Auth->user('id')) && !$this->Setting->repCheck($this->Auth->user('id'), 'rep_edit')) {
			$this->Session->setFlash(__('That is not your question to edit, and you need more reputation!',true), 'error');
			$this->redirect('/questions/' . $redirect['Post']['public_key'] . '/' . $redirect['Post']['url_title']);
		}
        if(!empty($question['Post']['title'])) {
        $tags = $this->PostTag->find(
            'all', array(
                'conditions' => array(
                    'PostTag.post_id' =>  $question['Post']['id']
                )
            )
        );
        $this->Tag->recursive = -1;
        foreach($tags as $key => $value) {
            $tag_names[$key] = $this->Tag->find(
                'first', array(
                    'conditions' => array(
                        'Tag.id' => $tags[$key]['PostTag']['tag_id']
                    ),
                    'fields' => array('Tag.tag')
                )
            );
            if($key == 0) {
                $tag_list = $tag_names[$key]['Tag']['tag'];
            }else {
                $tag_list = $tag_list . ', ' . $tag_names[$key]['Tag']['tag'];
            }
        }
        $this->set('tags', $tag_list);
        }

        if(!empty($this->data['Post']['tags'])) {
			$this->Post->Behaviors->attach('Tag', array('table_label' => 'tags', 'tags_label' => 'tag', 'separator' => ', '));
		}

		if(!empty($this->data)) {
			$this->data['Post']['id'] = $question['Post']['id'];
			if(!empty($this->data['Post']['title'])) {
				$this->data['Post']['url_title'] = $this->Post->niceUrl($this->data['Post']['title']);
			}
			$this->data['Post']['last_edited_timestamp'] = time();
			if($this->Post->save($this->data)) {
                $this->History->record('edited', $this->Post->id, $this->Auth->user('id'));
				$this->redirect('/questions/' . $redirect['Post']['public_key'] . '/' . $redirect['Post']['url_title']);
			}
		} else {
			$question['Post']['content'] = $this->Markdownify->parseString($question['Post']['content']);
			$this->set('question', $question);
		}

	}

	public function display($type='recent', $page=1) {
		$this->set('title_for_layout', ucwords($type) . ' Questions');
		$this->Post->recursive = -1;

        if(isset($this->passedArgs['type'])) {
            $search = $this->passedArgs['search'];
            if($search == 'yes') {
                $type = array(
                    'needle' => $this->passedArgs['type']
                );
            }else {
                $type = $this->passedArgs['type'];
            }
            $page = $this->passedArgs['page'];
        }elseif(!empty($this->data['Post'])) {
            $type = $this->data['Post'];
            $search = 'yes';
        }else {
            $search = 'no';
        }

        if($page <= 1) {
            $page = 1;
        }else{
            $previous = $page - 1;
            $this->set('previous', $previous);
        }

        $questions = $this->Post->monsterSearch($type, $page, $search);
        $count = $this->Post->monsterSearchCount($type, $search);

        if($count['0']['0']['count'] % 15 == 0) {
            $end_page = $count['0']['0']['count'] / 15;
        }else {
            $end_page = floor($count['0']['0']['count'] / 15) + 1;
        }

        if(($count['0']['0']['count'] - ($page * 15)) > 0) {
            $next = $page + 1;
            $this->set('next', $next);
        }

        $keywords = array('hot', 'week', 'month', 'recent', 'solved', 'unanswered');
        if(($search == 'no') && (!in_array($type, $keywords))) {
            $this->Session->setFlash(__('Invalid search type.',true), 'error');
            $this->redirect('/');
        }

		if(empty($questions)) {
            if(isset($type['needle'])) {
                $this->Session->setFlash(__('No results for',true) . ' "' . $type['needle'] . '"!', 'error');
            }else {
                $this->Session->setFlash(__('No results for',true) . ' "' . $type . '"!', 'error');
            }
			if($this->Post->find('count') > 0) {
            	$this->redirect('/');
			}
		}

        if($search == 'yes') {
            $this->set('type', $type['needle']);
        }else {
            $this->set('type', $type);
        }
        $this->set('questions', $questions);
        $this->set('end_page', $end_page);
        $this->set('current', $page);
        $this->set('search', $search);
	}

	public function miniSearch() {
		Configure::write('debug', 0);
		$this->autoLayout = false;
		$questions = $this->Post->monsterSearch(array('needle' => $_GET['query']), 1, 'yes');
		$this->set('questions', $questions);
	}
	
	public function markCorrect($public_key) {
		$answer = $this->Post->findByPublicKey($public_key);
		
		/**
		 * Check to make sure the Post is an answer
		 */
		if($answer['Post']['type'] != 'answer') {
			$this->Session->setFlash(__('What are you trying to do?',true), 'error');
			$this->redirect('/');
		}

		$question = $this->Post->findById($answer['Post']['related_id']);
		/**
		 * Check to make sure the logged in user is authorized to edit this Post
		 */
		if($question['Post']['user_id'] != $this->Auth->user('id')) {
			$this->Session->setFlash(__('You are not allowed to edit that.',true));
			$this->redirect('/questions/' . $question['Post']['public_key'] . '/' . $question['Post']['url_title']);
		}

        $rep = $this->User->find(
            'first', array(
                'conditions' => array(
                    'User.id' => $answer['Post']['user_id']
                ),
                'fields' => array('User.reputation', 'User.id')
            )
        );

		/**
		 * Set the Post as correct, and its question as closed.
		 */
        $quest = array(
            'id' => $question['Post']['id'],
            'status' => 'closed'
        );
        $answ = array(
            'id' => $answer['Post']['id'],
            'status' => 'correct'
        );
        $user = array(
            'User' => array(
                'id' => $rep['User']['id'],
                'reputation' => $rep['User']['reputation'] + 15
            )
        );
		$this->Post->save($answ);
        $this->Post->save($quest);
        $this->User->save($user);
		$this->redirect('/questions/' . $question['Post']['public_key'] . '/' . $question['Post']['url_title'] . '#a_' . $answer['Post']['public_key']);

	}

    public function flag($public_key) {
        $redirect = $this->Post->correctRedirect($public_key);
        if(!$this->Auth->user('id')) {
            $this->Session->setFlash(__('You need to be logged in to do that!',true), 'error');
            $this->redirect('/questions/' . $redirect['Post']['public_key'] . '/' . $redirect['Post']['url_title']);
        }elseif(!$this->Setting->repCheck($this->Auth->user('id'), 'rep_flag')) {
            $this->Session->setFlash(__('You need more reputation to do that.',true), 'error');
            $this->redirect('/questions/' . $redirect['Post']['public_key'] . '/' . $redirect['Post']['url_title']);
        }else{
            $flag = $this->Vote->throwFlag($this->Auth->user('id'), $public_key);
            if($flag == 'exists') {
                $this->Session->setFlash(__('You have already flagged that.',true), 'error');
                $this->redirect('/questions/' . $redirect['Post']['public_key'] . '/' . $redirect['Post']['url_title']);
            }elseif($flag == 'success') {
                $this->Session->setFlash(__('Post flagged.',true), 'error');
                $this->redirect('/questions/' . $redirect['Post']['public_key'] . '/' . $redirect['Post']['url_title']);
            }
        }
    }
    
    public function maintenance() {
    }
}
?>