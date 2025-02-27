<body class="bg-gray-100 p-4 h-screen flex">
    <!-- Parent Flex Container -->
    <div class="flex w-full space-x-4">
        
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
        <div class="w-96 h-40 bg-blue-500 text-white p-6 rounded-lg shadow-md text-center flex flex-col justify-center items-center ml-10 mr-10 mt-10 ">
    <div class="text-xl font-semibold">
        <span id="acceptedPercentage" class="text-green-300"></span>% Accepted
    </div>
    <h2 class="text-lg font-bold mt-2">Enrollment Percentage</h2>
</div>

<div class="flex justify-start items-center w-full h-60 ml-10"><canvas id="paymentStatusChart"></canvas> </div>
    </div>
</div>
    </div>
</body>

    <div class="bg-white p-6 rounded-lg shadow-lg">
        
    <input type="text" id="AdminSearch" class="border p-2 w-full mb-4" placeholder="Search Admins by Name...">
        <table id="AdminTable" class="data-table w-full border-collapse border border-gray-300">
            <thead class="bg-blue-500 text-white">
                <tr>
                    <th class="border p-2">ID</th>
                    <th class="border p-2">Name</th>
                    <th class="border p-2">Role</th>
                    <th class="border p-2">EMPID</th>
                    <th class="border p-2">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = $mydb->query("SELECT ACCOUNT_ID, ACCOUNT_NAME,ACCOUNT_TYPE,EMPID FROM useraccounts");
                while ($row = $query->fetch_assoc()) {
                    echo "<tr class='border'>
                            <td class='border p-2'>{$row['ACCOUNT_ID']}</td>
                            <td class='border p-2'>{$row['ACCOUNT_NAME']}</td>
                            <td class='border p-2'>{$row['ACCOUNT_TYPE']}</td>
                            <td class='border p-2'>{$row['EMPID']}</td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>

        <input type="text" id="preEnrolleesSearch" class="border p-2 w-full mt-6 mb-4 hidden" placeholder="Search Pre-Enrollees by Name...">
        <table id="preEnrolleesTable" class="w-full border-collapse border border-gray-300 hidden">
        <thead class="bg-green-500 text-white">
            <tr>
                <th class="border p-2">ID</th>
                <th class="border p-2">Name</th>
                <th class="border p-2">Sex</th>
                <th class="border p-2">Age</th>
                <th class="border p-2">Address</th>
                <th class="border p-2">Contact No.</th>
                <th class="border p-2">Status</th>
                <th class="border p-2">Course</th>
                <th class="border p-2">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php  
            $query = $mydb->query("SELECT * FROM `tblstudent` s, course c WHERE s.COURSE_ID=c.COURSE_ID AND NewEnrollees=1 AND student_status='New'");

            while ($result = $query->fetch_assoc()) {
                $age = ($result['BDAY'] != '0000-00-00') ? date_diff(date_create($result['BDAY']), date_create('today'))->y : 'None';

                echo "<tr class='border'>";
                echo "<td class='border p-2'>{$result['IDNO']}</td>";
                echo "<td class='border p-2'>{$result['LNAME']}, {$result['FNAME']} {$result['MNAME']}</td>";
                echo "<td class='border p-2'>{$result['SEX']}</td>";
                echo "<td class='border p-2'>{$age}</td>";
                echo "<td class='border p-2'>{$result['HOME_ADD']}</td>";
                echo "<td class='border p-2'>{$result['CONTACT_NO']}</td>";
                echo "<td class='border p-2'>{$result['student_status']}</td>"; 
                echo "<td class='border p-2'>{$result['COURSE_NAME']}</td>";

                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
    <input type="text" id="EnrolleesSearch" class="border p-2 w-full mt-6 mb-4 hidden" placeholder="Search Enrollees by Name...">
    <table id="EnrolleesTable" class="w-full border-collapse border border-gray-300 hidden">
        <thead class="bg-yellow-500 text-white">
            <tr>
                <th class="border p-2">ID</th>
                <th class="border p-2">Name</th>
                <th class="border p-2">Sex</th>
                <th class="border p-2">Age</th>
                <th class="border p-2">Address</th>
                <th class="border p-2">Contact No.</th>
                <th class="border p-2">Course</th>
                <th class="border p-2">Status</th>
                <th class="border p-2">Schedule</th>
                <th class="border p-2">Payment</th>
                <th class="border p-2">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php  
            $query = $mydb->query("
                SELECT 
                    s.IDNO, 
                    s.LNAME, 
                    s.FNAME, 
                    s.MNAME, 
                    s.SEX, 
                    s.BDAY, 
                    s.HOME_ADD, 
                    s.CONTACT_NO, 
                    c.COURSE_NAME, 
                    sa.SCHEDULE, 
                    sa.STATUS, 
                    sa.PAYMENT
                FROM tblstudent s
                JOIN course c ON s.COURSE_ID = c.COURSE_ID
                LEFT JOIN studentaccount sa ON s.IDNO = sa.user_id
                WHERE sa.STATUS = 'accepted'
                AND NOT EXISTS (
                    SELECT 1 FROM student st WHERE st.id = s.IDNO
                )
            ");

            while ($result = $query->fetch_assoc()) {
                $age = ($result['BDAY'] != '0000-00-00') ? date_diff(date_create($result['BDAY']), date_create('today'))->y : 'None';

                echo "<tr class='border'>";
                echo "<td class='border p-2'>{$result['IDNO']}</td>";
                echo "<td class='border p-2'>{$result['LNAME']}, {$result['FNAME']} {$result['MNAME']}</td>";
                echo "<td class='border p-2'>{$result['SEX']}</td>";
                echo "<td class='border p-2'>{$age}</td>";
                echo "<td class='border p-2'>{$result['HOME_ADD']}</td>";
                echo "<td class='border p-2'>{$result['CONTACT_NO']}</td>";
                echo "<td class='border p-2'>{$result['COURSE_NAME']}</td>";
                echo "<td class='border p-2'>{$result['STATUS']}</td>";
                echo "<td class='border p-2'>{$result['SCHEDULE']}</td>";  
                echo "<td class='border p-2'>{$result['PAYMENT']}</td>";   
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
    <input type="text" id="StudentSearch" class="border p-2 w-full mt-6 mb-4 hidden" placeholder="Search Students by Name...">
    <table id="StudentTable" class="w-full border-collapse border border-gray-300 hidden">
        <thead class="bg-red-500 text-white">
            <tr>
                <th class="border p-2">ID</th>
                <th class="border p-2">Name</th>
                <th class="border p-2">Sex</th>
                <th class="border p-2">Age</th>
                <th class="border p-2">Address</th>
                <th class="border p-2">Contact No.</th>
                <th class="border p-2">Course</th>
                <th class="border p-2">Payment</th>
                <th class="border p-2">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php  
            $query = $mydb->query("
                SELECT 
                    s.IDNO, 
                    s.LNAME, 
                    s.FNAME, 
                    s.MNAME, 
                    s.SEX, 
                    s.BDAY, 
                    s.HOME_ADD, 
                    s.CONTACT_NO, 
                    c.COURSE_NAME, 
                    sa.PAYMENT
                FROM tblstudent s
                JOIN course c ON s.COURSE_ID = c.COURSE_ID
                LEFT JOIN studentaccount sa ON s.IDNO = sa.user_id
                INNER JOIN student st ON s.IDNO = st.id  -- Ensures only students in 'student' table are included
                WHERE s.student_status = 'approved'
            ");

            while ($result = $query->fetch_assoc()) {
                $age = ($result['BDAY'] != '0000-00-00') ? date_diff(date_create($result['BDAY']), date_create('today'))->y : 'None';

                echo "<tr class='border'>";
                echo "<td class='border p-2'>{$result['IDNO']}</td>";
                echo "<td class='border p-2'>{$result['LNAME']}, {$result['FNAME']} {$result['MNAME']}</td>";
                echo "<td class='border p-2'>{$result['SEX']}</td>";
                echo "<td class='border p-2'>{$age}</td>";
                echo "<td class='border p-2'>{$result['HOME_ADD']}</td>";
                echo "<td class='border p-2'>{$result['CONTACT_NO']}</td>";
                echo "<td class='border p-2'>{$result['COURSE_NAME']}</td>";
                echo "<td class='border p-2'>{$result['PAYMENT']}</td>";   

                echo "</tr>";
            }
            ?>
        </tbody>
    </table>


        
        </div>





   