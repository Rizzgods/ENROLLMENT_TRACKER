<?php
// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbgreenvalley";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM tbl_bcpdepts";
$result = $conn->query($sql);
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
<style>
        .card-container {
            min-height: 400px; /* Set a minimum height for all cards */
            display: flex;
            flex-direction: column;
        }
        .card-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .card-description {
            flex: 1;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }
</style>
<body class="overflow-x-hidden"> <!-- Prevent horizontal scrolling -->
    <!-- Navbar -->
    <header class="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-4 md:p-6">
        <div class="container mx-auto max-w-[1920px] px-4">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 sm:gap-0">
                <div class="flex items-center space-x-4">
                    <img src="assets/logo" alt="Logo" class="h-10 w-10">
                    <h1 class="text-2xl font-bold tracking-tight"></h1>
                </div>
                <div class="space-x-4 sm:space-x-6">
                    <a href="#" class="text-white hover:text-blue-200 transition duration-150">About</a>
                    <a href="#" class="text-white hover:text-blue-200 transition duration-150">Department</a>
                    <a href="#" class="text-white hover:text-blue-200 transition duration-150">Contact</a>
                </div>
            </div>
        </div>
    </header>

    <div class="w-full">
        <div class="hero bg-cover bg-center h-96 md:h-[600px] lg:h-[800px]" style="background-image: url('assets/bestlink.jpg');">
            <div class="flex items-center justify-center h-full bg-black bg-opacity-50 text-center px-4">
                <div class="text-center whitespace-normal px-4 md:px-8">
                    <h1 class="text-4xl md:text-6xl font-bold text-white">Welcome to Bestlink College of the Philippines</h1>
                    <p class="text-white mt-4 text-lg md:text-xl max-w-3xl mx-auto leading-relaxed">
                        At Bestlink College of the Philippines, we provide and promote quality education with modern and unique techniques 
                        to enhance the skills and knowledge of our dear students, making them globally competitive and productive citizens.
                    </p>
                    <button class="mt-8 px-6 py-3 bg-blue-600 bg-opacity-10 text-white font-semibold rounded-full border-4 border-blue-300 hover:bg-blue-300 hover:bg-opacity-100 transition duration-150">
                        Enroll Now!
                    </button>
                </div>
            </div>
        </div>

        <!-- Carousel -->
        <div class="container mx-auto  px-2 py-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Departments</h1>
            <br>
            <!-- Added relative positioning to contain pagination -->
            <div class="relative">
                <!-- Increased bottom padding to make room for pagination -->
                <div class="swiper-container pb-12"> 
                    <div class="swiper-wrapper">
                        <?php
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                $imgData = base64_encode($row['dept_img']);
                                $src = 'data:image/jpeg;base64,' . $imgData;
                                echo '<div class="swiper-slide w-full">';
                                echo '<div class="bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 card-container">'; // Changed class
                                echo '<a href="#"><img class="rounded-t-lg w-full h-48 object-cover" src="' . $src . '" alt="" /></a>';
                                echo '<div class="p-5 card-content">'; // Changed class
                                echo '<div class="mb-auto">'; // Added margin-bottom auto
                                echo '<a href="#"><h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">' . $row["dept_name"] . ' (' . $row["dept_abbrev"] . ')</h5></a>';
                                $desc = strlen($row["dept_desc"]) > 100 ? substr($row["dept_desc"], 0, 100) . "..." : $row["dept_desc"];
                                echo '<p class="mb-3 font-normal text-gray-700 dark:text-gray-400 card-description">' . $desc . '</p>'; // Added class
                                echo '</div>';
                                echo '<a href="#" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Read more<svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/></svg></a>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }
                        } else {
                            echo "0 results";
                        }
                        $conn->close(); 
                        ?>
                    </div>
                    <!-- Positioned pagination absolutely at the bottom -->
                    <div class="swiper-pagination !absolute bottom-0 left-0 right-0"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Swiper JS CDN -->
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script>
        var swiper = new Swiper('.swiper-container', {
            slidesPerView: 1,
            spaceBetween: 5, // Reduced space between slides

            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                    spaceBetween: 8, // Reduced space
                },
                768: {
                    slidesPerView: 3,
                    spaceBetween: 10, // Reduced space
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 12, // Reduced space
                },
            },
        });

    </script>
</body>
</html>