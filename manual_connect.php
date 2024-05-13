<?php

$dsn = 'mysql:host=localhost;dbname=dbtabinasf3;charset=utf8';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $useraccount = "CREATE TABLE IF NOT EXISTS tbluseraccount (
                        acctid INT(11) AUTO_INCREMENT PRIMARY KEY,
                        emailadd VARCHAR(255) NOT NULL,
                        username VARCHAR(255) NOT NULL,
                        password VARCHAR(255) NOT NULL,
                        isAdmin TINYINT(1) NOT NULL DEFAULT 0,
                        is_deleted TINYINT(1) NOT NULL DEFAULT 0
                    )";

    $userprofile = "CREATE TABLE IF NOT EXISTS tbluserprofile (
                        userid INT(11) AUTO_INCREMENT PRIMARY KEY,
                        firstname VARCHAR(255) NOT NULL,
                        lastname VARCHAR(255) NOT NULL,
                        gender VARCHAR(10) NOT NULL,
                        birthdate DATE NOT NULL,
                        is_deleted TINYINT(1) NOT NULL DEFAULT 0
                    )";

    $answers = "CREATE TABLE IF NOT EXISTS answers (
                        AnswerID INT(11) AUTO_INCREMENT PRIMARY KEY,
                        QuestionID INT(11) NOT NULL,
                        UserID INT(11) NOT NULL,
                        AnswerText TEXT NOT NULL,
                        Timestamp DATETIME NOT NULL,
                        is_deleted TINYINT(1) NOT NULL DEFAULT 0
                    )";

    $questions = "CREATE TABLE IF NOT EXISTS tblquestion (
                        QuestionID INT(11) AUTO_INCREMENT PRIMARY KEY,
                        UserID INT(11) NOT NULL,
                        QuestionText TEXT NOT NULL,
                        Timestamp DATETIME NOT NULL,
                        isAnonymous TINYINT(1) NOT NULL DEFAULT 0,
                        is_deleted TINYINT(1) NOT NULL DEFAULT 0
                    )";


    $pdo->exec($useraccount);
    $pdo->exec($userprofile);
    $pdo->exec($answers);
    $pdo->exec($questions);

    // echo "DATABASE CREATED AND ALL TABLES ARE FILLED";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

?>