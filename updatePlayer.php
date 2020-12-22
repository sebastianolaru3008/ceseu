<?php
// Include config file
require_once "config.php";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
session_start();
$fname=$lname=$bdate=$nickname='';
$fname_err=$lname_err=$bdate_err=$nickname_err='';
$idTeam=$idPlayer=0;
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
	$sql="SELECT * from players JOIN persons on persons.id=players.id where players.id=".$_GET["id"];
	$result = mysqli_query($link, $sql);
	$row = mysqli_fetch_array($result);
	$fname=$row["fname"];
	$lname=$row["lname"];
	$bdate=$row["birthdate"];
	$nickname=$row["nickname"];
	
	$idTeam = $row["team"];
	$idPlayer = $_GET["id"];
	//echo "$team";
	$fname_err = $lname_err = $bdate_err =$nickname_err= "";
	mysqli_close($link);
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
		$sql_update="UPDATE persons, players
			SET persons.fname = ?,
				persons.lname=?,
				players.nickname=?,
				persons.birthdate=?
			WHERE
				persons.id = players.id
			AND players.id=?";
		try{
			$stmt1 = mysqli_prepare($link, $sql_update);
			mysqli_stmt_bind_param($stmt1, "ssssi", $fname, $lname, $nickname,$bdate,$idPlayer);
			mysqli_stmt_execute($stmt1);
			
			//echo "$fname, $lname, $nickname, $bdate, $idPlayer";
			header("location: readTeam.php?id=".$idTeam);
		} catch (mysqli_sql_exception $exception){
			mysqli_rollback($link);
			throw $exception;
		}
		//start the transaction to ensure that all the sql statements are executed

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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
	<link rel="stylesheet" href="src/css/index.css">
</head>
<body>
    <div class="wrapper">
        <h2>Edit player</h2>
        <p>Please fill this form to edit this player.</p>
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
                <input type="submit" class="btn btn-primary" value="Submit" name="submit">
				<a href="readTeam.php?id=<?php echo $_SESSION["currentTeamId"]; ?>" class="btn btn-danger pull-right">Back</a>
            </div>
    </form>
    </div>    
</body>
</html>