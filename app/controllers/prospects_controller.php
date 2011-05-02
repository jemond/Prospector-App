<?php
class ProspectsController extends AppController {

	var $name = 'Prospects';	
	var $uses = array('Prospect','Campaign','Comment','Source','Touch','Touchtype','User','Account'); // to do : change to ondemand binding
	var $helpers = array('Pretty','Text','Javascript','Ajax','Merge','Session');
	var $components = array('Authentication');

	function beforeFilter() 
    { 
		$this->Authentication->checkLoggedIn();
		$this->Authentication->requireNonSsl();
    }

	function index() {
		$filter = $this->User->getCriteria($this->Session->read('User.filter'));	
		$criteria = $this->Prospect->appendAccountId($filter,$this->Session->read('User.account_id'));	
		$criteria['Prospect.account_id']=$this->Session->read('User.account_id');
	
		$sort = $this->Prospect->getSortCriteria($this->Session->read('User.sort'));
		
		$search_results = $this->Prospect->find('all',array(
			'order'=>$sort,
			'conditions'=>$criteria
			)
		);
		
		if(!is_array($search_results)) // if the query errored, make it a clean array
			$search_results = array();
			
		$this->set('prospects',$search_results);
		
		$this->User->id=$this->Session->read('User.id');
		$this->data=$this->User->read();
		
		$q = $this->User->read('filter');
		if( isset($q['User']['filter']) && strlen($q['User']['filter']) > 0 )
			$this->set('q',$q['User']['filter']);
		else
			$this->set('q',false);
	}
	
	// we call this from other page links that will set the filter
	function setfilter($q1,$d1,$q2 = false,$d2 = false) {
		$q = '';
		if($q1)
			$q = "$q1:$d1";
		if($q2)
			$q .= " $q2:$d2";
			
		if($q != '') {
			$this->User->id=$this->Session->read('User.id');
			$this->Session->write('User.filter',$q);
			$this->User->saveField('filter',$q);
		}
		
		$this->redirect('/prospects/',null,true);
	}
	
	function letter($id=null) {
		
		if(!$this->Prospect->validateTouch($id,$this->Session->read('User.account_id')))
			$this->redirect('/prospects',null,true);
	
		$this->Touch->id = $id;		
		$this->Prospect->id=$this->Touch->field('prospect_id');
		$this->set('prospect', $this->Prospect->read(array(
			'fields'=>'firstname','lastname','address1','address2','city','state','postalcode','country'
			)
		));
		
		$this->set('content',$this->Touchtype->find("first",array(
			"fields"=>"lettercontent",
			"conditions"=>"id=".$this->Touch->field("touchtype_id")
			)
		));
		
		$this->render('letter','pdf');
	}
	
	function labels($id = null) {
		if(!$this->Prospect->validateTouch($id,$this->Session->read('User.account_id')))
			$this->redirect('/prospects',null,true);
			
		$this->Touch->id = $id;		
		$this->Prospect->id=$this->Touch->field('prospect_id');
		$this->set('prospect', $this->Prospect->read(array(
			'fields'=>'firstname','lastname','address1','address2','city','state','postalcode','country'
			)
		));
			
		$this->render('labels','pdf');	
	}
	
	function export($id=null) {
		if(!$this->Prospect->validateTouch($id,$this->Session->read('User.account_id')))
			$this->redirect('/prospects',null,true);
		
		$this->Touch->id = $id;		
		$this->Prospect->id=$this->Touch->field('prospect_id');
		$prospects = $this->Prospect->read(array(
			'fields'=>$this->Prospect->getExportFields()
			)
		);
		$this->set('prospect',$this->Prospect->prepareExportData($prospects));
		$this->render('export','csv');
	}
	
