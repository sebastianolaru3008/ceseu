<!DOCTYPE html>
<html>
<head>
<style>
table {
  width: 100%;
  border-collapse: collapse;
}

table, td, th {
  border: 1px solid black;
  padding: 5px;
}

th {text-align: left;}
</style>
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
echo "<table>
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
  echo "<td> <input type='number' name='kills[]'>";
  echo "<td> <input type='number' name='deaths[]'>";
  echo "<td> <input type='number' name='assists[]'>";
  echo "<td> <input type='number' name='headshots[]'>";
  echo "<td> <input type='number' name='mvps[]'>";
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