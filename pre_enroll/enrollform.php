<?php
require_once __DIR__ .  "/Logic_enroll.php";
require_once __DIR__ .  "/Logic_validate.php";
?>



<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-blue-50 to-gray-100 p-6">
    <div class="container mx-auto">
        <div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-2xl">
            <!-- Header Section -->
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Registration Form</h2>
                <div class="flex items-center justify-center space-x-2 mb-4">
                    <span class="text-sm font-medium text-gray-500">Step</span>
                    <span id="stepCounter" class="px-3 py-1 bg-blue-500 text-white rounded-full text-sm font-bold">1</span>
                    <span class="text-sm font-medium text-gray-500">of 5</span>
                </div>
                <!-- Progress Bar -->
                <div class="w-full bg-gray-200 rounded-full h-2 mb-6 overflow-hidden">
                    <div id="progressBar" 
                         class="bg-blue-500 h-2 rounded-full transition-all duration-300 ease-in-out" 
                         style="width: 25%">
                    </div>
                </div>
            </div>

            <form action="Logic_enroll.php" method="post" class="space-y-8" enctype="multipart/form-data">
                <!-- Step 1 -->
                <div class="step" id="step1">
                    <input type="hidden" id="IDNO" name="IDNO" value="<?php echo isset($_SESSION['STUDID']) ? $_SESSION['STUDID'] : $autonum->AUTO; ?>">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <label for="FNAME" class="block text-sm font-medium text-gray-700">First Name <span class="text-red-500">*</span></label>
                            <input required id="FNAME" name="FNAME" placeholder="Enter your first name" type="text" value="<?php echo isset($_SESSION['FNAME']) ? $_SESSION['FNAME'] : ''; ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                        <div class="space-y-4">
                            <label for="LNAME" class="block text-sm font-medium text-gray-700">Last Name <span class="text-red-500">*</span></label>
                            <input required id="LNAME" name="LNAME" placeholder="Last Name" type="text" value="<?php echo isset($_SESSION['LNAME']) ? $_SESSION['LNAME'] : ''; ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                        <div class="space-y-4">
                            <label for="MI" class="block text-sm font-medium text-gray-700">Middle Name <span class="text-red-500">*</span></label>
                            <input required id="MI" name="MI" placeholder="Middle Name" type="text" value="<?php echo isset($_SESSION['MI']) ? $_SESSION['MI'] : ''; ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                        <div class="space-y-4">
                            <label for="PADDRESS" class="block text-sm font-medium text-gray-700">Address <span class="text-red-500">*</span></label>
                            <input required id="PADDRESS" name="PADDRESS" placeholder="Permanent Address" type="text" value="<?php echo isset($_SESSION['PADDRESS']) ? $_SESSION['PADDRESS'] : ''; ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                        <div class="space-y-4">
                            <label for="EMAIL" class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="EMAIL" id="email" required 
                                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   value="<?php echo isset($_SESSION['verifiedEmail']) ? $_SESSION['verifiedEmail'] : ''; ?>" readonly>
                        </div>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="step hidden" id="step2">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <label class="block text-sm font-medium text-gray-700">Sex <span class="text-red-500">*</span></label>
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
                            <label for="BIRTHDATE" class="block text-sm font-medium text-gray-700">Date of Birth <span class="text-red-500">*</span></label>
                            <input required id="BIRTHDATE" name="BIRTHDATE" type="date" placeholder="mm/dd/yyyy" value="<?php echo isset($_SESSION['BIRTHDATE']) ? $_SESSION['BIRTHDATE'] : ''; ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                        <div class="space-y-4">
                            <label for="BIRTHPLACE" class="block text-sm font-medium text-gray-700">Place of Birth <span class="text-red-500">*</span></label>
                            <input required id="BIRTHPLACE" name="BIRTHPLACE" placeholder="Place of Birth" type="text" value="<?php echo isset($_SESSION['BIRTHPLACE']) ? $_SESSION['BIRTHPLACE'] : ''; ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="step hidden" id="step3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <label for="NATIONALITY" class="block text-sm font-medium text-gray-700">Nationality <span class="text-red-500">*</span></label>
                            <input required id="NATIONALITY" name="NATIONALITY" placeholder="Nationality" type="text" value="<?php echo isset($_SESSION['NATIONALITY']) ? $_SESSION['NATIONALITY'] : ''; ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                        <div class="space-y-4">
                            <label for="RELIGION" class="block text-sm font-medium text-gray-700">Religion <span class="text-red-500">*</span></label>
                            <input required id="RELIGION" name="RELIGION" placeholder="Religion" type="text" value="<?php echo isset($_SESSION['RELIGION']) ? $_SESSION['RELIGION'] : ''; ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                        <div class="space-y-4">
                            <label for="CONTACT" class="block text-sm font-medium text-gray-700">Contact No. <span class="text-red-500">*</span></label>
                            <input required id="CONTACT" name="CONTACT" placeholder="Contact Number" type="number" maxlength="11" value="<?php echo isset($_SESSION['CONTACT']) ? $_SESSION['CONTACT'] : ''; ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                        <div class="space-y-4">
                            <label for="COURSE" class="block text-sm font-medium text-gray-700">Course/Year <span class="text-red-500">*</span></label>
                            <select required name="COURSE" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                <?php
                                if(isset($_SESSION['COURSEID'])){
                                    $course = New Course();
                                    $singlecourse = $course->single_course($_SESSION['COURSEID']);
                                    echo '<option value='.$singlecourse->COURSE_ID.' >'.$singlecourse->COURSE_NAME.'-'.$singlecourse->COURSE_DESC.' </option>';
                                }else{
                                    echo '<option value="">Select</option>';
                                }
                                ?>
                                <?php 
                                $mydb->setQuery("SELECT * FROM `course` WHERE COURSE_LEVEL=1");
                                $cur = $mydb->loadResultList();
                                foreach ($cur as $result) {
                                    echo '<option value='.$result->COURSE_ID.' >'.$result->COURSE_NAME.'-'.$result->COURSE_DESC.' </option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="space-y-4">
                            <label for="SYEAR" class="block text-sm font-medium text-gray-700">School Year <span class="text-red-500">*</span></label>
                            <select required id="SYEAR" name="SYEAR" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                <option value="">Select School Year</option>
                                <option value="2025-2026" selected>2025-2026</option>
                                <option value="2026-2027">2026-2027</option>
                            </select>
                        </div>
                        <div class="space-y-4">
                            <label for="SEMESTER" class="block text-sm font-medium text-gray-700">Semester to Enroll <span class="text-red-500">*</span></label>
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
                            <label for="CIVILSTATUS" class="block text-sm font-medium text-gray-700">Civil Status <span class="text-red-500">*</span></label>
                            <select required name="CIVILSTATUS" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                <option value="">Select Status</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Widow">Widow</option>
                            </select>
                        </div>
                        <div class="space-y-4">
                            <label for="GUARDIAN" class="block text-sm font-medium text-gray-700">Guardian <span class="text-red-500">*</span></label>
                            <input required id="GUARDIAN" name="GUARDIAN" placeholder="Guardian Name" type="text" value="<?php echo isset($_SESSION['GUARDIAN']) ? $_SESSION['GUARDIAN'] : ''; ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                        <div class="space-y-4">
                            <label for="GCONTACT" class="block text-sm font-medium text-gray-700">Guardian Contact <span class="text-red-500">*</span></label>
                            <input required id="GCONTACT" name="GCONTACT" placeholder="Contact Number" type="text" value="<?php echo isset($_SESSION['GCONTACT']) ? $_SESSION['GCONTACT'] : ''; ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        </div>
                    </div>
                </div>

                <!-- Step 5 -->
                <div class="step hidden" id="step5">
                    <div class="space-y-6">
                        <!-- Student Type Dropdown -->
                        <div class="space-y-4">
                            <label for="stud_type" class="block text-sm font-medium text-gray-700">Student Type <span class="text-red-500">*</span></label>
                            <select required id="stud_type" name="stud_type" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                <option value="">Select Student Type</option>
                                <option value="senior high">Senior High</option>
                                <option value="octoberian">Octoberian</option>
                                <option value="freshmen college">Freshmen College</option>
                            </select>
                        </div>
                         <!-- Warning Banner for Requirements -->
                         <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700 font-medium">
                                        All missing requirements will be treated as "TO-FOLLOW" and must be present during the enrollment/submission day.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <!-- Required Documents -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Form 138 -->
                            <div class="space-y-4">
                                <label for="form_138" class="block text-sm font-medium text-gray-700">Form 138</label>
                                <div class="relative">
                                    <input type="file" id="form_138" name="form_138" accept="image/*,.pdf" 
                                        class="absolute inset-0 w-full h-full opacity-0 z-10 cursor-pointer"
                                        onchange="updateFileName(this)"/>
                                    <div class="w-full p-3 bg-white border border-gray-300 rounded-lg flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="w-6 h-6 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                            </svg>
                                            <span class="text-sm text-gray-500 file-name">Choose file...</span>
                                        </div>
                                        <span class="bg-blue-50 text-blue-700 py-1 px-3 rounded-full text-xs font-medium">Browse</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Good Moral -->
                            <div class="space-y-4">
                                <label for="good_moral" class="block text-sm font-medium text-gray-700">Good Moral</label>
                                <div class="relative">
                                    <input type="file" id="good_moral" name="good_moral" accept="image/*,.pdf" 
                                        class="absolute inset-0 w-full h-full opacity-0 z-10 cursor-pointer"
                                        onchange="updateFileName(this)"/>
                                    <div class="w-full p-3 bg-white border border-gray-300 rounded-lg flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="w-6 h-6 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                            </svg>
                                            <span class="text-sm text-gray-500 file-name">Choose file...</span>
                                        </div>
                                        <span class="bg-blue-50 text-blue-700 py-1 px-3 rounded-full text-xs font-medium">Browse</span>
                                    </div>
                                </div>
                            </div>

                            <!-- PSA Birth Certificate -->
                            <div class="space-y-4">
                                <label for="psa_birthCert" class="block text-sm font-medium text-gray-700">PSA Birth Certificate</label>
                                <div class="relative">
                                    <input type="file" id="psa_birthCert" name="psa_birthCert" accept="image/*,.pdf" 
                                        class="absolute inset-0 w-full h-full opacity-0 z-10 cursor-pointer"
                                        onchange="updateFileName(this)"/>
                                    <div class="w-full p-3 bg-white border border-gray-300 rounded-lg flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="w-6 h-6 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                            </svg>
                                            <span class="text-sm text-gray-500 file-name">Choose file...</span>
                                        </div>
                                        <span class="bg-blue-50 text-blue-700 py-1 px-3 rounded-full text-xs font-medium">Browse</span>
                                    </div>
                                </div>
                            </div>

                            <!-- ID Picture -->
                            <div class="space-y-4">
                                <label for="id_pic" class="block text-sm font-medium text-gray-700">2x2 ID Picture</label>
                                <div class="relative">
                                    <input type="file" id="id_pic" name="id_pic" accept="image/*" 
                                        class="absolute inset-0 w-full h-full opacity-0 z-10 cursor-pointer"
                                        onchange="updateFileName(this)"/>
                                    <div class="w-full p-3 bg-white border border-gray-300 rounded-lg flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="w-6 h-6 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                            </svg>
                                            <span class="text-sm text-gray-500 file-name">Choose file...</span>
                                        </div>
                                        <span class="bg-blue-50 text-blue-700 py-1 px-3 rounded-full text-xs font-medium">Browse</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Barangay Clearance -->
                            <div class="space-y-4">
                                <label for="Brgy_clearance" class="block text-sm font-medium text-gray-700">Barangay Clearance</label>
                                <div class="relative">
                                    <input type="file" id="Brgy_clearance" name="Brgy_clearance" accept="image/*,.pdf" 
                                        class="absolute inset-0 w-full h-full opacity-0 z-10 cursor-pointer"
                                        onchange="updateFileName(this)"/>
                                    <div class="w-full p-3 bg-white border border-gray-300 rounded-lg flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="w-6 h-6 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                            </svg>
                                            <span class="text-sm text-gray-500 file-name">Choose file...</span>
                                        </div>
                                        <span class="bg-blue-50 text-blue-700 py-1 px-3 rounded-full text-xs font-medium">Browse</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Transcript of Records -->
                            <div class="space-y-4">
                                <label for="tor" class="block text-sm font-medium text-gray-700">Transcript of Records</label>
                                <div class="relative">
                                    <input type="file" id="tor" name="tor" accept="image/*,.pdf" 
                                        class="absolute inset-0 w-full h-full opacity-0 z-10 cursor-pointer"
                                        onchange="updateFileName(this)"/>
                                    <div class="w-full p-3 bg-white border border-gray-300 rounded-lg flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="w-6 h-6 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                            </svg>
                                            <span class="text-sm text-gray-500 file-name">Choose file...</span>
                                        </div>
                                        <span class="bg-blue-50 text-blue-700 py-1 px-3 rounded-full text-xs font-medium">Browse</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Honorable Dismissal -->
                            <div class="space-y-4">
                                <label for="honor_dismissal" class="block text-sm font-medium text-gray-700">Honorable Dismissal</label>
                                <div class="relative">
                                    <input type="file" id="honor_dismissal" name="honor_dismissal" accept="image/*,.pdf" 
                                        class="absolute inset-0 w-full h-full opacity-0 z-10 cursor-pointer"
                                        onchange="updateFileName(this)"/>
                                    <div class="w-full p-3 bg-white border border-gray-300 rounded-lg flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="w-6 h-6 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                            </svg>
                                            <span class="text-sm text-gray-500 file-name">Choose file...</span>
                                        </div>
                                        <span class="bg-blue-50 text-blue-700 py-1 px-3 rounded-full text-xs font-medium">Browse</span>
                                    </div>
                                </div>
                            </div>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const verifiedEmail = localStorage.getItem('verifiedEmail');
            if (verifiedEmail) {
                const emailInput = document.getElementById('email');
                emailInput.value = verifiedEmail;
                emailInput.setAttribute('readonly', true);
            }
        });

        function updateFileName(input) {
            const fileName = input.files[0]?.name || 'Choose file...';
            const fileNameElement = input.parentElement.querySelector('.file-name');
            if (fileNameElement) {
                fileNameElement.textContent = fileName;
            }
            
            // Update border color based on validation
            const container = input.parentElement.querySelector('.border');
            if (input.files.length > 0) {
                container.classList.remove('border-red-500');
                container.classList.add('border-green-500');
            } else {
                container.classList.remove('border-green-500');
                container.classList.add('border-red-500');
            }
        }

        // Add drag and drop functionality
        document.querySelectorAll('.relative').forEach(dropZone => {
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.querySelector('.border').classList.add('border-blue-500', 'bg-blue-50');
            });

            dropZone.addEventListener('dragleave', (e) => {
                e.preventDefault();
                dropZone.querySelector('.border').classList.remove('border-blue-500', 'bg-blue-50');
            });

            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                const input = dropZone.querySelector('input[type="file"]');
                const dt = e.dataTransfer;
                input.files = dt.files;
                updateFileName(input);
                dropZone.querySelector('.border').classList.remove('border-blue-500', 'bg-blue-50');
            });
        });
    </script>

    <!-- Loading Screen -->
    <div id="loadingScreen" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-8 rounded-lg shadow-xl flex flex-col items-center">
            <div class="animate-spin rounded-full h-12 w-12 border-t-4 border-b-4 border-blue-500"></div>
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