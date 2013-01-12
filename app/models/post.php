<?php
class Post extends AppModel {

	var $name = 'Post';
    var $actsAs = array('Revision' => array('limit' => 5, 'ignore' => array('views', 'tags', 'Tag', 'status')));

	var $validate = array(
	    'content' => array(
	        'rule' => array('minLength', '10'),
	        'message' => 'Answers must be at least 10 characters long.'
	    ),
		'title' => array(
	        'rule' => array('minLength', '10'),
	        'message' => 'Titles must be at least 10 characters long.'
	    )
	);
	
	var $belongsTo = array(
			'User' => array(
				'className' => 'User',
				'foreignKey' => 'user_id',
				'fields' => array('User.username', 'User.public_key', 'User.reputation', 'User.image')
			)
		);
		
    var $hasMany = array(
        'Answer' => array(
            'className'     => 'Answer',
            'foreignKey'    => 'related_id',
            'conditions'    => array('Answer.type' => 'answer'),
            'dependent'=> true
        ),
	    'Comment' => array(
	        'className'     => 'Comment',
	        'foreignKey'    => 'related_id',
	        'dependent'=> true
	    )
    );  
	var $hasAndBelongsToMany = array('Tag' =>
	                            array('className'    => 'Tag',
	                                  'joinTable'    => 'post_tags',
	                                  'foreignKey'   => 'post_id',
	                                  'associationForeignKey'=> 'tag_id',
	                                  'conditions'   => '',
	                                  'order'        => '',
	                                  'limit'        => '',
	                                  'unique'       => true,
	                                  'finderQuery'  => '',
	                                  'deleteQuery'  => '',
	                            )
	                            );

    public function niceUrl($url) {
		return preg_replace("/[^0-9a-zA-Z-]/", "", str_replace(' ', '-', $url));
    }

