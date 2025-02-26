<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get the current page from URL parameter, default to 'profile'
$page = $_GET['page'] ?? 'profile';
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
    <!-- Add modal.js to the head section -->
    <script src="script_js/modal.js" defer></script>
</head>

<body class="overflow-x-hidden"> <!-- Prevent horizontal scrolling -->
    <?php 
        include "sidebar.php";
    ?>
    <div id="content" class="p-4">
        <?php
        // Show content based on page parameter
        switch($page) {
            case 'requirements':
                include "requirements.php";
                break;
            case 'profile':
            default:
                include "profilelaman.php";
                break;
        }
        ?>
    </div>

    <!-- Swiper JS CDN -->
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="script_js/sidebar.js"></script>
</body>
</html>