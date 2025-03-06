<?php
// Error reporting for debugging - remove in production
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'superadmin_errors.log');

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection with error handling
$database_file = __DIR__ . "/database.php";
if (file_exists($database_file)) {
    require_once $database_file;
} else {
    error_log("Database file missing: " . $database_file);
    die("Configuration error. Please contact the administrator.");
}
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
    <style>
        .content-section {
            display: none;
        }
        #list-content {
            display: block; /* Show by default */
        }
    </style>
</head>

<body class="overflow-x-hidden bg-gray-100"> <!-- Prevent horizontal scrolling -->
    <!-- Include Sidebar -->
    <?php 
    if (file_exists("sidebar.php")) {
        include "sidebar.php";
    } else {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">Navigation sidebar could not be loaded.</span>
        </div>';
    }
    ?>

    <!-- Main Content -->
    <div class="ml-32 p-4">
        <!-- Content sections -->
        <div id="list-content" class="content-section">
            <br>
            <br>
            <br>
            <br>
            <?php 
            if (file_exists("list.php")) {
                include "list.php";
            } else {
                echo '<div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Warning!</strong>
                    <span class="block sm:inline">List content could not be loaded.</span>
                </div>';
            }
            ?>
        </div>
        
        <div id="account-content" class="content-section">
            <?php 
            if (file_exists("account_create.php")) {
                include "account_create.php";
            }
            ?>
        </div>

        <div id="logs-content" class="content-section">
            <?php 
            if (file_exists("user_logs.php")) {
                include "user_logs.php";
            }
            ?>
        </div>
        
    </div>

    <!-- JavaScript -->
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Load sidebar.js first to ensure navigation works -->
    <script>
        // Fallback in case script file doesn't load
        document.addEventListener('DOMContentLoaded', function() {
            // Simple navigation functionality in case the external JS fails
            const sidebarLinks = document.querySelectorAll('[data-content]');
            if (sidebarLinks) {
                sidebarLinks.forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const target = this.getAttribute('data-content');
                        document.querySelectorAll('.content-section').forEach(section => {
                            section.style.display = 'none';
                        });
                        if (document.getElementById(target + '-content')) {
                            document.getElementById(target + '-content').style.display = 'block';
                        }
                    });
                });
            }
        });
    </script>
    <script src="scripts_js/sidebar.js"></script>
    <script src="scripts_js/list.js"></script>
</body>
</html>