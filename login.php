<?php
require("connection.php");
$submitted_username = '';
if (!empty($_POST)) {
    $query = "
        SELECT
            id,
            username,
            password,
            salt,
            email
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
    $login_ok = false;
    $row = $stmt->fetch();
    if ($row) { 
        $check_password = hash('sha256', $_POST['password'] . $row['salt']);
        for ($round = 0; $round < 65536; $round++) {
            $check_password = hash('sha256', $check_password . $row['salt']);
        }
        if ($check_password === $row['password']) { 
            $login_ok = true;
        } else {
            $_SESSION['wrongPassword'] = true;
        }
    } else {
        $_SESSION['wrongUsername'] = true;
    }
    if ($login_ok) { 
        unset($row['salt']);
        unset($row['password']);
        $_SESSION['hasError'] = false;
        $_SESSION['user'] = $row;
        header("Location: index.php");
        die("Redirecting to: index.php");
    }
    else {
        $_SESSION['hasError'] = true;
        $submitted_username = htmlentities($_POST['username'], ENT_QUOTES, 'UTF-8');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Log In - My Academic Space</title>
    <link rel=" icon" href="favicon.png"/> 
    <link rel="stylesheet" type="text/css" href="css/login.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
</head>
<body>
<div class="container">
    <div class="login">
        Expense Tracker
        <br>
        Log in to your account
    </div>
    <div class="wrapper">
        <form action="login.php" method="post">
            <div class="group">
            <input type="text" required="required" name="username" value="<?php echo $submitted_username; ?>"/>
            <span class="highlight"></span><span class="bar"></span>
            <label>Username</label>
            </div>
            <div class="group">
            <input type="password" required="required" name="password" value=""/><span class="highlight"></span><span class="bar"></span>
            <label>Password</label>
            </div>
            <div class="btn-box">
            <button class="btn btn-submit" type="submit">Login</button>
            <button class="btn btn-cancel" type="reset">Reset</button>
            </div>
        </form>
    </div>
    <div class="register">
        Don't have an account?
        <a href="register.php" class="noLink">Sign up.</a>
    </div>
</div>
<?php 
    if ($_SESSION['hasError'] == true)
        include("error.php");
?>
</body>
</html>