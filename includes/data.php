<?php
class WorldCupData{
	public $baseUri;
	public $apiKey;
	public $competition;
	public $config;
	
	public $fixtures;
	
	public function __construct() {
		$this->config = parse_ini_file('config.ini', true);
		$this->baseUri = $this->config['baseUri'];
		$this->apiKey = $this->config['apiKey'];
		$this->competition = $this->config['competition'];
		
		$this->loadFixtures();
	}
	
	private function loadFixtures() {
		$reqPrefs = array();
		$reqPrefs['http']['method'] = 'GET';
		$reqPrefs['http']['header'] = 'X-Auth-Token: ' . $this->apiKey;
		$this->fixtures = json_decode(file_get_contents($this->baseUri.'competitions/'.$this->competition.'/fixtures/', false, stream_context_create($reqPrefs)), true)['fixtures'];
	}
	
	public function getFixtures() {
		return $this->fixtures;
	}
	
	public function getUpcomingFixtures() {
		$temp = array();
		foreach($this->fixtures as $fixture) {
			$datentime = explode("T", $fixture["date"]);
			$dateObj = new DateTime($datentime[0]);
			$now = new DateTime();
			$now->sub(new DateInterval('P1D'));
			if($dateObj >= $now && $fixture['status'] != "SCHEDULED") {
				array_push($temp, $fixture);
			}
		}
		return $temp;
	}
	
	public function getAccountableFixtures() {
		$temp = array();
		foreach($this->fixtures as $fixture) {
			if($fixture['status'] == 'FINISHED' || $fixture['status'] == 'IN_PLAY') {
				array_push($temp, $fixture); 
			}
		}
		return $temp;
	}
	
}
?>