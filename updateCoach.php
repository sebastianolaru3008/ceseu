<?php
// Include config file
require_once "config.php";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
session_start();
$fname=$lname=$bdate=$experience='';
$fname_err=$lname_err=$bdate_err=$experience_err='';
$idTeam=$idCoach=0;
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["admin"] == 0){
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
	$sql="SELECT * from coaches JOIN persons on persons.id=coaches.id where coaches.id=".$_GET["id"];
	$result = mysqli_query($link, $sql);
	$row = mysqli_fetch_array($result);
	$fname=$row["fname"];
	$lname=$row["lname"];
	$bdate=$row["birthdate"];
	$experience=$row["experience"];
	
	$idCoach = $_GET["id"];
	//echo "$team";
	$fname_err = $lname_err = $bdate_err =$experience_err= "";
	mysqli_close($link);
}else{
	// Processing form data when form is submitted
	$idCoach = $_POST["idCoach"];
	$idTeam = $_SESSION["currentTeamId"];
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
	//Validate exp
	if(empty(trim($_POST["experience"]))){
			$experience_err = "Please fill this field!";
		}else if(trim($_POST["experience"]) < 0){
			$experience_err = "Invalid input: Negative number!";
		} else {
			$experience = trim($_POST["experience"]);
		}
	
	// Check input errors before inserting in database
	if(empty($fname_err) && empty($lname_err) && empty($bdate_err) && empty($experience_err)){
		// Prepare an insert statement
		$sql_update="UPDATE persons, coaches
			SET persons.fname = ?,
				persons.lname=?,
				coaches.experience=?,
				persons.birthdate=?
			WHERE
				persons.id = coaches.id
			AND coaches.id=?";
		try{
			$stmt1 = mysqli_prepare($link, $sql_update);
			mysqli_stmt_bind_param($stmt1, "ssisi", $fname, $lname, $experience,$bdate,$idCoach);
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
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Edit player</h2>
        <p>Please fill this form to edit this player.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
			<input type="hidden" name="idCoach" value="<?php echo $idCoach; ?>"/>
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
            <div class="form-group">
                <label>Birth Date</label>
                <input type="date" name="bdate" class="form-control" value="<?php echo $bdate; ?>">
                <span class="help-block"><?php echo $bdate_err; ?></span>
            </div>    
			<div class="form-group <?php echo (!empty($nickname_err)) ? 'has-error' : ''; ?>">
                <label>Experience</label>
                <input type="number" name="experience" class="form-control" value="<?php echo $experience; ?>">
                <span class="help-block"><?php echo $experience_err; ?></span>
            </div> 	
    
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit" name="submitplm">
				<a href="readTeam.php?id=<?php echo $_SESSION["currentTeamId"]; ?>" class="btn btn-danger pull-right">Back</a>
            </div>
    </form>
    </div>    
</body>
</html>