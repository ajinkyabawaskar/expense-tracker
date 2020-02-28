<?php
require("connection.php");
if (!empty($_POST)) {
    if (empty($_POST['username'])) {
        die("Please enter a username.");
    }
    if (empty($_POST['password'])) {
        die("Please enter a password.");
    }
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        die("Invalid E-Mail Address");
    }
    $query = "
        SELECT
            1
        FROM users
        WHERE
            username = :username
    ";
    $query_params = array(
        ':username' => $_POST['username']
    );
    try {
        $stmt = $db->prepare($query);
        $result = $stmt->execute($query_params);
    }
    catch(PDOException $ex) {
        die("Failed to run query: " . $ex->getMessage());
    }
    $row = $stmt->fetch();
    if ($row) {
        echo "
        <script type='text/javascript'>
        alert('User already exits');
        window.location.href='index.php';
        </script>";
        die();
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
    $query = "
        INSERT INTO users (
            username,
            password,
            salt,
            email
        ) VALUES (
            :username,
            :password,
            :salt,
            :email
        )
    ";
    $salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647));
    $password = hash('sha256', $_POST['password'] . $salt);
    for($round = 0; $round < 65536; $round++) { 
        $password = hash('sha256', $password . $salt);
    }
    $query_params = array(
        ':username' => $_POST['username'],
        ':password' => $password,
        ':salt' => $salt,
        ':email' => $_POST['email']
    );
    try { 
        $stmt = $db->prepare($query);
        $result = $stmt->execute($query_params);
        echo "
        <script type='text/javascript'>
        alert('Account created successfully!');
        window.location.href='login.php';
        </script>";
    }
    catch(PDOException $ex) {
        die("Failed to run query: " . $ex->getMessage());
        echo "
        <script type='text/javascript'>
        alert('Error');
        window.location.href='login.php';
        </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sign Up - My Academic Space</title>
    <link rel=" icon" href="../favicon.png"/> 
    <link rel="stylesheet" type="text/css" href="css/login.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
</head>
<body>
<div class="container">
    <div class="login">
        Expense Tracker
        <br>
        Signing up for an account.
    </div>
    <div class="wrapper">
        <form action="register.php" method="post">
            <div class="group">
                <input type="text" name="username" value="" required/>
                <span class="highlight"></span><span class="bar"></span>
                <label>Username</label>
            </div>
            <div class="group">
                <input type="email" name="email" value="" required/>
                <span class="highlight"></span><span class="bar"></span>
                <label>Email</label>
            </div>
            <div class="group">
                <input type="password" name="password" value="" required/>
                <span class="highlight"></span><span class="bar"></span>
                <label>Password</label>
            </div>
            <div class="btn-box">
            <button class="btn btn-submit" type="submit">Sign Up</button>
            <button class="btn btn-cancel" type="reset">Reset</button>
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