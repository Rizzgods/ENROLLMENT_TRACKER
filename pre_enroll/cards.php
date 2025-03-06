<?php
// Database connection
$servername = "localhost";
$username = "admi_greenvalley";
$password = "xr9%kxu%*my^+kf2";
$dbname = "admi_dbgreenvalley";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get courses without department join since the department table doesn't exist
$sql = "SELECT * FROM course ORDER BY COURSE_NAME";
$result = $conn->query($sql);
?>

<div id="courses" class="container mx-auto max-w-6xl px-4 py-8 scroll-mt-20"> <!-- Added id="courses" and scroll margin -->
    <h1 class="text-3xl font-bold text-gray-800 dark:text-white text-center">Available Programs</h1>
    <p class="text-center text-gray-600 dark:text-gray-400 mb-10">Choose from our wide range of academic programs</p>
    
    <?php if ($result && $result->num_rows > 0): ?>
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Course Code
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Description
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <?php
                                    // Generate a color based on the course name
                                    $colors = ['4F46E5', '2563EB', '0891B2', '0D9488', '4338CA', '7C3AED', 'DB2777', 'DC2626'];
                                    $colorIndex = ord(substr($row["COURSE_NAME"], 0, 1)) % count($colors);
                                    $color = $colors[$colorIndex];
                                    
                                    $courseInitial = substr($row["COURSE_NAME"], 0, 1);
                                    ?>
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-[#<?php echo $color; ?>] text-white flex items-center justify-center font-bold">
                                        <?php echo htmlspecialchars($courseInitial); ?>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($row["COURSE_NAME"]); ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($row["COURSE_DESC"]); ?></div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="text-center py-10 bg-white rounded-lg shadow-md">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No courses available</h3>
            <p class="mt-1 text-sm text-gray-500">Check back later for updates or contact admissions.</p>
        </div>
    <?php endif; ?>

    <?php if (isset($conn) && $conn) $conn->close(); ?>
</div>