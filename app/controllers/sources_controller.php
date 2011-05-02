<?php
class SourcesController extends AppController {

	var $name = 'Sources';
	var $helpers = array('Style','Javascript','Ajax','Pretty');
	var $uses = array('Source','Prospect');
	var $components = array('Authentication');

	function beforeFilter() 
    { 
		$this->Authentication->checkLoggedIn();
		$this->Authentication->requireNonSsl();
    }

	function index() {
		$this->set('sources', $this->Source->find('all',array(
			'order'=>'name',
			'conditions'=>array('disabled=0','account_id'=>$this->Session->read('User.account_id'))
		)));
		
		$this->set('closedsources', $this->Source->find('all',array(
			'order'=>'name',
			'conditions'=>array('disabled=1','account_id'=>$this->Session->read('User.account_id'))
		)));
	}
	
	function add() {
		if (!empty($this->data)) {
			$this->data['Source']['account_id'] = $this->Session->read('User.account_id');
			if ($this->Source->save($this->data)) {
				$this->Session->setFlash('Source added.','message-success');
				$this->redirect('/sources/',null,true);
			}
		}
	}
	
	function edit($id = null) {
		if(!$this->Source->validateSource($id,$this->Session->read('User.account_id')))
			$this->redirect('/sources',null,true);
	
		$this->Source->id = $id;
		
		if (empty($this->data)) {			
			$this->data = $this->Source->read();
		} else {
			if ($this->Source->save($this->data)) {
				$this->Session->setFlash('Source '.$id.' updated.','message-success');
				$this->redirect("/sources/");
			}
		}
	}
	
	function enable($id=null) {
		if(!$this->Source->validateSource($id,$this->Session->read('User.account_id')))
			$this->redirect('/touchtypes',null,true);
			
		$this->Source->id = $id;
		$this->Source->saveField('disabled',0);
		$this->redirect('/sources/');
	}
	
	function disable($id=null) {
		if(!$this->Source->validateSource($id,$this->Session->read('User.account_id')))
			$this->redirect('/touchtypes',null,true);
			
		$this->Source->id = $id;
		$this->Source->saveField('disabled',1);
		$this->redirect('/sources/');
	}
	
}

?>