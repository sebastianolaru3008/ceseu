<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !$_SESSION["tournament"]){
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
    <p>
		
        <a href="editTeams.php" class="btn btn-primary">Edit Teams</a>
        <a href="editMatch.php" class="btn btn-warning">Edit Match Results</a>
		<a href="welcome.php" class="btn btn-danger btn">Back</a>
    </p>
</body>
</html>