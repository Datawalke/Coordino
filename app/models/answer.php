<?php
class Answer extends AppModel {

	var $name = 'Answer';
	var $useTable = 'posts';
	
	var $belongsTo = array(
			'User' => array(
				'className' => 'User',
				'foreignKey' => 'user_id',
				'fields' => array('User.username', 'User.public_key', 'User.reputation', 'User.image')
			)
		);
		
    var $hasMany = array(
        'Comment' => array(
            'className'     => 'Comment',
            'foreignKey'    => 'related_id',
            'dependent'=> true
        )
    );
	
}
?>