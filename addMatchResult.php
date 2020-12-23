<?php
require_once "config.php";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
session_start();
// Define variables and initialize with empty values
$team1 = $team2= $score2= $score1= $kills = $deaths = $assists = $headshots = $mvps = "";
$tournament=$_SESSION["tournament"];

$team1_err = $team2_err = $score2_err= $score1_err= $playerScore_err = "";
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !$_SESSION["tournament"]){
    header("location: error.php");
    exit();
}
if($_SERVER["REQUEST_METHOD"] == "POST"){
	$players = $_POST['players'];
	$kills = $_POST["kills"];
	$deaths = $_POST["deaths"];
	$assists = $_POST["assists"];
	$headshots = $_POST["headshots"];
	$mvps = $_POST["mvps"];
	$team1 = trim($_POST["team1"]);
	$team2 = trim($_POST["team2"]);
	$score1 = trim($_POST["score1"]);
	$score2 = trim($_POST["score2"]);
	

	if(empty($team1)){
		$team1_err = "Null team1";
	}else if(empty($team2)){
		$team2_err = "Null team2";
	}else if($team1==$team2){
		$team1_err=$team2_err = "They can't play themselves you dumb fuck";
	}
	if($score1<0){
		$score1_err="No fucking way";
	}
	if($score2<0){
		$score2_err="No fucking way";
	}
	
	if(!isset($_POST['players']) || !isset($_POST["kills"]) || !isset($_POST["deaths"]) || !isset($_POST["assists"]) || !isset($_POST["headshots"]) || !isset($_POST["mvps"])){
		$playerScore_err = 'Null inputs!';
	} else {
		foreach($kills as $key => $value){
			if($kills[$key] < 0 || $deaths[$key] < 0 || $assists[$key] < 0 || $headshots[$key] < 0 || $mvps[$key] < 0 || $headshots[$key] > $kills[$key]){
				$playerScore_err = 'Invalid inputs!';
			}
		}
	}
	
	/* foreach (array_keys($playerScore) as $fieldKey) {
		foreach ($playerScore[$fieldKey] as $key=>$value) {
			if($value['kills'] < 0 || $value['deaths'] < 0 || $value['assists'] < 0 || $value['headshots'] < 0 || $value['mvps'] < 0 || $value['headshots'] <= $value['kills']){
			$playerScore_err = 'Invalid inputs!';
			}
		}
	} */
	/* foreach($playerScore as $value) {
		if($value['kills'] < 0 || $value['deaths'] < 0 || $value['assists'] < 0 || $value['headshots'] < 0 || $value['mvps'] < 0 || $value['headshots'] <= $value['kills']){
			$playerScore_err = 'Invalid inputs!';
		}
	} */
	
		 // Check input errors before inserting in database
    if(empty($team1_err) && empty($team2_err) &&empty($score1_err)&&empty($score2_err) && empty($playerScore_err)){
        
        // Prepare an insert statement
		$sql_match = "INSERT INTO matchresults(id_team1,id_team2,score_team1,score_team2) VALUES (?, ?, ?, ?)";
		$sql_team = 'SELECT id FROM teams where shortname = ?';
		
		
		$stmtteam = mysqli_prepare($link, $sql_team);
		mysqli_stmt_bind_param($stmtteam, "s",$team1);
		mysqli_stmt_execute($stmtteam);
		$result=mysqli_stmt_get_result($stmtteam);
		$row = mysqli_fetch_array($result);
		$team1_id=$row[0];
		
		$stmtteam = mysqli_prepare($link, $sql_team);
		mysqli_stmt_bind_param($stmtteam, "s",$team2);
		mysqli_stmt_execute($stmtteam);
		$result=mysqli_stmt_get_result($stmtteam);
		$row = mysqli_fetch_array($result);
		$team2_id=$row[0];
		//start the transaction to ensure that all the sql statements are executed
	
		try{
			mysqli_begin_transaction($link);
			
			$stmt1 = mysqli_prepare($link, $sql_match);
			mysqli_stmt_bind_param($stmt1, "iiii", $team1_id, $team2_id,$score1,$score2);
			mysqli_stmt_execute($stmt1);
			
			$currentMatchId = mysqli_insert_id($link);
			
			foreach($players as $key => $value){
				$sql_player_score = "INSERT INTO individualresults (id_player, id_match, kills, assists, deaths, mvps, headshots)
									VALUES (?, ?, ?, ?, ?, ?, ?)";
									
				$stmt = mysqli_prepare($link, $sql_player_score);
				mysqli_stmt_bind_param($stmt, "iiiiiii",$players[$key], $currentMatchId, $kills[$key], $assists[$key], $deaths[$key], $mvps[$key], $headshots[$key]);
				mysqli_stmt_execute($stmt);
			}
			
			
			mysqli_commit($link);
			header("location: editMatch.php");
		} catch (mysqli_sql_exception $exception){
			mysqli_rollback($link);
			throw $exception;
		}
         
            mysqli_close($link);
    }
    

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Team</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
	<style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
    <div class="wrapper">
        <h2>Adding Match Result</h2>
        <p>Please fill this form to add a new match</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
			<span class="help-block"><?php echo $playerScore_err; ?></span>
			<div class="form-group <?php echo (!empty($team1_err)) ? 'has-error' : ''; ?>" >
				<label>Team 1</label>
				<select id='team1' class='form-control' name='team1' onChange='getPlayers1(this.value)'>
				<?php
						echo "<option value='' selected disabled hidden></option>";
						$sql = "SELECT shortname FROM teams where tournament=?";
						$stmt = mysqli_prepare($link, $sql);
						mysqli_stmt_bind_param($stmt, "i",$tournament);
						mysqli_stmt_execute($stmt);
						$result=mysqli_stmt_get_result($stmt);
							while($row = mysqli_fetch_array($result)){
								echo "<option value=".$row[0].">".$row[0]."</option>";
							}						
				  ?>
				</select>
				 <span class="help-block"><?php echo $team1_err; ?></span>
			</div>
			<div id="result1">
				
			</div>
			<div class="form-group <?php echo (!empty($team2_err)) ? 'has-error' : ''; ?>" >
				<label>Team 2</label>
				<select id='team2' class="form-control" name='team2' onChange='getPlayers2(this.value)'>
				<?php
						echo "<option value='' selected disabled hidden></option>";
						$stmt = mysqli_prepare($link, $sql);
						mysqli_stmt_bind_param($stmt, "i",$tournament);
						mysqli_stmt_execute($stmt);
						$result=mysqli_stmt_get_result($stmt);
							while($row = mysqli_fetch_array($result)){
								echo "<option value=".$row[0].">".$row[0]."</option>";
							}   						
				  ?>
				</select>
				 <span class="help-block"><?php echo $team2_err; ?></span>
			</div>
			<div id="result2">
				
			</div>
			
			
			
			<div class="form-group <?php echo (!empty($score1_err)) ? 'has-error' : ''; ?>">
                <label>Score team 1</label>
                <input type="number" name="score1" class="form-control" value="<?php echo $score1; ?>">
                <span class="help-block"><?php echo $score1_err; ?></span>
            </div> 
			<div class="form-group <?php echo (!empty($score2_err)) ? 'has-error' : ''; ?>">
                <label>Score team 2</label>
                <input type="number" name="score2" class="form-control" value="<?php echo $score2; ?>">
                <span class="help-block"><?php echo $score2_err; ?></span>
            </div> 
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a href="editMatch.php" class="btn btn-danger pull-right">Back</a>
            </div>
        </form>
    </div> 


<script>
	
	function getPlayers1(str){
		if (str == "") {
			document.getElementById("result1").innerHTML = "";
			return;
		} else {
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				document.getElementById("result1").innerHTML = this.responseText;
				}
			};
		}
		xmlhttp.open("GET","TeamPlayersResult.php?q="+str,true);
		xmlhttp.send();
	}
	
	function getPlayers2(str){
		if (str == "") {
			document.getElementById("result2").innerHTML = "";
			return;
		} else {
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				document.getElementById("result2").innerHTML = this.responseText;
				}
			};
		}
		xmlhttp.open("GET","TeamPlayersResult.php?q="+str,true);
		xmlhttp.send();
	}
</script>

	
</body>
</html>