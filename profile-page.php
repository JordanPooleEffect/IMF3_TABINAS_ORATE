<!-- TO IMPLEMENT: CREATE USER PROFILE SETTINGS WHEREIN THEY CAN UPDATE THEIR STUFF -->
<!-- PK, PERSONAL INFO (FIRSTNAME, LASTNAME, BIRTHDAY) SHOULD NOT BE UPDATABLE -->
<?php
session_start();

include 'connect.php';
include 'header.php';

if (!isset($_SESSION['username'])) {
    header("Location: login-page.php");
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login-page.php');
    exit();
}

$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_question'])) {
    $questionText = mysqli_real_escape_string($connection, $_POST['question']);

    $query = "INSERT INTO tblquestion (UserID, QuestionText, Timestamp) VALUES (?, ?, NOW())";
    $stmt = $connection->prepare($query);

    if ($stmt) {
        $userID = $_SESSION['UserID'];
        $stmt->bind_param('is', $userID, $questionText);
        if ($stmt->execute()) {
            $message = "Question submitted successfully.";
        } else {
            $message = "Failed to submit the question: {$stmt->error}";
        }
        $stmt->close();
    } else {
        $message = "Error preparing statement: {$connection->error}";
    }
}

mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
</head>
<body>
    <div class="container-profile">
        <h1>Welcome, <?php echo $username; ?>!</h1>
        <div class="question-box">
            <h2>Question Box</h2>
            <?php if (isset($message)): ?>
                <div><?php echo $message; ?></div>
            <?php endif; ?>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <textarea name="question" rows="4" cols="50" placeholder="Ask your question here..."></textarea>
                <br>
                <input type="submit" name="submit_question" value="Submit Question">
                
                


               


            </form>
            
        </div>
    </div>
    <a href="profile-settings.php" class="btn">Settings</a>
    <a href="answers-view.php" class="btn">View Answers</a>
            <?php if ($username === 'admin'): ?>
                    <a href="ADMIN.php" class="btn">Admin Page</a>
                <?php endif; ?>
</body>
</html>