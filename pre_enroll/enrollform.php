<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-2xl">
        <h2 class="text-2xl font-bold mb-4 text-center">Pre-Registration Form</h2>
        <p class="text-center text-gray-500 mb-4">Step <span id="stepCounter">1</span> of 4</p>

        <form action="" method="post" class="space-y-6">
            <!-- Step 1 -->
            <div class="step" id="step1">
                <input type="hidden" id="IDNO" name="IDNO" value="<?php echo isset($_SESSION['STUDID']) ? $_SESSION['STUDID'] : $autonum->AUTO; ?>">

                <div>
                    <label for="FNAME" class="block text-sm font-medium text-gray-700">Firstname</label>
                    <input required id="FNAME" name="FNAME" placeholder="First Name" type="text" value="<?php echo isset($_SESSION['FNAME']) ? $_SESSION['FNAME'] : ''; ?>" class="mt-1 block w-full p-2 border rounded-md">
                </div>
                <div>
                    <label for="LNAME" class="block text-sm font-medium text-gray-700">Lastname</label>
                    <input required id="LNAME" name="LNAME" placeholder="Last Name" type="text" value="<?php echo isset($_SESSION['LNAME']) ? $_SESSION['LNAME'] : ''; ?>" class="mt-1 block w-full p-2 border rounded-md">
                </div>
                <div>
                    <label for="MI" class="block text-sm font-medium text-gray-700">Middle Name</label>
                    <input required id="MI" name="MI" placeholder="Middle Name" type="text" value="<?php echo isset($_SESSION['MI']) ? $_SESSION['MI'] : ''; ?>"class="mt-1 block w-full p-2 border rounded-md">
                </div>
                <div>
                    <label for="PADDRESS" class="block text-sm font-medium text-gray-700">Address</label>
                    <input required id="PADDRESS" name="PADDRESS" placeholder="Permanent Address" type="text" value="<?php echo isset($_SESSION['PADDRESS']) ? $_SESSION['PADDRESS'] : ''; ?>" class="mt-1 block w-full p-2 border rounded-md">
                </div>
                <div>
                    <label for="EMAIL" class="block text-sm font-medium text-gray-700">Email</label>
                    <input required id="EMAIL" name="EMAIL" placeholder="Email Address" type="text" value="<?php echo isset($_SESSION['EMAIL']) ? $_SESSION['EMAIL'] : ''; ?>" class="mt-1 block w-full p-2 border rounded-md">
                </div>
            </div>

            <!-- Step 2 -->
            <div class="step hidden" id="step2">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Sex</label>
                    <div class="mt-1">
                        <label class="inline-flex items-center">
                            <input checked id="optionsRadios1" name="optionsRadios" type="radio" value="Female" class="mr-2"> Female
                        </label>
                        <label class="inline-flex items-center ml-4">
                            <input id="optionsRadios2" name="optionsRadios" type="radio" value="Male" class="mr-2"> Male
                        </label>
                    </div>
                </div>
                <div>
                    <label for="BIRTHDATE" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                    <input required id="BIRTHDATE" name="BIRTHDATE" type="text" placeholder="mm/dd/yyyy" value="<?php echo isset($_SESSION['BIRTHDATE']) ? $_SESSION['BIRTHDATE'] : ''; ?>" class="mt-1 block w-full p-2 border rounded-md">
                </div>
                <div>
                    <label for="BIRTHPLACE" class="block text-sm font-medium text-gray-700">Place of Birth</label>
                    <input required id="BIRTHPLACE" name="BIRTHPLACE" placeholder="Place of Birth" type="text" value="<?php echo isset($_SESSION['BIRTHPLACE']) ? $_SESSION['BIRTHPLACE'] : ''; ?>" class="mt-1 block w-full p-2 border rounded-md">
                </div>
            </div>

            <!-- Step 3 -->
            <div class="step hidden" id="step3">
                <div>
                    <label for="NATIONALITY" class="block text-sm font-medium text-gray-700">Nationality</label>
                    <input required id="NATIONALITY" name="NATIONALITY" placeholder="Nationality" type="text" value="<?php echo isset($_SESSION['NATIONALITY']) ? $_SESSION['NATIONALITY'] : ''; ?>" class="mt-1 block w-full p-2 border rounded-md">
                </div>
                <div>
                    <label for="RELIGION" class="block text-sm font-medium text-gray-700">Religion</label>
                    <input required id="RELIGION" name="RELIGION" placeholder="Religion" type="text" value="<?php echo isset($_SESSION['RELIGION']) ? $_SESSION['RELIGION'] : ''; ?>" class="mt-1 block w-full p-2 border rounded-md">
                </div>
                <div>
                    <label for="CONTACT" class="block text-sm font-medium text-gray-700">Contact No.</label>
                    <input required id="CONTACT" name="CONTACT" placeholder="Contact Number" type="number" maxlength="11" value="<?php echo isset($_SESSION['CONTACT']) ? $_SESSION['CONTACT'] : ''; ?>" class="mt-1 block w-full p-2 border rounded-md">
                </div>
                <div>
                    <label for="CONTACT" class="block text-sm font-medium text-gray-700">Course/year</label>
                    <select class="form-control input-sm" name="COURSE">
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
                <div>
                    <label for="SEMESTER" class="block text-sm font-medium text-gray-700">Semester to Enroll</label>
                    <input required id="SEMESTER" name="SEMESTER" placeholder="1=1st Semester and 2=2nd Semester" type="number" maxlength="1" value="<?php echo isset($_SESSION['SEMESTER']) ? $_SESSION['SEMESTER'] : ''; ?>" class="mt-1 block w-full p-2 border rounded-md">
                </div>
            </div>

            <!-- Step 4 -->
            <div class="step hidden" id="step4">
            <div>
                    <label for="Civil Status" class="block text-sm font-medium text-gray-700">Civil Status</label>
                    <select class="form-control input-sm" name="CIVILSTATUS">
						<option value="<?php echo isset($_SESSION['CIVILSTATUS']) ? $_SESSION['CIVILSTATUS'] : 'Select'; ?>"><?php echo isset($_SESSION['CIVILSTATUS']) ? $_SESSION['CIVILSTATUS'] : 'Select'; ?></option>
						 <option value="Single">Single</option>
						 <option value="Married">Married</option> 
						 <option value="Widow">Widow</option>
					</select>
                </div>
                <div>
                    <label for="USER_NAME" class="block text-sm font-medium text-gray-700">Username</label>
                    <input required id="USER_NAME" name="USER_NAME" placeholder="Username" type="text" value="<?php echo isset($_SESSION['USER_NAME']) ? $_SESSION['USER_NAME'] : ''; ?>" class="mt-1 block w-full p-2 border rounded-md">
                </div>
                <div>
                    <label for="PASS" class="block text-sm font-medium text-gray-700">Password</label>
                    <input required id="PASS" name="PASS" placeholder="Password" type="password" value="<?php echo isset($_SESSION['PASS']) ? $_SESSION['PASS'] : ''; ?>" class="mt-1 block w-full p-2 border rounded-md">
                </div>
                <div>
                    <label for="GUARDIAN" class="block text-sm font-medium text-gray-700">Guardian</label>
                    <input required id="GUARDIAN" name="GUARDIAN" placeholder="Guardian Name" type="text" value="<?php echo isset($_SESSION['GUARDIAN']) ? $_SESSION['GUARDIAN'] : ''; ?>" class="mt-1 block w-full p-2 border rounded-md">
                </div>
                <div>
                    <label for="GCONTACT" class="block text-sm font-medium text-gray-700">Guardian Contact</label>
                    <input required id="GCONTACT" name="GCONTACT" placeholder="Contact Number" type="text" value="<?php echo isset($_SESSION['GCONTACT']) ? $_SESSION['GCONTACT'] : ''; ?>" class="mt-1 block w-full p-2 border rounded-md">
                </div>
            </div>

            <div class="relative flex justify-between">
                <button type="button" id="prev" class="hidden px-4 py-2 bg-gray-300 rounded-md">Previous</button>
                <button type="button" id="next" class="px-4 py-2 bg-blue-500 text-white rounded-md">Next</button>
                <button type="submit" id="submit" name="regsubmit" class="hidden px-4 py-2 bg-green-500 text-white rounded-md">Submit</button>
            </div>
        </form>
    </div>

    <script>
 let step = 1;
