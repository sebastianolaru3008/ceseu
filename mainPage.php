<?php
        function alert($message) { 
            // Display the alert box  
            echo "<script>alert('$message');</script>"; 
        }

        if(array_key_exists('button1', $_POST)) { 
            button1(); 
        } 
        else if(array_key_exists('button2', $_POST)) { 
            button2(); 
        }
        else if(array_key_exists('button3', $_POST)) { 
            button3(); 
        } 
        function button1() { 
            alert("This is Button1 that is selected"); 
        } 
        function button2() { 
            alert("This is Button2 that is selected"); 
        }
        function button3() { 
            alert("This is Button2 that is selected"); 
        }
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Main menu</title>
    <link rel = "stylesheet" href = "src/css/index.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
</head>
<body>

    <script>
    $("#nav-two button")
        .each(function(i) {
            if (i != 0) {
            $("#beep-two")
                .clone()
                .attr("id", "beep-two" + i)
                .appendTo($(this).parent());
            }
            $(this).data("beeper", i);
        })
        .mouseenter(function() {
            $("#beep-two" + $(this).data("beeper"))[0].play();
        });
        $("#beep-two").attr("id", "beep-two0");
    </script>

    <h1>Choose a fucking tournament</h2>
    <form id = "nav-two" method="post">
        <button name = "button1" value="button1">
            tournament 1
            <audio id = "beep-two" controls preload="auto" style = "display: none">
                <source src="src/audio/beep.mp3" controls></source>
                <source src="src/audio/beep.ogg" controls></source>
                Your browser isn't invited for super fun audio time.
            </audio>
        </button>
        <button name = "button2" value="button2">
            tournament 2
        </button>
        <button name = "button3" value="button3">
            tournament 3
        </button>
    </form> 
</body>
</html>