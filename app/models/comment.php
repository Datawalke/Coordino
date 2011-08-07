<?php
class Comment extends AppModel {

	var $name = 'Comment';
	var $belongsTo = array(
			'User' => array(
				'className' => 'User',
				'foreignKey' => 'user_id',
				'fields' => array('User.username', 'User.public_key')
			),
            'Post' => array(
                'className' => 'Post',
                'foreignKey' => 'related_id'
            )
		);
	
}
?>