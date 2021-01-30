<?php session_start(); ?>
<?php 
    require_once("dbinfo.php"); //all db info defined in this file
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
        exit;
    }

    $logins = array();
    $sql = "SELECT * FROM `doc-connect-users`";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $logins[count($logins)] = array($row["id"], $row["username"], $row["password"], $row["doctor"]);
        }
    }
    if($_POST["username"] != null && $_POST["password"] != null){
        if($_POST["type"] == 0){ //login
            $valid = false;
            foreach($logins as $login){
                if($login[1] == $_POST["username"] && $login[2] == $_POST["password"]){
                    $valid = true;
                    break;
                }
            }
            if($valid){
                $_SESSION["username"] = $_POST["username"];
                $_SESSION["password"] = $_POST["password"];
                header("Location: /" ); 
            }
        }else{ //signup
            $valid = true;
            foreach($logins as $login){
                if($login[1] == $_POST["username"]){
                    $valid = false;
                    echo "username already taken";      
                    break;
                }
            }
            if($valid){
                $doctor = 0;
                if($_POST["doctor"] == 1){
                    $doctor = 1;
                };
                $sql = "INSERT INTO `doc-connect-users`(`username`, `password`, `doctor`) VALUES (\"{$_POST["username"]}\",\"{$_POST["password"]}\",\"{$doctor}\")";
                $conn->query($sql);
                $_SESSION["username"] = $_POST["username"];
                $_SESSION["password"] = $_POST["password"];
                header("Location: /" ); 
            }

        }

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
        <h2>Log In</h2>
        <form action="/login.php" method="post">
            <input type="text" name="username" id="username" required/> <br />
            <input type="password" name="password" id="password" required/> <br />
            <input type="hidden" name="type" value="0">
            <input type="submit" value="Submit"> 
        </form>
        <h2>Sign Up</h2>
        <form action="/login.php" method="post">
            <input type="text" name="username" id="username" required/> <br />
            <input type="password" name="password" id="password" required/> <br />
            Doctor: <input type="checkbox" name="doctor" id="doctor" value="1" /> <br />
            <input type="hidden" name="type" value="1">
            <input type="submit" value="Submit"> 
        </form>
    </body>
</html>