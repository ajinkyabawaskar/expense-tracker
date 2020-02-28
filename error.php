<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Error</title>
    <link rel=" icon" href="favicon.png"/> 
    <link rel="stylesheet" type="text/css" href="css/login.css">
    <link rel="stylesheet" type="text/css" href="https://fontlibrary.org/face/glacial-indifference" media="screen"/>
</head>
<body>
    <div class="overlay">
        <div class="loginFailed">
            <div class="failedMessage">
                <?php
                if($_SESSION['wrongUsername'] == true) {
                echo 'Username not found';
                }
                if($_SESSION['wrongUsername'] == false && $_SESSION['wrongPassword'] == true)
                echo 'Password Incorrect';
                ?>
            </div>
            <div class="failedBtn" >
                <a href="forgot.php" class="btn btn-try">Forgot?</a>
                <a href="login.php" class="btn btn-try">OK</a>
            </div>
        </div>
    </div>
</body>
</html>