<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    exit('Please log in to view this content');
}

// Updated database connection with correct credentials for production server
$servername = "localhost";
$username = "admi_greenvalley";
$password = "xr9%kxu%*my^+kf2";
$dbname = "admi_dbgreenvalley";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    error_log("Connection failed in requirements.php: " . $conn->connect_error);
    exit("Connection failed: " . $conn->connect_error);
}

// Fetch only logged-in student's information
$stmt = $conn->prepare("SELECT * FROM tblstudent WHERE IDNO = ?");
$stmt->bind_param("s", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="ml-16 p-4 sm:p-6 lg:p-8"> <!-- Responsive padding -->
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl sm:text-2xl font-semibold text-gray-800">Student Information</h2>
        <a href="form_edit.php" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20h9M12 4h9M4 8h16M4 16h16"/>
            </svg>
            Edit
        </a>
    </div>
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-4 sm:p-6 space-y-6">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <!-- Profile Card -->
                    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Basic Info -->
                        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                            <h3 class="font-medium text-gray-700 border-b pb-2">Basic Information</h3>
                            <div class="space-y-2">
                                <p class="text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($row['FNAME'] . ' ' . $row['MNAME'] . ' ' . $row['LNAME']); ?>
                                </p>
                                <p class="text-sm text-gray-600">
                                    ID: <?php echo htmlspecialchars($row['IDNO']); ?>
                                </p>
                                <p class="text-sm text-gray-600">
                                    Sex: <?php echo htmlspecialchars($row['SEX']); ?>
                                </p>
                            </div>
                        </div>

                        <!-- Contact Details -->
                        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                            <h3 class="font-medium text-gray-700 border-b pb-2">Contact Information</h3>
                            <div class="space-y-2">
                                <p class="text-sm flex items-center text-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <?php echo htmlspecialchars($row['CONTACT_NO']); ?>
                                </p>
                                <p class="text-sm flex items-center text-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <?php echo htmlspecialchars($row['EMAIL']); ?>
                                </p>
                                <p class="text-sm flex items-center text-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                    <?php echo htmlspecialchars($row['HOME_ADD']); ?>
                                </p>
                            </div>
                        </div>

                        <!-- Academic Info -->
                        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                            <h3 class="font-medium text-gray-700 border-b pb-2">Academic Information</h3>
                            <div class="space-y-2">
                                <p class="text-sm text-gray-600">
                                    Course: <?php echo htmlspecialchars($row['COURSE_ID']); ?>
                                </p>
                                <p class="text-sm text-gray-600">
                                    Year: <?php echo htmlspecialchars($row['YEARLEVEL']); ?>
                                </p>
                                <p class="text-sm text-gray-600">
                                    Section: <?php echo htmlspecialchars($row['STUDSECTION']); ?>
                                </p>
                            </div>
                        </div>

                        <!-- Documents Section -->
                        <div class="col-span-2 bg-gray-50 rounded-lg p-4 space-y-3">
                            <h3 class="font-medium text-gray-700 border-b pb-2">Required Documents</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <?php 
                                $documents = [
                                    'form_138' => 'Form 138',
                                    'form_137' => 'Form 137',
                                    'good_moral' => 'Good Moral',
                                    'psa_birthCert' => 'PSA Birth Certificate',
                                    'Brgy_clearance' => 'Barangay Clearance',
                                    'tor' => 'Transcript of Records',
                                    'honor_dismissal' => 'Honorable Dismissal'
                                ];

                                
                                foreach ($documents as $field => $label):
                                $hasDocument = !empty($row[$field]);
                                $documentSrc = $hasDocument ? 'data:image/jpeg;base64,' . base64_encode($row[$field]) : '';
                            ?>
                                <div class="bg-white rounded-lg p-3 shadow-sm hover:shadow-md transition-shadow cursor-pointer"
                                     onclick="window.showModal('<?php echo $documentSrc; ?>')">
                                    <div class="flex flex-col items-center space-y-2">
                                        <?php if ($hasDocument): ?>
                                            <div class="relative group">
                                                <img src="<?php echo $documentSrc; ?>"
                                                    alt="<?php echo htmlspecialchars($label); ?>"
                                                    class="w-20 h-20 object-cover rounded-lg" />
                                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 rounded-lg transition-all flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <span class="text-green-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </span>
                                        <?php else: ?>
                                            <div class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            <span class="text-red-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </span>
                                        <?php endif; ?>
                                        <p class="text-xs text-gray-600 text-center"><?php echo htmlspecialchars($label); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Photo -->
                        <div class="bg-gray-50 rounded-lg p-4 flex flex-col items-center justify-center">
                            <?php if (!empty($row['id_pic'])): ?>
                                <div class="relative w-24 h-24">
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($row['id_pic']); ?>" 
                                         alt="Student Photo" 
                                         class="rounded-full w-full h-full object-cover shadow-lg"
                                         onerror="this.onerror=null; this.src='assets/default-avatar.png';">
                                    <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-400 rounded-full border-2 border-white"></div>
                                </div>
                                <p class="mt-2 text-sm font-medium text-gray-700">Student Photo</p>
                            <?php else: ?>
                                <div class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <p class="mt-2 text-sm font-medium text-gray-500">No photo available</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No Information Available</h3>
                    <p class="mt-1 text-sm text-gray-500">No student information could be found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Updated Modal Structure -->
<div id="imageModal" class="fixed inset-0 hidden z-[100] flex items-center justify-center">
    <div class="fixed inset-0 bg-black bg-opacity-75 modal-backdrop"></div>
    
    <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all max-w-2xl w-full mx-4 z-10">
        <div class="flex justify-between items-center p-4 border-b">
            <h3 class="text-lg font-medium text-gray-900">Document Preview</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <div class="p-4 flex items-center justify-center">
            <img id="modalImage" class="max-h-[70vh] max-w-full object-contain" alt="Document Preview">
        </div>
    </div>
</div>

<!-- Make sure the script is properly included with absolute path -->
<script src="/onlineenrolmentsystem/tracking/script_js/modal.js"></script>


<?php
$stmt->close();
$conn->close();
?>
