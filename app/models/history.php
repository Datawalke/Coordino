<?php
class History extends AppModel {
    var $name = 'History';

    public function record($type, $related_id, $user_id) {
        $this->create();
        $this->data['History']['related_id'] = $related_id;
        $this->data['History']['type'] = $type;
        $this->data['History']['user_id'] = $user_id;
        $this->data['History']['timestamp'] = time();

        $this->save($this->data);
    }

    public function retrieve($user_id) {
        $this->bindModel(
        	array(
        		'belongsTo' => array(
        			'Setting' => array(
        				'className' => 'Setting',
        				'foreignKey' => 'related_id'
        			)
        		)
        	)
        );
        $this->Setting->recursive = -1;
    	$flag_limit = $this->Setting->find('first', array('conditions' => array('name' => 'flag_display_limit')));
    	$this->bindModel(
            array(
                'belongsTo' => array(
                    'Post' => array(
                        'className' => 'Post',
                        'foreignKey' => 'related_id',
                        'conditions' => array('Post.flags <' => $flag_limit['Setting']['value'])
                    )
                )
            )
        );
        $results = $this->find(
            'all', array(
                'conditions' => array(
                    'History.user_id' => $user_id,
                    'OR' => array(
                        array('History.type' => 'edited'),
                        array('History.type' => 'answered'),
                        array('History.type' => 'asked')
                    )
                ),
                'order' => 'timestamp DESC',
                'fields' => array(
                    'History.type', 'Post.title', 'Post.url_title', 'Post.related_id',
                    'History.timestamp', 'Post.public_key', 'Post.status'
                )
            )
        );
        $this->Post->recursive = -1;
        foreach($results as $key => $value) {
            if(empty($results[$key]['Post']['title'])) {
            	unset($results[$key]);
            }
        }
        $results = array_values($results);
        foreach($results as $key => $value) {
        	if($results[$key]['Post']['related_id'] != 0) {
                $post_status = $results[$key]['Post']['status'];
                $related_id = $results[$key]['Post']['related_id'];
                unset($results[$key]['Post']);
                $results[$key]['Pad'] = $this->Post->find(
                    'first', array(
                        'fields' => array(
                            'Post.title', 'Post.url_title', 'Post.public_key'
                        ),
                        'conditions' => array(
                            'id' => $related_id
                        )
                    )
                );
                $results[$key]['Status'] = $post_status;
            }
        }
        $this->bindModel(
            array(
                'belongsTo' => array(
                    'Comment' => array(
                        'className' => 'Comment',
                        'foreignKey' => 'related_id'
                    )
                )
            )
        );
        $comments = $this->find(
            'all', array(
                'conditions' => array(
                    'History.user_id' => $user_id,
                    'History.type' => 'commented'
                ),
                'fields' => array(
                    'History.type', 'History.timestamp', 'Comment.related_id'
                )
            )
        );
        foreach($comments as $key => $value) {
            $comments[$key]['Pad'] = $this->Post->find(
                'first', array(
                    'conditions' => array(
                        'Post.id' => $comments[$key]['Comment']['related_id']
                    ),
                    'fields' => array(
                        'Post.title', 'Post.url_title', 'Post.related_id', 'Post.public_key'
                    )
                )
            );
            if($comments[$key]['Pad']['Post']['related_id'] != 0) {
                $related_id = $comments[$key]['Pad']['Post']['related_id'];
                unset($comments[$key]['Pad']);
                $comments[$key]['Real'] = $this->Post->find(
                    'first', array(
                        'conditions' => array(
                            'Post.id' => $related_id
                        ),
                        'fields' => array(
                            'Post.title', 'Post.url_title', 'Post.public_key'
                        )
                    )
                );
            }
        }
        $all_results = array_merge($comments, $results);
        usort($all_results, array("History", "__usorter"));
        return $all_results;
    }

    function __usorter($a, $b) {
        if ($a['History']['timestamp'] == $b['History']['timestamp']) {
            return 0;
        }
        return ($a['History']['timestamp'] < $b['History']['timestamp']) ? 1 : -1;
    }
}
?>