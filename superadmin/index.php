<?php
session_start();

if (!isset($_SESSION['id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit(); // Ensure no further execution


}
require_once __DIR__ .  "/database.php";
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
</head>

<body class="overflow-x-hidden"> <!-- Prevent horizontal scrolling -->
        

        <!-- Carousel -->
        <?php 
        
        include "header.php";
        echo"<br>";
        echo"<br>";
        echo"<br>";
        include "list.php";
        ?>
    </div>

    <!-- Swiper JS CDN -->
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="scripts_js/list.js"></script>
</body>
</html>