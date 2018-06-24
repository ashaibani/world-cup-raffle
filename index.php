<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
include('./includes/players.php');
include('./includes/data.php');
include('./includes/template.php');

$statusCodes = array(
    "IN_PLAY" => "Currently being played",
    "TIMED" => "To be played",
	"SCHEDULED" => "To be scheduled",
	"FINISHED" => "Finished"
);

$playerHandler = new PlayerHandler();
$dataHandler = new WorldCupData();
$template = new Template('index.tpl');

foreach($dataHandler->getAccountableFixtures() as $fixture) {
	$homePoints = intval($fixture["result"]["goalsHomeTeam"]);
	$awayPoints = intval($fixture["result"]["goalsAwayTeam"]);
	if(intval($fixture["result"]["goalsHomeTeam"]) > intval($fixture["result"]["goalsAwayTeam"])) {
		$homePoints = $homePoints + 3;
	} else if(intval($fixture["result"]["goalsAwayTeam"]) > intval($fixture["result"]["goalsHomeTeam"])) {
		$awayPoints = $awayPoints + 3;
	} else if(intval($fixture["result"]["goalsHomeTeam"]) == intval($fixture["result"]["goalsAwayTeam"])) {
		$homePoints = $homePoints + 1;
		$awayPoints = $awayPoints + 1;
	}
	
	$playerHandler->addPoints($playerHandler->getPlayer($fixture["homeTeamName"]), $homePoints);
	$playerHandler->addPoints($playerHandler->getPlayer($fixture["awayTeamName"]), $awayPoints);
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
	$datentime = explode("T", $fixture["date"]);
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
	$row->set('home', $fixture["homeTeamName"].' ('.$playerHandler->getPlayer($fixture["homeTeamName"]).')');
	$row->set('away', $fixture["awayTeamName"].' ('.$playerHandler->getPlayer($fixture["awayTeamName"]).')');
	$row->set('status', $statusCodes[$fixture["status"]]);
	
	if($fixture['status'] == 'IN_PLAY' || $fixture['status'] == "FINISHED") {
		$row->set('result1', $fixture["result"]["goalsHomeTeam"]);
		$row->set('result2', ':');
		$row->set('result3', $fixture["result"]["goalsAwayTeam"]);
	} else {
		$row->set('result1', 'N');
		$row->set('result2', '/');
		$row->set('result3', 'A');
	}
	$matchesTemplate[] = $row;
}

$pointsContents = Template::merge($pointsTemplate);
$matchesContents = Template::merge($matchesTemplate);

$template->set('title', 'world cup for jews');
$template->set('points', $pointsContents);
$template->set('matches', $matchesContents);

$template->set('updated', 'now bitch');

echo $template->output();
?>