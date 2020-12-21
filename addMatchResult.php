<?php
require_once "config.php";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
session_start();
// Define variables and initialize with empty values
$team1 = $team2= $score2= $score1= "";
$tournament=$_SESSION["tournament"];

$team1_err = $team2_err = $score2_err= $score1_err= "";
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !$_SESSION["tournament"]){
    header("location: error.php");
    exit();
}
if($_SERVER["REQUEST_METHOD"] == "POST"){
	$team1 = trim($_POST["team1"]);
	$team2 = trim($_POST["team2"]);
	$score1 = trim($_POST["score1"]);
	$score2 = trim($_POST["score2"]);
	if($team1==$team2){
		$team1_err=$team2_err = "They can't play themselves you dumb fuck";
	}
	if($score1<0){
		$score1_err="No fucking way";
	}
	if($score2<0){
		$score2_err="No fucking way";
	}
		 // Check input errors before inserting in database
    if(empty($team1_err) && empty($team2_err) &&empty($score1_err)&&empty($score2_err)){
        
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
			
			printf ("New Record has id %d.\n", mysqli_insert_id($link));
			
			mysqli_commit($link);
			header("location: editMatch.php");
		} catch (mysqli_sql_exception $exception){
			mysqli_rollback($link);
			throw $exception;
		}
         
        
    }
    
    // Close connection
   // mysqli_close($link);
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
</head>
<body>
    <div class="wrapper">
	<?php echo "<p>".$tournament."</p>";?>
        <h2>Adding Match Result</h2>
        <p>Please fill this form to add a new match</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
			<div class="form-group <?php echo (!empty($team1_err)) ? 'has-error' : ''; ?>" >
				<label>Team 1</label>
				<select class='form-control' name='team1'>
				<?php
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
			<div class="form-group <?php echo (!empty($team2_err)) ? 'has-error' : ''; ?>" >
				<label>Team 2</label>
				<select class="form-control" name='team2'>
				<?php
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
</body>
</html>