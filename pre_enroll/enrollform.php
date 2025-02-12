<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-blue-50 to-gray-100 p-6">
    <div class="container mx-auto">
        <div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-2xl">
            <!-- Header Section -->
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Pre-Registration Form</h2>
                <div class="flex items-center justify-center space-x-2 mb-4">
                    <span class="text-sm font-medium text-gray-500">Step</span>
                    <span id="stepCounter" class="px-3 py-1 bg-blue-500 text-white rounded-full text-sm font-bold">1</span>
                    <span class="text-sm font-medium text-gray-500">of 4</span>
                </div>
                <!-- Progress Bar -->
                <div class="w-full bg-gray-200 rounded-full h-2 mb-6 overflow-hidden">
                    <div id="progressBar" 
                         class="bg-blue-500 h-2 rounded-full transition-all duration-300 ease-in-out" 
                         style="width: 25%">
                    </div>
                </div>
            </div>

            <form action="" method="post" class="space-y-8">
                <!-- Step 1 -->
                <div class="step" id="step1">
                    <input type="hidden" id="IDNO" name="IDNO" value="<?php echo isset($_SESSION['STUDID']) ? $_SESSION['STUDID'] : $autonum->AUTO; ?>">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <label for="FNAME" class="block text-sm font-medium text-gray-700">First Name</label>
                            <input required id="FNAME" name="FNAME" placeholder="Enter your first name" type="text" value="<?php echo isset($_SESSION['FNAME']) ? $_SESSION['FNAME'] : ''; ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                        <div class="space-y-4">
                            <label for="LNAME" class="block text-sm font-medium text-gray-700">Last Name</label>
                            <input required id="LNAME" name="LNAME" placeholder="Last Name" type="text" value="<?php echo isset($_SESSION['LNAME']) ? $_SESSION['LNAME'] : ''; ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                        <div class="space-y-4">
                            <label for="MI" class="block text-sm font-medium text-gray-700">Middle Name</label>
                            <input required id="MI" name="MI" placeholder="Middle Name" type="text" value="<?php echo isset($_SESSION['MI']) ? $_SESSION['MI'] : ''; ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                        <div class="space-y-4">
                            <label for="PADDRESS" class="block text-sm font-medium text-gray-700">Address</label>
                            <input required id="PADDRESS" name="PADDRESS" placeholder="Permanent Address" type="text" value="<?php echo isset($_SESSION['PADDRESS']) ? $_SESSION['PADDRESS'] : ''; ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                        <div class="space-y-4">
                            <label for="EMAIL" class="block text-sm font-medium text-gray-700">Email</label>
                            <input required id="EMAIL" name="EMAIL" placeholder="Email Address" type="text" value="<?php echo isset($_SESSION['EMAIL']) ? $_SESSION['EMAIL'] : ''; ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="step hidden" id="step2">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <label class="block text-sm font-medium text-gray-700">Sex</label>
                            <div class="flex space-x-4">
                                <label class="inline-flex items-center">
                                    <input checked id="optionsRadios1" name="optionsRadios" type="radio" value="Female" class="w-4 h-4 text-blue-500 border-gray-300 focus:ring-blue-500">
                                    <span class="ml-2">Female</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input id="optionsRadios2" name="optionsRadios" type="radio" value="Male" class="w-4 h-4 text-blue-500 border-gray-300 focus:ring-blue-500">
                                    <span class="ml-2">Male</span>
                                </label>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <label for="BIRTHDATE" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                            <input required id="BIRTHDATE" name="BIRTHDATE" type="date" placeholder="mm/dd/yyyy" value="<?php echo isset($_SESSION['BIRTHDATE']) ? $_SESSION['BIRTHDATE'] : ''; ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                        <div class="space-y-4">
                            <label for="BIRTHPLACE" class="block text-sm font-medium text-gray-700">Place of Birth</label>
                            <input required id="BIRTHPLACE" name="BIRTHPLACE" placeholder="Place of Birth" type="text" value="<?php echo isset($_SESSION['BIRTHPLACE']) ? $_SESSION['BIRTHPLACE'] : ''; ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="step hidden" id="step3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <label for="NATIONALITY" class="block text-sm font-medium text-gray-700">Nationality</label>
                            <input required id="NATIONALITY" name="NATIONALITY" placeholder="Nationality" type="text" value="<?php echo isset($_SESSION['NATIONALITY']) ? $_SESSION['NATIONALITY'] : ''; ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                        <div class="space-y-4">
                            <label for="RELIGION" class="block text-sm font-medium text-gray-700">Religion</label>
                            <input required id="RELIGION" name="RELIGION" placeholder="Religion" type="text" value="<?php echo isset($_SESSION['RELIGION']) ? $_SESSION['RELIGION'] : ''; ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                        <div class="space-y-4">
                            <label for="CONTACT" class="block text-sm font-medium text-gray-700">Contact No.</label>
                            <input required id="CONTACT" name="CONTACT" placeholder="Contact Number" type="number" maxlength="11" value="<?php echo isset($_SESSION['CONTACT']) ? $_SESSION['CONTACT'] : ''; ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                        <div class="space-y-4">
                            <label for="COURSE" class="block text-sm font-medium text-gray-700">Course/Year</label>
                            <select name="COURSE" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                <?php
                                if(isset($_SESSION['COURSEID'])){
                                    $course = New Course();
                                    $singlecourse = $course->single_course($_SESSION['COURSEID']);
                                    echo '<option value='.$singlecourse->COURSE_ID.' >'.$singlecourse->COURSE_NAME.'-'.$singlecourse->COURSE_LEVEL.' </option>';
                                }else{
                                    echo '<option value="Select">Select</option>';
                                }
                                ?>
                                <?php 
                                $mydb->setQuery("SELECT * FROM `course` WHERE COURSE_LEVEL=1");
                                $cur = $mydb->loadResultList();
                                foreach ($cur as $result) {
                                    echo '<option value='.$result->COURSE_ID.' >'.$result->COURSE_NAME.'-'.$result->COURSE_LEVEL.' </option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="space-y-4">
                            <label for="SEMESTER" class="block text-sm font-medium text-gray-700">Semester to Enroll</label>
                            <select required id="SEMESTER" name="SEMESTER" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                <option value="">Select Semester</option>
                                <option value="1">1st Semester</option>
                                <option value="2">2nd Semester</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="step hidden" id="step4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <label for="CIVILSTATUS" class="block text-sm font-medium text-gray-700">Civil Status</label>
                            <select name="CIVILSTATUS" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                <option value="Select">Select Status</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Widow">Widow</option>
                            </select>
                        </div>
                        <div class="space-y-4">
                            <label for="USER_NAME" class="block text-sm font-medium text-gray-700">Username</label>
                            <input required id="USER_NAME" name="USER_NAME" placeholder="Username" type="text" value="<?php echo isset($_SESSION['USER_NAME']) ? $_SESSION['USER_NAME'] : ''; ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                        <div class="space-y-4">
                            <label for="PASS" class="block text-sm font-medium text-gray-700">Password</label>
                            <input required id="PASS" name="PASS" placeholder="Password" type="password" value="<?php echo isset($_SESSION['PASS']) ? $_SESSION['PASS'] : ''; ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                        <div class="space-y-4">
                            <label for="GUARDIAN" class="block text-sm font-medium text-gray-700">Guardian</label>
                            <input required id="GUARDIAN" name="GUARDIAN" placeholder="Guardian Name" type="text" value="<?php echo isset($_SESSION['GUARDIAN']) ? $_SESSION['GUARDIAN'] : ''; ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                        <div class="space-y-4">
                            <label for="GCONTACT" class="block text-sm font-medium text-gray-700">Guardian Contact</label>
                            <input required id="GCONTACT" name="GCONTACT" placeholder="Contact Number" type="text" value="<?php echo isset($_SESSION['GCONTACT']) ? $_SESSION['GCONTACT'] : ''; ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                    <button type="button" id="prev" class="hidden px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-all duration-200">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Previous
                        </span>
                    </button>
                    
                    <button type="button" id="next" class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300 transition-all duration-200">
                        <span class="flex items-center">
                            Next
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </span>
                    </button>
                    
                    <button type="submit" id="submit" name="regsubmit" class="hidden px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300 transition-all duration-200">
                        <span class="flex items-center">
                            Submit
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="scripts_js/enroll.js"></script>

    <!-- Loading Screen -->
    <div id="loadingScreen" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-8 rounded-lg shadow-xl flex flex-col items-center">
            <div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-500 border-t-transparent"></div>
            <p class="mt-4 text-gray-700">Submitting your application...</p>
        </div>
    </div>

    <!-- Success Popup -->
    <div id="successPopup" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-8 rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="flex items-center justify-center mb-4">
                <svg class="w-16 h-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-center text-gray-900 mb-2">Form Successfully Submitted!</h3>
            <p class="text-gray-600 text-center mb-4">Please check your gmail for updates.</p>
            <p class="text-gray-500 text-center text-sm">Redirecting to Home in <span id="countdownTimer">3</span>s</p>
        </div>
    </div>
</body>