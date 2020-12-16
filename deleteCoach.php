<?php
// Process delete operation after confirmation
session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if($_SESSION["admin"] == 1 && $_SESSION["loggedin"] == true){
    // Include config file
    require_once "config.php";
    
    if(isset($_POST["id"]) && !empty($_POST["id"])){
	
	$sql_delete_coach = "DELETE coaches, persons FROM coaches JOIN persons ON coaches.id = persons.id WHERE persons.id =".$_POST["id"];
	$sql_team_null = "UPDATE teams SET coach = NULL WHERE teams.id =".$_SESSION["currentTeamId"];
    //DELETE persons, players, teams FROM persons JOIN players ON players.id = persons.id JOIN teams ON teams.id = players.team WHERE players.team = 1
    try{
			//mysqli_begin_transaction($link);
			
			//$stmt1 = mysqli_prepare($link, $sql_persons);
			//mysqli_stmt_bind_param($stmt1, "sss", $fname, $lname, $bdate);
			//mysqli_stmt_execute($stmt1);
			
			//printf ("New Record has id %d.\n", mysqli_insert_id($link));
			//$stmt2 = mysqli_prepare($link, $sql_users);
			//mysqli_stmt_bind_param($stmt2, "sss", mysqli_insert_id($link), $email, password_hash($password, PASSWORD_DEFAULT));
			//mysqli_stmt_execute($stmt2);
			
			mysqli_query($link, $sql_team_null);
			mysqli_query($link, $sql_delete_coach);
			
			//mysqli_commit($link);
			header("location: readTeam.php?id=".$_SESSION["currentTeamId"]);
		} catch (mysqli_sql_exception $exception){
			mysqli_rollback($link);
			throw $exception;
		}
         
        
    }

     
  
    // Close connection
    mysqli_close($link);
} else{
    // Check existence of id parameter
    
        // URL doesn't contain id parameter. Redirect to error page
        //header("location: error.php");
        //exit();

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Record</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper{
            width: 500px;
            margin: 0 auto;
        }
    </style>
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
                        <div class="alert alert-danger fade in">
                            <input type="hidden" name="id" value="<?php echo trim($_GET["id"]); ?>"/>
                            <p>Are you sure you want to delete this record?</p><br>
                            <p>
                                <input type="submit" value="Yes" class="btn btn-danger">
                                <a href="readTeam.php?id=<?php echo $_SESSION["currentTeamId"]; ?>" class="btn btn-default">No</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>