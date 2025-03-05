<?php
session_start();

require_once __DIR__ .  "/Logic_reset.php";
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
        echo"<br>";
        include "reset.php";
        ?>
    </div>



    <!-- Swiper JS CDN -->
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="scripts_js/script.js"></script>
    <script src = "script_js/reset.js"></script>
</body>
</html>