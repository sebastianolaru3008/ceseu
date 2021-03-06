<?php
// Include config file
require_once "config.php";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
session_start();
$fname=$lname=$bdate=$nickname='';
$fname_err=$lname_err=$bdate_err=$nickname_err='';
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !$_SESSION["tournament"]){
    header("location: error.php");
    exit();
}
if($_SERVER["REQUEST_METHOD"] != "POST"){
	if(!isset($_GET["id"]) || empty(trim($_GET["id"]))){
		// Include config file
		// Prepare a select statement
		//header("location: error.php");
		//exit();
	}
	$idTeam = $_GET["id"];
}else{
	// Processing form data when form is submitted
	$idPlayer = $_POST["idPlayer"];
	$idTeam = $_POST["idTeam"];
	//Validate fname
	if(empty(trim($_POST["fname"]))){
		$fname_err = "Please fill this field!";
	} else {
		$fname = trim($_POST["fname"]);
	}
	
	//Validate lname
	if(empty(trim($_POST["lname"]))){
		$lname_err = "Please fill this field!";
	} else {
		$lname = trim($_POST["lname"]);
	}
	
	//Validate bdate
	if(empty(trim($_POST["bdate"]))){
		$bdate_err = "Please fill this field!";
	} else {
		$bdate = date('Y-m-d', strtotime($_POST["bdate"]));
	}
	//Validate nickname
	if(empty(trim($_POST["nickname"]))){
		$nickname_err = "Please fill this field!";
	} else {
		$nickname = trim($_POST["nickname"]);
	}
	
	// Check input errors before inserting in database
	if(empty($fname_err) && empty($lname_err) && empty($bdate_err) && empty($nickname_err)){
		// Prepare an insert statement
		//INSERT INTO persons (fname,lname,birthdate) VALUES('cosmin','stretea','10-03-2001');
		//INSERT INTO players(id,nickname,team) VALUES(20,'gutut',2);
		$sql_persons="INSERT INTO persons (fname,lname,birthdate) VALUES(?,?,?)";
		$sql_players="INSERT INTO players(id,nickname,team) VALUES(?,?,?)";
		try{
			mysqli_begin_transaction($link);
			
			$stmt1 = mysqli_prepare($link, $sql_persons);
			mysqli_stmt_bind_param($stmt1, "sss", $fname, $lname,$bdate);
			mysqli_stmt_execute($stmt1);
		
			$stmt2 = mysqli_prepare($link, $sql_players);
			mysqli_stmt_bind_param($stmt2, "iss",  mysqli_insert_id($link), $nickname, $idTeam);
			mysqli_stmt_execute($stmt2);
			
			mysqli_commit($link);
			header("location: readTeam.php?id=".$idTeam);
		} catch (mysqli_sql_exception $exception){
			mysqli_rollback($link);
			throw $exception;
		}


	}
	// Close connection
	mysqli_close($link);

}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="src/css/index.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous">
</head>
<body>
    <div class="wrapper">
        <h1>Edit player</h1>
        <h2>Please fill this form to edit this player.</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
			<input type="hidden" name="idPlayer" value="<?php echo $idPlayer; ?>"/>
			<input type="hidden" name="idTeam" value="<?php echo $idTeam; ?>"/>
			<div class="form-group <?php echo (!empty($fname_err)) ? 'has-error' : ''; ?>">
                <label>First Name</label>
                <input type="text" name="fname" class="form-control" value="<?php echo $fname; ?>">
                <span class="help-block"><?php echo $fname_err; ?></span>
            </div>  
			<div class="form-group <?php echo (!empty($lname_err)) ? 'has-error' : ''; ?>">
                <label>Last Name</label>
                <input type="text" name="lname" class="form-control" value="<?php echo $lname; ?>">
                <span class="help-block"><?php echo $lname_err; ?></span>
            </div> 
			<div class="form-group <?php echo (!empty($nickname_err)) ? 'has-error' : ''; ?>">
                <label>Nickname</label>
                <input type="text" name="nickname" class="form-control" value="<?php echo $nickname; ?>">
                <span class="help-block"><?php echo $nickname_err; ?></span>
            </div> 			
            <div class="form-group">
                <label>Birth Date</label>
                <input type="date" name="bdate" class="form-control" value="<?php echo $bdate; ?>">
                <span class="help-block"><?php echo $bdate_err; ?></span>
            </div>    
    
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit" name="submitplm">
				<a href="readTeam.php?id=<?php echo $_SESSION["currentTeamId"]; ?>" class="btn btn-danger float-right">Back</a>
            </div>
    </form>
	</div> 
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>