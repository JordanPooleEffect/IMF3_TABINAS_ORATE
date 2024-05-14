<?php
include 'connect.php';
session_start();

$adminUsernames = ['zmtabinas', 'feligwapo', 'admin'];

if (!isset($_SESSION['username']) || !in_array($_SESSION['username'], $adminUsernames)) {
    header('Location: login-page.php');
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login-page.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        echo "<script>
                if (confirmUpdate()) {
                    document.getElementById('messageBox').style.display = 'block';
                }
              </script>";

        if (confirmUpdate()) {
            $id = intval($_POST['id']);
            if ($_POST['type'] === 'user') {
                $sql = "UPDATE tbluserprofile SET firstname = ?, lastname = ?, gender = ?, birthdate = ? WHERE userid = ?";
                $stmt = $connection->prepare($sql);
                $stmt->bind_param("ssssi", $_POST['firstname'], $_POST['lastname'], $_POST['gender'], $_POST['birthdate'], $id);
                $stmt->execute();
                $stmt->close();
                $redirectType = 'records';
            }
            header("Location: ADMIN_dashboard-page.php?view=" . $redirectType);
            exit();
        }
    }

    if (isset($_POST['soft_delete'])) {
        echo "<script>
                if (showDeleteConfirmation()) {
                    document.getElementById('messageBox').style.display = 'block';
                }
              </script>";

        
            $deleteId = intval($_POST['id']);
            if ($_POST['type'] === 'user') {
                $profileSql = "UPDATE tbluserprofile SET is_deleted = 1 WHERE userid = ?";
                $profileStmt = $connection->prepare($profileSql);
                $profileStmt->bind_param("i", $deleteId);
                $profileStmt->execute();
                $profileStmt->close();

                $accountSql = "UPDATE tbluseraccount SET is_deleted = 1 WHERE acctid = ?";
                $accountStmt = $connection->prepare($accountSql);
                $accountStmt->bind_param("i", $deleteId);
                $accountStmt->execute();
                $accountStmt->close();

                header("Location: ADMIN_dashboard-page.php?view=records");
                exit();
            } else if ($_POST['type'] === 'answer') {
                $sql = "UPDATE answers SET is_deleted = 1 WHERE AnswerID = ?";
                $stmt = $connection->prepare($sql);
                $stmt->bind_param("i", $deleteId);
                $stmt->execute();
                $stmt->close();
                header("Location: ADMIN_dashboard-page.php?view=answers");
                exit();
            }
    }
}


$search = '';
if (isset($_GET['query'])) {
    $search = $connection->real_escape_string($_GET['query']);
}

$view = isset($_GET['view']) ? $_GET['view'] : 'records';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/message-box.css">
    <title>Curious KeyPie - Admin Dashboard</title>
    <script>
        function showMessageBox() {
            document.getElementById('messageBox').style.display = 'block';
        }

        function hideMessageBox() {
            document.getElementById('messageBox').style.display = 'none';
        }

        function showDeleteConfirmation(userId) {
            document.getElementById('deleteUserId').value = userId;
            showMessageBox();
        }

        function confirmUpdate() {
            return confirm("Are you sure you want to update this record?");
        }
    </script>
</head>

