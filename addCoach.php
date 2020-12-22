<?php
require_once "config.php";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
session_start();

// Define variables and initialize with empty values
$fname = $lname = $bdate = $experience ="";
$fname_err = $lname_err = $bdate_err = $experience_err ="";
if($_SERVER["REQUEST_METHOD"] == "POST"){
	if(empty(trim($_POST["fname"]))){
			$fname_err = "Please enter a team name.";
		} else{
			// Prepare a select statement
			$sql = "SELECT id FROM persons WHERE fname = ?";
			
			if($stmt = mysqli_prepare($link, $sql)){
				// Bind variables to the prepared statement as parameters
				mysqli_stmt_bind_param($stmt, "s", $param);
				
				// Set parameters
				$param = trim($_POST["fname"]);
				
				// Attempt to execute the prepared statement
				if(mysqli_stmt_execute($stmt)){
					/* store result */
					mysqli_stmt_store_result($stmt);
					
					if(mysqli_stmt_num_rows($stmt) == 1){
						$fname_err = "There is already a team with this name!";
					} else{
						$fname = trim($_POST["fname"]);
					}
				} else{
					echo "Oops! Something went wrong. Please try again later.";
				}

				// Close statement
				mysqli_stmt_close($stmt);
			}
		}
		if(empty(trim($_POST["lname"]))){
			$lname_err = "Please enter a short name.";
		}else {
			// Prepare a select statement
			$sql = "SELECT id FROM persons WHERE lname = ?";
			
			if($stmt = mysqli_prepare($link, $sql)){
				// Bind variables to the prepared statement as parameters
				mysqli_stmt_bind_param($stmt, "s", $param);
				
				// Set parameters
				$param = trim($_POST["lname"]);
				
				// Attempt to execute the prepared statement
				if(mysqli_stmt_execute($stmt)){
					/* store result */
					mysqli_stmt_store_result($stmt);
					
					if(mysqli_stmt_num_rows($stmt) == 1){
						$lname_err = "There is already a team with this short name!";
					} else{
						$lname = trim($_POST["lname"]);
					}
				} else{
					echo "Oops! Something went wrong. Please try again later.";
				}

				// Close statement
				mysqli_stmt_close($stmt);
			}
		}
		
		//validate exp
		if(empty(trim($_POST["experience"]))){
			$experience_err = "Please fill this field!";
		}else if(trim($_POST["experience"]) < 0){
			$experience_err = "Invalid input: Negative number!";
		} else {
			$experience = trim($_POST["experience"]);
		}
		
		//Validate bdate
		if(empty(trim($_POST["bdate"]))){
			$bdate_err = "Please fill this field!";
		} else {
			$bdate = date('Y-m-d', strtotime($_POST["bdate"]));
		}
		
		 // Check input errors before inserting in database
    if(empty($fname_err) && empty($lname_err) && empty($experience_err) && empty($bdate_err)){
        
        // Prepare an insert statement
		$sql_persons = "INSERT INTO persons (fname, lname, birthdate) VALUES (?, ?, ?)";
		$sql_coach = "INSERT INTO coaches (id, experience) VALUES (?, ?)";
		$sql_team = "UPDATE teams SET coach = ? WHERE teams.id = ".$_SESSION["currentTeamId"];
		
		//start the transaction to ensure that all the sql statements are executed
	
		try{
			mysqli_begin_transaction($link);
			
			$stmt1 = mysqli_prepare($link, $sql_persons);
			mysqli_stmt_bind_param($stmt1, "sss", $fname, $lname, $bdate);
			mysqli_stmt_execute($stmt1);
			
			$idPerson = mysqli_insert_id($link);
			
			$stmt2 = mysqli_prepare($link, $sql_coach);
			mysqli_stmt_bind_param($stmt2, "ss", $idPerson, $experience);
			mysqli_stmt_execute($stmt2);
			
			$stmt3 = mysqli_prepare($link, $sql_team);
			mysqli_stmt_bind_param($stmt3, "s", $idPerson);
			mysqli_stmt_execute($stmt3);
			
			
			mysqli_commit($link);
			header("location: readTeam.php?id=".$_SESSION["currentTeamId"]);
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
    <title>Add New Team</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" href="src/css/index.css">
</head>
<body>
    <div class="wrapper">
        <h2>Add Coach</h2>
        <p>Please fill this form to add new coach.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
			<div class="form-group <?php echo (!empty($fname_err)) ? 'has-error' : ''; ?>" >
                <label>First Name</label>
                <input type="text" name="fname" class="form-control" value="<?php echo $fname; ?>">
                <span class="help-block"><?php echo $fname_err; ?></span>
            </div> 
			<div class="form-group <?php echo (!empty($lname_err)) ? 'has-error' : ''; ?>" >
                <label>Last Name</label>
                <input type="text" name="lname" class="form-control" value="<?php echo $lname; ?>">
                <span class="help-block"><?php echo $lname_err; ?></span>
            </div> 
			<div class="form-group <?php echo (!empty($bdate_err)) ? 'has-error' : ''; ?>">
                <label>Birth Date</label>
                <input type="date" name="bdate" class="form-control" value="<?php echo $bdate; ?>">
                <span class="help-block"><?php echo $bdate_err; ?></span>
            </div> 
			<div class="form-group <?php echo (!empty($shortname_err)) ? 'has-error' : ''; ?>">
                <label>Experience</label>
                <input type="number" name="experience" class="form-control" value="<?php echo $experience; ?>">
                <span class="help-block"><?php echo $experience_err; ?></span>
            </div> 
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
				<a href="readTeam.php?id=<?php echo $_SESSION["currentTeamId"]; ?>" class="btn btn-danger pull-right">Back</a>
            </div>
        </form>
    </div>    
</body>
</html>