const totalSteps = 4;
let errorTimeout;

document.getElementById("next").addEventListener("click", () => {
    if (!validateStep(step)) return; // Prevent proceeding if validation fails

    if (step < totalSteps) {
        document.getElementById(`step${step}`).classList.add("hidden");
        step++;
        document.getElementById(`step${step}`).classList.remove("hidden");
        document.getElementById("stepCounter").textContent = step;
    }
    updateButtons();
});

document.getElementById("prev").addEventListener("click", () => {
    if (step > 1) {
        document.getElementById(`step${step}`).classList.add("hidden");
        step--;
        document.getElementById(`step${step}`).classList.remove("hidden");
        document.getElementById("stepCounter").textContent = step;
    }
    updateButtons();
});

function validateStep(step) {
    const inputs = document.querySelectorAll(`#step${step} input`);
    let isValid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            input.classList.add("border-red-500"); // Highlight empty fields
        } else {
            input.classList.remove("border-red-500");
        }
    });

    showErrorBubble(!isValid);
    return isValid;
}

function showErrorBubble(show) {
    const nextButton = document.getElementById("next");
    let errorBubble = document.getElementById("error-bubble");

    if (show) {
        if (!errorBubble) {
            errorBubble = document.createElement("div");
            errorBubble.id = "error-bubble";
            errorBubble.textContent = "Please fill in all fields before proceeding.";
            errorBubble.className = "absolute bg-red-500 text-white text-sm px-3 py-1 rounded-md bottom-full mb-2 shadow-md";
            nextButton.parentNode.classList.add("relative");
            nextButton.parentNode.appendChild(errorBubble);
        }

        // Clear any existing timeout before setting a new one
        clearTimeout(errorTimeout);
        errorTimeout = setTimeout(() => {
            errorBubble?.remove();
        }, 5000); // Hide error after 5 seconds
    } else {
        if (errorBubble) {
            errorBubble.remove();
        }
    }
}

function updateButtons() {
    document.getElementById("prev").classList.toggle("hidden", step === 1);
    document.getElementById("next").classList.toggle("hidden", step === totalSteps);
    document.getElementById("submit").classList.toggle("hidden", step !== totalSteps);
}


    </script>
</body>
</html>
