<?php
class WorldCupData {
	private $baseUri;
	private $config;
	private $lastUpdated;
	private $fixtures;
	
	public $updateInterval;
	
	public function __construct() {
		$this->config = parse_ini_file('config.ini', true);
		$this->lastUpdated = strtotime(file_get_contents('./lastUpdated'));
		$this->baseUri = $this->config['baseUri'];
		$this->updateInterval = intval($this->config['updateInterval']);
		$this->loadFixtures();
	}
	
	private function loadFixtures() {
		if($this->getLastUpdated() > $this->updateInterval) {
			$reqPrefs = array();
			$reqPrefs['http']['method'] = 'GET';
			$this->fixtures = json_decode(file_get_contents($this->baseUri, false, stream_context_create($reqPrefs)), true);
			$date = new DateTime();
			file_put_contents('./lastUpdated', $date->format('Y-m-d H:i:s'));
			file_put_contents("./fixtures.json",json_encode($this->fixtures));
		} else {
			$this->fixtures = json_decode(file_get_contents('./fixtures.json'), true);
		}
	}
	
	public function getFixtures() {
		return $this->fixtures;
	}
	
	public function getUpcomingFixtures() {
		$temp = array();
		foreach($this->fixtures as $fixture) {
			$datentime = explode("T", $fixture["datetime"]);
			$dateObj = new DateTime($datentime[0]);
			$now = new DateTime();
			$now->sub(new DateInterval('P1D'));
			if($dateObj >= $now && $fixture["home_team_country"] != null && $fixture["away_team_country"] != null) {
				array_push($temp, $fixture);
			}
		}
		return $temp;
	}
	
	public function getAccountableFixtures() {
		$temp = array();
		foreach($this->fixtures as $fixture) {
			if($fixture['status'] == 'completed' || $fixture['status'] == 'in progress') {
				array_push($temp, $fixture); 
			}
		}
		return $temp;
	}
	
	public function getLastUpdated() {
		return time() - $this->lastUpdated;
	}
}
?>