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
        move_uploaded_file($_FILES["eyesImage"]["tmp_name"], "upload/" . $_FILES["eyesImage"]["name"]);
        move_uploaded_file($_FILES["earsImage"]["tmp_name"], "upload/" . $_FILES["earsImage"]["name"]);
        $sql = "INSERT INTO `doc-connect-test-results`(`userID`, `age`, `sex`, `weight`, `height`, `temperature`, `reflex`, `hearingLow`, `hearingHigh`, `eyesImgUrl`, `earsImgUrl`) 
            VALUES (\"{$_SESSION['id']}\", \"{$_POST['age']}\", \"{$_POST['sex']}\", \"{$_POST['weight']}\", \"{$_POST['height']}\", \"{$_POST['temperature']}\", \"{$_POST['reflex']}\", \"{$_POST['hearingLow']}\", \"{$_POST['hearingHigh']}\", \"upload/{$_FILES['eyesImage']['name']}\", \"upload/{$_FILES['earsImage']['name']}\")";
        $conn->query($sql);
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
        
        <?php if($_SESSION["doctor"] == 1){ ?>
            <h3>Hello Doctor <?php echo $_SESSION["username"]; ?>.</h3>

            <h4>Test Results: </h4>
            <?php foreach($testResults as $t){ ?>
                <table border="1">
                    <tr>
                        <th colspan=4>
                            <?php 
                                $result = $conn->query("SELECT `username` FROM `doc-connect-users` WHERE `id`={$t[1]}"); 
                                while($row = $result->fetch_assoc()){
                                    echo $row["username"] . " - " . $t[12];
                                }
                            ?>
                        </th>
                    </tr>
                    <tr>
                        <td>Age: <?php echo $t[2];?></td>
                        <td>Sex: <?php echo $t[3];?></td>
                        <td>Weight: <?php echo $t[4];?></td>
                        <td>Height: <?php echo $t[5];?></td>
                    </tr>
                    <tr>
                        <td>Temperature: <?php echo $t[6];?></td>
                        <td>Reflex: <?php echo $t[7];?></td>
                        <td colspan=2>Hearing: <?php echo $t[8] . " - " . $t[9];?></td>
                    </tr>
                    <tr>
                        <td colspan=2>
                            Eyes: <img src="<?php echo $t[10];?>" width=100px />        
                        </td>
                        <td colspan=2>
                            Ears: <img src="<?php echo $t[11];?>" width=100px />        
                        </td>
                    </tr>
                </table>
            <?php  }?>
            

        <?php }else{ ?>
            <h3>Hello <?php echo $_SESSION["username"]; ?></h3>
            <h4>Submit your data:</h4>
            <form action="/" method="post" enctype="multipart/form-data">
                Age: <input type="text" name="age" id="age" required /> <br/>
                Sex: <input type="text" name="sex" id="sex" required /> <br/>
                Weight: <input type="text" name="weight" id="weight" required /> <br/>
                Height: <input type="text" name="height" id="height" required /> <br/>
                Temperature: <input type="text" name="temperature" id="temperature" required /> <br/>
                Reflex: <input type="text" name="reflex" id="reflex" required /> <br/>
                Hearing Range: LOW: <input type="text" name="hearingLow" id="hearingLow" required /> HIGH: <input type="text" name="hearingHigh" id="hearingHigh" required /> <br/>
                Eyes Image: <input type="file" name="eyesImage" id="eyesImage" required/>
                Ears Image: <input type="file" name="earsImage" id="earsImage" required/>

                <input type="submit" value="Submit" />
            </form>
        <?php } ?>

    </body>
</html>