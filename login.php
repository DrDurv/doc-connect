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
            
            foreach($logins as $login){
                if($login[1] == $_POST["username"] && $login[2] == $_POST["password"]){        
                    $_SESSION["id"] = $login[0];
                    $_SESSION["username"] = $_POST["username"];
                    $_SESSION["password"] = $_POST["password"];
                    $_SESSION["doctor"] = $login[3];
                    header("Location: /" ); 
                    break;
                }
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

<!DOCTYPE HTML>
<html>
	<head>
		<title>Doc-Connect</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="assets/css/main.css" />
	</head>
	<body class="is-preload">
		<div id="page-wrapper">

			<!-- Header -->
				<header id="header">
					<h1>Doc-Connect</h1>
					<nav id="nav">
						<ul>
						
						</ul>
					</nav>
				</header>

			<!-- Main -->
				<section id="main" class="container">
					<header>
						<h2>Hello <?php if($_SESSION["doctor"] == 1){?> Doctor <?php } ?> <?php echo $_SESSION["username"]; ?>.</h2>
					</header>
					<div class="box">
                        <h2>Log In</h2>
                        <form action="/login.php" method="post">
                            <input type="text" name="username" id="username" required/> <label for="username">Username</label><br />
                            <input type="password" name="password" id="password" required/> <label for="password">Password</label><br />
                            <input type="hidden" name="type" value="0">
                            <input type="submit" value="Submit"> 
                        </form>
                        <h2>Sign Up</h2>
                        <form action="/login.php" method="post">
                            <input type="text" name="username" id="username" required/> <label for="username">Username</label><br />
                            <input type="password" name="password" id="password" required/> <label for="password">Password</label><br />
                            <input type="checkbox" name="doctor" id="doctor" value="1" /> <label for="doctor">Doctor</label><br/>

                            <input type="hidden" name="type" value="1">
                            <input type="submit" value="Submit"> 
                        </form>
					</div>
				</section>

			<!-- Footer -->
				<footer id="footer">
					<ul class="copyright">
						<li>Design: <a href="http://html5up.net">HTML5 UP</a></li>
					</ul>
				</footer>

		</div>

		<!-- Scripts -->
			<script src="assets/js/jquery.min.js"></script>
			<script src="assets/js/jquery.dropotron.min.js"></script>
			<script src="assets/js/jquery.scrollex.min.js"></script>
			<script src="assets/js/browser.min.js"></script>
			<script src="assets/js/breakpoints.min.js"></script>
			<script src="assets/js/util.js"></script>
			<script src="assets/js/main.js"></script>

	</body>
</html>