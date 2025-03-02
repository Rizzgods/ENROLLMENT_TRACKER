<body class="bg-gray-100 p-4 h-screen flex justify-center">
    <!-- Parent Flex Container -->
    <div class="flex w-full max-w-7xl mx-auto space-x-4">
        
        <!-- Sidebar (Counts) -->
        <div class="w-1/5 bg-white p-4 rounded-lg shadow-md flex flex-col">
            <h2 class="text-lg font-bold mb-4">Dashboard</h2>
            <div class="bg-blue-500 text-white p-4 rounded-lg shadow-md text-center cursor-pointer border border-transparent hover:ring-4 hover:ring-gray-300 focus-within:ring-4 focus-within:ring-gray-300"
            data-target="AdminTable">
                <h2 class="text-lg font-bold">Admins</h2>
                <p class="text-2xl font-semibold"><?php echo $admin_count; ?></p> 
            </div>
            <br>
            <div class="bg-green-500 text-white p-4 rounded-lg shadow-md text-center cursor-pointer" 
            data-target="preEnrolleesTable">
                <h2 class="text-lg font-bold">Pre-Enrollees</h2>
                <p class="text-2xl font-semibold"><?php echo $pre_enrollees_count; ?></p> 
            </div>
            <br>
            <div class="bg-yellow-500 text-white p-4 rounded-lg shadow-md text-center cursor-pointer" 
            data-target="EnrolleesTable">
                <h2 class="text-lg font-bold">Enrollees</h2>
                <p class="text-2xl font-semibold"><?php echo $enrollees_count; ?></p> 
            </div>
            <br>
            <div class="bg-red-500 text-white p-4 rounded-lg shadow-md text-center cursor-pointer" 
            data-target="StudentTable">
                <h2 class="text-lg font-bold">Students</h2>
                <p class="text-2xl font-semibold"><?php echo $students_count; ?></p> 
            </div>
        </div>

        <!-- Enrollment Statistics (Charts) -->
        <div class="w-4/5 bg-white p-6 rounded-lg shadow-lg flex flex-col">
            <h2 class="text-lg font-bold mb-4">Enrollment Statistics</h2>
            <div class="grid grid-cols-2 gap-4">
                <div class="w-full h-60"> <canvas id="courseChart"></canvas> </div>
                <div class="w-full h-60"> <canvas id="weeklyEnrollmentChart"></canvas> </div>
                <div class="w-96 h-40 bg-blue-500 text-white p-6 rounded-lg shadow-md text-center flex flex-col justify-center items-center mx-auto mt-10">
                    <div class="text-xl font-semibold">
                        <span id="acceptedPercentage" class="text-green-300"></span>% Accepted
                    </div>
                    <h2 class="text-lg font-bold mt-2">Enrollment Percentage</h2>
                </div>
                <div class="flex justify-center items-center w-full h-60"><canvas id="paymentStatusChart"></canvas> </div>
            </div>
        </div>
    </div>
</body>







