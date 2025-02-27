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
</head>

<body class="overflow-x-hidden"> <!-- Prevent horizontal scrolling -->
    <!-- Include Sidebar -->
    <?php include "sidebar.php"; ?>

    <!-- Main Content -->
    <div class="ml-32 p-4">
        <!-- Content sections -->
        <div id="list-content" class="content-section">
            <?php include "list.php"; ?>
        </div>
        
        <div id="account-content" class="content-section hidden">
            <?php include "account_create.php"; ?>
        </div>
    </div>

    <!-- Swiper JS CDN -->
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="scripts_js/list.js"></script>
    <script src="scripts_js/sidebar.js"></script>
</body>
</html>