<!DOCTYPE html>
<html>
<head>
</head>
<body>

<?php
echo "<script>alert('muie dragnea');</script>";
$q = $_GET['q'];

require_once "config.php";
if (!$link) {
  die('Could not connect: ' . mysqli_error($link));
}

$sql="SELECT players.id, nickname FROM players JOIN teams ON teams.id = players.team WHERE teams.shortname = '".$q."'";
$result = mysqli_query($link,$sql);

if(mysqli_num_rows($result) > 0){
echo "<table class = 'table table-dark table-hover'>
<tr>
<th>Nickname</th>
<th>Kills</th>
<th>Deaths</th>
<th>Assists</th>
<th>Headshots</th>
<th>Mvps</th>
</tr>";
while($row = mysqli_fetch_array($result)) {

echo "<input hidden name='players[]' value='".$row[0]."'>";
  echo "<tr>";
  echo "<td>" . $row[1] . " </td>";
  echo "<td> <input class = 'table-control' type='number' name='kills[]'>";
  echo "<td> <input class = 'table-control' type='number' name='deaths[]'>";
  echo "<td> <input class = 'table-control' type='number' name='assists[]'>";
  echo "<td> <input class = 'table-control' type='number' name='headshots[]'>";
  echo "<td> <input class = 'table-control' type='number' name='mvps[]'>";
  echo "</tr>";
}
echo "</table>";
} else {
	echo "nincs results";
}
mysqli_close($link);
?>
</body>
</html>