<?php
include 'connect.php';
session_start();

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login-page.php');
    exit();
}

if (!isset($_SESSION['username']) || !isset($_SESSION['UserID'])) {
    header('Location: login-page.php');
    exit();
}


$query = "SELECT QuestionID, QuestionText FROM tblquestion WHERE QuestionID NOT IN (SELECT QuestionID FROM answers WHERE UserID = ?)";
$stmt = $connection->prepare($query);
$stmt->bind_param('i', $_SESSION['UserID']);
$stmt->execute();
$result = $stmt->get_result();

$questions = array();
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $questions[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $questionID = $_POST['QuestionID'];
    $userID = $_SESSION['UserID'];
    $answerText = mysqli_real_escape_string($connection, $_POST['AnswerText']);

    $query = "INSERT INTO answers (QuestionID, UserID, AnswerText, Timestamp) VALUES (?, ?, ?, NOW())";
    $stmt = $connection->prepare($query);

    if ($stmt) {
        $stmt->bind_param('iis', $questionID, $userID, $answerText);
        if ($stmt->execute()) {
            $message = "Answer submitted successfully.";
        } else {
            $message = "Failed to submit the answer: {$stmt->error}";
        }
        $stmt->close();
    } else {
        $message = "Error preparing statement: {$connection->error}";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/index-page.css" />
    <link rel="stylesheet" href="css/message-box.css">
    <title>Curious KeyPie - Home</title>
</head>
<body>
    <header>
        <a href="index.php" class="logo">
            <i class='bx bxs-site'></i>Curious KeyPie
        </a>

        <ul class="navbar">
            <li><a href="index.php" class="home-active">Home</a></li>
            <li><a href="#">About Us</a></li>
            <li><a href="#">Contact Us</a></li>
        </ul>

        <div class="user-info">
            <a href="profile-page.php" class="user-name"><?php echo htmlspecialchars($_SESSION['username']); ?></a>
            <a href="?logout=true" class="btn">Logout</a>
        </div>
    </header>

    <div class="container">
        <h2>Submit Your Answer</h2>
        <?php if (isset($message)) : ?>
            <div class="message-box active">
                <span class="close-btn" onclick="this.parentElement.classList.remove('active');">&times;</span>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php foreach ($questions as $question) : ?>
            <div class="question-container">
                <div class="question-box">
                    <h3>Question:</h3>
                    <p><?php echo $question['QuestionText']; ?></p>
                </div>
                <form method="POST" class="submit-answer-form">
                    <input type="hidden" name="QuestionID" value="<?php echo $question['QuestionID']; ?>">
                    <label for="AnswerText">Your Answer:</label>
                    <textarea id="AnswerText" name="AnswerText" required></textarea>
                    <input type="submit" value="Submit Answer">
                </form>
            </div>
        <?php endforeach; ?>

    </div>
</body>
</html>