	function add() {
		$this->set('campaigns',$this->Campaign->getCampaigns($this->Session->read('User.account_id')));
		$this->set('sources',$this->Source->getSources($this->Session->read('User.account_id')));
		
		if($this->Prospect->overage($this->Session->read('User.account_id'),$this->Session->read('Plan.prospect_limit'))) {
			$this->Session->setFlash('You are at the open prospect limit for your account! Either close some prospects or upgrade.','message-alert');
			$this->redirect('/prospects/',null,true);
		} //to do - can the erorr message be centralized?
		
		if (!empty($this->data)) {
			$new_campaign_id = $this->data['Prospect']['campaign_id'];
			$new_source_id = $this->data['Prospect']['source_id'];
			if(is_numeric($new_campaign_id) && !$this->Campaign->validateCampaign($new_campaign_id,$this->Session->read('User.account_id')))
				$this->redirect('/campaigns',null,true);
			if(is_numeric($new_source_id) && !$this->Source->validateSource($new_source_id,$this->Session->read('User.account_id')))
				$this->redirect('/sources',null,true);
			
			$this->data['Prospect']['account_id'] = $this->Session->read('User.account_id');
			if ($this->Prospect->save($this->data)) {
				$this->Source->setProspectCounts(false,$this->data['Prospect']['source_id']);
				$this->Campaign->setProspectCounts(false,$this->data['Prospect']['campaign_id']);
				
				$this->Session->setFlash('Your prospect has been created! Wicked!','message-success');
				$this->redirect('/prospects/view/'.$this->Prospect->id);
			}
		}
	}
	
	function edit($id = null) {
		if(!$this->Prospect->validateProspect($id,$this->Session->read('User.account_id')))
			$this->redirect('/prospects',null,true);
		
		$this->Prospect->id = $id;
		$this->set('prospect', $this->Prospect->read());
		
		$source_id = $this->Prospect->field('source_id');
		$campaign_id = $this->Prospect->field('campaign_id');
		
		$this->set('campaigns',$this->Campaign->getCampaigns($this->Session->read('User.account_id')));		
		$this->set('sources',$this->Source->getSources($this->Session->read('User.account_id'),$source_id));		
		
		if (empty($this->data)) {			
			$this->data = $this->Prospect->read();
		} else {
			// check they didn't spoof campaign, source
			// to do - could this be down with custom validation?
			$new_campaign_id = $this->data['Prospect']['campaign_id'];
			$new_source_id = $this->data['Prospect']['source_id'];
			if(is_numeric($new_campaign_id) && !$this->Campaign->validateCampaign($new_campaign_id,$this->Session->read('User.account_id')))
				$this->redirect('/campaigns',null,true);
			if(is_numeric($new_source_id) && !$this->Source->validateSource($new_source_id,$this->Session->read('User.account_id')))
				$this->redirect('/sources',null,true);
			
			if ($this->Prospect->save($this->data)) {
				$this->Source->setProspectCounts($source_id,$this->data['Prospect']['source_id']);
				$this->Campaign->setProspectCounts($campaign_id,$this->data['Prospect']['campaign_id']);
			
				$this->Session->setFlash('Your prospect has been updated.','message-success');
				$this->redirect('/prospects/view/'.$id,null,true);
			}
		}
	}
	
	function applied($id = null) {
		if(!$this->Prospect->validateProspect($id,$this->Session->read('User.account_id')))
			$this->redirect('/prospects',null,true);
		
		$this->Prospect->id = $id;
		if($this->Prospect->field('applied') == 1) {
			$this->Prospect->saveField('applied',0);
			$this->Source->incrementStat($this->Prospect->field('source_id'),'applied','down');
		}
		else {
			$this->Prospect->saveField('applied',1);
			$this->Source->incrementStat($this->Prospect->field('source_id'),'applied','up');
		}
		
		$this->redirect('/prospects/view/'.$id,null,true);
	}
	
	function admitted($id = null) {
		if(!$this->Prospect->validateProspect($id,$this->Session->read('User.account_id')))
			$this->redirect('/prospects',null,true);
		
		$this->Prospect->id = $id;
		if($this->Prospect->field('admitted') == 1) {
			$this->Prospect->saveField('admitted',0);
			$this->Source->incrementStat($this->Prospect->field('source_id'),'admitted','down');
		}
		else {
			$this->Prospect->saveField('admitted',1);
			$this->Source->incrementStat($this->Prospect->field('source_id'),'admitted','up');
		}
		
		$this->redirect('/prospects/view/'.$id,null,true);
	}
	
