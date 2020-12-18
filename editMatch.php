<?php
// Initialize the session
require_once "config.php";
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
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js"></script>
    <style type="text/css">
        .wrapper{
            width: 650px;
            margin: 0 auto;
        }
        .page-header h2{
            margin-top: 0;
        }
        table tr td:last-child a{
            margin-right: 15px;
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();   
        });
    </script>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2 class="pull-left">Match Results</h2>
						<a href="upload.php" class="btn btn-danger pull-right">Back</a>
                        <a href="addMatchResult.php" class="btn btn-success pull-right">Add Math Result</a>
                    </div>
                    <?php
                    // Include config file
                    //$_SESSION["currentTeamId"] = null;
                    // Attempt select query execution
                    $sql = "SELECT * FROM matchresults WHERE tournament = ".$_SESSION["tournament"];
                    if($result = mysqli_query($link, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo "<table class='table table-bordered table-striped'>";
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th>#</th>";
                                        echo "<th>Team 1</th>";
                                        echo "<th>Team 2</th>";
										echo "<th>Score 1</th>";
                                        echo "<th>Score 2</th>";
                                        echo "<th>Action</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result)){
									
									$sql_team1 = "SELECT shortname FROM teams WHERE id = ".$row["id_team1"];
									$sql_team2 = "SELECT shortname FROM teams WHERE id = ".$row["id_team2"];
									$team1 = mysqli_fetch_array(mysqli_query($link, $sql_team1))[0];
									$team2 = mysqli_fetch_array(mysqli_query($link, $sql_team2))[0];
									
                                    echo "<tr>";
                                        echo "<td>" . $row['id'] . "</td>";
                                        echo "<td>" . $team1 . "</td>";
                                        echo "<td>" . $team2 . "</td>";
										echo "<td>" . $row['score_team1'] . "</td>";
                                        echo "<td>" . $row['score_team2'] . "</td>";
                                        echo "<td>";
										
											echo "<a href='readMatchResult.php?id=". $row['id'] ."' title='View Record' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
                                            echo "<a href='deleteMatchResult.php?id=". $row['id'] ."' title='Delete Record' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
                                        echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";                            
                            echo "</table>";
                            // Free result set
                            mysqli_free_result($result);
                        } else{
                            echo "<p class='lead'><em>No records were found.</em></p>";
                        }
                    } else{
                        echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
                    }
 
                    // Close connection
                    mysqli_close($link);
                    ?>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>