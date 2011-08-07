<?php
class CommentsController extends AppController {

	var $name = 'Comments';
	var $uses = array('Comment', 'Post', 'History', 'Setting', 'User');
	var $components = array('Auth', 'Session', 'Cookie', 'Markdown');
	var $helpers = array('Javascript');
	
	function beforeRender() {
		$this->underMaintenance();
	}
	public function comment($public_key) {
		if(!$this->Auth->user('id')) {
			$this->Session->setFlash('You need to be logged in to do that!', 'error');
			$this->redirect('/');
		}
        $post = $this->Post->findByPublicKey($public_key);
        $redirect = $post;
        if(empty($redirect['Post']['title'])) {
            $redirect = $this->Post->findById($redirect['Post']['related_id']);
        }
        if(!$this->Setting->repCheck($this->Auth->user('id'), 'rep_comment')) {
            $this->Session->setFlash('You need more reputation to do that!', 'error');
            $this->redirect('/questions/' . $redirect['Post']['public_key'] . '/' . $redirect['Post']['url_title']);
        }
        $user = $this->User->find('first', array('conditions' => array('id' => $this->Auth->user('id'))));

		if(!empty($this->data)) {
			$this->data['Comment']['related_id'] = $post['Post']['id'];
			$this->data['Comment']['content'] = $this->Markdown->parseString(htmlspecialchars($this->data['Comment']['content']));
			$this->data['Comment']['user_id'] = $this->Auth->user('id');
			$this->data['Comment']['timestamp'] = time();
			if($this->Comment->save($this->data)) {
                $this->History->record('commented', $this->Comment->id, $this->Auth->user('id'));
				$this->User->save(array('id' => $user['User']['id'], 'comment_count' => $user['User']['comment_count'] + 1));
                $this->redirect('/questions/' . $redirect['Post']['public_key'] . '/' . $redirect['Post']['url_title']);
            }
		}
	}
}
?>