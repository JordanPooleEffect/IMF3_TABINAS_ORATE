<?php
session_start();
include 'connect.php';
include 'manual_connect.php';

$loginMessage = "";

if (isset($_POST['btnLogin'])) {
    $uname = mysqli_real_escape_string($connection, $_POST['txtusername']);
    $pword = mysqli_real_escape_string($connection, $_POST['txtpassword']);

    $query = "SELECT * FROM tbluseraccount WHERE username = '$uname'";
    $result = mysqli_query($connection, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $dbHashedPassword = $row['password'];

        if (password_verify($pword, $dbHashedPassword)) {
            $_SESSION['username'] = $uname;
            $_SESSION['UserID'] = $row['acctid'];
            header('Location: index.php');
            exit();
        } else {
            $loginMessage = "Invalid password.";
        }
    } else {
        $loginMessage = "Invalid username or password.";
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Curious Key Pie - Login</title>
    <link rel="stylesheet" href="css/login-page.css">
    <link rel="stylesheet" href="css/message-box.css">
</head>

<body class="login">
    <div class="login-container">
        <div class="login-content">
            <div class="image-column">
                <img src="images/inspo.png" alt="Login Image" class="login-image">
            </div>
            <div class="credentials-column">
                <h2>Welcome to <br> Curious Key Pie</h2>
                <p>Stay Curious.</p>

                <div class="message-box <?php echo ($loginMessage != "") ? 'active' : ''; ?>">
                    <span class="close-btn" onclick="this.parentElement.classList.remove('active');">&times;</span>
                    <?php echo $loginMessage; ?>
                </div>

                <form action="login-page.php" method="post">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="txtusername" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="txtpassword" required>
                    </div>
                    <button type="submit" name="btnLogin" class="login-button">Login</button>
                    <p class="register-link">Not a member? <a href="register-page.php">Register here</a></p>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
