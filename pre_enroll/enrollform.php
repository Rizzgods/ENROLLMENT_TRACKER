<?php
require_once __DIR__ .  "/Logic_enroll.php";
require_once __DIR__ .  "/Logic_validate.php";
?>

<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-blue-50 to-gray-100 p-6">
    <div class="container mx-auto">
        <!-- Data Privacy Notice Container (shown first) -->
        <div id="data_privacy_container" class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-2xl">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Data Privacy Notice</h2>
                <p class="text-gray-600">Please review and accept before proceeding to registration</p>
            </div>

            <!-- Data Privacy Notice -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                <h3 class="text-xl font-bold text-blue-800 mb-4">Data Privacy Notice</h3>
                <div class="max-h-60 overflow-y-auto mb-4 pr-2 text-sm text-gray-700 space-y-2">
                    <p>In compliance with the Data Privacy Act of 2012, we are committed to protecting your personal information. By providing your information, you consent to the following:</p>
                    
                    <p><strong>Information Collection:</strong> We collect personal information for enrollment, academic records, and administrative purposes.</p>
                    
                    <p><strong>Information Use:</strong> Your information will be used for:</p>
                    <ul class="list-disc ml-5">
                        <li>Processing enrollment and managing student records</li>
                        <li>Academic administration and support services</li>
                        <li>Communication regarding school-related matters</li>
                        <li>Compliance with regulatory requirements</li>
                    </ul>
                    
                    <p><strong>Information Sharing:</strong> Your information may be shared with:</p>
                    <ul class="list-disc ml-5">
                        <li>Authorized school personnel and departments</li>
                        <li>Government agencies as required by law</li>
                        <li>Third-party service providers under strict confidentiality</li>
                    </ul>
                    
                    <p><strong>Data Security:</strong> We implement reasonable measures to protect your personal information from unauthorized access or disclosure.</p>
                    
                    <p><strong>Your Rights:</strong> You have the right to access, correct, and request deletion of your personal information, subject to certain limitations.</p>
                </div>
                
                <!-- Data Privacy Agreement Checkbox -->
                <div class="flex items-start mt-4">
                    <div class="flex items-center h-5">
                        <input id="privacy_agreement" name="privacy_agreement" type="checkbox" required class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="privacy_agreement" class="font-medium text-gray-700">I have read and agree to the data privacy notice</label>
                    </div>
                </div>
            </div>
            
            <!-- Terms of Service -->
            <div class="flex items-start mb-6">
                <div class="flex items-center h-5">
                    <input id="terms_agreement" name="terms_agreement" type="checkbox" required class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                </div>
                <div class="ml-3 text-sm">
                    <label for="terms_agreement" class="font-medium text-gray-700">I agree to the school's <a href="#" class="text-blue-600 hover:underline">Terms of Service</a> and <a href="#" class="text-blue-600 hover:underline">Policies</a></label>
                </div>
            </div>

            <!-- Age Verification -->
            <div class="space-y-4 mb-6">
                <label for="age_verification" class="block text-sm font-medium text-gray-700">I am:</label>
                <div class="flex flex-col space-y-2">
                    <label class="inline-flex items-center">
                        <input type="radio" name="age_verification" value="under18" class="w-4 h-4 text-blue-500 border-gray-300 focus:ring-blue-500">
                        <span class="ml-2">Under 18 years old</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="age_verification" value="18orAbove" class="w-4 h-4 text-blue-500 border-gray-300 focus:ring-blue-500">
                        <span class="ml-2">18 years old or above</span>
                    </label>
                </div>
            </div>
            
            <!-- Proceed Button -->
            <button type="button" id="proceedFromPrivacy" class="w-full px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300 transition-all duration-200">
                <span class="flex items-center justify-center">
                    Proceed
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </button>
        </div>

        <!-- Parent/Guardian Consent Container (initially hidden) -->
        <div id="parent_consent_container" class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-2xl hidden">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Parent/Guardian Consent</h2>
                <p class="text-gray-600">Required for applicants under 18 years old</p>
            </div>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
                <h3 class="text-xl font-bold text-yellow-800 mb-4">Parent/Guardian Consent</h3>
                <div class="space-y-4 text-sm text-gray-700">
                    <p>As the applicant is under 18 years old, parent or guardian consent is required for enrollment.</p>
                    
                    <div class="space-y-4">
                        <label for="parent_name" class="block text-sm font-medium text-gray-700">Parent/Guardian Full Name <span class="text-red-500">*</span></label>
                        <input id="parent_name" name="parent_name" type="text" placeholder="Full Name of Parent/Guardian" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" required>
                    </div>
                    
                    <div class="space-y-4">
                        <label for="parent_relation" class="block text-sm font-medium text-gray-700">Relationship to Applicant <span class="text-red-500">*</span></label>
                        <input id="parent_relation" name="parent_relation" type="text" placeholder="e.g. Mother, Father, Legal Guardian" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" required>
                    </div>
                    
                    <!-- Parent/Guardian Agreement Checkbox -->
                    <div class="flex items-start mt-4">
                        <div class="flex items-center h-5">
                            <input id="parent_agreement" name="parent_agreement" type="checkbox" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" required>
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="parent_agreement" class="font-medium text-gray-700">I am the parent/legal guardian of the applicant and I consent to their enrollment and the processing of their personal information as described in the Data Privacy Notice.</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex space-x-4">
                <button type="button" id="backToPrivacy" class="w-1/3 px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-all duration-200">
                    <span class="flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back
                    </span>
                </button>
                
                <button type="button" id="proceedToRegistration" class="w-2/3 px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300 transition-all duration-200">
                    <span class="flex items-center justify-center">
                        Proceed to Registration
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                </button>
            </div>
        </div>

        <!-- Registration Form (initially hidden) -->
        <div id="registration_container" class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-2xl hidden">
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
                         style="width: 20%">
                    </div>
                </div>
            </div>

            <form id="registrationForm" action="Logic_enroll.php" method="post" class="space-y-8" enctype="multipart/form-data">
                <!-- Hidden fields to store consent data -->
                <input type="hidden" id="hidden_privacy_agreement" name="hidden_privacy_agreement" value="">
                <input type="hidden" id="hidden_age_verification" name="hidden_age_verification" value="">
                <input type="hidden" id="hidden_parent_name" name="hidden_parent_name" value="">
                <input type="hidden" id="hidden_parent_relation" name="hidden_parent_relation" value="">
                <input type="hidden" id="hidden_parent_agreement" name="hidden_parent_agreement" value="">
                <input type="hidden" id="hidden_terms_agreement" name="hidden_terms_agreement" value="">
                
                <!-- Step 1 -->
                <div class="step" id="step1">
                    <input type="hidden" id="IDNO" name="IDNO" value="<?php echo isset($_SESSION['STUDID']) ? $_SESSION['STUDID'] : $autonum->AUTO; ?>">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <label for="FNAME" class="block text-sm font-medium text-gray-700">First Name <span class="text-red-500">*</span></label>
                            <input required id="FNAME" name="FNAME" placeholder="Enter your first name" type="text" 
                                   pattern="[A-Za-z\s]+" title="Only letters are allowed"
                                   value="<?php echo isset($_SESSION['FNAME']) ? $_SESSION['FNAME'] : ''; ?>" 
                                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                   oninput="validateLettersOnly(this)">
                        </div>
                        <div class="space-y-4">
                            <label for="LNAME" class="block text-sm font-medium text-gray-700">Last Name <span class="text-red-500">*</span></label>
                            <input required id="LNAME" name="LNAME" placeholder="Last Name" type="text" 
                                   pattern="[A-Za-z\s]+" title="Only letters are allowed"
                                   value="<?php echo isset($_SESSION['LNAME']) ? $_SESSION['LNAME'] : ''; ?>" 
                                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                   oninput="validateLettersOnly(this)">
                        </div>
                        <div class="space-y-4">
                            <label for="MI" class="block text-sm font-medium text-gray-700">Middle Name <span class="text-red-500">*</span></label>
                            <input required id="MI" name="MI" placeholder="Middle Name" type="text" 
                                   pattern="[A-Za-z\s]+" title="Only letters are allowed"
                                   value="<?php echo isset($_SESSION['MI']) ? $_SESSION['MI'] : ''; ?>" 
                                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                   oninput="validateLettersOnly(this)">
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
                            <input required id="CONTACT" name="CONTACT" placeholder="Contact Number" type="text" 
                                   pattern="[0-9]+" title="Only numbers are allowed"
                                   maxlength="11" value="<?php echo isset($_SESSION['CONTACT']) ? $_SESSION['CONTACT'] : ''; ?>" 
                                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                   oninput="validateNumbersOnly(this)">
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
                            <input required id="GUARDIAN" name="GUARDIAN" placeholder="Guardian Name" type="text" 
                                   pattern="[A-Za-z\s]+" title="Only letters are allowed"
                                   value="<?php echo isset($_SESSION['GUARDIAN']) ? $_SESSION['GUARDIAN'] : ''; ?>" 
                                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                   oninput="validateLettersOnly(this)">
                        </div>
                        <div class="space-y-4">
                            <label for="GCONTACT" class="block text-sm font-medium text-gray-700">Guardian Contact <span class="text-red-500">*</span></label>
                            <input required id="GCONTACT" name="GCONTACT" placeholder="Contact Number" type="text" 
                                   pattern="[0-9]+" title="Only numbers are allowed"
                                   value="<?php echo isset($_SESSION['GCONTACT']) ? $_SESSION['GCONTACT'] : ''; ?>" 
                                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                   oninput="validateNumbersOnly(this)">
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
            // Container references
            const dataPrivacyContainer = document.getElementById('data_privacy_container');
            const parentConsentContainer = document.getElementById('parent_consent_container');
            const registrationContainer = document.getElementById('registration_container');
            
            // Button references
            const proceedFromPrivacyBtn = document.getElementById('proceedFromPrivacy');
            const backToPrivacyBtn = document.getElementById('backToPrivacy');
            const proceedToRegistrationBtn = document.getElementById('proceedToRegistration');
            
            // Store parent/guardian name for later use in registration form
            let parentGuardianName = '';
            
            // Proceed from privacy to next step
            proceedFromPrivacyBtn.addEventListener('click', function() {
                // Validate privacy agreements
                const privacyAgreement = document.getElementById('privacy_agreement');
                const termsAgreement = document.getElementById('terms_agreement');
                const ageVerification = document.querySelector('input[name="age_verification"]:checked');
                
                let isValid = true;
                
                // Validate privacy agreement
                if (!privacyAgreement.checked) {
                    privacyAgreement.classList.add('border-red-500');
                    isValid = false;
                } else {
                    privacyAgreement.classList.remove('border-red-500');
                }
                
                // Validate terms agreement
                if (!termsAgreement.checked) {
                    termsAgreement.classList.add('border-red-500');
                    isValid = false;
                } else {
                    termsAgreement.classList.remove('border-red-500');
                }
                
                // Validate age verification
                if (!ageVerification) {
                    document.querySelectorAll('input[name="age_verification"]').forEach(radio => {
                        radio.closest('label').classList.add('text-red-500');
                    });
                    isValid = false;
                } else {
                    document.querySelectorAll('input[name="age_verification"]').forEach(radio => {
                        radio.closest('label').classList.remove('text-red-500');
                    });
                }
                
                if (!isValid) {
                    alert('Please complete all required fields before proceeding.');
                    return;
                }
                
                // Store values in hidden fields
                document.getElementById('hidden_privacy_agreement').value = privacyAgreement.checked ? '1' : '0';
                document.getElementById('hidden_age_verification').value = ageVerification.value;
                document.getElementById('hidden_terms_agreement').value = termsAgreement.checked ? '1' : '0';
                
                // Determine next step based on age
                if (ageVerification.value === 'under18') {
                    // Show parent consent container
                    dataPrivacyContainer.classList.add('hidden');
                    parentConsentContainer.classList.remove('hidden');
                } else {
                    // Skip to registration form
                    dataPrivacyContainer.classList.add('hidden');
                    registrationContainer.classList.remove('hidden');
                }
            });
            
            // Go back from parent consent to privacy
            backToPrivacyBtn.addEventListener('click', function() {
                parentConsentContainer.classList.add('hidden');
                dataPrivacyContainer.classList.remove('hidden');
            });
            
            // Proceed from parent consent to registration
            proceedToRegistrationBtn.addEventListener('click', function() {
                // Validate parent consent form
                const parentName = document.getElementById('parent_name');
                const parentRelation = document.getElementById('parent_relation');
                const parentAgreement = document.getElementById('parent_agreement');
                
                let isValid = true;
                
                if (parentName.value.trim() === '') {
                    parentName.classList.add('border-red-500');
                    isValid = false;
                } else {
                    parentName.classList.remove('border-red-500');
                }
                
                if (parentRelation.value.trim() === '') {
                    parentRelation.classList.add('border-red-500');
                    isValid = false;
                } else {
                    parentRelation.classList.remove('border-red-500');
                }
                
                if (!parentAgreement.checked) {
                    parentAgreement.classList.add('border-red-500');
                    isValid = false;
                } else {
                    parentAgreement.classList.remove('border-red-500');
                }
                
                if (!isValid) {
                    alert('Please complete all required parent/guardian consent fields before proceeding.');
                    return;
                }
                
                // Store parent/guardian info
                parentGuardianName = parentName.value;
                document.getElementById('hidden_parent_name').value = parentGuardianName;
                document.getElementById('hidden_parent_relation').value = parentRelation.value;
                document.getElementById('hidden_parent_agreement').value = parentAgreement.checked ? '1' : '0';
                
                // Proceed to registration form
                parentConsentContainer.classList.add('hidden');
                registrationContainer.classList.remove('hidden');
            });

            // Listen for step changes to auto-fill guardian name in Step 4
            document.getElementById('next').addEventListener('click', function() {
                // Current step is tracked in the enroll.js file
                // We need to check if we're about to show Step 4
                const currentStepShown = document.querySelector('.step:not(.hidden)');
                const currentStepIndex = Array.from(document.querySelectorAll('.step')).indexOf(currentStepShown);
                
                // If we're moving from Step 3 to Step 4
                if (currentStepIndex === 2) {
                    // This will be called when next is clicked on Step 3, before Step 4 is shown
                    setTimeout(function() {
                        const guardianField = document.getElementById('GUARDIAN');
                        // Only auto-fill if the field is empty and we have a parent/guardian name
                        if (guardianField && guardianField.value === '' && parentGuardianName) {
                            guardianField.value = parentGuardianName;
                        }
                    }, 100); // Small delay to ensure step has changed
                }
            });

            // Existing code for email handling and file uploads
            const verifiedEmail = localStorage.getItem('verifiedEmail');
            if (verifiedEmail) {
                const emailInput = document.getElementById('email');
                emailInput.value = verifiedEmail;
                emailInput.setAttribute('readonly', true);
            }

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

            // Validation functions for input fields
            window.validateLettersOnly = function(input) {
                // Replace any non-letter characters (except spaces)
                input.value = input.value.replace(/[^A-Za-z\s]/g, '');
                
                // Add visual feedback
                if (input.value.trim() === '') {
                    input.classList.add('border-red-500');
                } else if (/^[A-Za-z\s]+$/.test(input.value)) {
                    input.classList.remove('border-red-500');
                    input.classList.add('border-green-500');
                } else {
                    input.classList.add('border-red-500');
                    input.classList.remove('border-green-500');
                }
            };
            
            window.validateNumbersOnly = function(input) {
                // Replace any non-numeric characters
                input.value = input.value.replace(/[^0-9]/g, '');
                
                // Add visual feedback
                if (input.value.trim() === '') {
                    input.classList.add('border-red-500');
                } else if (/^[0-9]+$/.test(input.value)) {
                    input.classList.remove('border-red-500');
                    input.classList.add('border-green-500');
                } else {
                    input.classList.add('border-red-500');
                    input.classList.remove('border-green-500');
                }
            };
            
            // Add validation handlers to parent name field
            const parentNameField = document.getElementById('parent_name');
            if (parentNameField) {
                parentNameField.setAttribute('pattern', '[A-Za-z\\s]+');
                parentNameField.setAttribute('title', 'Only letters are allowed');
                parentNameField.addEventListener('input', function() {
                    validateLettersOnly(this);
                });
            }
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
            <p class="text-gray-600 text-center mb-4 max-h-32 overflow-y-auto">Please check your gmail for updates.</p>
            <p class="text-gray-500 text-center text-sm">Redirecting to Home in <span id="countdownTimer">5</span>s</p>
        </div>
    </div>

    <!-- Add document verification warning banner -->
    <div id="verificationBanner" class="fixed top-0 left-0 right-0 bg-yellow-100 border-b border-yellow-300 text-yellow-800 px-4 py-2 text-center hidden">
        <strong>Note:</strong> Some documents require verification. Please bring original documents during your campus visit.
    </div>
    
    <!-- Add this script to ensure success popup works -->
    <script>
        // Make sure all required elements are available
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded. Checking elements:');
            console.log('- Success Popup:', document.getElementById('successPopup'));
            console.log('- Loading Screen:', document.getElementById('loadingScreen'));
            console.log('- Verification Banner:', document.getElementById('verificationBanner'));
        });
    </script>
</body>