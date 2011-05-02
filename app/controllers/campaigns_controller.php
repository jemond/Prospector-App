<?php
class CampaignsController extends AppController {

	var $name = 'Campaigns';
	var $helpers = array('Pretty','Merge','Javascript','Ajax','Html');
	var $uses = array('Campaign','Step','Touchtype','Prospect','Touch','Comment','Source');
	var $components = array('Authentication','Wrapper');

	function beforeFilter() 
    { 
		$this->Authentication->checkLoggedIn();
		$this->Authentication->requireNonSsl();
    }

	function index() {
		$this->set('opencampaigns', $this->Campaign->find('all',array(
		 	'fields'=>array('step_count','step_completed_count','id','name','prospect_count','next_step_due','COALESCE(Campaign.next_step_due,"NO") as next_step_due','CASE WHEN Campaign.next_step_due < NOW() THEN DATEDIFF(now(),next_step_due) ELSE 0 END as past_due'),
			// wacky, I know - but if I use the COALESCE the sort puts the latest at the top and the blanks at the bottom - no idea but it works. fuck it
		 	'order'=>'next_step_due,step_count,name',
			'conditions'=>array('open'=>1,'account_id'=>$this->Session->read('User.account_id'))
		 	)
		 ));
		
		$this->Campaign->bindModel(array('belongsTo'=>array('User'=>array(
			'fields'=>'User.name',
			'foreignKey'=>'closed_by'			
			)
		)));
		$this->set('closedcampaigns', $this->Campaign->find('all',array(
		 	'order'=>'Campaign.closed DESC, Campaign.name',
			'conditions'=>array('Campaign.open'=>0,'Campaign.account_id'=>$this->Session->read('User.account_id')),
			'fields'=>'Campaign.name,Campaign.id,Campaign.closed,User.name'
		 	)
		 ));

	}
	
	function add() {
		$this->set('touchtypes',$this->Touchtype->getList($this->Session->read('User.account_id')));
		
		if (!empty($this->data)) {
			$this->data['Campaign']['account_id'] = $this->Session->read('User.account_id');
			
			$this->data = $this->Campaign->cleanSteps($this->data);
			
			$this->Campaign->bindModel(array('hasMany'=>array('Step'=>array(
				"order"=>"position"
				)
			)),false);
			
			if ($this->Campaign->saveAll($this->data)) {
				$count = $this->Step->find("count",array(
					'conditions'=>array('campaign_id'=>$this->Campaign->id)
					)
				);
				$this->Campaign->saveField('step_count',$count);
				$this->Campaign->saveField('next_step_due',$this->Campaign->getNextStepDueDate($this->Campaign->id));
				
				$this->Session->setFlash('Your campaign has been created! Sweet!','message-success');
				$this->redirect('/campaigns/',null,true);
			}
		}
	}
	
	function edit($id = null) {
		if(!$this->Campaign->validateCampaign($id,$this->Session->read('User.account_id')))
			$this->redirect('/campaigns',null,true);
		
		$this->Step->bindModel(array('belongsTo'=>array('Touchtype'=>array(
			'fields'=>'name,letter,labels,export',
			'conditions'=>'account_id='.$this->Session->read('User.account_id')
			)
		)));
		$this->Campaign->bindModel(array('hasMany'=>array('Step'=>array(
			"order"=>"position"
			)
		)),false);
		$this->Campaign->id = $id;
		
		$this->set('touchtypes',$this->Touchtype->getList($this->Session->read('User.account_id')));
		
		if (empty($this->data)) {
			$this->data = $this->Campaign->read();
		} else {
			/* 
				clean up incoming steps: 
				delete all steps that aren't complete
				remove all incoming steps not selected
			*/
			$this->Step->deleteAll(array(
				'campaign_id'=>$id,
				'complete'=>0
				)
			);
						
			$this->data = $this->Campaign->cleanSteps($this->data);
			
			if ($this->Campaign->saveAll($this->data) ) {
				$count = $this->Step->find("count",array(
					'conditions'=>array('campaign_id'=>$id)
					)
				);
				$this->Campaign->saveField('step_count',$count);
				$this->Campaign->saveField('next_step_due',$this->Campaign->getNextStepDueDate($id));
				$this->Session->setFlash('Your campaign has been updated. Also, you rock.','message-success');
				$this->redirect('/campaigns/view/'.$id);
			}
		}
	}
	
