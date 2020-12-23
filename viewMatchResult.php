<?php
// Initialize the session
require_once "config.php";
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
					<?php
					$sql_find_teams ="SELECT id_team1,id_team2,score_team1,score_team2 from matchresults where id =".$_GET["id"];
					$result=mysqli_query($link, $sql_find_teams);
					$row = mysqli_fetch_array($result);
					$id_team1=$row['0'];
					$id_team2=$row['1'];
					$score_team1=$row['2'];
					$score_team2=$row['3'];
					$sql_team1="SELECT name from teams where id =".$id_team1;
					$result=mysqli_query($link, $sql_team1);
					$row = mysqli_fetch_array($result);
					?>
                        <h2 class="float-left"><?php echo $row["0"]." ".$score_team1;?></h2>
						<a href='viewTournamentDetails.php?id=<?php echo $_SESSION["tournamentID"]; ?>' class='btn btn-danger float-right'>Back</a>
                    </div>
                    <?php
					
                    // Include config file
                    //$_SESSION["currentTeamId"] = null;
                    // Attempt select query execution
                    $sql_team1_stats= "SELECT players.nickname,individualresults.kills,individualresults.assists,individualresults.deaths,individualresults.mvps,individualresults.headshots 
					from individualresults JOIN players on players.id = individualresults.id_player 
					WHERE individualresults.id_match=".$_GET["id"]." and players.team=".$id_team1;
					$sql_team2_stats= "SELECT players.nickname,individualresults.kills,individualresults.assists,individualresults.deaths,individualresults.mvps,individualresults.headshots 
					from individualresults JOIN players on players.id = individualresults.id_player 
					WHERE individualresults.id_match=".$_GET["id"]." and players.team=".$id_team2;
                    if($result = mysqli_query($link, $sql_team1_stats)){
                        if(mysqli_num_rows($result) > 0){
                            echo "<table class='table table-dark table-hover'>";
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th>Nickname</th>";
                                        echo "<th>kills</th>";
										echo "<th>assists</th>";
                                        echo "<th>deaths</th>";
                                        echo "<th>mvps</th>";
										echo "<th>headshots</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result)){
									echo "<tr>";
                                        echo "<td>" . $row['0'] . "</td>";
                                        echo "<td>" . $row['1'] . "</td>";
                                        echo "<td>" . $row['2']. "</td>";
										echo "<td>" . $row['3'] . "</td>";
                                        echo "<td>" . $row['4'] . "</td>";
										echo "<td>" . $row['5'] . "</td>";
										
											//echo "<a href='readMatchResult.php?id=". $row['id'] ."' title='View Record' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
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
					$sql_team2="SELECT name from teams where id =".$id_team2;
					$result=mysqli_query($link, $sql_team2);
					$row = mysqli_fetch_array($result);
					echo " <div class='page-header clearfix'>
                        <h2 class='float-left'>".$row["0"]." ".$score_team2."</h2>
                    </div>";
					   if($result = mysqli_query($link, $sql_team2_stats)){
                        if(mysqli_num_rows($result) > 0){
                            echo "<table class='table table-dark table-hover'>";
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th>Nickname</th>";
                                        echo "<th>kills</th>";
										echo "<th>assists</th>";
                                        echo "<th>deaths</th>";
                                        echo "<th>mvps</th>";
										echo "<th>headshots</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result)){
									
									
                                    echo "<tr>";
                                        echo "<td>" . $row['0'] . "</td>";
                                        echo "<td>" . $row['1'] . "</td>";
                                        echo "<td>" . $row['2']. "</td>";
										echo "<td>" . $row['3'] . "</td>";
                                        echo "<td>" . $row['4'] . "</td>";
										echo "<td>" . $row['5'] . "</td>";
											//echo "<a href='readMatchResult.php?id=". $row['id'] ."' title='View Record' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
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