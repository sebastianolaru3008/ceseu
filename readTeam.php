<?php

require_once "config.php";
session_start();
// Check existence of id parameter before processing further
if(!isset($_GET["id"]) || empty(trim($_GET["id"]))){
    // Include config file
    // Prepare a select statement
    header("location: error.php");
    exit();
}
$_SESSION["currentTeamId"] = $_GET["id"];

$sql_coach = "SELECT * FROM coaches JOIN teams ON teams.coach = coaches.id WHERE teams.id =". $_GET["id"];
$_SESSION["validAddCoach"] = (mysqli_num_rows(mysqli_query($link, $sql_coach)) >= 1) ? false : true;

$sql_team = "SELECT name from teams WHERE id =". $_GET["id"];
$result_team = mysqli_query($link, $sql_team);
$_SESSION["validAddPlayer"] = (mysqli_num_rows($result_team) > 10)? false : true;
$team_name = mysqli_fetch_row($result_team)[0];
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
                        <h2 class="pull-left">Players</h2>
						<a href="editTeams.php" class="btn btn-danger pull-right">Back</a>
						<?php 
						
							if($_SESSION["validAddCoach"] == true){
								echo '<a href="addCoach.php?id='.$_SESSION["currentTeamId"].'" class="btn btn-warning pull-right">Add Coach</a>';
							}
							unset($_SESSION["validAddCoach"]);
							
						
							if($_SESSION["validAddPlayer"] == true){
								echo '<a href="addPlayer.php?id='.$_SESSION["currentTeamId"].'" class="btn btn-success pull-right">Add New Player</a>';
							}
							unset($_SESSION["validAddPlayer"]);
							
						?>
                        
                    </div>
                    <?php
                    // Include config file
                    
                    
					
                    // Attempt select query execution
                    //$sql = "SELECT players.id, persons.fname, persons.lname, players.nickname, persons.birthdate FROM teams JOIN players on players.team = teams.id JOIN persons on persons.id = players.id WHERE teams.id =". $_GET["id"];
					$sql = "SELECT * FROM teams JOIN players on players.team = teams.id JOIN persons on persons.id = players.id WHERE teams.id =". $_GET["id"];
					
					
                    //echo $sql;
						if($result = mysqli_query($link, $sql)){
							if(mysqli_num_rows($result) > 0){
								echo "<h1><strong> Team:</strong> ".$team_name."</h1>";
								echo "<table class='table table-bordered table-striped'>";
									echo "<thead>";
										echo "<tr>";
											echo "<th>#</th>";
											echo "<th>First Name</th>";
											echo "<th>Last Name</th>";
											echo "<th>Nickname</th>";
											echo "<th>Birthdate</th>";
											echo "<th>Actions</th>";
										echo "</tr>";
									echo "</thead>";
									echo "<tbody>";
									//echo $result;
									while($row = mysqli_fetch_array($result)){
										//echo $row[2];
										echo "<tr>";
											echo "<td>" . $row['id'] . "</td>";
											echo "<td>" . $row['fname'] . "</td>";
											echo "<td>" . $row['lname'] . "</td>";
											echo "<td>" . $row['nickname'] . "</td>";
											echo "<td>" . $row['birthdate'] . "</td>";
											echo "<td>";
											
												echo "<a href='updatePlayer.php?id=". $row['id'] ."' title='Update Record' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
												echo "<a href='deletePlayer.php?id=". $row['id'] ."' title='Delete Record' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
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
                    //mysqli_close($link);
                    ?>
					
					<?php
                    // Include config file
                    
                    
					
                    // Attempt select query execution
                    //$sql = "SELECT players.id, persons.fname, persons.lname, players.nickname, persons.birthdate FROM teams JOIN players on players.team = teams.id JOIN persons on persons.id = players.id WHERE teams.id =". $_GET["id"];
					$sql = "SELECT * FROM coaches JOIN teams on coaches.id = teams.coach JOIN persons ON persons.id = coaches.id WHERE teams.id =". $_GET["id"];
					
					
                    //echo $sql;
						if($result = mysqli_query($link, $sql)){
							if(mysqli_num_rows($result) > 0){
								echo "<h1> Coach:</h1>";
								echo "<table class='table table-bordered table-striped'>";
									echo "<thead>";
										echo "<tr>";
											echo "<th>#</th>";
											echo "<th>First Name</th>";
											echo "<th>Last Name</th>";
											echo "<th>Experience</th>";
											echo "<th>Birthdate</th>";
											echo "<th>Actions</th>";
										echo "</tr>";
									echo "</thead>";
									echo "<tbody>";
									//echo $result;
									while($row = mysqli_fetch_array($result)){
										//echo $row[2];
										echo "<tr>";
											echo "<td>" . $row['id'] . "</td>";
											echo "<td>" . $row['fname'] . "</td>";
											echo "<td>" . $row['lname'] . "</td>";
											echo "<td>" . $row['experience'] . "</td>";
											echo "<td>" . $row['birthdate'] . "</td>";
											echo "<td>";
											
												echo "<a href='updateCoach.php?id=". $row['id'] ."' title='Update Record' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
												echo "<a href='deleteCoach.php?id=". $row['id'] ."' title='Delete Record' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
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