<?php
        function function_alert($message) { 
            // Display the alert box  
            echo "<script>alert('$message');</script>"; 
        }

        if(array_key_exists('button1', $_POST)) { 
            button1(); 
        } 
        else if(array_key_exists('button2', $_POST)) { 
            button2(); 
        } 
        function button1() { 
            function_alert("This is Button1 that is selected"); 
        } 
        function button2() { 
            echo "This is Button2 that is selected"; 
        } 
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Main menu</title>
    <link rel = "stylesheet" href = "src/index.css">
</head>
    <body>
        <h2>Choose a fucking tournament</h2>
        <!-- <form action="action.php" method="post"> -->
        <form method="post">
            <button name = "button1" value="button1">
                tournament 1
            </button>
            <button name = "button2" value="button2">
                tournament 2
            </button>
        </form> 
    </body>
</html>