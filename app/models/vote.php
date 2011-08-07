<?php
class Vote extends AppModel {
    var $name = 'Vote';

    public function castVote($user_id, $public_key, $type) {
        /*
            Check if the user voted for this post.
            If they did, setup for an easy redirect.  If they didn't add a vote to the votes table and
            some reputation changes depending on the type of vote.
        */
        $this->bindModel(
            array(
                'belongsTo' => array(
                    'Post' => array(
                        'className' => 'Post',
                        'foreignKey' => 'post_id'
                    ),
                    'User' => array(
                        'className' => 'User',
                        'foreignKey' => 'user_id'
                    )
                )
            ), false
        );
        $this->Post->recursive = -1;
        $this->User->recursive = -1;
        $voter_info = $this->User->find(
            'first', array(
                'conditions' => array(
                    'User.id' => $user_id
                ),
                'fields' => array('User.reputation')
            )
        );
        $post_info = $this->Post->find(
            'first', array(
                'conditions' => array(
                    'Post.public_key' => $public_key
                ),
                'fields' => array(
                    'Post.id', 'Post.votes', 'Post.user_id'
                )
            )
        );
        $existing_vote = $this->find(
            'first', array(
                'conditions' => array(
                    'Vote.user_id' => $user_id,
                    'Vote.post_id' => $post_info['Post']['id']
                )
            )
        );
        $poster_rep = $this->User->find(
            'first', array(
                'conditions' => array(
                    'User.id' => $post_info['Post']['user_id']
                ),
                'fields' => array('User.reputation')
            )
        );
        $one_vote_up = $this->find(
            'first', array(
                'conditions' => array(
                    'Vote.type' => 'up',
                    'Vote.post_id' => $post_info['Post']['id']
                ),
                'fields' => array(
                    'Vote.id'
                )
            )
        );
        $one_vote_down = $this->find(
            'first', array(
                'conditions' => array(
                    'Vote.type' => 'down',
                    'Vote.post_id' => $post_info['Post']['id']
                ),
                'fields' => array(
                    'Vote.id'
                )
            )
        );
        if(!empty($existing_vote)) {
            return 'exists';
        }else{
            $this->create();
            $this->data['Vote']['user_id'] = $user_id;
            $this->data['Vote']['post_id'] = $post_info['Post']['id'];
            $this->data['Vote']['timestamp'] = time();
            $this->data['Vote']['type'] = $type;
            $this->save($this->data);
            if($type == 'up') {
                if(empty($one_vote_up)) {
                    $vote = array(
                        'id' => $post_info['Post']['id'],
                        'votes' => $post_info['Post']['votes'] + 1
                    );
                    $reputation = array(
                        'id' => $post_info['Post']['user_id'],
                        'reputation' => $poster_rep['User']['reputation'] + 10
                    );
                }else {
                    $vote = array(
                        'id' => $post_info['Post']['id'],
                        'votes' => $post_info['Post']['votes'] + 1
                    );
                    $reputation = array(
                        'id' => $post_info['Post']['user_id'],
                        'reputation' => $poster_rep['User']['reputation'] + 1
                    );
                }
            }elseif($type == 'down') {
                if(empty($one_vote_down)) {
                    $vote = array(
                        'id' => $post_info['Post']['id'],
                        'votes' => $post_info['Post']['votes'] - 1
                    );
                    $reputation = array(
                        'id' => $post_info['Post']['user_id'],
                        'reputation' => $poster_rep['User']['reputation'] - 5
                    );
                }else {
                    $vote = array(
                        'id' => $post_info['Post']['id'],
                        'votes' => $post_info['Post']['votes'] - 1
                    );
                    $reputation = array(
                        'id' => $post_info['Post']['user_id'],
                        'reputation' => $poster_rep['User']['reputation'] - 1
                    );
                }
                $voter_rep = array(
                    'id' => $user_id,
                    'reputation' => $voter_info['User']['reputation'] - 1
                );
                $this->User->save($voter_rep);
            }
            $this->Post->save($vote);
            $this->User->save($reputation);
        }
    }

    /*  throwFlag flags a post (creates a flag vote for that post) as long as the given user hasn't done so already.
        It will also add one to the posts.flags for that post
    */
    public function throwFlag($user_id, $public_key) {
        $this->bindModel(
            array(
                'belongsTo' => array(
                    'Post' => array(
                        'className' => 'Post',
                        'foreignKey' => 'post_id'
                    )
                )
            )
        );
        $this->Post->recursive = -1;
        $post = $this->Post->findByPublicKey($public_key);
        $exists = $this->find(
            'first', array(
                'conditions' => array(
                    'Vote.user_id' => $user_id,
                    'Vote.post_id' => $post['Post']['id'],
                    'Vote.type' => 'flag'
                )
            )
        );
        if(!empty($exists)) {
            return 'exists';
        }else {
            $new_flag = array(
                'user_id' => $user_id,
                'post_id' => $post['Post']['id'],
                'type' => 'flag',
                'timestamp' => time()
            );
            $add_flag = array(
                'id' => $post['Post']['id'],
                'flags' => $post['Post']['flags'] + 1
            );
            $this->Post->save($add_flag);
            $this->save($new_flag);
            return 'success';
        }
    }
}
?>