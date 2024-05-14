<!DOCTYPE html>
<html>
<head>
    <title>My Answers</title>
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
</head>
<body>

<?php 
include 'connect.php'; 

session_start();

$userid = $_SESSION['UserID'];

$sql = "SELECT q.QuestionID, q.QuestionText, a.AnswerID, a.AnswerText
        FROM tblquestion q
        JOIN answers a ON q.QuestionID = a.QuestionID
        WHERE a.UserID = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param('i', $userid);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Error fetching answers: " . $connection->error);
}
?>

<div class="container">
    <h1>My Answers</h1>
    <table>
        <tr>
            <th>Question</th>
            <th>Answer</th>
            <th>Action</th>
        </tr>
        <?php while($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row["QuestionText"] ?></td>
                <td><?= $row["AnswerText"] ?></td>
                <td><a href="update-answer.php?question_id=<?= $row["QuestionID"] ?>&answer_id=<?= $row["AnswerID"] ?>" class="btn">Update</a></td>
            </tr>
        <?php } ?>
    </table>
</div>
<a href="profile-page.php" class="btn">Back</a>

</body>
</html>

<?php
$connection->close();
?>
