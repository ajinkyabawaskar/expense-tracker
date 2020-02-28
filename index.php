<?php
 
$dataPoints = array();
try{
    $link = new \PDO(   'mysql:host=localhost;dbname=transaction;charset=utf8mb4',
                        'ajinkya',
                        'ajinkya', 
                        array(
                            \PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            \PDO::ATTR_PERSISTENT => false
                        )
                    );
	
    $handle = $link->prepare('select purpose, amount, created from expense'); 
    $handle->execute(); 
    $result = $handle->fetchAll(\PDO::FETCH_OBJ);
      
	if(!isset($foodTotal)) {
        $foodTotal = 0;
    }
    if(!isset($travelTotal)) {
        $travelTotal = 0;
    }
    if(!isset($educationalTotal)) {
        $educationalTotal = 0;
    }
    if(!isset($householdTotal)) {
        $householdTotal = 0;
    }
    if(!isset($billTotal)) {
        $billTotal = 0;
    }
    if(!isset($otherTotal)) {
        $otherTotal = 0;
    }

    foreach($result as $row){
        if($row->purpose == "Food") {
            $foodTotal = $foodTotal + $row->amount;
        }
        if($row->purpose == "Travel") {
            $travelTotal = $travelTotal + $row->amount;
        }
        if($row->purpose == "Educational") {
            $educationalTotal = $educationalTotal + $row->amount;
        }
        if($row->purpose == "Household") {
            $householdTotal = $householdTotal + $row->amount;
        }
        if($row->purpose == "Bill Payment") {
            $billTotal = $billTotal + $row->amount;
        }
        if($row->purpose == "Other") {
            $otherTotal = $otherTotal + $row->amount;
        }
        
    }
    $dataPoints = array( 
        array("y" => $foodTotal, "label" => "Food" ),
        array("y" => $travelTotal, "label" => "Travel" ),
        array("y" => $educationalTotal, "label" => "Educational" ),
        array("y" => $householdTotal, "label" => "Household" ),
        array("y" => $billTotal, "label" => "Bill Payment" ),
        array("y" => $otherTotal, "label" => "Other" ),

    );
    // foreach($result as $row){
    //     array_push($dataPoints, array("y"=> $row->amount, "label"=> $row->purpose));
    // }
	$link = null;
}
catch(\PDOException $ex){
    print($ex->getMessage());
}

require("connection.php");
if (empty($_SESSION['user'])) { 
    header("Location: register.php");
    die("Redirecting to register.php");
}
if ($_SESSION['user']['username'] == 'admin') {
    $_SESSION['isAdmin'] = true;
}
if (isset($_POST['newEntry'])){
    $servername = "localhost";
    $username = "ajinkya";
    $password = "ajinkya";
    $dbname = "transaction";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $purpose = $_POST['purpose'];
    $amount = $_POST['amount'];
    $remark = $_POST['remark'];
    $sql = "INSERT INTO expense (purpose, amount, remark) VALUES ('$purpose', $amount, '$remark')";
    // $sql = "INSERT INTO expense (purpose, amount) VALUES ('$purpose', $amount)";

    if ($conn->query($sql) === TRUE) {
        header('Location: /expense-tracker/');
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
<!DOCTYPE HTML>
<html>
<head>  
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>My Academic Space</title>
    <link rel=" icon" href="favicon.png"/> 
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
<script>
window.onload = function () {
 
var chart = new CanvasJS.Chart("chartContainer", {
	animationEnabled: true,
	exportEnabled: true,
	theme: "dark2", // "light1", "light2", "dark1", "dark2"
	title:{
		// text: "PHP Column Chart from Database"
	},
	data: [{
		type: "pie", //change type to bar, line, area, pie, etc  
		dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
	}]
});
chart.render();
}
</script>
</head>
<body> 
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="index.php">Expense Tracker</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item active">
        <a class="nav-link" href="index.php">Dashboard <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="entry.php">Manage Expenses</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="edit_account.php">Edit Account</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="logout.php">Logout</a>
      </li>
      <?php if ($_SESSION['isAdmin']==true)
        echo '
        <li class="nav-item">
            <a href="memberlist.php" class="nav-link">Memberlist</a>
        </li>';
        ?>
    </ul>
  </div>
</nav>

<div class="container-fluid ">
    <div id="chartContainer" style="height: 50vh; margin-top: 20px;"> </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-12 mt-1 welcome text-center">
            Your Expenses
        </div>
    </div>
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8 mt-4">
        Add a new expense here 
            <form class="mt-3" action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST" name="newEntry">
                <div class="form-group">
                    <label for="purpose">Purpose</label>
                    <select class="form-control" id="purpose" name="purpose" required>
                        <option name= "purpose" value="Food">Food</option>
                        <option name= "purpose" value="Travel">Travel</option>
                        <option name= "purpose" value="Educational">Educational</option>
                        <option name= "purpose" value="Household">Household</option>
                        <option name= "purpose" value="Bill Payment">Bill Payment</option>
                        <option name= "purpose" value="Other">Other..</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="amount">Amount</label>
                    <input type="text" name="amount" class="form-control" id="amount" placeholder="Enter amount in Rupees" required>
                </div>
                <div class="form-group">
                    <label for="remark">Remark</label>
                    <textarea class="form-control" name="remark" id="remark" rows="2"></textarea>
                </div>
                <button class="btn btn-primary mt-2" name="newEntry" type="submit">Add Expense</button>
            </form>
        </div>
        <div class="col-md-2"></div>
    </div>
</div>


<script src="js/canvasjs.min.js"></script>
<script src="js/bootstrap.js"></script>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>  