    public function monsterSearch($type, $page, $search) {
        $week_help = explode(",", date('m, d, Y', mktime(1, 0, 0, date('m'), date('d')-date('w'), date('Y'))));
        $week = mktime(00, 00, 00, $week_help['0'], $week_help['1'], $week_help['2']);
        $month_help = explode(",", date('m, d, Y', mktime(1, 0, 0, date('m'), 1, date('Y'))));
        $month = mktime(00, 00, 00, $month_help['0'], $month_help['1'], $month_help['2']);
        $now = time();
        $record = ($page * 15) - 15;

        $this->bindModel(
            array(
                'hasOne' => array(
                    'Setting' => array(
                        'className' => 'Setting',
                        'foreignKey' => 'value'
                    )
                )
            )
        );
        $flag_check = $this->Setting->find(
            'first', array(
                'conditions' => array(
                    'name' => 'flag_display_limit'
                ),
                'fields' => array('value'),
                'recursive' => -1
            )
        );

    if($search == 'no') {
        if($type == 'recent') {
			return $this->find('all', array(
				'contain' => array(
                    'User', 'Tag.tag', 'Answer' => array(
                        'conditions' => array(
                            'Answer.flags <' => $flag_check['Setting']['value']
                        ),
                        'fields' => array('Answer.id')
                    )
                ),
				'conditions' => array(
                    'Post.type' => 'question', 'Post.flags <' => $flag_check['Setting']['value']
                ),
				'order' => 'Post.timestamp DESC',
				'fields' => array(
					'Post.title', 'Post.views', 'Post.url_title',
                    'Post.public_key', 'Post.timestamp', 'User.username',
                    'User.public_key', 'User.image', 'User.reputation'
					),
				'limit' => $record . ',' . 15
			));
        }elseif($type == 'unanswered') {
			return $this->find(
                'all', array(
				    'contain' => array(
                        'User', 'Tag.tag', 'Answer' => array(
                            'conditions' => array(
                                'Answer.flags <' => $flag_check['Setting']['value']
                            ),
                            'fields' => array('Answer.id')
                        )
                    ),
				'conditions' => array(
                    'Post.type' => 'question',
                    'Post.status' => 'open',
                    'Post.flags <' => $flag_check['Setting']['value']),
				'order' => 'Post.timestamp DESC',
				'fields' => array(
					'Post.title', 'Post.views',
                    'Post.url_title', 'Post.public_key',
                    'Post.timestamp', 'User.username', 'User.public_key', 
                    'User.image', 'User.reputation'
					),
				'limit' => $record . ',' . 15
			));
		}elseif($type == 'solved') {
            return $this->find(
                'all', array(
				    'contain' => array(
                        'User', 'Tag.tag', 'Answer' => array(
                            'conditions' => array(
                                'Answer.flags <' => $flag_check['Setting']['value']
                            ),
                            'fields' => array('Answer.id')
                        )
                    ),
				'conditions' => array(
                    'Post.type' => 'question',
                    'Post.status' => 'closed',
                    'Post.flags <' => $flag_check['Setting']['value']),
				'order' => 'Post.timestamp DESC',
				'fields' => array(
					'Post.title', 'Post.views',
                    'Post.url_title', 'Post.public_key',
                    'Post.timestamp', 'User.username', 'User.public_key', 
                    'User.image', 'User.reputation'
					),
				'limit' => $record . ',' . 15
			));
        }elseif($type == 'hot') {
            return $this->find(
                'all', array(
				    'contain' => array(
                        'User', 'Tag.tag', 'Answer' => array(
                            'conditions' => array(
                                'Answer.flags <' => $flag_check['Setting']['value']
                            ),
                            'fields' => array('Answer.id')
                        )
                    ),
				'conditions' => array(
                    'Post.type' => 'question',
                    'Post.flags <' => $flag_check['Setting']['value']),
				'order' => 'Post.views DESC',
				'fields' => array(
					'Post.title', 'Post.views',
                    'Post.url_title', 'Post.public_key',
                    'Post.timestamp', 'User.username', 'User.public_key',
                    'User.image', 'User.reputation'
					),
				'limit' => $record . ',' . 15
			));
        }elseif($type == 'week') {
            return $this->find(
                'all', array(
				    'contain' => array(
                        'User', 'Tag.tag', 'Answer' => array(
                            'conditions' => array(
                                'Answer.flags <' => $flag_check['Setting']['value']
                            ),
                            'fields' => array('Answer.id')
                        )
                    ),
				'conditions' => array(
                    'Post.type' => 'question',
                    'Post.timestamp BETWEEN ? and ?' => array($week, $now),
                    'Post.flags <' => $flag_check['Setting']['value']),
				'order' => 'Post.timestamp DESC',
				'fields' => array(
					'Post.title', 'Post.views',
                    'Post.url_title', 'Post.public_key',
                    'Post.timestamp', 'User.username', 'User.public_key',
                    'User.image', 'User.reputation'
					),
				'limit' => $record . ',' . 15
			));
        }elseif($type == 'month') {
            return $this->find(
                'all', array(
				    'contain' => array(
                        'User', 'Tag.tag', 'Answer' => array(
                            'conditions' => array(
                                'Answer.flags <' => $flag_check['Setting']['value']
                            ),
                            'fields' => array('Answer.id')
                        )
                    ),
				'conditions' => array(
                    'Post.type' => 'question',
                    'Post.timestamp BETWEEN ? and ?' => array($month, $now),
                    'Post.flags <' => $flag_check['Setting']['value']),
				'order' => 'Post.timestamp DESC',
				'fields' => array(
					'Post.title', 'Post.views',
                    'Post.url_title', 'Post.public_key',
                    'Post.timestamp', 'User.username', 'User.public_key',
                    'User.image', 'User.reputation'
					),
				'limit' => $record . ',' . 15
			));
        }
    } else {
            $escapedNeedle = $this->getDataSource()->value($type['needle']);

            return $this->find(
                'all', array(
                    'conditions' => array(
                        "MATCH(Post.content, Post.title) against (" . $escapedNeedle . " IN BOOLEAN MODE)",
                        'Post.type' => 'question',
                        'Post.flags <' => $flag_check['Setting']['value']),
                    'contain' => array(
                        'User', 'Tag.tag', 'Answer' => array(
                            'conditions' => array(
                                'Answer.flags <' => $flag_check['Setting']['value']
                            ),
                            'fields' => array('Answer.id')
                        )
                    ),
                    'fields' => array(
						"match(Post.content, Post.title) against(" . $escapedNeedle . ") as relevance",
                        'Post.title', 'Post.views', 'Post.url_title', 'Post.public_key',
                        'Post.timestamp', 'User.username', 'User.public_key', 'User.image',
                        'User.reputation'),
                    'order' => 'relevance DESC',
                    'limit' => $record . ',' . 15)
            );
        }
    }

