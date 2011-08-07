<?php
class VotesController extends AppController {
    var $name = 'Votes';
    var $uses = array('Post', 'Vote', 'User', 'Setting');
	var $helpers = array('Javascript');
	
	function beforeFilter() {
		parent::beforeFilter();
	}
	
    function beforeRender() {
    	$this->underMaintenance();
    }
    
    function vote($public_key, $type) {
        $this->Post->recursive = -1;
        $title = $this->Post->find(
            'first', array(
                'conditions' => array(
                    'Post.public_key' => $public_key
                ),
                'fields' => array('Post.url_title', 'Post.type', 'Post.related_id', 'Post.public_key',
                                  'Post.user_id')
            )
        );
        $check = $title;
        if($title['Post']['type'] == 'answer') {
            $title = $this->Post->find(
                'first', array(
                    'conditions' => array(
                        'Post.id' => $title['Post']['related_id']
                    ),
                    'fields' => array('Post.url_title', 'Post.public_key', 'Post.user_id')
                )
            );
        }
        if(!isset($_SESSION['Auth']['User']['id'])) {
                $this->Session->setFlash('You must be logged in to do that!', 'error');
                $this->redirect('/questions/' . $title['Post']['public_key'] . '/' . $title['Post']['url_title']);
        }
        if($check['Post']['user_id'] == $_SESSION['Auth']['User']['id']) {
            $this->Session->setFlash('You cannot vote for yourself.', 'error');
            $this->redirect('/questions/' . $title['Post']['public_key'] . '/' . $title['Post']['url_title']);
        }

        if($type == 'up') {
            $check_against = 'rep_vote_up';
        }elseif($type == 'down') {
            $check_against = 'rep_vote_down';
        }
        if(!$this->Setting->repCheck($_SESSION['Auth']['User']['id'], $check_against)) {
            $this->Session->setFlash('You need more reputation to do that!', 'error');
            $this->redirect('/questions/' . $title['Post']['public_key'] . '/' . $title['Post']['url_title']);
        }
    $vote = $this->Vote->castVote($_SESSION['Auth']['User']['id'], $public_key, $type);
        if($vote == 'exists') {
            $this->Session->setFlash('You have already voted for that!', 'error');
            $this->redirect('/questions/' . $title['Post']['public_key'] . '/' . $title['Post']['url_title']);
        }else {
            $this->Session->setFlash('Voted successfully!', 'error');
            $this->redirect('/questions/' . $title['Post']['public_key'] . '/' . $title['Post']['url_title']);
        }
    }
}
?>