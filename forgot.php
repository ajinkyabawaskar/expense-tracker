<?php
require("connection.php");
if (!empty($_POST)) {
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        die("Invalid E-Mail Address");
    }
    $query = "
        SELECT
            1
        FROM users
        WHERE
            email = :email
    ";
    $query_params = array(
        ':email' => $_POST['email']
    );
    try { 
        $stmt = $db->prepare($query);
        $result = $stmt->execute($query_params);
    }
    catch(PDOException $ex) {
        die("Failed to run query: " . $ex->getMessage());
    }
    $row = $stmt->fetch();
    if($row) {
        // die("This email address is already registered");
        echo "
        <script type='text/javascript'>
        alert('This email address is already registered');
        window.location.href='index.php';
        </script>";
        die();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Recover your account - My Academic Space</title>
    <link rel=" icon" href="favicon.png"/> 
    <link rel="stylesheet" type="text/css" href="css/login.css">
    <link rel="stylesheet" type="text/css" href="css/forgot.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
</head>
<body>
<div class="container">
    <div class="login">
        To recover your account,<br>please enter your email
    </div>
    <div class="wrapper">
        <form action="forgot.php" method="post">
            <div class="group">
                <input type="email" name="email" value="" required/>
                <span class="highlight"></span><span class="bar"></span>
                <label>Email</label>
            </div>
            <div class="btn-box">
                <button class="btn btn-submit" type="submit">Send</button>
                <button class="btn btn-cancel" type="reset">Clear</button>
            </div>
        </form>
    </div>
    <div class="register">
        Already have an account?
        <a href="login.php" class="noLink">Log In.</a>
    </div>
</div>
</body>
</html>