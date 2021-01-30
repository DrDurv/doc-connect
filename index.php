<?php session_start(); ?>
<?php 
    if($_SESSION["username"] == null || $_SESSION["username"] == null){
        header("Location: /login.php" );
    }

    require_once("dbinfo.php"); //all db info defined in this file
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
        exit;
    }
    if($_SESSION["doctor"] != 1){
		if(isset($_POST["age"])){
			move_uploaded_file($_FILES["eyesImage"]["tmp_name"], "upload/" . $_FILES["eyesImage"]["name"]);
			move_uploaded_file($_FILES["earsImage"]["tmp_name"], "upload/" . $_FILES["earsImage"]["name"]);
			$sql = "INSERT INTO `doc-connect-test-results`(`userID`, `age`, `sex`, `weight`, `height`, `temperature`, `reflex`, `hearingLow`, `hearingHigh`, `eyesImgUrl`, `earsImgUrl`) 
				VALUES (\"{$_SESSION['id']}\", \"{$_POST['age']}\", \"{$_POST['sex']}\", \"{$_POST['weight']}\", \"{$_POST['height']}\", \"{$_POST['temperature']}\", \"{$_POST['reflex']}\", \"{$_POST['hearingLow']}\", \"{$_POST['hearingHigh']}\", \"upload/{$_FILES['eyesImage']['name']}\", \"upload/{$_FILES['earsImage']['name']}\")";
			$conn->query($sql);
			$errorMsg = "Data Submitted";
		}
    }else{
        $testResults = array();
        $sql = "SELECT * FROM `doc-connect-test-results`";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $testResults[count($testResults)] = array($row["id"], $row["userID"], $row["age"], $row["sex"], $row["weight"], $row["height"], $row["temperature"], $row["reflex"], $row["hearingLow"], $row["hearingHigh"], $row["eyesImgUrl"], $row["earsImgUrl"], $row["time"]);
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
							<li><a href="/">Home</a></li>
							<li><a href="logout.php" class="button">Logout</a></li>
						</ul>
					</nav>
				</header>

			<!-- Main -->
				<section id="main" class="container">
					<header>
						<h2>Hello <?php if($_SESSION["doctor"] == 1){?> Doctor <?php } ?> <?php echo $_SESSION["username"]; ?>.</h2>
					</header>
					<div class="box">
						<h1><?php echo $errorMsg?></h1>
						<?php if($_SESSION["doctor"] != 1){ ?>
							<h4>Instructions: </h4>
							<ol>
								<img src="images/machine.png" />
								<li>Power on the device. </li>
								<li>The first test will begin. The display will read “Reflex Examination”.</li>
								<li>When the yellow light turns on, press the silver button down as fast as possible. The number shown on the display will tell you your reflex times in milliseconds. Record it.</li>
								<li>The next test will begin shortly. This will test your temperature. Simply put the blue temperature sensor to your forehead and record the displayed number.</li>
								<li>The final test will determine your hearing. When instructed, begin playing the <a href="https://www.youtube.com/watch?v=qNf9nzvnd1k" target="_blank">audio spectrum test</a>. Press the button once more when you are able to begin hearing the noise, and press it once more when you are no longer able to. Record these two numbers.</li>
								<li>Now that the tests are completed, review your other personal information and submit the report to your physician. </li>
								<li>If you are unable to send the report for any reason, the RFID card within the wall of your unit will be written to with the information of your exam. Simply mail this card to your physician’s office and they will use a reader writer unit (see below) to upload your information.</li>
							</ol>
							

							<h4>Submit your data:</h4>
							<form action="/" method="post" enctype="multipart/form-data">
								Age: <input type="text" name="age" id="age" required /> <br/>
								Sex: <input type="text" name="sex" id="sex" required /> <br/>
								Weight: <input type="text" name="weight" id="weight" required /> <br/>
								Height: <input type="text" name="height" id="height" required /> <br/>
								Temperature: <input type="text" name="temperature" id="temperature" required /> <br/>
								Reflex: <input type="text" name="reflex" id="reflex" required /> <br/>
								Hearing Low: <input type="text" name="hearingLow" id="hearingLow" required />
								Hearing High: <input type="text" name="hearingHigh" id="hearingHigh" required /> <br/>
								Eyes Image: <input type="file" name="eyesImage" id="eyesImage" required/>
								Ears Image: <input type="file" name="earsImage" id="earsImage" required/>
				
								<input type="submit" value="Submit" />
							</form>
						<?php }else{ ?>
							<h3><b>Test Results: </b></h3>
							<?php foreach($testResults as $t){ ?>
								<center><table border="1">
									<tr>
										<th colspan=4>
											<h4><b><center>
											<?php 
												$result = $conn->query("SELECT `username` FROM `doc-connect-users` WHERE `id`={$t[1]}"); 
												while($row = $result->fetch_assoc()){
													echo $row["username"] . " - " . $t[12];
												}
											?>
											</center></b></h4>
										</th>
									</tr>
									<tr>
										<td><b>Age:    </b><?php echo $t[2];?></td>
										<td><b>Sex:    </b><?php echo $t[3];?></td>
										<td><b>Weight: </b><?php echo $t[4];?></td>
										<td><b>Height: </b><?php echo $t[5];?></td>
									</tr>
									<tr>
										<td>           <b>Temperature:</b>   <?php echo $t[6];?></td>
										<td>           <b>Reflex:     </b>   <?php echo $t[7];?></td>
										<td colspan=2> <b>Hearing:    </b>   <?php echo $t[8] . " - " . $t[9];?></td>
									</tr>
									<tr>
										<td colspan=2>
											<b>Eyes: </b> <br/><img src="<?php echo $t[10];?>" height=100px />        
										</td>
										<td colspan=2>
											<b> Ears: </b> <br/><img src="<?php echo $t[11];?>" height=100px />        
										</td>
									</tr>
								</table></center>
							<?php } ?>
						<?php } ?>
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