<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f7f7f7;
        }
        form {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #f7f7f7;
            color: #333;
            text-decoration: none;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #eaeaea;
        }
    </style>
    <script>
        function showMessage(message) {
            alert(message);
        }
    </script>
</head>
<body>

<?php 
include 'connect.php'; 

session_start();

$message = "";

if (isset($_POST['updatePassword'])) {
    $currentPassword = mysqli_real_escape_string($connection, $_POST['currentPassword']);
    $newPassword = mysqli_real_escape_string($connection, $_POST['newPassword']);
    $confirmPassword = mysqli_real_escape_string($connection, $_POST['confirmPassword']);

    $userid = $_SESSION['UserID']; // Change 'userid' to 'UserID'
    $sql = "SELECT password FROM tbluseraccount WHERE acctid = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param('i', $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $dbHashedPassword = $row['password'];

        if (password_verify($currentPassword, $dbHashedPassword)) {
            if ($newPassword === $confirmPassword) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                $updateSql = "UPDATE tbluseraccount SET password = ? WHERE acctid = ?";
                $stmt = $connection->prepare($updateSql);
                $stmt->bind_param('si', $hashedPassword, $userid);
                if ($stmt->execute()) {
                    $message = "Password updated successfully";
                } else {
                    $message = "Error updating password: " . $stmt->error;
                }
            } else {
                $message = "New password and confirmation do not match";
            }
        } else {
            $message = "Incorrect current password";
        }
    } else {
        $message = "User not found";
    }
}

$sql = "SELECT up.firstname, up.lastname, up.gender, up.birthdate, ua.emailadd, '********' AS password_masked
        FROM tbluserprofile up
        JOIN tbluseraccount ua ON up.userid = ua.acctid
        WHERE up.userid = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param('i', $_SESSION['UserID']); 
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Error fetching user profile data: " . $connection->error);
}
?>

<div class="container">
    <head><link rel="stylesheet" href="css/message-box.css"></head>
    <h1>User Profile</h1>
    <table>
        <tr>
            <th>Firstname</th>
            <th>Lastname</th>
            <th>Gender</th>
            <th>Birthdate</th>
            <th>Email Address</th>
            <th>Password</th>
        </tr>
        <?php while($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row["firstname"] ?></td>
                <td><?= $row["lastname"] ?></td>
                <td><?= $row["gender"] ?></td>
                <td><?= $row["birthdate"] ?></td>
                <td><?= $row["emailadd"] ?></td>
                <td><?= $row["password_masked"] ?></td>
            </tr>
        <?php } ?>
    </table>

    <h2>Update Password</h2>

    <div class="message-box <?php echo ($message != "") ? 'active' : ''; ?>">
                    <span class="close-btn" onclick="this.parentElement.classList.remove('active');">&times;</span>
                    <?php echo $message; ?>
                </div>

    <form action="profile-settings.php" method="post">
        <label for="currentPassword">Current Password:</label>
        <input type="password" id="currentPassword" name="currentPassword" required><br>
        
        <label for="newPassword">New Password:</label>
        <input type="password" id="newPassword" name="newPassword" required><br>
        
        <label for="confirmPassword">Confirm New Password:</label>
        <input type="password" id="confirmPassword" name="confirmPassword" required><br>
        
        <input type="submit" name="updatePassword" value="Update Password">
    </form>

    <a href="profile-page.php" class="btn">Back</a>
</div>

</body>

</html>

<?php
$connection->close();
?>
