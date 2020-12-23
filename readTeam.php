<?php
error_reporting(E_ERROR | E_PARSE);
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

$sql_team = "SELECT teams.name, COUNT(players.id) AS 'playerCount' FROM teams JOIN players ON teams.id = players.team WHERE teams.id = ".$_GET["id"]." GROUP BY teams.name";
$result_team = mysqli_fetch_row(mysqli_query($link, $sql_team));
$_SESSION["validAddPlayer"] = ($result_team[1] < 5)? true : false;
$team_name = $result_team[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="src/css/index.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
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
                        <h1 class="float-left">Players</h1>
						<a href="editTeams.php" class="btn btn-danger float-right">Back</a>
						<?php 
						
							if($_SESSION["validAddCoach"] == true){
								echo '<a href="addCoach.php?id='.$_SESSION["currentTeamId"].'" class="btn btn-warning float-right">Add Coach</a>';
							}
							unset($_SESSION["validAddCoach"]);
							
						
							if($_SESSION["validAddPlayer"] == true){
								echo '<a href="addPlayer.php?id='.$_SESSION["currentTeamId"].'" class="btn btn-success float-right">Add New Player</a>';
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
								echo "<h2><strong> Team:</strong> ".$team_name."</h2>";
								echo "<table class='table table-dark table-hover'>";
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
											
												echo "<a class = 'action' href='updatePlayer.php?id=". $row['id'] ."' title='Update Record' data-toggle='tooltip'><span class='fas fa-edit'></span></a>";
												echo "<a class = 'action' href='deletePlayer.php?id=". $row['id'] ."' title='Delete Record' data-toggle='tooltip'><span class='fas fa-trash'></span></a>";
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
					echo "<h2> Coach:</h2>";
					
                    //echo $sql;
						if($result = mysqli_query($link, $sql)){
							if(mysqli_num_rows($result) > 0){
								echo "<table class='table table-dark table-hover'>";
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
											
												echo "<a class = 'action' href='updateCoach.php?id=". $row['id'] ."' title='Update Record' data-toggle='tooltip'><span class='fas fa-edit'></span></a>";
												echo "<a class = 'action' href='deleteCoach.php?id=". $row['id'] ."' title='Delete Record' data-toggle='tooltip'><span class='fas fa-trash'></span></a>";
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
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>