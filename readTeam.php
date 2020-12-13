<?php
// Check existence of id parameter before processing further
if(!isset($_GET["id"]) || empty(trim($_GET["id"]))){
    // Include config file
    // Prepare a select statement
    header("location: error.php");
    exit();
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
                        <h2 class="pull-left">Teams</h2>
                        <a href="create.php" class="btn btn-success pull-right">Add New Team</a>
                    </div>
                    <?php
                    // Include config file
                    require_once "config.php";
                    
                    // Attempt select query execution
                    $sql = "SELECT persons.fname, persons.lname, players.nickname, persons.birthdate FROM teams JOIN players on players.team = teams.id JOIN persons on persons.id = players.id WHERE teams.id = ?";
                    if($stmt = mysqli_prepare($link, $sql)){
						mysqli_stmt_bind_param($stmt, "s", $param_id);
						$param_id = $_GET["id"];
						echo $param_id;
						mysqli_stmt_execute($stmt);
						
						if($result = mysqli_stmt_get_result($stmt)){
							if(mysqli_num_rows($result) > 0){
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
									while($row = mysqli_fetch_row($result)){
										echo "<tr>";
											echo "<td>" . $row['id'] . "</td>";
											echo "<td>" . $row['persons.fname'] . "</td>";
											echo "<td>" . $row['persons.lname'] . "</td>";
											echo "<td>" . $row['players.nickname'] . "</td>";
											echo "<td>" . $row['persons.birthdate'] . "</td>";
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