	function enrolled($id = null) {
		if(!$this->Prospect->validateProspect($id,$this->Session->read('User.account_id')))
			$this->redirect('/prospects',null,true);
		
		$this->Prospect->id = $id;
		if($this->Prospect->field('enrolled') == 1) {
			$this->Prospect->saveField('enrolled',0);
			$this->Source->incrementStat($this->Prospect->field('source_id'),'enrolled','down');
		}
		else {
			$this->Prospect->saveField('enrolled',1);
			$this->Source->incrementStat($this->Prospect->field('source_id'),'enrolled','up');
		}
		
		$this->redirect('/prospects/view/'.$id,null,true);
	}
	
	function view($id = null) {
		if(!$this->Prospect->validateProspect($id,$this->Session->read('User.account_id')))
			$this->redirect('/prospects',null,true);
			
		$this->Prospect->id = $id;
		$this->set('prospect', $this->Prospect->read());
		$this->set('touchid', $this->Touch->getLatestTouchId($id));
	}
	
	function contactlog($id=null) {
		if(!$this->Prospect->validateProspect($id,$this->Session->read('User.account_id')))
			$this->redirect('/prospects',null,true);
			
		// clean up the data set but dig deep to get touch type details
		$this->Comment->unbindModel(array('belongsTo'=>array('Prospect')));
		$this->Touch->unbindModel(array('belongsTo'=>array('Prospect')));
		$this->Touch->unbindModel(array('belongsTo'=>array('Comment')));
		$this->Touch->unbindModel(array('belongsTo'=>array('Touchtype')));
		$this->Touch->bindModel(array('belongsTo'=>array('Touchtype'=>array(
			'fields'=>'labels,letter,export'
			)
		)));
		$this->Comment->bindModel(array('belongsTo'=>array('Prospect'=>array(
			'fields'=>'open'
			)
		)));		
		
		$this->set('comments', 
			$this->Comment->find("all",array(
				'conditions'=>array('Comment.prospect_id'=>$id),
				'fields'=>array('Comment.touch_id,Comment.created,Comment.prospect_id,Comment.note, User.name, Comment.source,Comment.id'),
				'order'=>'created DESC',
				'recursive'=>2
				)
			)
		);
		
		$this->render('contactlog','ajax');
	}
	
	function close($id) {
		if(!$this->Prospect->validateProspect($id,$this->Session->read('User.account_id')))
			$this->redirect('/prospects',null,true);
			
		$this->Prospect->id = $id;		
		$this->Source->setProspectCounts($this->Prospect->field('source_id'),false);
		$this->Campaign->setProspectCounts($this->Prospect->field('campaign_id'),false);
		
		$this->data['Prospect']['open'] = 0;
		$this->data['Comment']['source'] = "Record closed";
		$this->data['Comment']['user_id'] = $this->Session->read('User.id');
		$this->data['Comment']['prospect_id'] = $id;
		
		$this->Prospect->save($this->data,false,array("open"));
		$this->Comment->save($this->data);
		
		$this->Session->setFlash('Prospect '.$id.' closed.','message-success');
		$this->redirect("/prospects/");
	}
	
	function open($id) {
		if(!$this->Prospect->validateProspect($id,$this->Session->read('User.account_id')))
			$this->redirect('/prospects',null,true);
			
		if($this->Prospect->overage($this->Session->read('User.account_id'),$this->Session->read('Plan.prospect_limit'))) {
			$this->Session->setFlash('I can\'t open that prospect. You are at the open prospect limit for your account! Either close some prospects or <a href="/settings/">upgrade</a>.','message-alert');
			$this->redirect('/prospects/',null,true);
		} //to do - can the erorr message be centralized?
			
		$this->Prospect->id = $id;
		
		$this->Source->setProspectCounts(false,$this->Prospect->field('source_id'));
		$this->Campaign->setProspectCounts(false,$this->Prospect->field('campaign_id'));
		
		$this->data['Prospect']['open'] = true;
		$this->data['Comment']['source'] = "Record opened";
		$this->data['Comment']['user_id'] = $this->Session->read('User.id'); 
		$this->data['Comment']['prospect_id'] = $id;
		
		$this->Prospect->save($this->data,false,array("open"));
		$this->Comment->save($this->data);
		
		$this->Session->setFlash('Prospect '.$id.' opened.','message-success');
		$this->redirect("/prospects/view/$id");
	}
	
