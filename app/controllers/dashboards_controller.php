<?php
class DashboardsController extends AppController {

	var $name = 'Dashboards';
	var $uses = array('Prospect','Campaign','Source','Debug','Profile');
	var $helpers = array('Pretty','Text','Javascript','Ajax','Merge','Session');
	var $components = array('Authentication');

	function beforeFilter() 
    {
		$this->Authentication->checkLoggedIn();		
		$this->Authentication->requireNonSsl();
    }
		
	function index() {
		$this->set('nProspects',$this->Prospect->openProspectCount($this->Session->read('User.account_id')));
		$this->set('nCampaigns',$this->Campaign->getOpenCampaignCount($this->Session->read('User.account_id')));
		$this->set('fOverage',$this->Prospect->overage($this->Session->read('User.account_id'),$this->Session->read('Plan.prospect_limit')));
		$this->set('stats',$this->Source->getStats($this->Session->read('User.account_id')));
		$this->set('overallstats',$this->Source->getOverallStats($this->Session->read('User.account_id')));
		$this->set('title','Dashboard');
	}
	
	function stats() {
		$this->set('stats',$this->Source->getStats($this->Session->read('User.account_id')));
		$this->render('stats','csv');
	}
}

?>