	function view($id = null) {
		if(!$this->Campaign->validateCampaign($id,$this->Session->read('User.account_id')))
			$this->redirect('/campaigns',null,true);
		
		$this->Step->bindModel(array('belongsTo'=>array('Touchtype'=>array(
			'fields'=>'Touchtype.name,Touchtype.letter,Touchtype.labels,Touchtype.export'	
			)
		)));
		
		$this->Campaign->bindModel(array('hasMany'=>array('Step'=>array(
			'fields'=>'Step.position,Step.touchtype_id,Step.complete,Step.applied,Step.days',
			'order'=>'Step.position'	
			),
		'Prospect'=>array(
			'fields'=>'Prospect.firstname,Prospect.lastname,Prospect.cwh,Prospect.id',
			'conditions'=>array('open'=>1)
			)
		)));
		$this->Campaign->id = $id;
		$this->set('campaign', $this->Campaign->find('first',array(
			'recursive'=>2,
			'conditions'=>array('id'=>$id),
			'fields'=>'Campaign.open,Campaign.name,Campaign.created,Campaign.modified,Campaign.step_count,Campaign.step_completed_count,Campaign.next_step_due,Campaign.prospect_count'
			)
		));
	}
	
	function close($id = null) {
		if(!$this->Campaign->validateCampaign($id,$this->Session->read('User.account_id')))
			$this->redirect('/campaigns',null,true);
			
		$this->Campaign->close($id,$this->Session->read('User.id'));
		$this->Session->setFlash('Your campaign has been closed.','message-alert');
		$this->redirect('/campaigns/',null,true);
	}
	
	function wrapup($id = null) {
		if(!$this->Campaign->validateCampaign($id,$this->Session->read('User.account_id')))
			$this->redirect('/campaigns',null,true);
			
		$this->Campaign->close($id,$this->Session->read('User.id'));
		
		$prospects = $this->Prospect->getProspectsByCampaign($id);
		
		$this->Campaign->id=$id;
		$campaign_name = $this->Campaign->field('name');
		
		$comments = array(); //array of comments to save at once for efficiency's sake
		$comment_row = 0;
		
		foreach($prospects as $prospect) {
			$prospect_id = $prospect['Prospect']['id'];
			
			$this->Prospect->create();
			$this->Comment->create();
			
			$this->Prospect->id = $prospect_id;		
			$this->Source->setProspectCounts($this->Prospect->field('source_id'),false);
			$this->Campaign->setProspectCounts($this->Prospect->field('campaign_id'),false);
			$this->Prospect->saveField('open',0);
			
			$comments[$comment_row]['source'] = "Campaign <em>$campaign_name</em> completed and record closed";
			$comments[$comment_row]['user_id'] = $this->Session->read('User.id');
			$comments[$comment_row]['prospect_id'] = $prospect_id;
			
			$comment_row++;
		}
		
		if(count($comments)>0)
			$this->Comment->saveAll($comments);
		
		$this->Session->setFlash($campaign_name.' has been closed along with all of it\'s prospects.','message-success');
		$this->redirect('/campaigns',null,true);
	}
	
	function open($id = null) {
		if(!$this->Campaign->validateCampaign($id,$this->Session->read('User.account_id')))
			$this->redirect('/campaigns',null,true);
			
		$this->Campaign->id=$id;
		$this->Campaign->saveField('open',1);
		$this->Campaign->saveField('closed',null);
		$this->Session->setFlash('Your campaign has been opened.','message-success');
		$this->redirect('/campaigns/view/'.$id,null,true);
	}
	
	function export($id = null) {
		if(!$this->Campaign->validateCampaign($id,$this->Session->read('User.account_id')))
			$this->redirect('/campaigns',null,true);
			
		$this->Prospect->unbindModel(array('hasMany'=>array('Comment','Touch'))); //to do remove when we remove prospect global bindings
		
		//$this->Prospect->bindModel(array('belongsTo'=>array('Source')),false); -- will need these when I remove global binding
		//$this->Prospect->bindModel(array('belongsTo'=>array('Campaign')),false);
		$prospects = $this->Prospect->find('all',array(
			'fields'=>$this->Prospect->getExportFields(),
			'conditions'=>array('Prospect.campaign_id'=>$id,'Prospect.open'=>1)
			)
		);	
		$this->set('prospects',$this->Prospect->prepareExportData($prospects));
		$this->render('export','csv');
	}
	
