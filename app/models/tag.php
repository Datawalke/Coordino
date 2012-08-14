<?php
class Tag extends AppModel {

	var $name = 'Tag';
	var $actsAs = array('Containable');
    
    var $hasAndBelongsToMany = array(
        'Post' => array(
            'className'    => 'Post',
	        'joinTable'    => 'post_tags',
	        'foreignKey'   => 'tag_id',
	        'associationForeignKey'=> 'post_id',
	        'conditions'   => '',
	        'order'        => '',
	        'limit'        => '',
	        'unique'       => true,
	        'finderQuery'  => '',
	        'deleteQuery'  => '',
        )
    );

	public function getSuggestions() {
		return $this->query("SELECT COUNT(post_tags.tag_id) as count, tags.tag
                            FROM post_tags, tags
                            WHERE post_tags.tag_id=tags.id
                            GROUP BY post_tags.tag_id
                            ORDER BY count DESC");

    }

    public function tagSearch($tag, $page) {
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
        $results = $this->find(
            'all', array(
                'contain' => array(
                    'Post' => array(
                        'User' => array(
                            'fields' => array('User.public_key', 'User.username')
                        ),
                        'Answer' => array(
                            'fields' => array('Answer.id'),
                            'conditions' => array('Answer.flags <' => $flag_check['Setting']['value'])
                        ),
                        'conditions' => array('Post.flags <' => $flag_check['Setting']['value']),
                        'fields' => array('Post.title', 'Post.url_title', 'Post.public_key',
                                          'Post.views', 'Post.timestamp'),
                        'limit' => $page . ',' . 10
                    )
                ),
                'conditions' => array('Tag.tag' => $tag),
                'fields' => array('Tag.tag')
            )
        );
        foreach($results['0']['Post'] as $key => $value) {
            $tags_per_site[$key] = $this->Post->find(
                'all', array(
                    'contain' => array(
                        'Tag.tag'
                    ),
                    'conditions' => array(
                        'Post.id' => $results['0']['Post'][$key]['id']
                    )
                )
            );
        }
        foreach($results['0']['Post'] as $key => $value) {
            $questions[$key]['Post'] = $results['0']['Post'][$key];
            $questions[$key]['User'] = $results['0']['Post'][$key]['User'];
            $questions[$key]['Answer'] = $results['0']['Post'][$key]['Answer'];
            $questions[$key]['Tag'] = $tags_per_site[$key]['0']['Tag'];
            unset($questions[$key]['Post']['User']);
            unset($questions[$key]['Post']['Answer']);
        }
        $final_results = array_reverse($questions);
        return $final_results;
    }
}
?>