<?php
    include 'connect.php';
?>
 
<link rel="stylesheet" href="css/indexstyle.css"/>
<link rel="stylesheet" href="css/index-page.css"/>
 
    <header>
        <a href=""class="logo">
            <i class='bx bxs-site'></i>Curious KeyPie
        </a>
 
        <ul class="navbar">
            <li><a href="index.php"class="home-active">Home </a></li>
            <li><a href="#aboutus">About Us</a></li>
            <li><a href="#contact">Contact Us </a></li>
        </ul>
 
        <div class="user-info">
            <a href="profile-page.php" class="user-name"><?php echo htmlspecialchars($_SESSION['username']); ?></a>
            <a href="?logout=true" class="btn">Logout</a>
        </div>
           
    </header>
 
    <body>