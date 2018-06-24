<title>world cup for jews</title>
<?php
$angusPoints = 0;
$waddPoints = 0;
$fennyPoints = 0;
$frewinPoints = 0;
$baniPoints = 0;
$config = parse_ini_file('config.ini', true);
$reqPrefs = array();
$reqPrefs['http']['method'] = 'GET';
$reqPrefs['http']['header'] = 'X-Auth-Token: ' . $config['authToken'];

function whichJew($team) {
	$angus = array("argentina", "germany", "iran", "serbia", "egypt", "nigeria");
	$wadd = array("australia", "tunisia", "spain", "switzerland", "england", "costa rica");
	$frewin = array("south korea", "peru", "denmark", "iceland", "france", "belgium");
	$fenny = array("poland", "russia", "panama", "sweden", "portugal", "croatia");
	$bani = array("uruguay", "colombia", "brazil", "japan", "morocco", "senegal");
	$team = strtolower($team);
	if(in_array($team, $angus)) {
		return "angus";
	} else if(in_array($team, $wadd)) {
		return "wadd";
	} else if(in_array($team, $fenny)) {
		return "fenny";
	} else if(in_array($team, $frewin)) {
		return "frewin";
	} else if(in_array($team, $bani)) {
		return "bani";
	} else {
		return "none";
	}
}

function newWeekFixtures() {
	$now = new DateTime();
    $end = new DateTime(); $end->add(new DateInterval('P7D'));
	return json_decode(file_get_contents("http://api.football-data.org/v1/competitions/467/fixtures/?timeFrameStart=" . $now->format('Y-m-d') . "&timeFrameEnd=" . $end->format('Y-m-d'), false, stream_context_create($reqPrefs)), true)["fixtures"];
}

$fixtures = json_decode(file_get_contents("http://api.football-data.org/v1/competitions/467/fixtures", false, stream_context_create($reqPrefs)), true);
$fixtures = $fixtures["fixtures"];
foreach($fixtures as $fixture){
	if($fixture["status"] == "FINISHED") {
		$homeGoals = intval($fixture["result"]["goalsHomeTeam"]);
		$awayGoals = intval($fixture["result"]["goalsAwayTeam"]);
		$winner = "draw";
		if($homeGoals > $awayGoals) {
			$winner = "home";
		} else if($awayGoals > $homeGoals) {
			$winner = "away";
		}
		switch(whichJew($fixture["homeTeamName"])){
			case "angus":
				$angusPoints = $angusPoints + $homeGoals;
				if($winner == "home") {
					$angusPoints = $angusPoints + 3;
				}
				if($winner == "draw") {
					$angusPoints = $angusPoints + 1;
				}
				break;
			case "frewin":
				$frewinPoints = $frewinPoints + $homeGoals;
				if($winner == "home") {
					$frewinPoints = $frewinPoints + 3;
				}
				if($winner == "draw") {
					$frewinPoints = $frewinPoints + 1;
				}
				break;
			case "wadd":
				$waddPoints = $waddPoints + $homeGoals;
				if($winner == "home") {
					$waddPoints = $waddPoints + 3;
				}
				if($winner == "draw") {
					$waddPoints = $waddPoints + 1;
				}
				break;
			case "fenny":
				$fennyPoints = $fennyPoints + $homeGoals;
				if($winner == "home") {
					$fennyPoints = $fennyPoints + 3;
				}
				if($winner == "draw") {
					$fennyPoints = $fennyPoints + 1;
				}
				break;
			case "bani":
				$baniPoints = $baniPoints + $homeGoals;
				if($winner == "home") {
					$baniPoints = $baniPoints + 3;
				}
				if($winner == "draw") {
					$baniPoints = $baniPoints + 1;
				}
				break;
			case "none":
				break;
		}
		switch(whichJew($fixture["awayTeamName"])){
			case "angus":
				$angusPoints = $angusPoints + $awayGoals;
				if($winner == "away") {
					$angusPoints = $angusPoints + 3;
				}
				if($winner == "draw") {
					$angusPoints = $angusPoints + 1;
				}
				break;
			case "frewin":
				$frewinPoints = $frewinPoints + $awayGoals;
				if($winner == "away") {
					$frewinPoints = $frewinPoints + 3;
				}
				if($winner == "draw") {
					$frewinPoints = $frewinPoints + 1;
				}
				break;
			case "wadd":
				$waddPoints = $waddPoints + $awayGoals;
				if($winner == "away") {
					$waddPoints = $waddPoints + 3;
				}
				if($winner == "draw") {
					$waddPoints = $waddPoints + 1;
				}
				break;
			case "fenny":
				$fennyPoints = $fennyPoints + $awayGoals;
				if($winner == "away") {
					$fennyPoints = $fennyPoints + 3;
				}
				if($winner == "draw") {
					$fennyPoints = $fennyPoints + 1;
				}
				break;
			case "bani":
				$baniPoints = $baniPoints + $awayGoals;
				if($winner == "away") {
					$baniPoints = $baniPoints + 3;
				}
				if($winner == "draw") {
					$baniPoints = $baniPoints + 1;
				}
				break;
			case "none":
				break;
		}
	}
}
echo "<h1>point totals: </h1>angus: ".$angusPoints."<br>";
echo "wadd: ".$waddPoints."<br>";
echo "fenny: ".$fennyPoints."<br>";
echo "frewin: ".$frewinPoints."<br>";
echo "bani: ".$baniPoints."<br>";
echo "<h1>upcoming matches: </h1>";
$response = newWeekFixtures();
?>
				<table>
                    <tr>
						<th>date</th>
						<th>time</th>
                        <th>home</th>
                        <th>away</th>
                        <th>status</th>
                        <th colspan="3">Result</th>
                    </tr>
                    <?php foreach ($response as $fixture) { ?>
                    <tr>
						<?php 
							$datentime = explode("T", $fixture["date"]);
							echo "<td>".$datentime[0]."</td>";
							echo "<td>".substr($datentime[1], 0, -1)."</td>";
						?>
                        <td><?php echo $fixture["homeTeamName"]; ?></td>
                        <td><?php echo $fixture["awayTeamName"]; ?></td>
						<td><?php echo $fixture["status"]; ?></td>
						<?php 
							if($fixture["status"] == "TIMED") {
								echo "<td>N</td>";
								echo "<td>/</td>";
								echo "<td>A</td>";
							} else {
								echo "<td>".$fixture["result"]["goalsHomeTeam"]."</td>";
								echo "<td>:</td>";
								echo "<td>".$fixture["result"]["goalsAwayTeam"]."</td>";
							}
						?>
                        
                    </tr>
                    <?php } ?>
                </table>