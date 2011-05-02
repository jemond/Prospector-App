<?php
class UploadsController extends AppController {

	var $name = 'Uploads';
	var $uses = array('Campaign','Source','Upload','Prospect');
	var $helpers = array('javascript','pretty');
	var $components = array('Authentication');
	
	function beforeFilter() 
    { 
		$this->Authentication->checkLoggedIn();
		$this->Authentication->requireNonSsl();
    }
	
	function index() {
		$account_id = $this->Session->read('User.account_id');
		
		$this->set('campaigns',$this->Campaign->getCampaigns($account_id));
		$this->set('sources',$this->Source->getSources($account_id));
		
		if(!empty($this->data)) {
			// check source			
			$step = $this->data['Upload']['step'];
			
			if($step == 2) {		
				$this->Upload->set($this->data);
				if(!$this->Upload->validates())
					$this->render();
				else {
					$account_id = $this->Session->read('User.account_id');
					$filedetails = $this->data['Upload']['data'];
					$filepath = $filedetails['tmp_name'];
					$this->Session->write('Upload.source_id',$this->data['Upload']['source_id']);
					$this->Session->write('Upload.campaign_id',$this->data['Upload']['campaign_id']);		
					$this->Session->write('Upload.campaign_name',$this->data['Campaign']['name']);			
					
					if(fopen($filepath,'r')) {
						$handle = fopen($filepath,'r');
						while (!feof($handle) ) {
							$trow = fgetcsv($handle);
							if($trow)
								$csv[] = $trow;
						}
						
						$columns = $csv[0];
						$import_count = count($csv)-1;
						$this->Session->write('Upload.csv',$csv);
						
						// make sure this wouldn't bring them over the limit
						if($import_count+$this->Prospect->openProspectCount($this->Session->read('User.account_id')) > $this->Session->read('Plan.prospect_limit')) {
							$this->Session->delete('Upload');
							$errorMsg = "This file includes $import_count prospects, which would bring you over the open prospect limit for your account (which is ".$this->Session->read('Plan.prospect_limit')."). Either close some prospects or upgrade your account.";
							$this->Session->setFlash($errorMsg,'message-error'); 
							$this->redirect('/uploads/',null,true); 
						}
						
						foreach($columns as $column) {
							$column_matches[] = $this->Upload->matchColumn($column);
						}
						
						$this->set('columns',$this->Upload->getColumnList());
						$this->set('user_columns',$columns);
						$this->set('records',$import_count);
						$this->set('field_count',count($columns));
						$this->set('sample',$csv[1]);
						$this->set('matches',$column_matches);
					}
					
					$this->render('step2');
				}
			}
			else if($step == 3) {
				$mappings = $this->data['Upload'];
				$data = $this->Session->read('Upload.csv');
				$source_id = $this->Session->read('Upload.source_id');
				$campaign_id = $this->Session->read('Upload.campaign_id');
				$campaign_name = $this->Session->read('Upload.campaign_name');
				$account_id = $this->Session->read('User.account_id');
				
				// protect against faked sources, camapigns
				if(!$this->Source->validateSource($source_id,$account_id))
					$this->redirect('/upload',null,true);
				if(is_numeric($campaign_id) && !$this->Campaign->validateCampaign($campaign_id,$account_id))
					$this->redirect('/upload',null,true);
				
				// create the campaign is 	
				if($campaign_name != '') {
					$campaign['Campaign']['name'] = $campaign_name;
					$campaign['Campaign']['account_id'] = $account_id;
					$this->Campaign->save($campaign);
					$campaign_id = $this->Campaign->id;
					pr($campaign_id);
				}
				
				// confirm mappings
				foreach($mappings as $id=>$column) {
					$mappings[$id] = $this->Upload->matchColumn($column);
				}
				
				$prospects = array();
				$prospect_counter = 0;
				foreach($data as $record=>$row) {
					if($record != 0) {
						foreach($row as $key=>$fieldata) {
							$field = $mappings['field'.$key];						
							if($field) {
								$prospects[$prospect_counter][$field] = $fieldata;
							}
						}
						$prospect_counter++;
					}
				}
				
				$import_count = 0;
				
				foreach($prospects as $prospect) {
					if(is_numeric($campaign_id))
						$prospect['campaign_id'] = $campaign_id;

					$prospect['source_id'] = $source_id;					
					$prospect['account_id'] = $this->Session->read('User.account_id');
					$p = array('Prospect'=>$prospect);
		
					$this->Prospect->create();
					$this->Prospect->set($p);
					if($this->Prospect->validates()) {
						$this->Prospect->save();
						$import_count++;
						
						$this->Source->setProspectCounts(false,$source_id);
						if(is_numeric($campaign_id))
							$this->Campaign->setProspectCounts(false,$campaign_id);				
					}
				}
				
				$this->Session->delete('Upload');
				
				$this->Session->setFlash("You imported $import_count prospects!",'message-success'); 
				$this->redirect('done',null,true); 
			}
			
		}
	}
	
	function done() {
	
	}
}

?>