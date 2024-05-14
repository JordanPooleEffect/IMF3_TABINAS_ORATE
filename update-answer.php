<!DOCTYPE html>
<html>
<head>
    <title>Update Answer</title>
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
        form {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"] {
            width: 100%;
            padding: 5px;
            margin-bottom: 10px;
        }
        textarea {
            width: 100%;
            padding: 5px;
            margin-bottom: 10px;
        }
        .btn {
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
</head>
<body>

<?php
include 'connect.php';

session_start();

$userid = $_SESSION['UserID'];

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['QuestionID']) && isset($_GET['AnswerID'])) {
        $QuestionID = $_GET['QuestionID'];
        $AnswerID = $_GET['AnswerID'];

        $sql = "SELECT q.QuestionText, a.AnswerText
                FROM tblquestion q
                JOIN answers a ON q.QuestionID = a.QuestionID
                WHERE q.QuestionID = ? AND a.AnswerID = ? AND a.UserID = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param('iii', $QuestionID, $AnswerID, $userid);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result) {
            die("Error fetching answer: " . $connection->error);
        }

        if ($row = $result->fetch_assoc()) {
            $QuestionText = $row['QuestionText'];
            $AnswerText = $row['AnswerText'];
        } else {
            die("Answer not found.");
        }
    } else {
        die("Invalid request.");
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['AnswerText'])) {
        $AnswerText = $_POST['AnswerText'];

        $sql = "UPDATE answers SET AnswerText = ? WHERE AnswerID = ? AND UserID = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param('sii', $AnswerText, $AnswerID, $userid);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "Answer updated successfully.";
        } else {
            echo "Failed to update answer.";
        }
    }
}
?>

<div class="container">
    <h1>Update Answer</h1>
    <form method="post">
        <label for="questionText">Question:</label>
        <p><?= $QuestionText ?></p>
        <label for="answerText">Answer:</label>
        <textarea id="answerText" name="AnswerText" rows="4"><?= $AnswerText ?></textarea>
        <input type="submit" value="Update" class="btn">
    </form>
</div>

<a href="profile-page.php" class="btn">Back</a>

</body>
</html>

<?php
$connection->close();
?>
