<?php
class PlayerHandler{
	private $players;
	private $points;
	
	public function  __construct() {
		$this->players = array();
		$this->points = array();
		$this->parsePlayers();
		$this->setupPoints();
	}
	
	private function parsePlayers() {
		$playersFile = parse_ini_file("players.ini", true);
		foreach($playersFile as $playerName => $player) {
			$this->players[$playerName] = array();
			foreach($player['teams'] as $teamName) {
				array_push($this->players[$playerName], $teamName);
			}
		}
	}
	
	public function getPlayers() {
		return $this->players;
	}
	
	private function setupPoints() {
		foreach($this->players as $playerName => $teams) {
			$this->points[$playerName] = 0;
		}
	}
	
	public function getPoints() {
		return $this->points;
	}
	
	public function getPlayer($team) {
		$tempName = '';
		foreach($this->players as $playerName => $teams) {
			foreach($teams as $teamName) {
				if(strtolower($teamName) == strtolower($team)) {
					$tempName = $playerName;
				}
			}
		}
		if($tempName == '') {
			$tempName = 'no one';
		}
		return $tempName;
	}
	
	public function addPoints($player, $amount) {
		$this->points[$player] = $this->points[$player] + intval($amount);
		arsort($this->points);
	}
}
?>