	function comment($id = NULL) {
		if(!$this->Prospect->validateProspect($id,$this->Session->read('User.account_id')))
			$this->redirect('/prospects',null,true);
			
		$this->Prospect->id = $id;
		$this->set('prospect', $this->Prospect->read());
		$this->render("comment","ajax");
		if (!empty($this->data)) {
			$this->data['Comment']['prospect_id'] = $id;
			$this->data['Comment']['id'] = '';
			$this->data['Comment']['source'] = 'Comment added';
			$this->data['Comment']['user_id'] = $this->Session->read('User.id');
			if ($this->Comment->save($this->data)) {
				$this->redirect("/prospects/view/$id");
			}
		}
	}
	
	function touchback($id = null) {
		if(!$this->Prospect->validateProspect($id,$this->Session->read('User.account_id')))
			$this->redirect('/prospects',null,true);

		$this->Prospect->id = $id;
		$this->set('prospect', $this->Prospect->read());
		
		$touchid = $this->Touch->getLatestTouchId($id);
		
		if($touchid) {
			if (!empty($this->data)) {
				$toucbackcount = $this->Touch->field('Touch.touchback','Touch.prospect_id='.$id,'Touch.id DESC');
				
				$this->data['Touch']['id'] = $this->Touch->field('Touch.id','Touch.prospect_id='.$id,'Touch.id DESC');
				$this->data['Touch']['touchback']=$toucbackcount+1;
				unset($this->data['Prospect']);
				
				$this->data['Comment']['prospect_id'] = $id;
				$this->data['Comment']['source'] = 'Touchback added';
				$this->data['Comment']['note'] = $this->Comment->formatNote($this->data['Touch']['touchbacknote']);
				$this->data['Comment']['user_id'] = $this->Session->read('User.id');
				
				if ($this->Touch->save($this->data,false) && $this->Comment->save($this->data)) {
					$this->Prospect->saveField('touchback_count',$this->Touch->getTouchbackCount($id));
					$this->redirect("/prospects/view/$id");
				}
			}
			
		}
		
		$this->render("touchback","ajax");
	}
	
	function touch($id = NULL) {
		if(!$this->Prospect->validateProspect($id,$this->Session->read('User.account_id')))
			$this->redirect('/prospects',null,true);

		$this->Prospect->id = $id;
		$this->set('prospect', $this->Prospect->read());
		$this->set('touchtypes',$this->Touchtype->find('list',array(
			'conditions'=>array('Touchtype.disabled'=>0,'Touchtype.account_id'=>$this->Session->read('User.account_id')),
			'order'=>'Touchtype.name'
			)
		));
		
		$this->render("touch","ajax");
		
		if (!empty($this->data)) {
		
			// TO DO: move this to the model!
		
			$this->data['Touch']['prospect_id'] = $id;
			$this->data['Touch']['id'] = '';
			$this->data['Touch']['user_id'] = $this->Session->read('User.id');
			
			$this->data['Comment']['prospect_id'] = $id;
			$this->data['Comment']['id'] = '';
			$this->data['Comment']['user_id'] = $this->Session->read('User.id');
	
			$this->data['Prospect']['touch_count'] = $this->Prospect->getTouchCount($id)+1;
			$this->data['Prospect']['lasttouch'] = date("Y-m-d");
			$this->data['Prospect']['id'] = $id;
	
			// logging
			$touchtypename = $this->Touchtype->read(array("name"),$this->data['Touch']['touchtype_id']);
			$this->data['Comment']['source'] = 'Touch "'.$touchtypename["Touchtype"]["name"].'" added';
			
			// annotate the comment table
			if($this->data['Touch']['note'])
				$this->data['Comment']['note'] = "Note: ".$this->data['Touch']['note'];
			
			if ($this->Touch->save($this->data,false) && $this->Prospect->save($this->data,false,array('lasttouch','touch_count'))) {
				$this->data['Comment']['touch_id'] = $this->Touch->id;
				if($this->Comment->save($this->data))
					$this->redirect('/prospects/view/'.$id,null,true);
			}
		}
	}
	
	function sort($column = null) {
		$db=$this->Prospect->getSortCriteria($column,'pref');
		$this->Session->write('User.sort',$column);
		$this->User->id=$this->Session->read('User.id');
		$this->User->saveField('sort',$db);
		$this->redirect('/prospects/',null,false);
	}
	
}
?>