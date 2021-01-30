<?php session_start(); ?>
<?php 
    if($_SESSION["username"] == null || $_SESSION["username"] == null){
        header("Location: /login.php" );
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Doc Connect</title>
    </head>
    <body>

        <h1>Doc Connect</h1>    
        <form action="/logout.php"><input type="submit" value="Logout"></form>

    </body>
</html>