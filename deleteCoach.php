<?php
// Process delete operation after confirmation
session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if($_SESSION["tournament"] && $_SESSION["loggedin"] == true){
    // Include config file
    require_once "config.php";
    
    if(isset($_POST["id"]) && !empty($_POST["id"])){
	
	$sql_delete_coach = "DELETE coaches, persons FROM coaches JOIN persons ON coaches.id = persons.id WHERE persons.id =".$_POST["id"];
	$sql_team_null = "UPDATE teams SET coach = NULL WHERE teams.id =".$_SESSION["currentTeamId"];
    //DELETE persons, players, teams FROM persons JOIN players ON players.id = persons.id JOIN teams ON teams.id = players.team WHERE players.team = 1
    try{
			mysqli_query($link, $sql_team_null);
			mysqli_query($link, $sql_delete_coach);
			header("location: readTeam.php?id=".$_SESSION["currentTeamId"]);
		} catch (mysqli_sql_exception $exception){
			mysqli_rollback($link);
			throw $exception;
		}
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Record</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="src/css/index.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous">
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h1>Delete Record</h1>
                    </div>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="alert alert-danger">
                            <input type="hidden" name="id" value='<?php echo trim($_GET["id"]);?>'>
                            <h2>Are you sure you want to delete this record?</h2><br>
                            <div>
                                <input type="submit" value="Yes" class="btn btn-danger">
                                <a href="readTeam.php?id=<?php echo $_SESSION["currentTeamId"]; ?>" class="btn btn-default">No</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>        
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script> 
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>