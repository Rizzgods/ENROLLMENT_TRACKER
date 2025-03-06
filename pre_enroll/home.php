<?php

session_start();
// Database credentials
$servername = "localhost";
$username = "root";
$password = "OH3nb3jPdGnCM8gK";
$dbname = "admi_dbgreenvalley";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM tbl_bcpdepts";
$result = $conn->query($sql);

$count_stud = "SELECT COUNT(*) FROM tblstudent";
$total = $conn->query($count_stud);

$count_course = "SELECT COUNT(*) FROM course";
$total_course = $conn->query($count_course);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Swiper CSS CDN -->
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <link rel="stylesheet" href="css_files/cards.css" />
    <style>
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>

<body class="overflow-x-hidden"> <!-- Prevent horizontal scrolling -->
        

        <!-- Carousel -->
        <?php 
        
        include "header.php";
        include "banner.php";
        include "num.php";
        include 'cards.php'; 
        include 'chatbot_notification.php'; 
        include 'mission.php';
        include 'footer.php';
        ?>
    </div>

    <!-- Swiper JS CDN -->
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="scripts_js/script.js"></script>
</body>
</html>