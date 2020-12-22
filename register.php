<?php
// Include config file
require_once "config.php";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
 
// Define variables and initialize with empty values
$email = $password = $confirm_password = $fname = $lname = $bdate = "";
$email_err = $password_err = $confirm_password_err = $fname_err = $lname_err = $bdate_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate username
    if(empty(trim($_POST["email"]))){
        $username_err = "Please enter an email.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE email = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            // Set parameters
            $param_email = trim($_POST["email"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $email_err = "There is already an account with this email!";
                } else{
                    $email = trim($_POST["email"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
	
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
    
    // Check input errors before inserting in database
    if(empty($fname_err) && empty($lname_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)){
        
        // Prepare an insert statement
		$sql_persons = "INSERT INTO persons (fname, lname, birthdate) VALUES (?, ?, ?)";
        $sql_users = "INSERT INTO users (id, email, password) VALUES (?, ?, ?)";
		
		//start the transaction to ensure that all the sql statements are executed
	
		try{
			mysqli_begin_transaction($link);
			
			$stmt1 = mysqli_prepare($link, $sql_persons);
			mysqli_stmt_bind_param($stmt1, "sss", $fname, $lname, $bdate);
			mysqli_stmt_execute($stmt1);
			
			printf ("New Record has id %d.\n", mysqli_insert_id($link));
			$stmt2 = mysqli_prepare($link, $sql_users);
			mysqli_stmt_bind_param($stmt2, "sss", mysqli_insert_id($link), $email, password_hash($password, PASSWORD_DEFAULT));
			mysqli_stmt_execute($stmt2);
			
			mysqli_commit($link);
			header("location: login.php");
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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" href="src/css/index.css">
</head>
<body>
    <div class="wrapper">
        <h1>Sign Up</h1>
        <h2>Please fill this form to create an account.</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
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
			<div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label>Email</label>
                <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
                <span class="help-block"><?php echo $email_err; ?></span>
            </div> 			
            <div class="form-group <?php echo (!empty($bdate_err)) ? 'has-error' : ''; ?>">
                <label>Birth Date</label>
                <input type="date" name="bdate" class="form-control" value="<?php echo $bdate; ?>">
                <span class="help-block"><?php echo $bdate_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="reset" class="btn btn-default" value="Reset">
                <input type="submit" class="btn btn-primary" value="Submit">
            </div>
            <h3>Already have an account? <a href="login.php">Login here</a>.</h3>
        </form>
    </div>    
</body>
</html>