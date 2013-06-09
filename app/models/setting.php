<?php
class Setting extends AppModel {
    var $name = 'Setting';


    /* Returns true if the user can perform the action where
        param1 : user     param2 : action
    */
    public function repCheck($user_id, $setting_name) {
        $this->bindModel(
            array(
                'belongsTo' => array(
                    'User' => array(
                        'className' => 'User',
                        'foreignKey' => 'id'
                    )
                )
            ), false
        );
        $this->recursive = -1;
        $this->User->recursive = -1;
        $user_rep = $this->User->find(
            'first', array(
                'conditions' => array(
                    'User.id' => $user_id
                ),
                'fields' => array('User.reputation')
            )
        );
        $rep_needed = $this->find(
            'first', array(
                'conditions' => array(
                    'Setting.name' => $setting_name
                ),
                'fields' => array('Setting.value')
            )
        );
        if($user_rep['User']['reputation'] >= $rep_needed['Setting']['value']) {
            return true;
        }else {
            return false;
        }
    }

	public function getValue($name) {
        $check = $this->find(
            'first', array(
                'conditions' => array(
                    'name' => $name
                ),
                'fields' => array('value'),
                'recursive' => -1
            )
        );
		return $check['Setting']['value'];
	}
	
	public function getBlacklist() {
		$serialized = $this->find(
			'first', array(
				'conditions' => array('name' => 'blacklist')
			)
		);	
		$unserialized = unserialize($serialized['Setting']['description']);
		return $unserialized;
	}
	
	public function updateBlacklist(array $new_list) {
		$id = $this->find('first', array('conditions' => array('name' => 'blacklist')));
		$save_this = array(
			'id' => $id['Setting']['id'],
			'description' => serialize($new_list)
		);
		$this->save($save_this);
	}
}
?>