	function touch($id = null) {
		if(!$this->Campaign->validateCampaign($id,$this->Session->read('User.account_id')))
			$this->redirect('/campaigns',null,true);
		
		$this->Campaign->bindModel(array('hasMany'=>array('Step','Prospect')),false);
		$this->Step->bindModel(array('belongsTo'=>array('Touchtype'=>array(
			'fields'=>'Touchtype.name'	
			)
		)),false);
		
		$this->Campaign->id=$id;
		$this->set('campaigns',$this->Campaign->read());	
		
		$prospects = $this->Campaign->read();	
		
		$stepdetails = $this->Campaign->getCurrentStepDetails($id);
		$touchtypename = $stepdetails['Touchtype']['name'];
		$touchtypeid = $stepdetails['Touchtype']['id'];
		
		$campaign_name = $this->Campaign->read('name');

		foreach($prospects['Prospect'] as $prospect) { // to do -> can this whole section becombined with logic in prospect touch export? 
			$this->data['Touch']['prospect_id'] = $prospect['id'];
			$this->data['Touch']['user_id'] = $this->Session->read('User.id');
			$this->data['Touch']['touchtype_id'] = $touchtypeid;
			$this->Touch->save($this->data);
		
			$this->data['Comment']['prospect_id'] = $prospect['id'];
			$this->data['Comment']['user_id'] = $this->Session->read('User.id');
			$this->data['Comment']['source'] = "Touch \"$touchtypename\" added via the {$campaign_name['Campaign']['name']} campaign";
			$this->data['Comment']['touch_id'] = $this->Touch->id;
			$this->Comment->save($this->data);
			
			$this->Prospect->id=$prospect['id'];
			$this->Prospect->saveField('lasttouch', date("Y-m-d"));
			$this->Prospect->saveField('touch_count', $this->Prospect->getTouchCount($prospect['id'])+1);
			
			$this->Comment->create("data");
			$this->Touch->create("data");			
		}
		
		$this->Step->id=$stepdetails['Step']['id'];
		$this->Step->saveField('complete',1);
		$this->Step->saveField('applied',date('Y-m-d H:i:s'));
		
		$this->Campaign->id=$id;
		$this->Campaign->saveField('step_completed_count',$this->Campaign->getStepsCompleted($id));
		$this->Campaign->saveField('next_step_due',$this->Campaign->getNextStepDueDate($id));
		
		$this->Session->setFlash('Touch applied! Wicked! Now run any related reports listed to the right.','message-success');
		$this->redirect('/campaigns/view/'.$id);
	}
	
	function letter($id = null) {
		if(!$this->Campaign->validateCampaign($id,$this->Session->read('User.account_id')))
			$this->redirect('/campaigns',null,true);
			
		$this->Campaign->bindModel(array('hasMany'=>array('Step','Prospect'=>array(
			'fields'=>'Prospect.firstname,Prospect.lastname,Prospect.address1,Prospect.address2,Prospect.city,Prospect.state,Prospect.postalcode,Prospect.country',
			'conditions'=>array('Prospect.open'=>1)
			)
		)),false);
		$this->Step->bindModel(array('belongsTo'=>array('Touchtype')),false);
		
		$this->Campaign->id=$id;
		$this->set('campaigns',$this->Campaign->read());
		
		$stepdetails = $this->Step->find('first',array(
				'conditions'=>array('campaign_id'=>$id,'complete'=>1),
				'order'=>'position DESC',
				'fields'=>'Touchtype.lettercontent',
				'recursive'=>2
				)
			);
		$this->set('lettercontent',$stepdetails['Touchtype']['lettercontent']);
		$this->render('letter','pdf');
	}
	
	function labels($id = null) {
		if(!$this->Campaign->validateCampaign($id,$this->Session->read('User.account_id')))
			$this->redirect('/campaigns',null,true);
			
		$this->Campaign->bindModel(array('hasMany'=>array('Step','Prospect'=>array(
			'fields'=>'Prospect.firstname,Prospect.lastname,Prospect.address1,Prospect.address2,Prospect.city,Prospect.state,Prospect.postalcode,Prospect.country',
			'conditions'=>array('Prospect.open'=>1)
			)
		)),false);
		$this->Step->bindModel(array('belongsTo'=>array('Touchtype')),false);
		
		$this->Campaign->id=$id;
		$this->set('campaigns',$this->Campaign->read());
		
		$this->render('labels','pdf');	
	}
	
}
?>