<?php
class Widget extends AppModel {
	 var $name = 'Widget';
	
	public function findPage($page, $all=true) {
		$conditions = array (
			'or' => array(
				'Widget.page' => $page,
				'Widget.global' => 1
				)
		);
		
		$widgets = $this->find('all',array('conditions' => $conditions));
		$i = 0;
		foreach($widgets as $widget) {
		 	$widgets[$i]['Widget']['content'] = $this->_parseText($widget['Widget']['content']);
			$i++;
		}
		return $widgets;
	}
	
	public function _parseText($text) {
		$tokens = array('[user.username]',
						'[user.reputation]',
						'[user.age]',
						'[user.website]',
						'[user.info]',
						'[user.location]',
						'[user.answer-count]',
						'[user.comment-count]',
						'[user.question-count]',
						'[user.profile-link]');
		
		/**
		 * User/Session Specific
		 */
		$user_username = (empty($_SESSION['Auth']['User']['username'])) ? 'Guest' : $_SESSION['Auth']['User']['username'];
		$user_reputation = (empty($_SESSION['Auth']['User']['reputation'])) ? 'Unknown' : $_SESSION['Auth']['User']['reputation'];
		$user_age = (empty($_SESSION['Auth']['User']['age'])) ? 'Unknown' : $_SESSION['Auth']['User']['age'];
		$user_website = (empty($_SESSION['Auth']['User']['website'])) ? 'Unknown' : $_SESSION['Auth']['User']['website'];
		$user_info = (empty($_SESSION['Auth']['User']['info'])) ? 'Unknown' : $_SESSION['Auth']['User']['info'];
		$user_location = (empty($_SESSION['Auth']['User']['location'])) ? 'Unknown' : $_SESSION['Auth']['User']['location'];
		$user_answer_count = (!isset($_SESSION['Auth']['User']['answer_count'])) ? 'Unknown' : $_SESSION['Auth']['User']['answer_count'];
		$user_comment_count = (!isset($_SESSION['Auth']['User']['comment_count'])) ? 'Unknown' : $_SESSION['Auth']['User']['comment_count'];
		$user_question_count = (!isset($_SESSION['Auth']['User']['question_count'])) ? 'Unknown' : $_SESSION['Auth']['User']['question_count'];
		$user_profile_link = (empty($_SESSION['Auth']['User']['username'])) ? '/' : 'http://' . $_SERVER['HTTP_HOST'] . '/users/' . $_SESSION['Auth']['User']['public_key'] . '/' . $_SESSION['Auth']['User']['username'];
		$values = array($user_username,
						$user_reputation,
						$user_age,
						$user_website,
						$user_info,
						$user_location,
						$user_answer_count,
						$user_comment_count,
						$user_question_count,
						$user_profile_link);
		return str_replace($tokens, $values, $text);
	}
}
?>