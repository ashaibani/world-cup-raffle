<html>
	<head>
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.1/css/bulma.min.css">
		<title>[@title]</title>
	</head>
	<body>
		<center>
			<br><div class="title"><h1>standings:</h1></div>
			<table class="table is-bordered is-striped is-narrow is-hoverable">
				<thead>
					<tr>
						<th>position</th>
						<th>person</th>
						<th>points</th>
					</tr>
				</thead>
				<tbody>
					[@points]
				</tbody>
			</table>
			<div class="title"><h1>upcoming matches:</h1></div>
			<table class="table is-bordered is-striped is-narrow is-hoverable">
				<thead>
					<tr>
						<th>date</th>
						<th>time</th>
						<th>home</th>
						<th>away</th>
						<th>status</th>
						<th colspan="3">result</th>
					</tr>
				</thead>
				<tbody>
					[@matches]
				</tbody>
			</table>
			<p>
				updates are made every [@interval] minute(s)<br>
				last update was: [@updated]<br>
				<a href="https://github.com/ashaibani/world-cup-raffle">src code</a>
			</p>
			<br>
		</center>
	</body>
</html>
