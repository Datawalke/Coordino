<?php
class User extends AppModel {

	var $name = 'User';
    var $validate = array(
		'username' => array(
			'rule' => array('custom', "/^[A-z0-9 ]+/i"),
			'message' => 'Usernames must contain only letters and numbers'),
			
		'email' => array(
			'emailvalid' => array('rule' => 'email',
								  'message' => 'Not a valid email address'),
			'isunique'   => array('rule' => 'isUnique',
								  'message' => 'Email already in use.')),
		'secret' => array(
			'min' => array(
				'rule' => array('minLength', '4'),
				'message' => 'Password must be at least 4 characters long.'),
			'notempty' => array('rule' => 'notEmpty',
								'message' => 'Password cannot be left empty')
		)
	);

    public function adminCheck($user_id, $action='read') {
        $rights = $this->find(
            'first', array(
                'conditions' => array('User.id' => $user_id),
                'fields' => array('User.permission')
            )

        );

        if(empty($rights['User']['permission'])) {
            return true;
        }else {
            $array = unserialize($rights['User']['permission']);
		    if(in_array($action, $array)) {
                return false;
            }else {
                return true;
            }
        }
    }

}
?>