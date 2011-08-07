<?php
class BugsController extends AppController
{
	var $name = 'Bugs';
	var $uses = array('Bug', 'User');
	var $components = array('Auth', 'Session', 'Cookie');
		
	var $validate = array(
		'content' => array(
			'rule' => 'notEmpty'
		)
	);
	
	function beforeRender() {
		$this->underMaintenance();
	}
	
function beforeFilter(){
	$this->Auth->allow('add', 'openBugs', 'closedBugs', 'invalidBugs', 'changeStatus');
}

function openBugs() {
	$openBugs = $this->Bug->find('all', array('conditions'=>array('status'=>'open')));
	$this->set('openBugs', $openBugs);
}

function closedBugs() {
	$closedBugs = $this->Bug->find('all', array('conditions'=>array('status'=>'closed')));
	$this->set('closedBugs', $closedBugs);
}
	
function invalidBugs() {
	$invalidBugs = $this->Bug->find('all', array('conditions'=>array('status'=>'invalid')));
	$this->set('invalidBugs',$invalidBugs);
}
	
function changeStatus($status) {
	echo $status;
	foreach($this->data['Bugs'] as $key => $value) :
		if($value != 'off') {
			$this->data['Bug']['id'] = $key;
			$this->data['Bug']['status'] = $status;
			if ($this->Bug->save($this->data)){
				echo 'good';
			}
	 	}
	endforeach;
	$this->redirect('/');
}

	
function add() {
	
	if (!empty($this->data)) {
		if ($this->Bug->save($this->data)) {
			$this->Session->setFlash('Thank you for submitting a bug report.');
			$this->redirect(array('action' => 'index'));
		}
	}
}
}
?>