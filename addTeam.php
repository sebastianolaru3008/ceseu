<?php
require_once "config.php";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
session_start();
// Define variables and initialize with empty values
$teamname = $shortname = "";
$teamname_err = $shortname_err = "";
if($_SESSION["tournament"] && $_SESSION["loggedin"] == true){
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		if(empty(trim($_POST["teamname"]))){
				$teamname_err = "Please enter a team name.";
			} else{
				// Prepare a select statement
				$sql = "SELECT id FROM teams WHERE name = ?";
				if($stmt = mysqli_prepare($link, $sql)){
					// Bind variables to the prepared statement as parameters
					mysqli_stmt_bind_param($stmt, "s", $param_name);
					
					// Set parameters
					$param_name = trim($_POST["teamname"]);
					
					// Attempt to execute the prepared statement
					if(mysqli_stmt_execute($stmt)){
						/* store result */
						mysqli_stmt_store_result($stmt);
						
						if(mysqli_stmt_num_rows($stmt) == 1){
							$teamname_err = "There is already a team with this name!";
						} else{
							$teamname = trim($_POST["teamname"]);
						}
					} else{
						echo "Oops! Something went wrong. Please try again later.";
					}

					// Close statement
					mysqli_stmt_close($stmt);
				}
			}
			if(empty(trim($_POST["shortname"]))){
				$shortname_err = "Please enter a short name.";
			} else if(strlen($_POST["shortname"]) != 4){
				$shortname_err = "Short Name should be 4 characters long!";
			}else {
				// Prepare a select statement
				$sql = "SELECT id FROM teams WHERE shortname = ?";
				
				if($stmt = mysqli_prepare($link, $sql)){
					// Bind variables to the prepared statement as parameters
					mysqli_stmt_bind_param($stmt, "s", $param_shortname);
					
					// Set parameters
					$param_shortname = trim($_POST["shortname"]);
					
					// Attempt to execute the prepared statement
					if(mysqli_stmt_execute($stmt)){
						/* store result */
						mysqli_stmt_store_result($stmt);
						
						if(mysqli_stmt_num_rows($stmt) == 1){
							$shortname_err = "There is already a team with this short name!";
						} else{
							$shortname = trim($_POST["shortname"]);
						}
					} else{
						echo "Oops! Something went wrong. Please try again later.";
					}

					// Close statement
					mysqli_stmt_close($stmt);
				}
			}
			 // Check input errors before inserting in database
		if(empty($teamname_err) && empty($shortname_err)){
			
			// Prepare an insert statement
			$sql_teams = "INSERT INTO teams (name,shortname,tournament) VALUES (?, ?, ?)";
			//start the transaction to ensure that all the sql statements are executed
		
			try{
				mysqli_begin_transaction($link);
				
				$stmt1 = mysqli_prepare($link, $sql_teams);
				mysqli_stmt_bind_param($stmt1, "ssi", $teamname, $shortname, $_SESSION["tournament"]);
				mysqli_stmt_execute($stmt1);
				
				printf ("New Record has id %d.\n", mysqli_insert_id($link));
				
				mysqli_commit($link);
				header("location: editTeams.php");
			} catch (mysqli_sql_exception $exception){
				mysqli_rollback($link);
				throw $exception;
			}
			 
			
		}
		
		// Close connection
		mysqli_close($link);
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Team</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="src/css/index.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous">
</head>
<body>
    <div class="wrapper">
        <h2>Creating new team</h2>
        <p>Please fill this form to create a new team.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
			<div class="form-group <?php echo (!empty($teamname_err)) ? 'has-error' : ''; ?>" >
                <label>Team Name</label>
                <input type="text" name="teamname" class="form-control" value="<?php echo $teamname; ?>">
                <span class="help-block"><?php echo $teamname_err; ?></span>
            </div>  
			<div class="form-group <?php echo (!empty($shortname_err)) ? 'has-error' : ''; ?>">
                <label>Short Name</label>
                <input type="text" name="shortname" class="form-control" value="<?php echo $shortname; ?>">
                <span class="help-block"><?php echo $shortname_err; ?></span>
            </div> 
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a href="editTeams.php" class="btn btn-danger float-right">Back</a>
            </div>
        </form>
	</div>   
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>  
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>