    public function monsterSearchCount($type, $search) {
        $week_help = explode(",", date('m, d, Y', mktime(1, 0, 0, date('m'), date('d')-date('w'), date('Y'))));
        $week = mktime(00, 00, 00, $week_help['0'], $week_help['1'], $week_help['2']);
        $month_help = explode(",", date('m, d, Y', mktime(1, 0, 0, date('m'), 1, date('Y'))));
        $month = mktime(00, 00, 00, $month_help['0'], $month_help['1'], $month_help['2']);
        $now = time();

        $this->bindModel(
            array(
                'hasOne' => array(
                    'Setting' => array(
                        'className' => 'Setting',
                        'foreignKey' => 'value'
                    )
                )
            )
        );
        $flag_check = $this->Setting->find(
            'first', array(
                'conditions' => array(
                    'name' => 'flag_display_limit'
                ),
                'fields' => array('value'),
                'recursive' => -1
            )
        );

        if($search == 'no') {
            if($type == 'recent' || $type == 'hot') {
                return $this->find(
                    'all', array(
                        'fields' => 'COUNT(Post.title) as count',
                        'conditions' => array(
                            'Post.type' => 'question',
                            'Post.flags <' => $flag_check['Setting']['value']))
                );
            }elseif($type == 'unanswered') {
                return $this->find(
                    'all', array(
                        'fields' => 'COUNT(Post.title) as count',
                        'conditions' => array(
                            'Post.type' => 'question',
                            'Post.status' => 'open',
                            'Post.flags <' => $flag_check['Setting']['value']))
                );
            }elseif($type == 'solved') {
                return $this->find(
                    'all', array(
                        'fields' => 'COUNT(Post.title) as count',
                        'conditions' => array(
                            'Post.type' => 'question',
                            'Post.status' => 'closed',
                            'Post.flags <' => $flag_check['Setting']['value']))
                );
            }elseif($type == 'week') {
                return $this->find(
                    'all', array(
                        'fields' => 'COUNT(Post.title) as count',
                        'conditions' => array(
                            'Post.type' => 'question',
                            'Post.timestamp BETWEEN ? and ?' => array($week, $now),
                            'Post.flags <' => $flag_check['Setting']['value']))
                );
            }elseif($type == 'month') {
                return $this->find(
                    'all', array(
                        'fields' => 'COUNT(Post.title) as count',
                        'conditions' => array(
                            'Post.type' => 'question',
                            'Post.timestamp BETWEEN ? and ?' => array($month, $now),
                            'Post.flags <' => $flag_check['Setting']['value']))
                );
            }
        }else {
            return $this->find(
                'all', array(
                    'fields' => 'COUNT(Post.title) as count',
                    'conditions' => array(
                        'Post.type' => 'question',
                        "match(content, title) against('" . $type['needle'] . "')",
                        'Post.flags <' => $flag_check['Setting']['value']))
            );
        }
    }

    public function correctRedirect($public_key) {
        $this->Post->recursive = -1;
        $post = $this->find(
            'first', array(
                'conditions' => array('Post.public_key' => $public_key),                          
                'fields' => array('Post.url_title', 'Post.related_id', 'Post.public_key')
            )
        );
        $question = $post;
        if($post['Post']['related_id'] != 0) {
            $post = $this->find(
                'first', array(
                    'conditions' => array('Post.id' => $post['Post']['related_id']),
                    'fields' => array('Post.public_key', 'Post.url_title')
                )
            );
        }
        return $post;
    }
}
?>