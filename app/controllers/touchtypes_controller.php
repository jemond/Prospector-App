<?php
class TouchtypesController extends AppController {

	var $name = 'Touchtypes';
	var $uses = array('Touch','Touchtype');
	var $helpers = array('Pretty','Style','Javascript','Ajax');
	var $components = array('Authentication');

	function beforeFilter() 
    { 
		$this->Authentication->checkLoggedIn();
		$this->Authentication->requireNonSsl();
    }

	function index() {
		$this->set('touchtypes', $this->Touchtype->find('all',array(
			'fields'=>'id,disabled,name,letter,labels,export',
			'order'=>'name',
			'conditions'=>array('disabled=0','account_id='.$this->Session->read('User.account_id'))
		)));
		
		$this->set('closedtouchtypes', $this->Touchtype->find('all',array(
			'fields'=>'id,name',
			'order'=>'name',
			'conditions'=>array('disabled=1','account_id='.$this->Session->read('User.account_id'))
		)));
		
	}
	
	function add() {
		if (!empty($this->data)) {
		
			$this->data['Touchtype']['account_id'] = $this->Session->read('User.account_id');
			
			if ($this->Touchtype->save($this->data)) {
				$this->Session->setFlash('Touch type ' . $this->data["Touchtype"]["name"] . ' added.','message-success');
				$this->redirect('/touchtypes/',null,true);
			}
		}
	}
	
	function letter($id=null) {
		if(!$this->Touchtype->validateTouchtype($id,$this->Session->read('User.account_id')))
			$this->redirect('/sources',null,true);
			
		$this->Touchtype->id = $id;
		$this->set('lettercontent', $this->Touchtype->field("lettercontent"));
		$this->render('letter','blank');
	}
	
	function edit($id=null) {
		if(!$this->Touchtype->validateTouchtype($id,$this->Session->read('User.account_id')))
			$this->redirect('/touchtypes',null,true);
			
		$this->Touchtype->id = $id;
		
		if (empty($this->data)) {			
			$this->data = $this->Touchtype->read();
		} else {

			// to do - remove this old code
			/*if(isset($this->data["Touchtype"]["lettercontent"]["tmp_name"]) && file_get_contents($this->data["Touchtype"]["lettercontent"]["tmp_name"]) )
			{
				$uploadlocation=$this->data["Touchtype"]["lettercontent"]["tmp_name"];
				$lettercontent = file_get_contents($uploadlocation);
				$this->data["Touchtype"]["lettercontent"] = $lettercontent;
			}
			else
				unset($this->data["Touchtype"]["lettercontent"]);
			*/
			
			if ($this->Touchtype->save($this->data)) {
				$this->Session->setFlash('Touch type '.$this->data["Touchtype"]["name"].' updated.','message-success');
				$this->redirect('/touchtypes/',null,true);
			}
		}
	}
	
	function enable($id=null) {
		if(!$this->Touchtype->validateTouchtype($id,$this->Session->read('User.account_id')))
			$this->redirect('/touchtypes',null,true);
			
		$this->Touchtype->id = $id;
		$this->Touchtype->saveField('disabled',0);
		$this->redirect('/touchtypes/',null,true);
	}
	
	function disable($id=null) {
		if(!$this->Touchtype->validateTouchtype($id,$this->Session->read('User.account_id')))
			$this->redirect('/touchtypes',null,true);
			
		$this->Touchtype->id = $id;
		$this->Touchtype->saveField('disabled',1);
		$this->redirect('/touchtypes/',null,true);
	}
	
}

?>