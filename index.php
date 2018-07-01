<?php
ini_set('display_errors', 1);
date_default_timezone_set('Europe/London');
include('./includes/players.php');
include('./includes/data.php');
include('./includes/template.php');
$teamPoints = array();
$statusCodes = array(
    "in progress" => "Currently being played",
	"future" => "To be played",
	"completed" => "Finished"
);

$playerHandler = new PlayerHandler();
$dataHandler = new WorldCupData();
$template = new Template('index.tpl');

foreach($dataHandler->getAccountableFixtures() as $fixture) {
	$homePoints = intval($fixture["home_team"]["goals"]);
	$awayPoints = intval($fixture["away_team"]["goals"]);
	if($fixture['winner'] == $fixture["home_team_country"]) {
		$homePoints = $homePoints + 3;
	} else if($fixture['winner'] == $fixture["away_team_country"]) {
		$awayPoints = $awayPoints + 3;
	} else if($fixture['winner'] == "Draw") {
		$homePoints = $homePoints + 1;
		$awayPoints = $awayPoints + 1;
	}
	$teamPoints[$fixture["home_team_country"]] = $teamPoints[$fixture["home_team_country"]] + $homePoints;
	$teamPoints[$fixture["away_team_country"]] = $teamPoints[$fixture["away_team_country"]] + $awayPoints;
	
	$playerHandler->addPoints($playerHandler->getPlayer($fixture["home_team_country"]), $homePoints);
	$playerHandler->addPoints($playerHandler->getPlayer($fixture["away_team_country"]), $awayPoints);
}

$i = 1;
foreach($playerHandler->getPoints() as $player => $points) {
	if(!($player == "no one" || is_null($player))) {
		$row = new Template('points_tbl.tpl');
		$row->set('player', $player);
		$row->set('points', $points);
		$row->set('position', $i);
		$pointsTemplate[] = $row;
		$i++;
	}
}

foreach($dataHandler->getUpcomingFixtures() as $fixture) {
	$row = new Template('upcoming_tbl.tpl');
	$datentime = explode("T", $fixture["datetime"]);
	$time = substr($datentime[1], 0, -1);
	
	sscanf($time, "%d:%d:%d", $hours, $minutes, $seconds);
	$hours = intval($hours) + 1;
	if($minutes == 0 || $minutes == "0") {
		$minutes = "00";
	}
	if($seconds == 0 || $seconds == "0") {
		$seconds = "00";
	}
	
	$time = $hours.':'.$minutes.':'.$seconds;
	
	$row->set('date', $datentime[0]);
	$row->set('time', $time);
	$row->set('stage', $fixture['stage_name']);
	$row->set('home', ($fixture['winner'] == $fixture["home_team_country"] ? '<b>'.$fixture["home_team_country"].'</b>' : $fixture["home_team_country"].'').' ('.$playerHandler->getPlayer($fixture["home_team_country"]).')');
	$row->set('away', ($fixture['winner'] == $fixture["away_team_country"] ? '<b>'.$fixture["away_team_country"].'</b>' : $fixture["away_team_country"].'').' ('.$playerHandler->getPlayer($fixture["away_team_country"]).')');
	$row->set('status', $statusCodes[$fixture["status"]]);
	
	if($fixture['status'] == 'in progress' || $fixture['status'] == "completed") {
		$row->set('result', $fixture["home_team"]["goals"] . ':' . $fixture["away_team"]["goals"]);
	} else {
		$row->set('result', 'N/A');
	}
	$matchesTemplate[] = $row;
}
$pointsContents = Template::merge($pointsTemplate);
$matchesContents = Template::merge($matchesTemplate);

$template->set('title', 'world cup raffle');
$template->set('points', $pointsContents);
$template->set('matches', $matchesContents);
$template->set('interval', ($dataHandler->updateInterval / 60));
$template->set('updated', ($dataHandler->getLastUpdated() > $dataHandler->updateInterval ? 'now!' : $dataHandler->getLastUpdated().' seconds ago'));
echo $template->output();
?>