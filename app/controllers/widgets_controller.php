<?php
class WidgetsController extends AppController {
	var $name = 'Widgets';
	var $uses = array('Widget', 'User');
	var $components = array('Auth', 'Session', 'Cookie', 'Markdown', 'Markdownify');
	var $helpers = array('Javascript');
	
	public function beforeRender() {
		$this->getWidgets();
	}

	public function beforeFilter() {
		parent::beforeFilter();
		$this->isAdmin($this->Auth->user('id'));
		if($this->User->adminCheck($this->Auth->user('id'), 'update')) {
			$this->Session->setFlash('You are not allowed to access that.', 'error');
			$this->redirect('/');
		 }
	}
	public function delete($id) {
		$this->Widget->delete($id);
		$this->Session->setFlash('Widget Deleted', 'error');
		$this->redirect('/');
	}
	public function edit($id) {
		$this->set('referer', $this->referer());
		$current = $this->Widget->findById($id);
		$current['Widget']['content'] = $this->Markdownify->parseString($current['Widget']['content']);
		if(!empty($this->data)) {
			$this->data['Widget']['id'] = $current['Widget']['id'];
			$this->data['Widget']['global'] = (isset($this->data['Widget']['global']) && $this->data['Widget']['global'] == 'on') ?  1 : 0;
			if($this->Widget->save($this->data)) {
				$this->Session->setFlash('Your widget has been saved.', 'error');
				$this->redirect($this->data['referer']);
			}
		}
		
		$this->set('widget', $current);
	}
	
	public function add() {
		$this->set('referer', $this->referer());
		if(!empty($this->data)) {
			$this->data['Widget']['global'] = ($this->data['Widget']['global'] == 'on') ?  1 : 0;
			$page = end(explode('/widgets/add', Router::url(null, false)));
			$this->data['Widget']['page'] = $page;
			if(strpos($page, 'questions/') == 1) { $this->data['Widget']['page'] = '/questions/view'; }
			if($this->Widget->save($this->data)) {
				$this->Session->setFlash('Your widget has been added.', 'error');
				$this->redirect($this->data['referer']);
			}
		}
	}
}
?>