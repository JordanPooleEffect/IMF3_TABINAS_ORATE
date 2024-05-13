<?php
include 'connect.php';

$registerMessage = "";

if (isset($_POST['btnRegister'])) {
    $fname = mysqli_real_escape_string($connection, $_POST['txtfirstname']);
    $lname = mysqli_real_escape_string($connection, $_POST['txtlastname']);
    $gender = mysqli_real_escape_string($connection, $_POST['txtgender']);
    $email = mysqli_real_escape_string($connection, $_POST['txtemail']);
    $uname = mysqli_real_escape_string($connection, $_POST['txtusername']);
    $birthdate = mysqli_real_escape_string($connection, $_POST['txtbirthdate']);
    $pword = mysqli_real_escape_string($connection, $_POST['txtpassword']);
    $confirmpword = mysqli_real_escape_string($connection, $_POST['txtconfirmpassword']);

    $check_query = "SELECT * FROM tbluseraccount WHERE username = '$uname' OR emailadd = '$email'";
    $check_result = mysqli_query($connection, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $registerMessage = "Username or email already exists. Please choose a different one.";
    } else {
        if ($pword !== $confirmpword) {
            $registerMessage = "Passwords do not match.";
        } else {
            $hashedPassword = password_hash($pword, PASSWORD_DEFAULT);
            
            $sql1 = "INSERT INTO tbluserprofile (firstname, lastname, gender, birthdate) VALUES ('$fname', '$lname', '$gender', '$birthdate')";
            if (mysqli_query($connection, $sql1)) {
                $profileID = mysqli_insert_id($connection);

                $sql2 = "INSERT INTO tbluseraccount (emailadd, username, password) VALUES ('$email', '$uname', '$hashedPassword')";
                if (mysqli_query($connection, $sql2)) {
                    $registerMessage = "Registration successful. You can now login.";
                } else {
                    $registerMessage = "Error occurred while creating account. Please try again.";
                }
            } else {
                $registerMessage = "Error occurred while creating profile. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Curious Key Pie - Register</title>
    <link rel="stylesheet" href="css/register-page.css">
    <link rel="stylesheet" href="css/message-box.css">
</head>

<body class="login">
    <div class="login-container">
        <div class="login-content">
            <div class="image-column">
                <img src="images/cartoon-mountain.jpg" alt="Login Image" class="login-image">
            </div>
            <div class="credentials-column">
                <h2>Welcome to Curious Key Pie</h2>
                <p class="under-title">Come on and create an account</p>

                <div class="message-box <?php echo ($registerMessage != "") ? 'active' : ''; ?>">
                    <span class="close-btn" onclick="this.parentElement.classList.remove('active');">&times;</span>
                    <?php echo htmlspecialchars($registerMessage); ?>
                </div>

                <form action="register-page.php" method="post">
                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <input type="text" id="firstName" name="txtfirstname" placeholder="Enter First Name" required/>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <input type="text" id="lastName" name="txtlastname" placeholder="Enter Last Name" required/>
                    </div>
                    <div class="form-group">
                        <label for="birthdate">Birthdate</label>
                        <input type="date" id="birthdate" name="txtbirthdate" required />
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="txtusername" placeholder="Enter Username" required/>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="txtemail" placeholder="Enter Email" required/>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="txtpassword" placeholder="Enter Password" required/>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Confirm Password</label>
                        <input type="password" id="confirmPassword" name="txtconfirmpassword" placeholder="Enter Password Again" required/>
                    </div>
                    <div class="form-group gender-details-box">
                        <label for="gender">Gender</label>
                        <div class="gender-category">
                            <input type="radio" name="txtgender" id="male" value="Male" required>
                            <label for="male">Male</label>
                            <input type="radio" name="txtgender" id="female" value="Female" required>
                            <label for="female">Female</label>
                            <input type="radio" name="txtgender" id="other" value="Other" required>
                            <label for="other">Other</label>
                        </div>
                    </div>
                    
                    <button type="submit" name="btnRegister" class="login-button">Sign up</button>
                    <p class="register-link">Already a member? <a href="login-page.php">Sign in</a></p>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