<body>
    <header class="navbar">
        <div class="company-name">Curious KeyPie</div>
        <div class="admin-info">
            <a href="index.php" class="admin-name">
                <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <?php if (in_array($_SESSION['username'], $adminUsernames)) echo "<span>(Super Admin)</span>"; ?>
            </a>
            <a href="?logout=true" class="logout-button">Logout</a>
        </div>
    </header>

    <div class="container">
        <nav class="sidebar">
                <ul>
                    <li><a href="?view=records" class="<?php echo ($view === 'records') ? 'active' : ''; ?>">User Records</a></li>
                    <li><a href="?view=answers" class="<?php echo ($view === 'answers') ? 'active' : ''; ?>">All User Answers</a></li>
                    <li><a href="?view=questions" class="<?php echo ($view === 'questions') ? 'active' : ''; ?>">All User Questions</a></li>
                    <li><a href="?view=female_records" class="<?php echo ($view === 'female_records') ? 'active' : ''; ?>">(Gender) Female Records</a></li>
                    <li><a href="?view=male_records" class="<?php echo ($view === 'male_records') ? 'active' : ''; ?>">(Gender) Male Records</a></li>
                    <li><a href="?view=other_gender_records" class="<?php echo ($view === 'other_gender_records') ? 'active' : ''; ?>">(Gender) Other Records</a></li>
                    <li><a href="?view=QnA_reports" class="<?php echo ($view === 'QnA_reports') ? 'active' : ''; ?>">Top Users by Question Submission</a></li>
                    <li><a href="?view=top_answerers" class="<?php echo ($view === 'top_answerers') ? 'active' : ''; ?>">Top Users by Answer Submission</a></li>
                </ul>
            </nav>
        <div class="content">
            <?php
            if ($view === 'records') {
                echo "<h2>User Records</h2>";
                $sql = $search ? "SELECT * FROM tbluserprofile WHERE firstname LIKE '%$search%' OR lastname LIKE '%$search%'" : "SELECT * FROM tbluserprofile WHERE is_deleted = 0";
                $result = $connection->query($sql);

                if ($result && $result->num_rows > 0) {
                    echo "<table>";
                    echo "<tr><th>User ID</th><th>First Name</th><th>Last Name</th><th>Gender</th><th>Birthdate</th><th>Actions</th></tr>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr><form method='POST'>";
                        echo "<td>" . htmlspecialchars($row["userid"]) . "<input type='hidden' name='id' value='" . $row["userid"] . "'><input type='hidden' name='type' value='user'></td>";
                        echo "<td><input type='text' name='firstname' value='" . htmlspecialchars($row["firstname"]) . "'></td>";
                        echo "<td><input type='text' name='lastname' value='" . htmlspecialchars($row["lastname"]) . "'></td>";
                        echo "<td><input type='text' name='gender' value='" . htmlspecialchars($row["gender"]) . "'></td>";
                        echo "<td><input type='date' name='birthdate' value='" . htmlspecialchars($row["birthdate"]) . "'></td>";
                        echo "<td>
                                <button type='submit' name='update' onclick='return confirmUpdate()'>Update</button>
                                <button type='button' name='soft_delete' onclick='showDeleteConfirmation(" . $row['userid'] . ")'>Soft Delete</button>
                              </td>";

                        echo "</form></tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>No results found.</p>";
                }
            } elseif ($view === 'answers') {
                $search = isset($_POST['search']) ? $_POST['search'] : '';

                echo "<h2>All User Answers</h2>";
                echo "<div style='text-align: right;'>";
                echo "<form method='POST'>";
                echo "<label for='search'>Search by Timestamp:</label>";
                echo "<input type='text' id='search' name='search' value='$search'>";
                echo "<button type='submit'>Search</button>";
                echo "</form>";
                echo "</div>";

                $sql = $search ?
                    "SELECT a.*, u.firstname, u.lastname FROM answers a JOIN tbluserprofile u ON a.UserID = u.userid WHERE a.Timestamp LIKE '%$search%'" :
                    "SELECT a.*, u.firstname, u.lastname FROM answers a JOIN tbluserprofile u ON a.UserID = u.userid";
                $result = $connection->query($sql);

                if ($result && $result->num_rows > 0) {
                    echo "<table>";
                    echo "<tr><th>Answer ID</th><th>User</th><th>Question ID</th><th>Answer Text</th><th>Timestamp</th><th>Actions</th></tr>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr><form method='POST'>";
                        echo "<td>" . htmlspecialchars($row["AnswerID"]) . "<input type='hidden' name='id' value='" . $row["AnswerID"] . "'><input type='hidden' name='type' value='answer'></td>";
                        echo "<td>" . htmlspecialchars($row["firstname"]) . " " . htmlspecialchars($row["lastname"]) . "</td>";
                        echo "<td><input type='number' name='QuestionID' value='" . htmlspecialchars($row["QuestionID"]) . "'></td>";
                        echo "<td><input type='text' name='AnswerText' value='" . htmlspecialchars($row["AnswerText"]) . "'></td>";
                        echo "<td>" . htmlspecialchars($row["Timestamp"]) . "</td>";
                        echo "<button type='submit' name='update'>Update</button>";
                        echo "<button type='button' name='soft_delete' onclick='showDeleteConfirmation(" . $row['AnswerID'] . ")'>Soft Delete</button>";
                        echo "</form></tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>No answers to display.</p>";
                }

            } elseif ($view === 'questions') {
                $search = isset($_POST['search']) ? $_POST['search'] : '';

                echo "<h2>All User Questions</h2>";
                echo "<div style='text-align: right;'>";
                echo "<form method='POST'>";
                echo "<label for='search'>Search by Timestamp:</label>";
                echo "<input type='text' id='search' name='search' value='$search'>";
                echo "<button type='submit'>Search</button>";
                echo "</form>";
                echo "</div>";

                $sql = $search ?
                    "SELECT q.*, u.firstname, u.lastname FROM tblquestion q JOIN tbluserprofile u ON q.UserID = u.userid WHERE q.Timestamp LIKE '%$search%'" :
                    "SELECT q.*, u.firstname, u.lastname FROM tblquestion q JOIN tbluserprofile u ON q.UserID = u.userid";
                $result = $connection->query($sql);

                if ($result && $result->num_rows > 0) {
                    echo "<table>";
                    echo "<tr><th>Question ID</th><th>User</th><th>Question Text</th><th>Timestamp</th><th>Actions</th></tr>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr><form method='POST'>";
                        echo "<td>" . htmlspecialchars($row["QuestionID"]) . "<input type='hidden' name='id' value='" . $row["QuestionID"] . "'><input type='hidden' name='type' value='question'></td>";
                        echo "<td>" . htmlspecialchars($row["firstname"]) . " " . htmlspecialchars($row["lastname"]) . "</td>";
                        echo "<td><input type='text' name='QuestionText' value='" . htmlspecialchars($row["QuestionText"]) . "'></td>";
                        echo "<td>" . htmlspecialchars($row["Timestamp"]) . "</td>";
                        echo "<td><button type='submit' name='update'>Update</button>
                              <button type='submit' name='delete' onclick='return confirm(\"Are you sure?\");'>Delete</button></td>";
                        echo "</form></tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>No questions to display.</p>";
                }


            } else if ($view === 'female_records') {
                  echo "<h2>(Gender) Female User Records</h2>";
                  $sql = $search ? "SELECT * FROM tbluserprofile WHERE (firstname LIKE '%$search%' OR lastname LIKE '%$search%') AND gender = 'Female'" : "SELECT * FROM tbluserprofile WHERE gender = 'Female'";
                  $result = $connection->query($sql);

                  if ($result && $result->num_rows > 0) {
                      echo "<table>";
                      echo "<tr><th>User ID</th><th>First Name</th><th>Last Name</th><th>Gender</th><th>Deleted?</th></tr>";
                      while ($row = $result->fetch_assoc()) {
                          echo "<tr><form method='POST'>";
                          echo "<td>" . htmlspecialchars($row["userid"]) . "<input type='hidden' name='id' value='" . $row["userid"] . "'><input type='hidden' name='type' value='user'></td>";
                          echo "<td><input type='text' name='firstname' value='" . htmlspecialchars($row["firstname"]) . "'></td>";
                          echo "<td><input type='text' name='lastname' value='" . htmlspecialchars($row["lastname"]) . "'></td>";
                          echo "<td><input type='text' name='gender' value='" . htmlspecialchars($row["gender"]) . "' readonly></td>";
                          echo "<td>" . htmlspecialchars($row["is_deleted"]) . "</td>";
                         // echo "<td><button type='submit' name='update'>Update</button>
                        //          <button type='submit' name='delete' onclick='return confirm(\"Are you sure?\");'>Delete</button></td>";
                          echo "</form></tr>";
                      }
                      echo "</table>";
                  } else {
                      echo "<p>No female user records found.</p>";
                  }
          } else if ($view === 'male_records') {
                echo "<h2>(Gender) Male User Records</h2>";
                $sql = $search ? "SELECT * FROM tbluserprofile WHERE (firstname LIKE '%$search%' OR lastname LIKE '%$search%') AND gender = 'Male'" : "SELECT * FROM tbluserprofile WHERE gender = 'Male'";
                $result = $connection->query($sql);

                if ($result && $result->num_rows > 0) {
                    echo "<table>";
                    echo "<tr><th>User ID</th><th>First Name</th><th>Last Name</th><th>Gender</th><th>Deleted?</th></tr>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr><form method='POST'>";
                        echo "<td>" . htmlspecialchars($row["userid"]) . "<input type='hidden' name='id' value='" . $row["userid"] . "'><input type='hidden' name='type' value='user'></td>";
                        echo "<td><input type='text' name='firstname' value='" . htmlspecialchars($row["firstname"]) . "'></td>";
                        echo "<td><input type='text' name='lastname' value='" . htmlspecialchars($row["lastname"]) . "'></td>";
                        echo "<td><input type='text' name='gender' value='" . htmlspecialchars($row["gender"]) . "' readonly></td>";
                        echo "<td>" . htmlspecialchars($row["is_deleted"]) . "</td>";
                    //    echo "<td><button type='submit' name='update'>Update</button>
                     //           <button type='submit' name='delete' onclick='return confirm(\"Are you sure?\");'>Delete</button></td>";
                        echo "</form></tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>No male user records found.</p>";
                }
            } else if ($view === 'other_gender_records') {
                  echo "<h2>(Gender) Other User Records</h2>";
                  $sql = $search ? "SELECT * FROM tbluserprofile WHERE (firstname LIKE '%$search%' OR lastname LIKE '%$search%') AND gender = 'Other'" : "SELECT * FROM tbluserprofile WHERE gender = 'Other'";
                  $result = $connection->query($sql);

                  if ($result && $result->num_rows > 0) {
                      echo "<table>";
                      echo "<tr><th>User ID</th><th>First Name</th><th>Last Name</th><th>Gender</th><th>Deleted?</th></tr>";
                      while ($row = $result->fetch_assoc()) {
                          echo "<tr><form method='POST'>";
                          echo "<td>" . htmlspecialchars($row["userid"]) . "<input type='hidden' name='id' value='" . $row["userid"] . "'><input type='hidden' name='type' value='user'></td>";
                          echo "<td><input type='text' name='firstname' value='" . htmlspecialchars($row["firstname"]) . "'></td>";
                          echo "<td><input type='text' name='lastname' value='" . htmlspecialchars($row["lastname"]) . "'></td>";
                          echo "<td><input type='text' name='gender' value='" . htmlspecialchars($row["gender"]) . "' readonly></td>";
                          echo "<td>" . htmlspecialchars($row["is_deleted"]) . "</td>";
                     //    echo "<td><button type='submit' name='update'>Update</button>
                       //           <button type='submit' name='delete' onclick='return confirm(\"Are you sure?\");'>Delete</button></td>";
                          echo "</form></tr>";
                      }
                      echo "</table>";
                  } else {
                      echo "<p>No male user records found.</p>";
                  }
 } elseif ($view === 'QnA_reports') {
     echo "<h2>Top Users by Question Submission</h2>";

     $sql = "SELECT a.UserID, COUNT(a.UserID) AS total_questions
             FROM answers a
             LEFT JOIN tblquestion q ON a.QuestionID = q.QuestionID
             GROUP BY a.UserID
             ORDER BY total_questions DESC
             LIMIT 5";

     $result = $connection->query($sql);

     if ($result) {
         if ($result->num_rows > 0) {
             echo "<table>";
             echo "<tr><th>User ID</th><th>First Name</th><th>Last Name</th><th>Total Questions</th></tr>";
             while ($row = $result->fetch_assoc()) {
                 $userID = $row['UserID'];
                 $totalQuestions = $row['total_questions'];


                 $userDetailsSql = "SELECT * FROM tbluserprofile WHERE UserID = '$userID'";
                 $userDetailsResult = $connection->query($userDetailsSql);
                 $userDetails = $userDetailsResult->fetch_assoc();

                 $firstname = $userDetails['firstname'];
                 $lastname = $userDetails['lastname'];

                 echo "<tr><td>$userID</td><td>$firstname</td><td>$lastname</td><td>$totalQuestions</td></tr>";
             }
             echo "</table>";
         } else {
             echo "<p>No users found.</p>";
         }
     } else {
         echo "<p>Error retrieving data from the database.</p>";
     }
 } elseif ($view === 'top_answerers') {
      echo "<h2>Top Users by Answer Submission</h2>";

      $sql = "SELECT a.UserID, COUNT(a.UserID) AS total_answers
              FROM answers a
              GROUP BY a.UserID
              ORDER BY total_answers DESC
              LIMIT 5";

      $result = $connection->query($sql);

      if ($result) {
          if ($result->num_rows > 0) {
              echo "<table>";
              echo "<tr><th>User ID</th><th>First Name</th><th>Last Name</th><th>Total Answers</th></tr>";
              while ($row = $result->fetch_assoc()) {
                  $userID = $row['UserID'];
                  $totalAnswers = $row['total_answers'];


                  $userDetailsSql = "SELECT * FROM tbluserprofile WHERE UserID = '$userID'";
                  $userDetailsResult = $connection->query($userDetailsSql);
                  $userDetails = $userDetailsResult->fetch_assoc();

                  $firstname = $userDetails['firstname'];
                  $lastname = $userDetails['lastname'];

                  echo "<tr><td>$userID</td><td>$firstname</td><td>$lastname</td><td>$totalAnswers</td></tr>";
              }
              echo "</table>";
          } else {
              echo "<p>No users found.</p>";
          }
      } else {
          echo "<p>Error retrieving data from the database.</p>";
      }
  }




            ?>
            <div id="messageBox" class="message-box">
                <span class="close-btn" onclick="hideMessageBox()">&times;</span>
                <br>
                <p>Are you sure you want to delete this record?</p>
                <form method="POST" id="deleteForm">
                    <input type="hidden" name="id" id="deleteUserId" value="">
                    <input type="hidden" name="type" value="user">
                    <button type="submit" name="soft_delete">Confirm Delete</button>              
                         <!-- <button type="button" onclick="hideMessageBox()">Cancel</button> -->
                    
                </form>
            </div>
        </div>
    </div>


    
</body>
</html>