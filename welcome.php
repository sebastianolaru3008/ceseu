<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" href="src/css/index.css">
</head>
<body>
    <div class="page-header">
        <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["user"]); ?></b>. Welcome to our site.</h1>
    </div>
    <p class = "command-wrapper">
        <a href="reset-password.php" class="btn btn-warning">Reset Your Password</a>
		<?php
			if($_SESSION["tournament"]){
				echo "<a href='upload.php' class='btn btn-primary upload'>Upload</a>";
			}
		?>
		
        <a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a>
    </p>
</body>
</html>