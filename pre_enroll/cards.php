<div class="container mx-auto max-w-full px-2 py-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white text-center">Departments</h1>
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